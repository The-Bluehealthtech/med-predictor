<!-- Onglet: Évaluations Physiques -->
<div class="space-y-6">
    <div class="bg-green-50 border border-green-200 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-green-900 mb-4">{{ __('health_records_create.physical_assessments_heading') }}</h3>
        <p class="text-green-700 mb-4">{{ __('health_records_create.physical_assessments_subtitle') }}</p>
        
        <!-- Cardiovascular Assessment -->
        <div class="space-y-4">
            <h4 class="text-md font-semibold text-gray-800">{{ __('health_records_create.cardio_assessment_heading') }}</h4>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="cardio_blood_pressure" class="block text-sm font-medium text-gray-700 mb-2">
                        {{ __('health_records_create.blood_pressure_label') }}
                    </label>
                    <div class="flex space-x-2">
                        <input 
                            type="number" 
                            id="cardio_systolic" 
                            name="cardio_systolic"
                            placeholder="{{ __('pcma.murmur_systolic_option') }}"
                            class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                        >
                        <span class="self-center text-gray-500">/</span>
                        <input 
                            type="number" 
                            id="cardio_diastolic" 
                            name="cardio_diastolic"
                            placeholder="{{ __('pcma.murmur_diastolic_option') }}"
                            class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                        >
                    </div>
                </div>
                
                <div>
                    <label for="cardio_heart_rate" class="block text-sm font-medium text-gray-700 mb-2">
                        {{ __('health_records_edit.heart_rate_bpm') }}
                    </label>
                    <input 
                        type="number" 
                        id="cardio_heart_rate" 
                        name="cardio_heart_rate"
                        min="40" max="200"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    >
                </div>
                
                <div>
                    <label for="cardio_oxygen_saturation" class="block text-sm font-medium text-gray-700 mb-2">
                        {{ __('health_records_create.oxygen_saturation_label') }}
                    </label>
                    <input 
                        type="number" 
                        id="cardio_oxygen_saturation" 
                        name="cardio_oxygen_saturation"
                        min="70" max="100"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    >
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="cardio_ecg" class="block text-sm font-medium text-gray-700 mb-2">
                        ECG
                    </label>
                    <select 
                        id="cardio_ecg" 
                        name="cardio_ecg"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    >
                        <option value="">{{ __('clinical.select_button') }}</option>
                        <option value="normal">Normal</option>
                        <option value="abnormal">{{ __('pcma.abnormal_option') }}</option>
                        <option value="not_performed">{{ __('health_records_extra.label_aa0c0c84c3d6') }}</option>
                    </select>
                </div>
                
                <div>
                    <label for="cardio_ecg_notes" class="block text-sm font-medium text-gray-700 mb-2">
                        {{ __('pcma.ecg_notes_label') }}
                    </label>
                    <textarea 
                        id="cardio_ecg_notes" 
                        name="cardio_ecg_notes"
                        rows="2"
                        placeholder="Observations ECG..."
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    ></textarea>
                </div>
            </div>
        </div>
    </div>

    <!-- Musculoskeletal Assessment -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-blue-900 mb-4">{{ __('health_records_extra.label_c7d03ac3d1f8') }}</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h4 class="text-md font-semibold text-gray-800 mb-3">{{ __('pcma.muscle_strength_label') }}</h4>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-700">{{ __('health_records_extra.label_312ab7443a24') }}</span>
                        <select name="muscle_strength_upper" class="px-2 py-1 border border-gray-300 rounded text-sm">
                            <option value="">-</option>
                            <option value="5">5/5 - Normal</option>
                            <option value="4">{{ __('health_records_extra.label_04d8fcf3d2a1') }}</option>
                            <option value="3">{{ __('health_records_extra.label_53d630be2ad6') }}</option>
                            <option value="2">{{ __('health_records_extra.label_63d56cb9cb2e') }}</option>
                            <option value="1">{{ __('health_records_extra.label_b88100f9d911') }}</option>
                            <option value="0">{{ __('health_records_extra.label_c600dc9726c5') }}</option>
                        </select>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-700">{{ __('health_records_extra.label_644f1c97e99b') }}</span>
                        <select name="muscle_strength_lower" class="px-2 py-1 border border-gray-300 rounded text-sm">
                            <option value="">-</option>
                            <option value="5">5/5 - Normal</option>
                            <option value="4">{{ __('health_records_extra.label_04d8fcf3d2a1') }}</option>
                            <option value="3">{{ __('health_records_extra.label_53d630be2ad6') }}</option>
                            <option value="2">{{ __('health_records_extra.label_63d56cb9cb2e') }}</option>
                            <option value="1">{{ __('health_records_extra.label_b88100f9d911') }}</option>
                            <option value="0">{{ __('health_records_extra.label_c600dc9726c5') }}</option>
                        </select>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-700">{{ __('pcma.brainstem_label') }}</span>
                        <select name="muscle_strength_trunk" class="px-2 py-1 border border-gray-300 rounded text-sm">
                            <option value="">-</option>
                            <option value="5">5/5 - Normal</option>
                            <option value="4">{{ __('health_records_extra.label_04d8fcf3d2a1') }}</option>
                            <option value="3">{{ __('health_records_extra.label_53d630be2ad6') }}</option>
                            <option value="2">{{ __('health_records_extra.label_63d56cb9cb2e') }}</option>
                            <option value="1">{{ __('health_records_extra.label_b88100f9d911') }}</option>
                            <option value="0">{{ __('health_records_extra.label_c600dc9726c5') }}</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div>
                <h4 class="text-md font-semibold text-gray-800 mb-3">{{ __('health_records_extra.label_8ec3a8985279') }}</h4>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-700">{{ __('health_records_create.shoulders_label') }}</span>
                        <select name="rom_shoulders" class="px-2 py-1 border border-gray-300 rounded text-sm">
                            <option value="">-</option>
                            <option value="normal">{{ __('pcma.function_normal_option') }}</option>
                            <option value="limited">{{ __('pcma.limited_option') }}</option>
                            <option value="restricted">{{ __('pcma.restricted_option') }}</option>
                        </select>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-700">{{ __('health_records_create.knees_label') }}</span>
                        <select name="rom_knees" class="px-2 py-1 border border-gray-300 rounded text-sm">
                            <option value="">-</option>
                            <option value="normal">{{ __('pcma.function_normal_option') }}</option>
                            <option value="limited">{{ __('pcma.limited_option') }}</option>
                            <option value="restricted">{{ __('pcma.restricted_option') }}</option>
                        </select>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-700">{{ __('health_records_extra.label_f94d8976dfbd') }}</span>
                        <select name="rom_ankles" class="px-2 py-1 border border-gray-300 rounded text-sm">
                            <option value="">-</option>
                            <option value="normal">{{ __('pcma.function_normal_option') }}</option>
                            <option value="limited">{{ __('pcma.limited_option') }}</option>
                            <option value="restricted">{{ __('pcma.restricted_option') }}</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="mt-4">
            <label for="musculoskeletal_notes" class="block text-sm font-medium text-gray-700 mb-2">
                {{ __('health_records_extra.label_d20e7ccada2f') }}
            </label>
            <textarea 
                id="musculoskeletal_notes" 
                name="musculoskeletal_notes"
                rows="3"
                placeholder="Observations, limitations, douleurs..."
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            ></textarea>
        </div>
    </div>

    <!-- Neurological Assessment -->
    <div class="bg-purple-50 border border-purple-200 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-purple-900 mb-4">{{ __('pcma.neuro_assessment_title') }}</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h4 class="text-md font-semibold text-gray-800 mb-3">{{ __('health_records_extra.label_15b0e7e10e95') }}</h4>
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-700">{{ __('health_records_extra.label_698343932a21') }}</span>
                        <select name="reflexes_patellar" class="px-2 py-1 border border-gray-300 rounded text-sm">
                            <option value="">-</option>
                            <option value="normal">Normal</option>
                            <option value="hyperactive">{{ __('health_records_extra.label_645a78db5caa') }}</option>
                            <option value="hypoactive">{{ __('health_records_extra.label_b479ff6aac00') }}</option>
                            <option value="absent">Absent</option>
                        </select>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-700">{{ __('health_records_extra.label_506870e8028e') }}</span>
                        <select name="reflexes_achilles" class="px-2 py-1 border border-gray-300 rounded text-sm">
                            <option value="">-</option>
                            <option value="normal">Normal</option>
                            <option value="hyperactive">{{ __('health_records_extra.label_645a78db5caa') }}</option>
                            <option value="hypoactive">{{ __('health_records_extra.label_b479ff6aac00') }}</option>
                            <option value="absent">Absent</option>
                        </select>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-700">{{ __('health_records_extra.label_1ecc2c5ca5c3') }}</span>
                        <select name="reflexes_plantar" class="px-2 py-1 border border-gray-300 rounded text-sm">
                            <option value="">-</option>
                            <option value="normal">Normal</option>
                            <option value="brisk">{{ __('health_records_extra.label_f292d02dfebc') }}</option>
                            <option value="absent">Absent</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div>
                <h4 class="text-md font-semibold text-gray-800 mb-3">{{ __('health_records_extra.label_f7925ecb11f2') }}</h4>
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-700">{{ __('health_records_extra.label_4396a45d8751') }}</span>
                        <select name="sensation_touch" class="px-2 py-1 border border-gray-300 rounded text-sm">
                            <option value="">-</option>
                            <option value="normal">Normal</option>
                            <option value="decreased">{{ __('health_records_extra.label_5b74e9cd99b6') }}</option>
                            <option value="absent">Absent</option>
                        </select>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-700">{{ __('health_records_extra.label_afaf1a99778d') }}</span>
                        <select name="sensation_pain" class="px-2 py-1 border border-gray-300 rounded text-sm">
                            <option value="">-</option>
                            <option value="normal">Normal</option>
                            <option value="decreased">{{ __('health_records_extra.label_5b74e9cd99b6') }}</option>
                            <option value="absent">Absent</option>
                        </select>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-700">Proprioception</span>
                        <select name="sensation_proprioception" class="px-2 py-1 border border-gray-300 rounded text-sm">
                            <option value="">-</option>
                            <option value="normal">Normal</option>
                            <option value="decreased">{{ __('health_records_extra.label_5b74e9cd99b6') }}</option>
                            <option value="absent">Absent</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="mt-4">
            <label for="neurological_notes" class="block text-sm font-medium text-gray-700 mb-2">
                {{ __('health_records_extra.label_daa32cfb1e15') }}
            </label>
            <textarea 
                id="neurological_notes" 
                name="neurological_notes"
                rows="3"
                placeholder="Observations neurologiques..."
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
            ></textarea>
        </div>
    </div>

    <!-- Functional Assessment -->
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-yellow-900 mb-4">{{ __('health_records_extra.label_79ed680adbeb') }}</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h4 class="text-md font-semibold text-gray-800 mb-3">{{ __('health_records_extra.label_aa6eff832c74') }}</h4>
                <div class="space-y-3">
                    <div>
                        <label for="functional_walk_test" class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('health_records_extra.label_93e562d4500c') }}
                        </label>
                        <input 
                            type="number" 
                            id="functional_walk_test" 
                            name="functional_walk_test"
                            min="0" max="1000"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent"
                        >
                    </div>
                    
                    <div>
                        <label for="functional_balance_test" class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('health_records_extra.label_c43b7509835f') }}
                        </label>
                        <input 
                            type="number" 
                            id="functional_balance_test" 
                            name="functional_balance_test"
                            min="0" max="300"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent"
                        >
                    </div>
                    
                    <div>
                        <label for="functional_strength_test" class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('health_records_extra.label_2346bdaf66e0') }}
                        </label>
                        <input 
                            type="number" 
                            id="functional_strength_test" 
                            name="functional_strength_test"
                            min="0" max="100"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent"
                        >
                    </div>
                </div>
            </div>
            
            <div>
                <h4 class="text-md font-semibold text-gray-800 mb-3">{{ __('health_records_extra.label_99ccee3b8f23') }}</h4>
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-700">{{ __('health_records_extra.label_113fca40e524') }}</span>
                        <select name="adl_walking" class="px-2 py-1 border border-gray-300 rounded text-sm">
                            <option value="">-</option>
                            <option value="independent">{{ __('health_records_extra.label_de8d4f953a5a') }}</option>
                            <option value="assisted">{{ __('health_records_extra.label_ffacde0e30e2') }}</option>
                            <option value="dependent">{{ __('health_records_extra.label_e563e66260e2') }}</option>
                        </select>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-700">{{ __('navigation.transfers') }}</span>
                        <select name="adl_transfers" class="px-2 py-1 border border-gray-300 rounded text-sm">
                            <option value="">-</option>
                            <option value="independent">{{ __('health_records_extra.label_de8d4f953a5a') }}</option>
                            <option value="assisted">{{ __('health_records_extra.label_ffacde0e30e2') }}</option>
                            <option value="dependent">{{ __('health_records_extra.label_e563e66260e2') }}</option>
                        </select>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-700">{{ __('health_records_extra.label_eb3684152da8') }}</span>
                        <select name="adl_dressing" class="px-2 py-1 border border-gray-300 rounded text-sm">
                            <option value="">-</option>
                            <option value="independent">{{ __('health_records_extra.label_de8d4f953a5a') }}</option>
                            <option value="assisted">{{ __('health_records_extra.label_ffacde0e30e2') }}</option>
                            <option value="dependent">{{ __('health_records_extra.label_e563e66260e2') }}</option>
                        </select>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-700">Hygiene</span>
                        <select name="adl_hygiene" class="px-2 py-1 border border-gray-300 rounded text-sm">
                            <option value="">-</option>
                            <option value="independent">{{ __('health_records_extra.label_de8d4f953a5a') }}</option>
                            <option value="assisted">{{ __('health_records_extra.label_ffacde0e30e2') }}</option>
                            <option value="dependent">{{ __('health_records_extra.label_e563e66260e2') }}</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="mt-4">
            <label for="functional_notes" class="block text-sm font-medium text-gray-700 mb-2">
                {{ __('health_records_extra.label_161a41ae0b56') }}
            </label>
            <textarea 
                id="functional_notes" 
                name="functional_notes"
                rows="3"
                placeholder="Observations fonctionnelles, limitations..."
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent"
            ></textarea>
        </div>
    </div>
</div> 