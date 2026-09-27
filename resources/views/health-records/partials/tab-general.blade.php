<!-- Onglet: Informations Générales -->
<div class="space-y-6">
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-blue-900 mb-4">{{ __('pcma.report_patient_info_title') }}</h3>
        
        <!-- Player Selection -->
        <div class="mb-6">
            <label for="player_id" class="block text-sm font-medium text-gray-700 mb-2">
                {{ __('health_records_edit.player') }}
            </label>
            <select 
                id="player_id" 
                name="player_id" 
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            >
                <option value="">{{ __('health_records_edit.select_a_player') }}</option>
                @foreach($players as $player)
                    <option value="{{ $player->id }}" {{ old('player_id') == $player->id ? 'selected' : '' }}>
                        {{ $player->name }} ({{ $player->club->name ?? 'N/A' }})
                    </option>
                @endforeach
            </select>
        </div>

        <!-- EMR Visit Information -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label for="visit_date" class="block text-sm font-medium text-gray-700 mb-2">
                    {{ __('health_records_edit.visit_date') }}
                </label>
                <input 
                    type="date" 
                    id="visit_date" 
                    name="visit_date" 
                    value="{{ old('visit_date', date('Y-m-d')) }}"
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
            </div>
            
            <div>
                <label for="doctor_name" class="block text-sm font-medium text-gray-700 mb-2">
                    {{ __('health_records_edit.doctor') }}
                </label>
                <input 
                    type="text" 
                    id="doctor_name" 
                    name="doctor_name" 
                    value="{{ old('doctor_name') }}"
                    required
                    placeholder="{{ __('health_records_edit.doctor_s_name') }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
            </div>
            
            <div>
                <label for="visit_type" class="block text-sm font-medium text-gray-700 mb-2">
                    {{ __('health_records_edit.visit_type') }}
                </label>
                <select 
                    id="visit_type" 
                    name="visit_type" 
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
                    <option value="">{{ __('health_records_create.select_visit_type_placeholder') }}</option>
                    <option value="consultation" {{ old('visit_type') == 'consultation' ? 'selected' : '' }}>Consultation</option>
                    <option value="emergency" {{ old('visit_type') == 'emergency' ? 'selected' : '' }}>{{ __('health_records.show_page.visit_type_emergency') }}</option>
                    <option value="follow_up" {{ old('visit_type') == 'follow_up' ? 'selected' : '' }}>{{ __('health_records.show_page.visit_type_follow_up') }}</option>
                    <option value="pre_season" {{ old('visit_type') == 'pre_season' ? 'selected' : '' }}>{{ __('health_records.show_page.visit_type_pre_season') }}</option>
                    <option value="pcma" {{ old('visit_type') == 'pcma' ? 'selected' : '' }}>{{ __('health_records_create.visit_type_pcma') }}</option>
                    <option value="post_match" {{ old('visit_type') == 'post_match' ? 'selected' : '' }}>Post-match</option>
                    <option value="rehabilitation" {{ old('visit_type') == 'rehabilitation' ? 'selected' : '' }}>{{ __('health_records.show_page.visit_type_rehabilitation') }}</option>
                </select>
            </div>
        </div>

        <!-- Chief Complaint -->
        <div class="mt-6">
            <label for="chief_complaint" class="block text-sm font-medium text-gray-700 mb-2">
                {{ __('health_records.show_page.chief_complaint_label') }}
            </label>
            <textarea 
                id="chief_complaint" 
                name="chief_complaint" 
                rows="3"
                placeholder="{{ __('health_records_create.chief_complaint_placeholder') }}"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            >{{ old('chief_complaint') }}</textarea>
        </div>
    </div>

    <!-- Vital Signs -->
    <div class="bg-green-50 border border-green-200 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-green-900 mb-4">{{ __('health_records_create.vital_signs_heading') }}</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label for="blood_pressure_systolic" class="block text-sm font-medium text-gray-700 mb-2">
                    {{ __('health_records_create.systolic_label') }}
                </label>
                <input 
                    type="number" 
                    id="blood_pressure_systolic" 
                    name="blood_pressure_systolic" 
                    value="{{ old('blood_pressure_systolic') }}"
                    min="70" max="200"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                >
            </div>
            
            <div>
                <label for="blood_pressure_diastolic" class="block text-sm font-medium text-gray-700 mb-2">
                    {{ __('health_records_create.diastolic_label') }}
                </label>
                <input 
                    type="number" 
                    id="blood_pressure_diastolic" 
                    name="blood_pressure_diastolic" 
                    value="{{ old('blood_pressure_diastolic') }}"
                    min="40" max="130"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                >
            </div>
            
            <div>
                <label for="heart_rate" class="block text-sm font-medium text-gray-700 mb-2">
                    {{ __('health_records_edit.heart_rate_bpm') }}
                </label>
                <input 
                    type="number" 
                    id="heart_rate" 
                    name="heart_rate" 
                    value="{{ old('heart_rate') }}"
                    min="40" max="200"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                >
            </div>
        </div>

        <!-- Physical Measurements -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mt-6">
            <div>
                <label for="temperature" class="block text-sm font-medium text-gray-700 mb-2">
                    {{ __('health_records.show_page.temperature_label') }}
                </label>
                <input 
                    type="number" 
                    id="temperature" 
                    name="temperature" 
                    value="{{ old('temperature') }}"
                    min="35" max="42" step="0.1"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                >
            </div>
            
            <div>
                <label for="weight" class="block text-sm font-medium text-gray-700 mb-2">
                    {{ __('health_records.show_page.weight_label') }}
                </label>
                <input 
                    type="number" 
                    id="weight" 
                    name="weight" 
                    value="{{ old('weight') }}"
                    min="30" max="200" step="0.1"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                >
            </div>
            
            <div>
                <label for="height" class="block text-sm font-medium text-gray-700 mb-2">
                    {{ __('health_records.show_page.height_label') }}
                </label>
                <input 
                    type="number" 
                    id="height" 
                    name="height" 
                    value="{{ old('height') }}"
                    min="100" max="250"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                >
            </div>
            
            <div>
                <label for="blood_type" class="block text-sm font-medium text-gray-700 mb-2">
                    {{ __('health_records_edit.blood_type') }}
                </label>
                <select 
                    id="blood_type" 
                    name="blood_type"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                >
                    <option value="">{{ __('clinical.select_button') }}</option>
                    <option value="A+" {{ old('blood_type') == 'A+' ? 'selected' : '' }}>A+</option>
                    <option value="A-" {{ old('blood_type') == 'A-' ? 'selected' : '' }}>A-</option>
                    <option value="B+" {{ old('blood_type') == 'B+' ? 'selected' : '' }}>B+</option>
                    <option value="B-" {{ old('blood_type') == 'B-' ? 'selected' : '' }}>B-</option>
                    <option value="AB+" {{ old('blood_type') == 'AB+' ? 'selected' : '' }}>AB+</option>
                    <option value="AB-" {{ old('blood_type') == 'AB-' ? 'selected' : '' }}>AB-</option>
                    <option value="O+" {{ old('blood_type') == 'O+' ? 'selected' : '' }}>O+</option>
                    <option value="O-" {{ old('blood_type') == 'O-' ? 'selected' : '' }}>O-</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Medical Information -->
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-yellow-900 mb-4">{{ __('health_records_extra.label_8d5676ec26c7') }}</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="allergies" class="block text-sm font-medium text-gray-700 mb-2">
                    🚨 Allergies (WHO/IUIS Nomenclature)
                </label>
                <select id="allergies" name="allergies[]" multiple class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent" size="6">
                    <option value="">{{ __('health_records_extra.label_575905eabd28') }}</option>
                    <optgroup label="🍽️ Allergènes Alimentaires (WHO/IUIS)">
                        <option value="f1">f1 - Egg white (Ovalbumin)</option>
                        <option value="f2">f2 - Milk (Casein)</option>
                        <option value="f3">f3 - Fish (Cod)</option>
                        <option value="f4">f4 - Wheat (Gluten)</option>
                        <option value="f5">f5 - Peanut (Arachis hypogaea)</option>
                        <option value="f6">f6 - Soybean (Glycine max)</option>
                        <option value="f7">f7 - Apple (Malus domestica)</option>
                        <option value="f8">f8 - Almond (Prunus dulcis)</option>
                        <option value="f9">f9 - Walnut (Juglans regia)</option>
                        <option value="f10">f10 - Hazelnut (Corylus avellana)</option>
                    </optgroup>
                    <optgroup label="🌬️ Allergènes Inhalés (WHO/IUIS)">
                        <option value="d1">d1 - House dust mite (Dermatophagoides pteronyssinus)</option>
                        <option value="d2">d2 - House dust mite (Dermatophagoides farinae)</option>
                        <option value="d3">d3 - Storage mite (Blomia tropicalis)</option>
                    </optgroup>
                </select>
                <p class="text-xs text-gray-500 mt-1">{{ __('health_records_extra.label_5b528a804e05') }}</p>
            </div>                        
            <div>
                <label for="medications" class="block text-sm font-medium text-gray-700 mb-2">
                    {{ __('health_records_extra.label_fb38bf335424') }}
                </label>
                <select id="medications" name="medications[]" multiple class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent" size="6">
                    <option value="">{{ __('health_records_extra.label_b45633f79e38') }}</option>
                    <optgroup label="💊 Analgésiques et Anti-inflammatoires">
                        <option value="paracetamol">Paracétamol (Doliprane, Efferalgan)</option>
                        <option value="ibuprofen">Ibuprofène (Advil, Nurofen)</option>
                        <option value="aspirin">Aspirine (Aspégic, Kardégic)</option>
                        <option value="diclofenac">Diclofénac (Voltarène)</option>
                        <option value="ketoprofen">Kétoprofène (Profenid)</option>
                        <option value="naproxen">Naproxène (Naprosyne)</option>
                        <option value="celecoxib">Célécoxib (Celebrex)</option>
                        <option value="etoricoxib">Étoricoxib (Arcoxia)</option>
                        <option value="meloxicam">Méloxicam (Mobic)</option>
                        <option value="piroxicam">Piroxicam (Feldène)</option>
                    </optgroup>
                    <optgroup label="💊 Antibiotiques">
                        <option value="amoxicillin">Amoxicilline (Clamoxyl, Augmentin)</option>
                        <option value="penicillin">Pénicilline G (Pénicilline)</option>
                        <option value="cephalexin">Céfalexine (Keforal)</option>
                        <option value="azithromycin">Azithromycine (Zithromax)</option>
                        <option value="clarithromycin">Clarithromycine (Zéclar)</option>
                        <option value="doxycycline">Doxycycline (Vibramycine)</option>
                        <option value="ciprofloxacin">Ciprofloxacine (Ciflox)</option>
                        <option value="levofloxacin">Lévofloxacine (Tavanic)</option>
                        <option value="metronidazole">Métronidazole (Flagyl)</option>
                        <option value="clindamycin">Clindamycine (Dalacine)</option>
                    </optgroup>
                </select>
                <p class="text-xs text-gray-500 mt-1">{{ __('health_records_extra.label_fa92c6841d43') }}</p>
            </div>
        </div>

        <!-- Medical History and Symptoms -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
            <div>
                <label for="medical_history" class="block text-sm font-medium text-gray-700 mb-2">
                    {{ __('pcma.medical_history_title') }}
                </label>
                <textarea 
                    id="medical_history" 
                    name="medical_history" 
                    rows="4"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent"
                    placeholder="{{ __('health_records_edit.important_medical_history') }}"
                >{{ old('medical_history') }}</textarea>
            </div>
            
            <div>
                <label for="symptoms" class="block text-sm font-medium text-gray-700 mb-2">
                    {{ __('health_records_edit.current_symptoms') }}
                </label>
                <textarea 
                    id="symptoms" 
                    name="symptoms" 
                    rows="4"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent"
                    placeholder="{{ __('health_records_edit.symptoms_reported_by_the_patient') }}"
                >{{ old('symptoms') }}</textarea>
            </div>
        </div>
    </div>
</div> 