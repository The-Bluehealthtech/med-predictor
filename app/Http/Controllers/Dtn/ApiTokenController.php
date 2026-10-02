<?php

namespace App\Http\Controllers\Dtn;

use App\Http\Controllers\Controller;
use App\Services\Dtn\ApiAbilities;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Jetons d'API des sélections nationales : chaque espace (fédération, club)
 * crée et révoque les siens. Un jeton n'est affiché qu'une seule fois.
 */
class ApiTokenController extends Controller
{
    public function index(Request $request, string $space): View
    {
        $config = ApiAbilities::SPACES[$space];

        return view('dtn.api-access', [
            'space' => $space,
            'config' => $config,
            'tokens' => $request->user()->tokens()->where('name', 'like', $config['prefix'] . ':%')->orderByDesc('created_at')->get(),
            'canHaveMedical' => ApiAbilities::canHaveMedical($space, $request->user()),
            'plainToken' => session('plain_token'),
        ]);
    }

    public function store(Request $request, string $space): RedirectResponse
    {
        $config = ApiAbilities::SPACES[$space];
        $data = $request->validate([
            'name' => ['required', 'string', 'max:60'],
            'expires_in_days' => ['required', 'in:30,90,365'],
            'with_medical' => ['nullable', 'boolean'],
        ]);
        $user = $request->user();
        $token = $user->createToken(
            $config['prefix'] . ':' . $data['name'],
            ApiAbilities::forSpace($space, $user, (bool) ($data['with_medical'] ?? false)),
            now()->addDays((int) $data['expires_in_days'])
        );

        return redirect()->route($this->route($space))->with('plain_token', $token->plainTextToken)
            ->with('status', 'Jeton créé. Copiez-le maintenant : il ne sera plus affiché.');
    }

    public function destroy(Request $request, string $token, string $space): RedirectResponse
    {
        $config = ApiAbilities::SPACES[$space];
        $deleted = $request->user()->tokens()->where('id', (int) $token)->where('name', 'like', $config['prefix'] . ':%')->delete();
        abort_unless($deleted, 404);

        return redirect()->route($this->route($space))->with('status', 'Jeton révoqué.');
    }

    private function route(string $space): string
    {
        return $space === 'federation' ? 'dtn.api-access' : 'club.selections.api-access';
    }
}
