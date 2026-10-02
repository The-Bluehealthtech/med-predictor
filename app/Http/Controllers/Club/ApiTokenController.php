<?php

namespace App\Http\Controllers\Club;

use App\Http\Controllers\Controller;
use App\Services\Dtn\ApiAbilities;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Espace club — jetons d'API du logiciel du club.
 * Un jeton est limité à cet espace et n'est affiché qu'une seule fois.
 */
class ApiTokenController extends Controller
{
    private const SPACE = 'club';

    public function index(Request $request): View
    {
        $config = ApiAbilities::SPACES[self::SPACE];

        return view('club.selections.api-access', [
            'config' => $config,
            'tokens' => $request->user()->tokens()->where('name', 'like', $config['prefix'] . ':%')->orderByDesc('created_at')->get(),
            'canHaveMedical' => ApiAbilities::canHaveMedical(self::SPACE, $request->user()),
            'plainToken' => session('plain_token'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:60'],
            'expires_in_days' => ['required', 'in:30,90,365'],
            'with_medical' => ['nullable', 'boolean'],
        ]);
        $user = $request->user();
        $token = $user->createToken(
            ApiAbilities::SPACES[self::SPACE]['prefix'] . ':' . $data['name'],
            ApiAbilities::forSpace(self::SPACE, $user, (bool) ($data['with_medical'] ?? false)),
            now()->addDays((int) $data['expires_in_days'])
        );

        return redirect()->route('club.selections.api-access')->with('plain_token', $token->plainTextToken)
            ->with('status', 'Jeton créé. Copiez-le maintenant : il ne sera plus affiché.');
    }

    public function destroy(Request $request, string $token): RedirectResponse
    {
        $deleted = $request->user()->tokens()->where('id', (int) $token)
            ->where('name', 'like', ApiAbilities::SPACES[self::SPACE]['prefix'] . ':%')->delete();
        abort_unless($deleted, 404);

        return redirect()->route('club.selections.api-access')->with('status', 'Jeton révoqué.');
    }
}
