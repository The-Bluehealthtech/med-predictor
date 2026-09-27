<?php

namespace App\Http\Controllers;

use App\Models\Association;
use App\Models\Club;
use App\Models\Confederation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OrganizationCardController extends Controller
{
    private const TYPES = [
        'confederations' => Confederation::class,
        'associations' => Association::class,
        'clubs' => Club::class,
    ];

    public static function canEdit($user, string $type, ?Model $record = null): bool
    {
        if (!$user || !isset(self::TYPES[$type])) {
            return false;
        }
        if ($user->isSystemAdmin()) {
            return true;
        }
        if ($type === 'confederations' || $record === null) {
            return $type === 'clubs' && $user->role === 'association_admin' && $user->association_id !== null;
        }
        if ($type === 'associations') {
            return $user->role === 'association_admin' && $user->association_id !== null
                && (int) $user->association_id === (int) $record->id;
        }
        return ($user->role === 'association_admin' && $user->association_id !== null
            && (int) $user->association_id === (int) $record->association_id)
            || ($user->role === 'club_admin' && $user->club_id !== null
            && (int) $user->club_id === (int) $record->id);
    }

    private function record(string $type, int $id): Model
    {
        abort_unless(isset(self::TYPES[$type]), 404);
        return self::TYPES[$type]::findOrFail($id);
    }

    public function create(Request $request, string $type)
    {
        abort_unless(self::canEdit($request->user(), $type), 403);
        return $this->form($type);
    }

    public function edit(Request $request, string $type, int $id)
    {
        $record = $this->record($type, $id);
        abort_unless(self::canEdit($request->user(), $type, $record), 403);
        return $this->form($type, $record);
    }

    private function form(string $type, ?Model $record = null)
    {
        $confederations = $type === 'associations' ? Confederation::orderBy('name')->get() : collect();
        $associations = $type === 'clubs' ? Association::orderBy('name')->get() : collect();
        return view('modules.organization-cards.form', compact('type', 'record', 'confederations', 'associations'));
    }

    public function store(Request $request, string $type)
    {
        abort_unless(self::canEdit($request->user(), $type), 403);
        return $this->save($request, $type);
    }

    public function update(Request $request, string $type, int $id)
    {
        $record = $this->record($type, $id);
        abort_unless(self::canEdit($request->user(), $type, $record), 403);
        return $this->save($request, $type, $record);
    }

    private function save(Request $request, string $type, ?Model $record = null)
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'short_name' => ['nullable', 'string', 'max:50'],
        ];
        if ($type === 'confederations') {
            $rules += [
                'country' => ['nullable', 'string', 'max:255'],
                'founded_year' => ['nullable', 'integer', 'between:1800,' . date('Y')],
                'status' => ['required', Rule::in(['active', 'inactive', 'suspended'])],
            ];
        } elseif ($type === 'associations') {
            $rules += [
                'country' => ['required', 'string', 'max:255'],
                'confederation_id' => ['required', 'exists:confederations,id'],
            ];
        } else {
            $rules['association_id'] = ['required', 'exists:associations,id'];
        }
        $data = $request->validate($rules);
        if ($type === 'clubs' && $request->user()->role === 'association_admin') {
            abort_unless((int) $request->user()->association_id === (int) $data['association_id'], 403);
        }
        $modelClass = self::TYPES[$type];
        $record ??= new $modelClass();
        $record->fill($data);
        // FIFA identifiers, ranking, sync status and version belong to the FIFA integration.
        $record->save();

        return redirect()->route('organization-cards.edit', [$type, $record->id])
            ->with('success', 'Modifications enregistrées.');
    }
}
