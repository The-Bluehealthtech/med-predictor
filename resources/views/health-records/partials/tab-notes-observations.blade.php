<!-- Onglet: Notes et Observations -->
<div class="space-y-6">
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-blue-900 mb-4">{{ __('health_records_extra.label_462622abb067') }}</h3>
        <p class="text-blue-700 mb-4">{{ __('health_records_extra.label_02a6a31ae74f') }}</p>
        
        <!-- Clinical Notes -->
        <div class="space-y-4">
            <div>
                <label for="clinical_notes" class="block text-sm font-medium text-gray-700 mb-2">
                    {{ __('health_records_extra.label_56835fce685e') }}
                </label>
                <textarea 
                    id="clinical_notes" 
                    name="clinical_notes"
                    rows="8"
                    placeholder="{{ __('health_records_extra.label_c49a35c5513a') }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >{{ old('clinical_notes') }}</textarea>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="physical_examination" class="block text-sm font-medium text-gray-700 mb-2">
                        {{ __('health_records_extra.label_7d436b499b88') }}
                    </label>
                    <textarea 
                        id="physical_examination" 
                        name="physical_examination"
                        rows="6"
                        placeholder="{{ __('health_records_extra.label_de99cb7601d6') }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >{{ old('physical_examination') }}</textarea>
                </div>
                
                <div>
                    <label for="differential_diagnosis" class="block text-sm font-medium text-gray-700 mb-2">
                        {{ __('health_records_extra.label_dfc6e3033a37') }}
                    </label>
                    <textarea 
                        id="differential_diagnosis" 
                        name="differential_diagnosis"
                        rows="6"
                        placeholder="{{ __('health_records_extra.label_885c4d17aa5e') }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >{{ old('differential_diagnosis') }}</textarea>
                </div>
            </div>
        </div>
    </div>

    <!-- Treatment Plan -->
    <div class="bg-green-50 border border-green-200 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-green-900 mb-4">{{ __('health_records_extra.label_91bd537ad260') }}</h3>
        
        <div class="space-y-4">
            <div>
                <label for="treatment_plan" class="block text-sm font-medium text-gray-700 mb-2">
                    {{ __('health_records_extra.label_d6f1687b6672') }}
                </label>
                <textarea 
                    id="treatment_plan" 
                    name="treatment_plan"
                    rows="6"
                    placeholder="{{ __('health_records_extra.label_4a5281cf48e2') }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                >{{ old('treatment_plan') }}</textarea>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="medications_prescribed" class="block text-sm font-medium text-gray-700 mb-2">
                        {{ __('health_records_extra.label_a3251db29548') }}
                    </label>
                    <textarea 
                        id="medications_prescribed" 
                        name="medications_prescribed"
                        rows="4"
                        placeholder="{{ __('health_records_extra.label_06f52d5b5cc0') }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    >{{ old('medications_prescribed') }}</textarea>
                </div>
                
                <div>
                    <label for="recommendations" class="block text-sm font-medium text-gray-700 mb-2">
                        {{ __('health_records_extra.label_c9a44610a2d6') }}
                    </label>
                    <textarea 
                        id="recommendations" 
                        name="recommendations"
                        rows="4"
                        placeholder="Recommandations pour le patient (mode de vie, suivi...)..."
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    >{{ old('recommendations') }}</textarea>
                </div>
            </div>
        </div>
    </div>

    <!-- Follow-up Plan -->
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-yellow-900 mb-4">{{ __('health_records_extra.label_6650faeb276a') }}</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="follow_up_date" class="block text-sm font-medium text-gray-700 mb-2">
                    {{ __('health_records_extra.label_e8c8695176b8') }}
                </label>
                <input 
                    type="date" 
                    id="follow_up_date" 
                    name="follow_up_date"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent"
                >
            </div>
            
            <div>
                <label for="follow_up_type" class="block text-sm font-medium text-gray-700 mb-2">
                    {{ __('health_records_extra.label_017d0d456694') }}
                </label>
                <select 
                    id="follow_up_type" 
                    name="follow_up_type"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent"
                >
                    <option value="">{{ __('clinical.select_button') }}</option>
                    <option value="consultation">{{ __('health_records_extra.label_37a32ab59555') }}</option>
                    <option value="telephone">{{ __('health_records_extra.label_6536a7ce83c4') }}</option>
                    <option value="video">{{ __('health_records_extra.label_98e2e7d396db') }}</option>
                    <option value="emergency">{{ __('health_records_extra.label_7423412cd48f') }}</option>
                    <option value="specialist">{{ __('health_records_extra.label_c5a0eb0a5f3d') }}</option>
                </select>
            </div>
        </div>
        
        <div class="mt-4">
            <label for="follow_up_notes" class="block text-sm font-medium text-gray-700 mb-2">
                {{ __('health_records_extra.label_22ae2ed53555') }}
            </label>
            <textarea 
                id="follow_up_notes" 
                name="follow_up_notes"
                rows="4"
                placeholder="{{ __('health_records_extra.label_21919e2ae153') }}"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent"
            >{{ old('follow_up_notes') }}</textarea>
        </div>
    </div>

    <!-- Referrals -->
    <div class="bg-purple-50 border border-purple-200 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-purple-900 mb-4">{{ __('health_records_extra.label_633cda51b796') }}</h3>
        
        <div class="space-y-4">
            <div>
                <label for="referrals" class="block text-sm font-medium text-gray-700 mb-2">
                    {{ __('health_records_extra.label_d111e8b94229') }}
                </label>
                <textarea 
                    id="referrals" 
                    name="referrals"
                    rows="4"
                    placeholder="{{ __('health_records_extra.label_2fd1c40ef8d7') }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                >{{ old('referrals') }}</textarea>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="urgent_referral" class="block text-sm font-medium text-gray-700 mb-2">
                        {{ __('health_records_extra.label_7a6737b896e9') }}
                    </label>
                    <select 
                        id="urgent_referral" 
                        name="urgent_referral"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                    >
                        <option value="">{{ __('pcma.report_none_f') }}</option>
                        <option value="emergency_room">{{ __('health_records_extra.label_ce45b3a7ce55') }}</option>
                        <option value="cardiology">{{ __('health_records_extra.label_f1703cc56ce5') }}</option>
                        <option value="neurology">{{ __('health_records_extra.label_d676857a6944') }}</option>
                        <option value="orthopedics">{{ __('health_records_extra.label_ec4dd0775ab5') }}</option>
                        <option value="surgery">{{ __('health_records_extra.label_524a3cffa7d7') }}</option>
                    </select>
                </div>
                
                <div>
                    <label for="referral_urgency" class="block text-sm font-medium text-gray-700 mb-2">
                        {{ __('health_records_extra.label_84ee15cc1cdc') }}
                    </label>
                    <select 
                        id="referral_urgency" 
                        name="referral_urgency"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                    >
                        <option value="">{{ __('clinical.select_button') }}</option>
                        <option value="immediate">{{ __('health_records_extra.label_97231ac7e7d6') }}</option>
                        <option value="urgent">Urgent (24-48h)</option>
                        <option value="routine">{{ __('health_records_extra.label_96354c543445') }}</option>
                        <option value="elective">{{ __('health_records_extra.label_ccf5b502e7b0') }}</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Consent and Legal -->
    <div class="bg-red-50 border border-red-200 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-red-900 mb-4">{{ __('health_records_extra.label_8dfb28051a97') }}</h3>
        
        <div class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="informed_consent" class="block text-sm font-medium text-gray-700 mb-2">
                        {{ __('health_records_extra.label_c9a49f282faa') }}
                    </label>
                    <select 
                        id="informed_consent" 
                        name="informed_consent"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                    >
                        <option value="">{{ __('clinical.select_button') }}</option>
                        <option value="obtained">{{ __('health_records_extra.label_8ea61fd60dfd') }}</option>
                        <option value="refused">{{ __('health_records_extra.label_340218a511f1') }}</option>
                        <option value="not_applicable">{{ __('health_records_extra.label_16ed38d0207d') }}</option>
                        <option value="emergency">{{ __('health_records_extra.label_f1d4cb8624bf') }}</option>
                    </select>
                </div>
                
                <div>
                    <label for="legal_guardian" class="block text-sm font-medium text-gray-700 mb-2">
                        {{ __('health_records_extra.label_b53008774f4c') }}
                    </label>
                    <input 
                        type="text" 
                        id="legal_guardian" 
                        name="legal_guardian"
                        placeholder="{{ __('health_records_extra.label_9fc0977b64ed') }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                    >
                </div>
            </div>
            
            <div>
                <label for="legal_notes" class="block text-sm font-medium text-gray-700 mb-2">
                    {{ __('health_records_extra.label_a686a24cd31e') }}
                </label>
                <textarea 
                    id="legal_notes" 
                    name="legal_notes"
                    rows="3"
                    placeholder="{{ __('health_records_extra.label_df5fe1a60e96') }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                >{{ old('legal_notes') }}</textarea>
            </div>
        </div>
    </div>

    <!-- Summary and Final Notes -->
    <div class="bg-gray-50 border border-gray-200 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('health_records_extra.label_d67a87d1585f') }}</h3>
        
        <div class="space-y-4">
            <div>
                <label for="visit_summary" class="block text-sm font-medium text-gray-700 mb-2">
                    {{ __('health_records_extra.label_e1f031752b2f') }}
                </label>
                <textarea 
                    id="visit_summary" 
                    name="visit_summary"
                    rows="4"
                    placeholder="{{ __('health_records_extra.label_a13c83f3a7f2') }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent"
                >{{ old('visit_summary') }}</textarea>
            </div>
            
            <div>
                <label for="final_notes" class="block text-sm font-medium text-gray-700 mb-2">
                    {{ __('health_records_extra.label_d2dc3e798951') }}
                </label>
                <textarea 
                    id="final_notes" 
                    name="final_notes"
                    rows="4"
                    placeholder="Observations finales, impressions cliniques..."
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent"
                >{{ old('final_notes') }}</textarea>
            </div>
        </div>
    </div>
</div> 