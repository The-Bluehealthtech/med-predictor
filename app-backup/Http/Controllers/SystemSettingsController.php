<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SystemSetting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class SystemSettingsController extends Controller
{
    /**
     * Afficher le tableau de bord des paramètres système
     */
    public function index(Request $request)
    {
        if (!Auth::check() || !in_array(Auth::user()->role, ['super_admin', 'system_admin'])) {
            return redirect()->route('login')->withErrors(['email' => 'Accès administrateur requis.']);
        }

        $group = $request->get('group', 'general');
        $settings = SystemSetting::byGroup($group)->orderBy('name')->get();
        $groups = SystemSetting::distinct()->pluck('group')->sort();

        // Statistiques
        $stats = [
            'total_settings' => SystemSetting::count(),
            'editable_settings' => SystemSetting::editable()->count(),
            'required_settings' => SystemSetting::required()->count(),
            'public_settings' => SystemSetting::public()->count(),
            'groups_count' => $groups->count()
        ];

        return view('admin.system-settings.index', compact('settings', 'groups', 'group', 'stats'));
    }

    /**
     * Afficher les détails d'un paramètre
     */
    public function show($id)
    {
        if (!Auth::check() || !in_array(Auth::user()->role, ['super_admin', 'system_admin'])) {
            return redirect()->route('login')->withErrors(['email' => 'Accès administrateur requis.']);
        }

        $setting = SystemSetting::with('updater')->findOrFail($id);

        return view('admin.system-settings.show', compact('setting'));
    }

    /**
     * Afficher le formulaire d'édition d'un paramètre
     */
    public function edit($id)
    {
        if (!Auth::check() || !in_array(Auth::user()->role, ['super_admin', 'system_admin'])) {
            return redirect()->route('login')->withErrors(['email' => 'Accès administrateur requis.']);
        }

        $setting = SystemSetting::findOrFail($id);

        if (!$setting->is_editable) {
            return redirect()->route('admin.system-settings.index')
                ->withErrors(['error' => 'Ce paramètre ne peut pas être modifié.']);
        }

        return view('admin.system-settings.edit', compact('setting'));
    }

    /**
     * Mettre à jour un paramètre
     */
    public function update(Request $request, $id)
    {
        if (!Auth::check() || !in_array(Auth::user()->role, ['super_admin', 'system_admin'])) {
            return redirect()->route('login')->withErrors(['email' => 'Accès administrateur requis.']);
        }

        $setting = SystemSetting::findOrFail($id);

        if (!$setting->is_editable) {
            return redirect()->route('admin.system-settings.index')
                ->withErrors(['error' => 'Ce paramètre ne peut pas être modifié.']);
        }

        // Validation
        $rules = ['value' => 'required'];
        
        if ($setting->validation_rules) {
            $rules['value'] .= '|' . $setting->validation_rules;
        }

        $request->validate($rules);

        // Mise à jour
        $setting->update([
            'value' => $request->value,
            'updated_by' => Auth::id()
        ]);

        // Clear cache
        Cache::forget("system_setting_{$setting->key}");
        Cache::forget("system_settings_group_{$setting->group}");
        Cache::forget("system_settings_public");

        return redirect()->route('admin.system-settings.index', ['group' => $setting->group])
            ->with('success', 'Paramètre mis à jour avec succès.');
    }

    /**
     * Mettre à jour plusieurs paramètres en masse
     */
    public function updateBulk(Request $request)
    {
        if (!Auth::check() || !in_array(Auth::user()->role, ['super_admin', 'system_admin'])) {
            return redirect()->route('login')->withErrors(['email' => 'Accès administrateur requis.']);
        }

        $group = $request->get('group');
        $settings = $request->get('settings', []);

        foreach ($settings as $key => $value) {
            $setting = SystemSetting::where('key', $key)->first();
            
            if ($setting && $setting->is_editable) {
                $setting->update([
                    'value' => $value,
                    'updated_by' => Auth::id()
                ]);

                // Clear cache
                Cache::forget("system_setting_{$key}");
            }
        }

        // Clear group cache
        if ($group) {
            Cache::forget("system_settings_group_{$group}");
        }
        Cache::forget("system_settings_public");

        return redirect()->route('admin.system-settings.index', ['group' => $group])
            ->with('success', 'Paramètres mis à jour avec succès.');
    }

    /**
     * Réinitialiser un paramètre à sa valeur par défaut
     */
    public function reset($id)
    {
        if (!Auth::check() || !in_array(Auth::user()->role, ['super_admin', 'system_admin'])) {
            return redirect()->route('login')->withErrors(['email' => 'Accès administrateur requis.']);
        }

        $setting = SystemSetting::findOrFail($id);

        if (!$setting->is_editable) {
            return redirect()->route('admin.system-settings.index')
                ->withErrors(['error' => 'Ce paramètre ne peut pas être modifié.']);
        }

        $setting->update([
            'value' => $setting->default_value,
            'updated_by' => Auth::id()
        ]);

        // Clear cache
        Cache::forget("system_setting_{$setting->key}");
        Cache::forget("system_settings_group_{$setting->group}");
        Cache::forget("system_settings_public");

        return redirect()->route('admin.system-settings.index', ['group' => $setting->group])
            ->with('success', 'Paramètre réinitialisé à sa valeur par défaut.');
    }

    /**
     * Initialiser les paramètres par défaut
     */
    public function initialize()
    {
        if (!Auth::check() || !in_array(Auth::user()->role, ['super_admin', 'system_admin'])) {
            return redirect()->route('login')->withErrors(['email' => 'Accès administrateur requis.']);
        }

        SystemSetting::createDefaultSettings();

        // Clear all cache
        Cache::flush();

        return redirect()->route('admin.system-settings.index')
            ->with('success', 'Paramètres par défaut initialisés avec succès.');
    }

    /**
     * Exporter les paramètres
     */
    public function export(Request $request)
    {
        if (!Auth::check() || !in_array(Auth::user()->role, ['super_admin', 'system_admin'])) {
            return redirect()->route('login')->withErrors(['email' => 'Accès administrateur requis.']);
        }

        $format = $request->get('format', 'json');
        $group = $request->get('group');

        $query = SystemSetting::query();
        if ($group) {
            $query->byGroup($group);
        }

        $settings = $query->get();

        if ($format === 'json') {
            return response()->json($settings, 200, [], JSON_PRETTY_PRINT);
        }

        if ($format === 'csv') {
            $filename = 'system_settings_' . ($group ? $group . '_' : '') . now()->format('Y-m-d_H-i-s') . '.csv';
            
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];

            $callback = function() use ($settings) {
                $file = fopen('php://output', 'w');
                
                // En-têtes CSV
                fputcsv($file, [
                    'Key', 'Name', 'Description', 'Value', 'Type', 'Group', 
                    'Is Public', 'Is Editable', 'Is Required', 'Default Value'
                ]);

                // Données
                foreach ($settings as $setting) {
                    fputcsv($file, [
                        $setting->key,
                        $setting->name,
                        $setting->description,
                        $setting->value,
                        $setting->type,
                        $setting->group,
                        $setting->is_public ? 'Yes' : 'No',
                        $setting->is_editable ? 'Yes' : 'No',
                        $setting->is_required ? 'Yes' : 'No',
                        $setting->default_value
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }

        return response()->json($settings);
    }

    /**
     * Afficher le formulaire de création d'un paramètre
     */
    public function create()
    {
        if (!Auth::check() || !in_array(Auth::user()->role, ['super_admin', 'system_admin'])) {
            return redirect()->route('login')->withErrors(['email' => 'Accès administrateur requis.']);
        }

        $groups = SystemSetting::getGroupDescriptions();
        $types = ['string', 'integer', 'boolean', 'text', 'json'];

        return view('admin.system-settings.create', compact('groups', 'types'));
    }

    /**
     * Créer un nouveau paramètre
     */
    public function store(Request $request)
    {
        if (!Auth::check() || !in_array(Auth::user()->role, ['super_admin', 'system_admin'])) {
            return redirect()->route('login')->withErrors(['email' => 'Accès administrateur requis.']);
        }

        $request->validate([
            'key' => 'required|string|unique:system_settings,key|max:255',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'value' => 'required',
            'type' => 'required|in:string,integer,boolean,text,json',
            'group' => 'required|string',
            'is_public' => 'boolean',
            'is_editable' => 'boolean',
            'is_required' => 'boolean',
            'default_value' => 'nullable|string',
            'validation_rules' => 'nullable|string'
        ]);

        $setting = SystemSetting::create([
            'key' => $request->key,
            'name' => $request->name,
            'description' => $request->description,
            'value' => $request->value,
            'type' => $request->type,
            'group' => $request->group,
            'is_public' => $request->boolean('is_public'),
            'is_editable' => $request->boolean('is_editable'),
            'is_required' => $request->boolean('is_required'),
            'default_value' => $request->default_value,
            'validation_rules' => $request->validation_rules,
            'updated_by' => Auth::id()
        ]);

        return redirect()->route('admin.system-settings.index', ['group' => $setting->group])
            ->with('success', 'Paramètre créé avec succès.');
    }

    /**
     * Supprimer un paramètre
     */
    public function destroy($id)
    {
        if (!Auth::check() || !in_array(Auth::user()->role, ['super_admin', 'system_admin'])) {
            return redirect()->route('login')->withErrors(['email' => 'Accès administrateur requis.']);
        }

        $setting = SystemSetting::findOrFail($id);
        $group = $setting->group;
        
        $setting->delete();

        // Clear cache
        Cache::forget("system_setting_{$setting->key}");
        Cache::forget("system_settings_group_{$group}");
        Cache::forget("system_settings_public");

        return redirect()->route('admin.system-settings.index', ['group' => $group])
            ->with('success', 'Paramètre supprimé avec succès.');
    }

    /**
     * API pour récupérer un paramètre
     */
    public function get($key)
    {
        $setting = SystemSetting::where('key', $key)->first();
        
        if (!$setting) {
            return response()->json(['error' => 'Setting not found'], 404);
        }

        if (!$setting->is_public) {
            if (!Auth::check() || !in_array(Auth::user()->role, ['super_admin', 'system_admin'])) {
                return response()->json(['error' => 'Access denied'], 403);
            }
        }

        return response()->json([
            'key' => $setting->key,
            'value' => $setting->formatted_value,
            'type' => $setting->type,
            'name' => $setting->name,
            'description' => $setting->description
        ]);
    }
}
