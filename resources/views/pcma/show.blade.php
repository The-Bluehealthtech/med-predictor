@extends('layouts.app')

@section('title', 'Détails PCMA - Med Predictor')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">{{ __('pcma_extra.label_2b602a195163') }}</h1>
                    <p class="text-gray-600 mt-2">{{ __('pcma_extra.label_09a5f07e4b4c') }}</p>
                </div>
                <div class="flex space-x-4">
                    <a href="{{ route('pcma.edit', $pcma) }}" 
                       class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                        {{ __('pcma_extra.label_723bbbfede8a') }}
                    </a>
                    <a href="{{ route('pcma.pdf', $pcma) }}" 
                       class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                        {{ __('pcma_extra.label_35c8a484cdfd') }}
                    </a>
                    <a href="{{ route('pcma.index') }}" 
                       class="bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                        {{ __('health_records_edit.back_to_list') }}
                    </a>
                </div>
            </div>
        </div>

        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                {{ session('error') }}
            </div>
        @endif

        <!-- PCMA Information -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-800">{{ __('pcma_extra.label_61255cf3526e') }}</h2>
            </div>
            
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('pcma_extra.label_1466efa7b745') }}</h3>
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">{{ __('secretary.col_athlete') }}</dt>
                                <dd class="text-sm text-gray-900">
                                    <div class="flex items-center space-x-3">
                                        <span>{{ ($pcma->player ?? $pcma->athlete)->name ?? 'N/A' }}</span>
                                        @if(($pcma->player ?? $pcma->athlete))
                                            <x-flag-logo-display 
                                                :nationality="($pcma->player ?? $pcma->athlete)->nationality"
                                                :association="($pcma->player ?? $pcma->athlete)->association"
                                                size="small"
                                            />
                                        @endif
                                    </div>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">{{ __('pcma_extra.label_c6f1e645013d') }}</dt>
                                <dd class="text-sm text-gray-900">
                                    <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                                        {{ ucfirst($pcma->type) }}
                                    </span>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">{{ __('pcma_extra.label_b06a10708210') }}</dt>
                                <dd class="text-sm text-gray-900">{{ $pcma->assessor->name ?? __('pcma_extra.unassigned') }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">{{ __('pcma_extra.label_453380528fb2') }}</dt>
                                <dd class="text-sm text-gray-900">{{ $pcma->created_at->format('d/m/Y') }}</dd>
                            </div>
                        </dl>
                    </div>
                    
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('pcma_extra.label_4c0b0e79677c') }}</h3>
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">{{ __('pcma_extra.label_45e0f5076dc5') }}</dt>
                                <dd class="text-sm text-gray-900">
                                    <span class="px-2 py-1 text-xs rounded-full {{ $pcma->status === 'completed' ? 'bg-green-100 text-green-800' : ($pcma->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                        {{ ucfirst($pcma->status) }}
                                    </span>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">{{ __('landing.hero.fifa_compliant') }}</dt>
                                <dd class="text-sm text-gray-900">
                                    <span class="px-2 py-1 text-xs rounded-full {{ ($pcma->fifa_compliance_data['fifa_compliant'] ?? false) ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ ($pcma->fifa_compliance_data['fifa_compliant'] ?? false) ? __('pcma_extra.yes') : __('pcma_extra.no') }}
                                    </span>
                                </dd>
                            </div>
                            @if(isset($pcma->fifa_compliance_data['fifa_id']))
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">{{ __('pcma.fifa_id_label') }}</dt>
                                    <dd class="text-sm text-gray-900">{{ $pcma->fifa_compliance_data['fifa_id'] }}</dd>
                                </div>
                            @endif
                            @if(isset($pcma->fifa_compliance_data['competition_name']))
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">{{ __('competitions.competition') }}</dt>
                                    <dd class="text-sm text-gray-900">{{ $pcma->fifa_compliance_data['competition_name'] }}</dd>
                                </div>
                            @endif
                        </dl>
                    </div>

                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('pcma_extra.label_20eb97a7d4a4') }}</h3>
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">{{ __('user_management.created') }}</dt>
                                <dd class="text-sm text-gray-900">{{ $pcma->created_at->format('d/m/Y H:i') }}</dd>
                            </div>
                            @if($pcma->completed_at)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">{{ __('pcma_extra.label_d0ef0d476f30') }}</dt>
                                    <dd class="text-sm text-gray-900">{{ $pcma->completed_at->format('d/m/Y H:i') }}</dd>
                                </div>
                            @endif
                            @if(isset($pcma->fifa_compliance_data['approved_at']))
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">{{ __('pcma_extra.label_0de17afbe9d0') }}</dt>
                                    <dd class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($pcma->fifa_compliance_data['approved_at'])->format('d/m/Y H:i') }}</dd>
                                </div>
                            @endif
                            @if(isset($pcma->fifa_compliance_data['approved_by']))
                            <div>
                                    <dt class="text-sm font-medium text-gray-500">{{ __('pcma_extra.label_70a6f4351502') }}</dt>
                                    <dd class="text-sm text-gray-900">{{ $pcma->fifa_compliance_data['approved_by'] }}</dd>
                            </div>
                            @endif
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Athlete Information with Flags and Logos -->
        @if(($pcma->player ?? $pcma->athlete))
        <div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-800">{{ __('pcma_extra.label_1e41ecaaf9c1') }}</h2>
            </div>
            
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Athlete Identity -->
                    <div class="text-center">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('pcma_extra.label_3138be1c280f') }}</h3>
                        <div class="mb-4">
                            <x-flag-logo-display 
                                :nationality="($pcma->player ?? $pcma->athlete)->nationality"
                                :association="($pcma->player ?? $pcma->athlete)->association"
                                size="large"
                            />
                        </div>
                        <div class="space-y-2">
                            <p class="text-lg font-semibold text-gray-900">{{ ($pcma->player ?? $pcma->athlete)->name }}</p>
                            <p class="text-sm text-gray-600">{{ ($pcma->player ?? $pcma->athlete)->position ?? __('pcma_extra.undefined_position') }}</p>
                            <p class="text-sm text-gray-600">{{ ($pcma->player ?? $pcma->athlete)->age ?? __('pcma_extra.undefined_age') }} {{ __('pcma_extra.years') }}</p>
                        </div>
                    </div>
                    
                    <!-- Club Information -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Club</h3>
                        @if(($pcma->player ?? $pcma->athlete)->club)
                            <div class="mb-4">
                                <x-club-logo 
                                    :club="($pcma->player ?? $pcma->athlete)->club"
                                    size="medium" 
                                    :showName="true" 
                                    :showCountry="true"
                                />
                            </div>
                            <dl class="space-y-3">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">{{ __('pcma_extra.label_aa40e08a0d95') }}</dt>
                                    <dd class="text-sm text-gray-900">{{ ($pcma->player ?? $pcma->athlete)->club->name }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">{{ __('pcma_extra.label_d482782c346b') }}</dt>
                                    <dd class="text-sm text-gray-900">{{ ($pcma->player ?? $pcma->athlete)->club->country ?? __('pcma_extra.not_recorded') }}</dd>
                                </div>
                            </dl>
                        @else
                            <p class="text-sm text-gray-500">{{ __('Aucun club assigné') }}</p>
                        @endif
                    </div>
                    
                    <!-- Association Information -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Association</h3>
                        <dl class="space-y-3">
                            @if(($pcma->player ?? $pcma->athlete)->association)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">{{ __('pcma_extra.label_c2351a5b7050') }}</dt>
                                    <dd class="text-sm text-gray-900">{{ ($pcma->player ?? $pcma->athlete)->association->name }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">{{ __('pcma_extra.label_49b18e300c98') }}</dt>
                                    <dd class="text-sm text-gray-900">{{ ($pcma->player ?? $pcma->athlete)->association->country ?? __('pcma_extra.not_recorded') }}</dd>
                                </div>
                            @else
                                <p class="text-sm text-gray-500">{{ __('pcma_extra.label_f7c3fd1b8115') }}</p>
                            @endif
                        </dl>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Assessment Results -->
        @if($pcma->result_json)
            <div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-800">{{ __('pcma_extra.label_4b44f26393ec') }}</h2>
                </div>
                
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Overall Assessment -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('pcma_extra.label_e2ba2dcb6a17') }}</h3>
                            <dl class="space-y-3">
                                @if(isset($pcma->result_json['overall_health']))
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">{{ __('pcma_extra.label_1ecaf5cbfcdf') }}</dt>
                                        <dd class="text-sm text-gray-900">
                                            <span class="px-2 py-1 text-xs rounded-full 
                                                {{ $pcma->result_json['overall_health'] === 'excellent' ? 'bg-green-100 text-green-800' : 
                                                   ($pcma->result_json['overall_health'] === 'good' ? 'bg-blue-100 text-blue-800' : 
                                                   ($pcma->result_json['overall_health'] === 'fair' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800')) }}">
                                                {{ ucfirst($pcma->result_json['overall_health']) }}
                                            </span>
                                        </dd>
                                    </div>
                                @endif
                                
                                @if(isset($pcma->result_json['assessment_type']))
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">{{ __('pcma_extra.label_c6f1e645013d') }}</dt>
                                        <dd class="text-sm text-gray-900">{{ ucfirst($pcma->result_json['assessment_type']) }}</dd>
                                    </div>
                                @endif
                                
                                @if(isset($pcma->result_json['recommendations']))
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">{{ __('navigation.recommendations') }}</dt>
                                        <dd class="text-sm text-gray-900">
                                            <ul class="list-disc list-inside space-y-1">
                                                @foreach($pcma->result_json['recommendations'] as $recommendation)
                                                    <li>{{ $recommendation }}</li>
                                                @endforeach
                                            </ul>
                                        </dd>
                                    </div>
                                @endif
                            </dl>
                        </div>

                        <!-- Type-specific Results -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('pcma_extra.label_140bd83721bc') }}</h3>
                            <dl class="space-y-3">
                                @if($pcma->type === 'cardio' && isset($pcma->result_json['cardiac_rhythm']))
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">{{ __('pcma_extra.label_5c42d8b9595a') }}</dt>
                                        <dd class="text-sm text-gray-900">{{ ucfirst($pcma->result_json['cardiac_rhythm']) }}</dd>
                                    </div>
                                @endif
                                
                                @if($pcma->type === 'cardio' && isset($pcma->result_json['blood_pressure']))
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">{{ __('pcma_extra.label_a9917598a310') }}</dt>
                                        <dd class="text-sm text-gray-900">{{ $pcma->result_json['blood_pressure'] }}</dd>
                                    </div>
                                @endif
                                
                                @if($pcma->type === 'cardio' && isset($pcma->result_json['heart_rate']))
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">{{ __('pcma_extra.label_641f424357ed') }}</dt>
                                        <dd class="text-sm text-gray-900">{{ $pcma->result_json['heart_rate'] }} bpm</dd>
                                    </div>
                                @endif
                                
                                @if($pcma->type === 'neurological' && isset($pcma->result_json['consciousness']))
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">{{ __('pcma_extra.label_d898eb126c64') }}</dt>
                                        <dd class="text-sm text-gray-900">{{ ucfirst($pcma->result_json['consciousness']) }}</dd>
                                    </div>
                                @endif
                                
                                @if($pcma->type === 'neurological' && isset($pcma->result_json['cranial_nerves']))
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">{{ __('pcma_extra.label_af86530505c5') }}</dt>
                                        <dd class="text-sm text-gray-900">{{ ucfirst($pcma->result_json['cranial_nerves']) }}</dd>
                                    </div>
                                @endif
                                
                                @if($pcma->type === 'orthopedic' && isset($pcma->result_json['joint_mobility']))
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">{{ __('pcma_extra.label_1882aea56ede') }}</dt>
                                        <dd class="text-sm text-gray-900">{{ ucfirst($pcma->result_json['joint_mobility']) }}</dd>
                                    </div>
                                @endif
                                
                                @if($pcma->type === 'orthopedic' && isset($pcma->result_json['muscle_strength']))
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">{{ __('pcma_extra.label_d533eeaafaec') }}</dt>
                                        <dd class="text-sm text-gray-900">{{ ucfirst($pcma->result_json['muscle_strength']) }}</dd>
                                    </div>
                                @endif
                                
                                @if($pcma->type === 'dental' && isset($pcma->result_json['dental_health']))
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">{{ __('pcma_extra.label_a5203705898b') }}</dt>
                                        <dd class="text-sm text-gray-900">
                                            <span class="px-2 py-1 text-xs rounded-full 
                                                {{ $pcma->result_json['dental_health'] === 'excellent' ? 'bg-green-100 text-green-800' : 
                                                   ($pcma->result_json['dental_health'] === 'good' ? 'bg-blue-100 text-blue-800' : 
                                                   ($pcma->result_json['dental_health'] === 'fair' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800')) }}">
                                                {{ ucfirst($pcma->result_json['dental_health']) }}
                                            </span>
                                        </dd>
                                    </div>
                                @endif
                                
                                @if($pcma->type === 'dental' && isset($pcma->result_json['cavities']))
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">{{ __('pcma_extra.label_64b2d806f0d7') }}</dt>
                                        <dd class="text-sm text-gray-900">{{ $pcma->result_json['cavities'] }}</dd>
                                    </div>
                                @endif
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Notes -->
        @if($pcma->notes)
            <div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-800">📝 Notes</h2>
                </div>
                
                <div class="p-6">
                    <p class="text-gray-700">{{ $pcma->notes }}</p>
                </div>
            </div>
        @endif

        <!-- Clinical Notes -->
        @if($pcma->result_json['clinical_notes'] ?? null)
            <div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-800">{{ __('pcma_extra.label_1e808cafd11d') }}</h2>
                </div>
                
                <div class="p-6">
                    <p class="text-gray-700">{{ $pcma->result_json['clinical_notes'] }}</p>
                </div>
            </div>
        @endif

        <!-- Vital Signs Section -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-800">{{ __('pcma_extra.label_b44dcbf4831e') }}</h2>
            </div>
            
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('Tension Artérielle') }}</dt>
                        <dd class="text-sm text-gray-900">{{ $pcma->result_json['vital_signs']['blood_pressure'] ?? __('pcma_extra.not_recorded') }}</dd>
                    </div>
                    
                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('pcma.heart_rate_label') }}</dt>
                        <dd class="text-sm text-gray-900">{{ isset($pcma->result_json['vital_signs']['heart_rate']) ? $pcma->result_json['vital_signs']['heart_rate'] . ' bpm' : __('pcma_extra.not_recorded') }}</dd>
                    </div>
                    
                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('pcma.temperature_label') }}</dt>
                        <dd class="text-sm text-gray-900">{{ isset($pcma->result_json['vital_signs']['temperature']) ? $pcma->result_json['vital_signs']['temperature'] . ' °C' : __('pcma_extra.not_recorded') }}</dd>
                    </div>
                    
                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('pcma.respiratory_rate_label') }}</dt>
                        <dd class="text-sm text-gray-900">{{ isset($pcma->result_json['vital_signs']['respiratory_rate']) ? $pcma->result_json['vital_signs']['respiratory_rate'] . '/min' : __('pcma_extra.not_recorded') }}</dd>
                    </div>
                    
                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('pcma.oxygen_saturation_label') }}</dt>
                        <dd class="text-sm text-gray-900">{{ isset($pcma->result_json['vital_signs']['oxygen_saturation']) ? $pcma->result_json['vital_signs']['oxygen_saturation'] . '%' : __('pcma_extra.not_recorded') }}</dd>
                    </div>
                    
                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('pcma.weight_label') }}</dt>
                        <dd class="text-sm text-gray-900">{{ isset($pcma->result_json['vital_signs']['weight']) ? $pcma->result_json['vital_signs']['weight'] . ' kg' : __('pcma_extra.not_recorded') }}</dd>
                    </div>
                </div>
            </div>
        </div>

        <!-- Medical History Section -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-800">{{ __('pcma_extra.label_3235555a712f') }}</h2>
            </div>
            
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('pcma.cardiovascular_history_label') }}</dt>
                        <dd class="text-sm text-gray-900">{{ $pcma->result_json['medical_history']['cardiovascular_history'] ?? __('pcma_extra.not_recorded') }}</dd>
                    </div>
                    
                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('pcma.surgical_history_label') }}</dt>
                        <dd class="text-sm text-gray-900">{{ $pcma->result_json['medical_history']['surgical_history'] ?? __('pcma_extra.not_recorded') }}</dd>
                    </div>
                    
                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('pcma.medications_label') }}</dt>
                        <dd class="text-sm text-gray-900">{{ $pcma->result_json['medical_history']['medications'] ?? __('pcma_extra.not_recorded') }}</dd>
                    </div>
                    
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Allergies</dt>
                        <dd class="text-sm text-gray-900">{{ $pcma->result_json['medical_history']['allergies'] ?? __('pcma_extra.not_recorded') }}</dd>
                    </div>
                </div>
            </div>
        </div>

        <!-- Physical Examination Section -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-800">{{ __('pcma_extra.label_eb2c9ce1a3ff') }}</h2>
            </div>
            
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('pcma.general_appearance_label') }}</dt>
                        <dd class="text-sm text-gray-900">
                            @if($pcma->result_json['physical_examination']['general_appearance'] ?? null)
                                <span class="px-2 py-1 text-xs rounded-full {{ ($pcma->result_json['physical_examination']['general_appearance'] ?? '') === 'normal' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ ucfirst($pcma->result_json['physical_examination']['general_appearance'] ?? '') }}
                                </span>
                            @else
                                Non renseigné
                            @endif
                        </dd>
                    </div>
                    
                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('pcma.skin_exam_label') }}</dt>
                        <dd class="text-sm text-gray-900">
                            @if($pcma->result_json['physical_examination']['skin_examination'] ?? null)
                                <span class="px-2 py-1 text-xs rounded-full {{ ($pcma->result_json['physical_examination']['skin_examination'] ?? '') === 'normal' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ ucfirst($pcma->result_json['physical_examination']['skin_examination'] ?? '') }}
                                </span>
                            @else
                                Non renseigné
                            @endif
                        </dd>
                    </div>
                    
                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('pcma.lymph_nodes_label') }}</dt>
                        <dd class="text-sm text-gray-900">
                            @if($pcma->result_json['physical_examination']['lymph_nodes'] ?? null)
                                <span class="px-2 py-1 text-xs rounded-full {{ ($pcma->result_json['physical_examination']['lymph_nodes'] ?? '') === 'normal' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ ($pcma->result_json['physical_examination']['lymph_nodes'] ?? '') === 'normal' ? 'Normal' : 'Hypertrophiés' }}
                                </span>
                            @else
                                Non renseigné
                            @endif
                        </dd>
                    </div>
                    
                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('pcma.abdomen_exam_label') }}</dt>
                        <dd class="text-sm text-gray-900">
                            @if($pcma->result_json['physical_examination']['abdomen_examination'] ?? null)
                                <span class="px-2 py-1 text-xs rounded-full {{ ($pcma->result_json['physical_examination']['abdomen_examination'] ?? '') === 'normal' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ ucfirst($pcma->result_json['physical_examination']['abdomen_examination'] ?? '') }}
                                </span>
                            @else
                                Non renseigné
                            @endif
                        </dd>
                    </div>
                </div>
            </div>
        </div>

        <!-- Medical History -->
        @if(isset($pcma->result_json['medical_history']) && is_array($pcma->result_json['medical_history']) && count($pcma->result_json['medical_history']) > 0)
            <div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-800">{{ __('pcma_extra.label_355b7bd00ec0') }}</h2>
                </div>
                
                <div class="p-6">
                    <div class="flex flex-wrap gap-2">
                        @foreach($pcma->result_json['medical_history'] as $history)
                            <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-blue-100 text-blue-800">
                                {{ $history }}
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        <!-- Physical Examination -->
        @if(isset($pcma->result_json['physical_examination']) && is_array($pcma->result_json['physical_examination']) && count($pcma->result_json['physical_examination']) > 0)
            <div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-800">{{ __('pcma_extra.label_eb2c9ce1a3ff') }}</h2>
                </div>
                
                <div class="p-6">
                    <div class="flex flex-wrap gap-2">
                        @foreach($pcma->result_json['physical_examination'] as $exam)
                            <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-800">
                                {{ $exam }}
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        <!-- Cardiovascular Investigations -->
        @if(isset($pcma->result_json['cardiovascular_investigations']) && is_array($pcma->result_json['cardiovascular_investigations']) && count($pcma->result_json['cardiovascular_investigations']) > 0)
            <div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-800">{{ __('pcma_extra.label_6c0217e25fb1') }}</h2>
                </div>
                
                <div class="p-6">
                    <div class="flex flex-wrap gap-2">
                        @foreach($pcma->result_json['cardiovascular_investigations'] as $investigation)
                            <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-red-100 text-red-800">
                                {{ $investigation }}
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        <!-- Cardiovascular Assessment Section -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-800">{{ __('pcma.cardio_assessment_title') }}</h2>
            </div>
            
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('pcma.cardiac_rhythm_label') }}</dt>
                        <dd class="text-sm text-gray-900">
                            @if($pcma->result_json['cardiovascular_assessment']['cardiac_rhythm'] ?? null)
                                <span class="px-2 py-1 text-xs rounded-full {{ ($pcma->result_json['cardiovascular_assessment']['cardiac_rhythm'] ?? '') === 'sinus' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ ucfirst($pcma->result_json['cardiovascular_assessment']['cardiac_rhythm'] ?? '') }}
                                </span>
                            @else
                                Non renseigné
                            @endif
                        </dd>
                    </div>
                    
                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('pcma.heart_murmur_label') }}</dt>
                        <dd class="text-sm text-gray-900">
                            @if($pcma->result_json['cardiovascular_assessment']['heart_murmur'] ?? null)
                                <span class="px-2 py-1 text-xs rounded-full {{ ($pcma->result_json['cardiovascular_assessment']['heart_murmur'] ?? '') === 'none' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ ($pcma->result_json['cardiovascular_assessment']['heart_murmur'] ?? '') === 'none' ? __('Aucun') : ucfirst($pcma->result_json['cardiovascular_assessment']['heart_murmur'] ?? '') }}
                                </span>
                            @else
                                Non renseigné
                            @endif
                        </dd>
                    </div>
                    
                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('pcma.bp_rest_label') }}</dt>
                        <dd class="text-sm text-gray-900">{{ $pcma->result_json['cardiovascular_assessment']['blood_pressure_rest'] ?? __('pcma_extra.not_recorded') }}</dd>
                    </div>
                    
                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('pcma.bp_exercise_label') }}</dt>
                        <dd class="text-sm text-gray-900">{{ $pcma->result_json['cardiovascular_assessment']['blood_pressure_exercise'] ?? __('pcma_extra.not_recorded') }}</dd>
                    </div>
                </div>
            </div>
        </div>

        <!-- Neurological Assessment Section -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-800">{{ __('pcma.neuro_assessment_title') }}</h2>
            </div>
            
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('pcma.consciousness_label') }}</dt>
                        <dd class="text-sm text-gray-900">
                            @if($pcma->result_json['neurological_assessment']['consciousness'] ?? null)
                                <span class="px-2 py-1 text-xs rounded-full {{ ($pcma->result_json['neurological_assessment']['consciousness'] ?? '') === 'alert' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ ucfirst($pcma->result_json['neurological_assessment']['consciousness'] ?? '') }}
                                </span>
                            @else
                                Non renseigné
                            @endif
                        </dd>
                    </div>
                    
                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('pcma.cranial_nerves_label') }}</dt>
                        <dd class="text-sm text-gray-900">
                            @if($pcma->result_json['neurological_assessment']['cranial_nerves'] ?? null)
                                <span class="px-2 py-1 text-xs rounded-full {{ ($pcma->result_json['neurological_assessment']['cranial_nerves'] ?? '') === 'normal' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ ucfirst($pcma->result_json['neurological_assessment']['cranial_nerves'] ?? '') }}
                                </span>
                            @else
                                Non renseigné
                            @endif
                        </dd>
                    </div>
                    
                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('pcma.motor_function_label') }}</dt>
                        <dd class="text-sm text-gray-900">
                            @if($pcma->result_json['neurological_assessment']['motor_function'] ?? null)
                                <span class="px-2 py-1 text-xs rounded-full {{ ($pcma->result_json['neurological_assessment']['motor_function'] ?? '') === 'normal' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ ucfirst($pcma->result_json['neurological_assessment']['motor_function'] ?? '') }}
                                </span>
                            @else
                                Non renseigné
                            @endif
                        </dd>
                    </div>
                    
                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('pcma.sensory_function_label') }}</dt>
                        <dd class="text-sm text-gray-900">
                            @if($pcma->result_json['neurological_assessment']['sensory_function'] ?? null)
                                <span class="px-2 py-1 text-xs rounded-full {{ ($pcma->result_json['neurological_assessment']['sensory_function'] ?? '') === 'normal' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ ucfirst($pcma->result_json['neurological_assessment']['sensory_function'] ?? '') }}
                                </span>
                            @else
                                Non renseigné
                            @endif
                        </dd>
                    </div>
                </div>
            </div>
        </div>

        <!-- Musculoskeletal Assessment Section -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-800">{{ __('pcma.msk_assessment_title') }}</h2>
            </div>
            
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('pcma.joint_mobility_label') }}</dt>
                        <dd class="text-sm text-gray-900">
                            @if($pcma->result_json['musculoskeletal_assessment']['joint_mobility'] ?? null)
                                <span class="px-2 py-1 text-xs rounded-full {{ ($pcma->result_json['musculoskeletal_assessment']['joint_mobility'] ?? '') === 'normal' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ ucfirst($pcma->result_json['musculoskeletal_assessment']['joint_mobility'] ?? '') }}
                                </span>
                            @else
                                Non renseigné
                            @endif
                        </dd>
                    </div>
                    
                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('pcma.muscle_strength_label') }}</dt>
                        <dd class="text-sm text-gray-900">
                            @if($pcma->result_json['musculoskeletal_assessment']['muscle_strength'] ?? null)
                                <span class="px-2 py-1 text-xs rounded-full {{ ($pcma->result_json['musculoskeletal_assessment']['muscle_strength'] ?? '') === 'normal' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ ucfirst($pcma->result_json['musculoskeletal_assessment']['muscle_strength'] ?? '') }}
                                </span>
                            @else
                                Non renseigné
                            @endif
                        </dd>
                    </div>
                    
                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('pcma.pain_assessment_label') }}</dt>
                        <dd class="text-sm text-gray-900">
                            @if($pcma->result_json['musculoskeletal_assessment']['pain_assessment'] ?? null)
                                <span class="px-2 py-1 text-xs rounded-full 
                                    {{ ($pcma->result_json['musculoskeletal_assessment']['pain_assessment'] ?? '') === 'none' ? 'bg-green-100 text-green-800' : 
                                       (($pcma->result_json['musculoskeletal_assessment']['pain_assessment'] ?? '') === 'mild' ? 'bg-yellow-100 text-yellow-800' : 
                                       (($pcma->result_json['musculoskeletal_assessment']['pain_assessment'] ?? '') === 'moderate' ? 'bg-orange-100 text-orange-800' : 'bg-red-100 text-red-800')) }}">
                                    {{ ($pcma->result_json['musculoskeletal_assessment']['pain_assessment'] ?? '') === 'none' ? __('Aucune') : ucfirst($pcma->result_json['musculoskeletal_assessment']['pain_assessment'] ?? '') }}
                                </span>
                            @else
                                Non renseigné
                            @endif
                        </dd>
                    </div>
                    
                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('pcma.rom_label') }}</dt>
                        <dd class="text-sm text-gray-900">
                            @if($pcma->result_json['musculoskeletal_assessment']['range_of_motion'] ?? null)
                                <span class="px-2 py-1 text-xs rounded-full {{ ($pcma->result_json['musculoskeletal_assessment']['range_of_motion'] ?? '') === 'full' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ ($pcma->result_json['musculoskeletal_assessment']['range_of_motion'] ?? '') === 'full' ? 'Complète' : ucfirst($pcma->result_json['musculoskeletal_assessment']['range_of_motion'] ?? '') }}
                                </span>
                            @else
                                Non renseigné
                            @endif
                        </dd>
                    </div>
                </div>
            </div>
        </div>

        <!-- Medical Imaging Section -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-800">{{ __('health_records_create.medical_imaging_heading') }}</h2>
            </div>
            
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- ECG -->
                    <div class="border rounded-lg p-4">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('pcma_extra.label_13ca7c912fe5') }}</h3>
                        <div class="space-y-2">
                            <div>
                                <span class="text-sm font-medium text-gray-500">{{ __('pcma_extra.label_720601ed64d2') }}</span>
                                <span class="text-sm text-gray-900">@if($pcma->ecg_file)<a href="{{ route('pcma.file', [$pcma, 'ecg_file']) }}">{{ basename($pcma->ecg_file) }}</a>@else{{ __('pcma_extra.not_recorded') }}@endif</span>
                            </div>
                            @if($pcma->result_json['medical_imaging']['ecg_date'] ?? null)
                                <div>
                                    <span class="text-sm font-medium text-gray-500">Date:</span>
                                    <span class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($pcma->result_json['medical_imaging']['ecg_date'])->format('d/m/Y') }}</span>
                                </div>
                            @endif
                            @if($pcma->result_json['medical_imaging']['ecg_interpretation'] ?? null)
                                <div>
                                    <span class="text-sm font-medium text-gray-500">{{ __('pcma_extra.label_003e0031eb21') }}</span>
                                    <span class="text-sm text-gray-900">{{ ucfirst(str_replace('_', ' ', $pcma->result_json['medical_imaging']['ecg_interpretation'])) }}</span>
                                </div>
                            @endif
                            @if($pcma->result_json['medical_imaging']['ecg_notes'] ?? null)
                                <div>
                                    <span class="text-sm font-medium text-gray-500">Notes:</span>
                                    <span class="text-sm text-gray-900">{{ $pcma->result_json['medical_imaging']['ecg_notes'] }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- MRI -->
                    <div class="border rounded-lg p-4">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('pcma_extra.label_a7736bc90371') }}</h3>
                        <div class="space-y-2">
                            <div>
                                <span class="text-sm font-medium text-gray-500">{{ __('pcma_extra.label_720601ed64d2') }}</span>
                                <span class="text-sm text-gray-900">@if($pcma->mri_file)<a href="{{ route('pcma.file', [$pcma, 'mri_file']) }}">{{ basename($pcma->mri_file) }}</a>@else{{ __('pcma_extra.not_recorded') }}@endif</span>
                            </div>
                            @if($pcma->result_json['medical_imaging']['mri_date'] ?? null)
                                <div>
                                    <span class="text-sm font-medium text-gray-500">Date:</span>
                                    <span class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($pcma->result_json['medical_imaging']['mri_date'])->format('d/m/Y') }}</span>
                                </div>
                            @endif
                            @if($pcma->result_json['medical_imaging']['mri_type'] ?? null)
                                <div>
                                    <span class="text-sm font-medium text-gray-500">Type:</span>
                                    <span class="text-sm text-gray-900">{{ ucfirst($pcma->result_json['medical_imaging']['mri_type']) }}</span>
                                </div>
                            @endif
                            @if($pcma->result_json['medical_imaging']['mri_findings'] ?? null)
                                <div>
                                    <span class="text-sm font-medium text-gray-500">{{ __('pcma_extra.label_eec6c16660be') }}</span>
                                    <span class="text-sm text-gray-900">{{ ucfirst(str_replace('_', ' ', $pcma->result_json['medical_imaging']['mri_findings'])) }}</span>
                                </div>
                            @endif
                            @if($pcma->result_json['medical_imaging']['mri_notes'] ?? null)
                                <div>
                                    <span class="text-sm font-medium text-gray-500">Notes:</span>
                                    <span class="text-sm text-gray-900">{{ $pcma->result_json['medical_imaging']['mri_notes'] }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- X-Ray -->
                    <div class="border rounded-lg p-4">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('pcma_extra.label_3b02b9456e5b') }}</h3>
                        <div class="space-y-2">
                            <div>
                                <span class="text-sm font-medium text-gray-500">{{ __('pcma_extra.label_720601ed64d2') }}</span>
                                <span class="text-sm text-gray-900">{{ $pcma->xray_file ?? __('pcma_extra.not_recorded') }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- CT Scan -->
                    <div class="border rounded-lg p-4">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('pcma_extra.label_1345d5a4d2e9') }}</h3>
                        <div class="space-y-2">
                            <div>
                                <span class="text-sm font-medium text-gray-500">{{ __('pcma_extra.label_720601ed64d2') }}</span>
                                <span class="text-sm text-gray-900">{{ $pcma->ct_scan_file ?? __('pcma_extra.not_recorded') }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Ultrasound -->
                    <div class="border rounded-lg p-4">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('pcma_extra.label_5e3137e780d5') }}</h3>
                        <div class="space-y-2">
                            <div>
                                <span class="text-sm font-medium text-gray-500">{{ __('pcma_extra.label_720601ed64d2') }}</span>
                                <span class="text-sm text-gray-900">{{ $pcma->ultrasound_file ?? __('pcma_extra.not_recorded') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- SCAT Assessment -->
        @if(isset($pcma->result_json['scat_assessment']) && is_array($pcma->result_json['scat_assessment']) && count($pcma->result_json['scat_assessment']) > 0)
            <div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-800">{{ __('pcma_extra.label_ace13247e550') }}</h2>
                </div>
                
                <div class="p-6">
                    <div class="flex flex-wrap gap-2">
                        @foreach($pcma->result_json['scat_assessment'] as $scat)
                            <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-purple-100 text-purple-800">
                                {{ $scat }}
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        <!-- Medical Signature Section -->
        @if($pcma->is_signed)
            <div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-800">{{ __('pcma_extra.label_6179129457aa') }}</h2>
                    <p class="text-sm text-gray-600 mt-1">{{ __('pcma_extra.label_c21471f8829b') }}</p>
                </div>
                
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('pcma_extra.label_8089605df9ee') }}</h3>
                            <dl class="space-y-3">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">{{ __('pcma_extra.label_2e52ab6b3b40') }}</dt>
                                    <dd class="text-sm text-gray-900">{{ $pcma->signed_by ?? 'Non spécifié' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">{{ __('pcma_extra.label_677132c8fabf') }}</dt>
                                    <dd class="text-sm text-gray-900">{{ $pcma->license_number ?? 'Non spécifié' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">{{ __('pcma_extra.label_64722771a286') }}</dt>
                                    <dd class="text-sm text-gray-900">
                                        @if($pcma->signed_at)
                                            {{ \Carbon\Carbon::parse($pcma->signed_at)->format('d/m/Y H:i') }}
                                        @else
                                            Non spécifiée
                                        @endif
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">{{ __('Statut') }}</dt>
                                    <dd class="text-sm text-gray-900">
                                        <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">
                                            {{ __('pcma_extra.label_f74f754458bb') }}
                                        </span>
                                    </dd>
                                </div>
                            </dl>
                        </div>
                        
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('pcma_extra.label_4341b5cb4b5f') }}</h3>
                            @if($pcma->signature_image)
                                <div class="border border-gray-300 rounded-lg p-4 bg-gray-50">
                                    <img src="{{ route('pcma.file', [$pcma, 'signature_image']) }}"
                                         alt="{{ __('pcma.report_signature_alt') }}"
                                         class="max-w-full h-auto max-h-48 object-contain">
                                </div>
                            @else
                                <div class="border border-gray-300 rounded-lg p-4 bg-gray-50 text-center">
                                    <p class="text-gray-500 text-sm">{{ __('pcma_extra.label_e0ca9ea2ba13') }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    @if($pcma->signature_data)
                        <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                            <h4 class="text-sm font-medium text-blue-900 mb-2">{{ __('pcma_extra.label_2841870add5f') }}</h4>
                            <div class="text-xs text-blue-800 space-y-1">
                                <p><strong>Assessment ID:</strong> {{ $pcma->signature_data['assessmentId'] ?? 'N/A' }}</p>
                                <p><strong>Fitness Status:</strong> {{ $pcma->signature_data['fitnessStatus'] ?? 'N/A' }}</p>
                                <p><strong>IP Address:</strong> {{ $pcma->signature_data['ipAddress'] ?? 'N/A' }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @else
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-8">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-yellow-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                    <div>
                        <h3 class="text-sm font-medium text-yellow-800">{{ __('pcma_extra.label_3dd0740369ee') }}</h3>
                        <p class="text-sm text-yellow-700 mt-1">{{ __('pcma_extra.label_9169c8b66d85') }}</p>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
