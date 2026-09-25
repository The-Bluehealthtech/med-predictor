@extends('layouts.app')

@section('title', 'Match Sheet - ' . ($match->homeTeam->club->name ?? $match->homeTeam->name) . ' vs ' . ($match->awayTeam->club->name ?? $match->awayTeam->name))

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Match Sheet') }} - {{ $match->homeTeam->club->name ?? $match->homeTeam->name }} vs {{ $match->awayTeam->club->name ?? $match->awayTeam->name }}
            </h2>
            <div class="flex space-x-2">
                <button onclick="window.print()" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150 print:hidden">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9V2h12v7M6 18H4a2 2 0 01-2-2V9a2 2 0 012-2h16a2 2 0 012 2v7a2 2 0 01-2 2h-2m-4 0v4m0 0H8m4 0h4"></path>
                    </svg>
                    {{ __('Print Sheet') }}
                </button>
                <a href="{{ route('competition-management.competitions.standings', $match->competition) }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    {{ __('Back to Standings') }}
                </a>
            </div>
        </div>

        <!-- Team Logos and Match Header -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex items-center justify-center space-x-8">
                    <!-- Home Team -->
                    <div class="text-center">
                        <div class="w-20 h-20 mx-auto mb-3">
                            @if($match->homeTeam->club && $match->homeTeam->club->logo_url)
                                <img src="{{ $match->homeTeam->club->logo_url }}" 
                                     alt="{{ $match->homeTeam->club->name }} Logo" 
                                     class="w-full h-full object-contain">
                            @else
                                <div class="w-full h-full bg-gray-200 rounded-full flex items-center justify-center">
                                    <span class="text-gray-500 text-xs font-medium">{{ substr($match->homeTeam->club->name ?? $match->homeTeam->name, 0, 2) }}</span>
                                </div>
                            @endif
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900">{{ $match->homeTeam->club->name ?? $match->homeTeam->name }}</h3>
                    </div>

                    <!-- VS -->
                    <div class="text-center">
                        <div class="text-2xl font-bold text-gray-400">VS</div>
                        <div class="text-sm text-gray-500 mt-1">{{ $match->match_date ? $match->match_date->format('M j, Y') : 'TBD' }}</div>
                    </div>

                    <!-- Away Team -->
                    <div class="text-center">
                        <div class="w-20 h-20 mx-auto mb-3">
                            @if($match->awayTeam->club && $match->awayTeam->club->logo_url)
                                <img src="{{ $match->awayTeam->club->logo_url }}" 
                                     alt="{{ $match->awayTeam->club->name }} Logo" 
                                     class="w-full h-full object-contain">
                            @else
                                <div class="w-full h-full bg-gray-200 rounded-full flex items-center justify-center">
                                    <span class="text-gray-500 text-xs font-medium">{{ substr($match->awayTeam->club->name ?? $match->awayTeam->name, 0, 2) }}</span>
                                </div>
                            @endif
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900">{{ $match->awayTeam->club->name ?? $match->awayTeam->name }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Hand-Signed Match Sheet Upload -->
        <div class="mb-6 bg-white shadow-sm sm:rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('Upload Hand-Signed Match Sheet') }}</h3>
            <form method="POST" action="{{ route('competition-management.matches.match-sheet.upload-signed', $match) }}" enctype="multipart/form-data" class="flex items-center space-x-4">
                @csrf
                <input type="file" name="signed_sheet" accept="image/*,application/pdf" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" required>
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5" />
                    </svg>
                    {{ __('Upload') }}
                </button>
            </form>
            @if($matchSheet->signed_sheet_path)
                <div class="mt-4">
                    <h4 class="text-sm font-medium text-gray-700 mb-2">{{ __('Hand-Signed Sheet:') }}</h4>
                    @if(Str::endsWith($matchSheet->signed_sheet_path, ['.jpg', '.jpeg', '.png', '.gif']))
                        <img src="{{ asset('storage/' . $matchSheet->signed_sheet_path) }}" alt="Hand-Signed Match Sheet" class="max-w-full h-auto border rounded shadow">
                    @elseif(Str::endsWith($matchSheet->signed_sheet_path, ['.pdf']))
                        <a href="{{ asset('storage/' . $matchSheet->signed_sheet_path) }}" target="_blank" class="text-blue-600 underline">View PDF</a>
                    @endif
                </div>
            @endif
        </div>

        <!-- Team Signatures Section -->
        <div class="mb-6 bg-white shadow-sm sm:rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Team Signatures') }}</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Home Team Signature -->
                <div class="border rounded-lg p-4">
                    <h4 class="font-medium text-gray-700 mb-3">{{ $match->homeTeam->club->name ?? $match->homeTeam->name }} - {{ __('Team Official Signature') }}</h4>
                    @if($matchSheet->home_team_signature)
                        <div class="mb-3 p-3 bg-green-50 border border-green-200 rounded">
                            <div class="text-sm text-green-800">
                                <strong>{{ __('Signed by') }}:</strong> {{ $matchSheet->home_team_signature }}
                            </div>
                            <div class="text-xs text-green-600">
                                {{ __('Signed on') }}: {{ $matchSheet->home_team_signed_at->format('M j, Y g:i A') }}
                            </div>
                        </div>
                    @else
                        <form method="POST" action="{{ route('competition-management.matches.match-sheet.sign-team', $match) }}" class="space-y-3">
                            @csrf
                            <input type="hidden" name="team_type" value="home">
                            <div>
                                <label for="home_signature" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Official Name') }}</label>
                                <input type="text" id="home_signature" name="signature" required 
                                       class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="{{ __('Enter your full name as signature') }}">
                            </div>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                                {{ __('Sign for Home Team') }}
                            </button>
                        </form>
                    @endif
                </div>

                <!-- Away Team Signature -->
                <div class="border rounded-lg p-4">
                    <h4 class="font-medium text-gray-700 mb-3">{{ $match->awayTeam->club->name ?? $match->awayTeam->name }} - {{ __('Team Official Signature') }}</h4>
                    @if($matchSheet->away_team_signature)
                        <div class="mb-3 p-3 bg-green-50 border border-green-200 rounded">
                            <div class="text-sm text-green-800">
                                <strong>{{ __('Signed by') }}:</strong> {{ $matchSheet->away_team_signature }}
                            </div>
                            <div class="text-xs text-green-600">
                                {{ __('Signed on') }}: {{ $matchSheet->away_team_signed_at->format('M j, Y g:i A') }}
                            </div>
                        </div>
                    @else
                        <form method="POST" action="{{ route('competition-management.matches.match-sheet.sign-team', $match) }}" class="space-y-3">
                            @csrf
                            <input type="hidden" name="team_type" value="away">
                            <div>
                                <label for="away_signature" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Official Name') }}</label>
                                <input type="text" id="away_signature" name="signature" required 
                                       class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="{{ __('Enter your full name as signature') }}">
                            </div>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                                {{ __('Sign for Away Team') }}
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                {{ session('error') }}
            </div>
        @endif

        <!-- Stage Progress Indicator -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6 bg-white border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Match Sheet Progress') }}</h3>
                <div class="space-y-4">
                    @foreach($stageProgress as $stage => $progress)
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                @if($progress['completed'])
                                    <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                @elseif($progress['current'])
                                    <div class="w-8 h-8 bg-{{ $matchSheet->stage_color }}-500 rounded-full flex items-center justify-center">
                                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                @else
                                    <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center">
                                        <span class="text-gray-600 text-sm font-medium">{{ array_search($stage, array_keys($stageProgress)) + 1 }}</span>
                                    </div>
                                @endif
                            </div>
                            <div class="ml-4 flex-1">
                                <div class="text-sm font-medium text-gray-900">{{ $progress['label'] }}</div>
                                @if($progress['timestamp'])
                                    <div class="text-sm text-gray-500">{{ $progress['timestamp']->format('M j, Y g:i A') }}</div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Match Information -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6 bg-white border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Match Information') }}</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <h4 class="font-medium text-gray-700 mb-2">{{ __('Teams') }}</h4>
                        <div class="text-lg font-semibold text-gray-900">
                            {{ $match->homeTeam->club->name ?? $match->homeTeam->name }} vs {{ $match->awayTeam->club->name ?? $match->awayTeam->name }}
                        </div>
                    </div>
                    <div>
                        <h4 class="font-medium text-gray-700 mb-2">{{ __('Competition') }}</h4>
                        <div class="text-gray-900">{{ $match->competition->name }}</div>
                    </div>
                    <div>
                        <h4 class="font-medium text-gray-700 mb-2">{{ __('Current Stage') }}</h4>
                        <div class="flex items-center">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-{{ $matchSheet->stage_color }}-100 text-{{ $matchSheet->stage_color }}-800">
                                {{ $matchSheet->stage_label }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Match Results Section -->
        @if($matchSheet->match_status === 'completed' && ($matchSheet->home_team_score !== null || $matchSheet->away_team_score !== null))
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6 bg-white border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Match Results') }}</h3>
                <div class="text-center">
                    <div class="flex items-center justify-center space-x-8 mb-4">
                        <div class="text-center">
                            <div class="text-sm font-medium text-gray-600 mb-2">{{ $match->homeTeam->club->name ?? $match->homeTeam->name }}</div>
                            <div class="text-4xl font-bold text-blue-600">{{ $matchSheet->home_team_score ?? 0 }}</div>
                        </div>
                        <div class="text-2xl font-bold text-gray-400">vs</div>
                        <div class="text-center">
                            <div class="text-sm font-medium text-gray-600 mb-2">{{ $match->awayTeam->club->name ?? $match->awayTeam->name }}</div>
                            <div class="text-4xl font-bold text-blue-600">{{ $matchSheet->away_team_score ?? 0 }}</div>
                        </div>
                    </div>
                    <div class="text-sm text-gray-500">
                        @if($matchSheet->referee_signed_at)
                            Match completed on {{ $matchSheet->referee_signed_at->format('M j, Y g:i A') }}
                        @else
                            Match completed
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Venue and Match Details -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6 bg-white border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Venue & Match Details') }}</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Venue Information -->
                    <div>
                        <h4 class="font-medium text-gray-700 mb-3">{{ __('Venue Information') }}</h4>
                        <div class="space-y-2">
                            <div>
                                <span class="text-sm font-medium text-gray-600">{{ __('Stadium') }}:</span>
                                <span class="text-sm text-gray-900">{{ $venueInfo['stadium'] }}</span>
                            </div>
                            <div>
                                <span class="text-sm font-medium text-gray-600">{{ __('Capacity') }}:</span>
                                <span class="text-sm text-gray-900">{{ $venueInfo['capacity'] }}</span>
                            </div>
                            <div>
                                <span class="text-sm font-medium text-gray-600">{{ __('Address') }}:</span>
                                <span class="text-sm text-gray-900">{{ $venueInfo['address'] }}</span>
                            </div>
                            <div>
                                <span class="text-sm font-medium text-gray-600">{{ __('City') }}:</span>
                                <span class="text-sm text-gray-900">{{ $venueInfo['city'] }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Match Details -->
                    <div>
                        <h4 class="font-medium text-gray-700 mb-3">{{ __('Match Details') }}</h4>
                        <div class="space-y-2">
                            <div>
                                <span class="text-sm font-medium text-gray-600">{{ __('Match Number') }}:</span>
                                <span class="text-sm text-gray-900">{{ $matchSheet->match_number ?? 'TBD' }}</span>
                            </div>
                            <div>
                                <span class="text-sm font-medium text-gray-600">{{ __('Kickoff Time') }}:</span>
                                <span class="text-sm text-gray-900">
                                    @if($matchSheet->kickoff_time)
                                        {{ $matchSheet->kickoff_time->format('d/m/Y H:i') }}
                                    @else
                                        TBD
                                    @endif
                                </span>
                            </div>
                            <div>
                                <span class="text-sm font-medium text-gray-600">{{ __('Weather') }}:</span>
                                <span class="text-sm text-gray-900">{{ $matchSheet->weather_conditions ?? 'TBD' }}</span>
                            </div>
                            <div>
                                <span class="text-sm font-medium text-gray-600">{{ __('Pitch Condition') }}:</span>
                                <span class="text-sm text-gray-900">{{ $matchSheet->pitch_conditions ?? 'TBD' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Team Officials -->
                    <div>
                        <h4 class="font-medium text-gray-700 mb-3">{{ __('Team Officials') }}</h4>
                        <div class="space-y-3">
                            <div>
                                <h5 class="text-sm font-medium text-gray-600 mb-1">{{ $match->homeTeam->club->name ?? $match->homeTeam->name }}</h5>
                                <div class="text-xs text-gray-500">
                                    <div>{{ __('Coach') }}: {{ $homeTeamOfficials['coach'] }}</div>
                                    <div>{{ __('Manager') }}: {{ $homeTeamOfficials['manager'] }}</div>
                                </div>
                            </div>
                            <div>
                                <h5 class="text-sm font-medium text-gray-600 mb-1">{{ $match->awayTeam->club->name ?? $match->awayTeam->name }}</h5>
                                <div class="text-xs text-gray-500">
                                    <div>{{ __('Coach') }}: {{ $awayTeamOfficials['coach'] }}</div>
                                    <div>{{ __('Manager') }}: {{ $awayTeamOfficials['manager'] }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Referee Assignment Section -->
        @if($canAssignReferee && $matchSheet->isInProgress())
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6 bg-white border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Referee Assignment') }}</h3>
                
                @if($matchSheet->assigned_referee_id)
                    <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                        <h4 class="font-medium text-green-800 mb-2">{{ __('Currently Assigned Referee Team') }}</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @if($matchSheet->mainReferee)
                                <div class="flex items-center">
                                    <div class="w-3 h-3 bg-red-500 rounded-full mr-2"></div>
                                    <div>
                                        <div class="text-sm font-medium text-green-800">{{ __('Main Referee') }}</div>
                                        <div class="text-sm text-green-700">{{ $matchSheet->mainReferee->name }}</div>
                                    </div>
                                </div>
                            @endif
                            @if($matchSheet->assistantReferee1)
                                <div class="flex items-center">
                                    <div class="w-3 h-3 bg-yellow-500 rounded-full mr-2"></div>
                                    <div>
                                        <div class="text-sm font-medium text-green-800">{{ __('Assistant Referee 1') }}</div>
                                        <div class="text-sm text-green-700">{{ $matchSheet->assistantReferee1->name }}</div>
                                    </div>
                                </div>
                            @endif
                            @if($matchSheet->assistantReferee2)
                                <div class="flex items-center">
                                    <div class="w-3 h-3 bg-yellow-500 rounded-full mr-2"></div>
                                    <div>
                                        <div class="text-sm font-medium text-green-800">{{ __('Assistant Referee 2') }}</div>
                                        <div class="text-sm text-green-700">{{ $matchSheet->assistantReferee2->name }}</div>
                                    </div>
                                </div>
                            @endif
                            @if($matchSheet->fourthOfficial)
                                <div class="flex items-center">
                                    <div class="w-3 h-3 bg-blue-500 rounded-full mr-2"></div>
                                    <div>
                                        <div class="text-sm font-medium text-green-800">{{ __('Fourth Official') }}</div>
                                        <div class="text-sm text-green-700">{{ $matchSheet->fourthOfficial->name }}</div>
                                    </div>
                                </div>
                            @endif
                            @if($matchSheet->varReferee)
                                <div class="flex items-center">
                                    <div class="w-3 h-3 bg-purple-500 rounded-full mr-2"></div>
                                    <div>
                                        <div class="text-sm font-medium text-green-800">{{ __('VAR Referee') }}</div>
                                        <div class="text-sm text-green-700">{{ $matchSheet->varReferee->name }}</div>
                                    </div>
                                </div>
                            @endif
                            @if($matchSheet->varAssistant)
                                <div class="flex items-center">
                                    <div class="w-3 h-3 bg-purple-500 rounded-full mr-2"></div>
                                    <div>
                                        <div class="text-sm font-medium text-green-800">{{ __('VAR Assistant') }}</div>
                                        <div class="text-sm text-green-700">{{ $matchSheet->varAssistant->name }}</div>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="text-sm text-green-600 mt-2">Assigned on {{ $matchSheet->referee_assigned_at->format('M j, Y g:i A') }}</div>
                    </div>
                @endif

                <form method="POST" action="{{ route('competition-management.matches.match-sheet.assign-referee', $match) }}" class="space-y-6">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <!-- Main Referee -->
                        <div>
                            <label for="referee_id" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('Main Referee') }} <span class="text-red-500">*</span>
                            </label>
                            <select name="referee_id" id="referee_id" required class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <option value="">{{ __('Select main referee...') }}</option>
                                @foreach($availableReferees as $referee)
                                    <option value="{{ $referee['id'] }}" {{ $matchSheet->main_referee_id == $referee['id'] ? 'selected' : '' }}>
                                        {{ $referee['name'] }} ({{ $referee['qualifications'] }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Assistant Referee 1 -->
                        <div>
                            <label for="assistant_referee_1_id" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('Assistant Referee 1') }}
                            </label>
                            <select name="assistant_referee_1_id" id="assistant_referee_1_id" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <option value="">{{ __('Select assistant referee...') }}</option>
                                @foreach($availableReferees as $referee)
                                    <option value="{{ $referee['id'] }}" {{ $matchSheet->assistant_referee_1_id == $referee['id'] ? 'selected' : '' }}>
                                        {{ $referee['name'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Assistant Referee 2 -->
                        <div>
                            <label for="assistant_referee_2_id" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('Assistant Referee 2') }}
                            </label>
                            <select name="assistant_referee_2_id" id="assistant_referee_2_id" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <option value="">{{ __('Select assistant referee...') }}</option>
                                @foreach($availableReferees as $referee)
                                    <option value="{{ $referee['id'] }}" {{ $matchSheet->assistant_referee_2_id == $referee['id'] ? 'selected' : '' }}>
                                        {{ $referee['name'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Fourth Official -->
                        <div>
                            <label for="fourth_official_id" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('Fourth Official') }}
                            </label>
                            <select name="fourth_official_id" id="fourth_official_id" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <option value="">{{ __('Select fourth official...') }}</option>
                                @foreach($availableReferees as $referee)
                                    <option value="{{ $referee['id'] }}" {{ $matchSheet->fourth_official_id == $referee['id'] ? 'selected' : '' }}>
                                        {{ $referee['name'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- VAR Referee -->
                        <div>
                            <label for="var_referee_id" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('VAR Referee') }}
                            </label>
                            <select name="var_referee_id" id="var_referee_id" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <option value="">{{ __('Select VAR referee...') }}</option>
                                @foreach($availableReferees as $referee)
                                    <option value="{{ $referee['id'] }}" {{ $matchSheet->var_referee_id == $referee['id'] ? 'selected' : '' }}>
                                        {{ $referee['name'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- VAR Assistant -->
                        <div>
                            <label for="var_assistant_id" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('VAR Assistant') }}
                            </label>
                            <select name="var_assistant_id" id="var_assistant_id" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <option value="">{{ __('Select VAR assistant...') }}</option>
                                @foreach($availableReferees as $referee)
                                    <option value="{{ $referee['id'] }}" {{ $matchSheet->var_assistant_id == $referee['id'] ? 'selected' : '' }}>
                                        {{ $referee['name'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3">
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ __('Assign Referee Team') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @endif

        <!-- Team Lineups Section -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">{{ __('Team Lineups') }}</h3>
                    <div class="flex items-center space-x-2">
                        @if($matchSheet->lineups_locked)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                {{ __('Lineups Locked') }}
                            </span>
                        @endif
                        
                        <!-- Import Players Button -->
                        <a href="{{ route('competition-management.matches.match-sheet.import-players', $match) }}" 
                           class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700 focus:bg-purple-700 active:bg-purple-900 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                            </svg>
                            {{ __('Import Players') }}
                        </a>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Home Team -->
                    <div>
                        <h4 class="font-medium text-gray-700 mb-4">{{ $match->homeTeam->club->name ?? $match->homeTeam->name }}</h4>
                        
                        @if($homeTeamRoster)
                            <div class="space-y-3">
                                <div>
                                    <h5 class="text-sm font-medium text-gray-600 mb-2">{{ __('Starting XI') }}</h5>
                                    <div class="grid grid-cols-1 gap-2">
                                        @foreach($homeTeamRoster->players->where('is_starter', true) as $rosterPlayer)
                                            <div class="flex items-center justify-between p-2 bg-gray-50 rounded">
                                                <div class="flex items-center">
                                                    <span class="w-8 h-8 bg-blue-500 text-white text-xs font-medium rounded-full flex items-center justify-center mr-3">
                                                        {{ $rosterPlayer->jersey_number }}
                                                    </span>
                                                    <span class="text-sm font-medium text-gray-900">{{ $rosterPlayer->player->name }}</span>
                                                </div>
                                                <span class="text-xs text-gray-500">{{ $rosterPlayer->position }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <div>
                                    <h5 class="text-sm font-medium text-gray-600 mb-2">{{ __('Substitutes') }}</h5>
                                    <div class="grid grid-cols-1 gap-2">
                                        @foreach($homeTeamRoster->players->where('is_starter', false) as $rosterPlayer)
                                            <div class="flex items-center justify-between p-2 bg-gray-50 rounded">
                                                <div class="flex items-center">
                                                    <span class="w-8 h-8 bg-green-500 text-white text-xs font-medium rounded-full flex items-center justify-center mr-3">
                                                        {{ $rosterPlayer->jersey_number }}
                                                    </span>
                                                    <span class="text-sm font-medium text-gray-900">{{ $rosterPlayer->player->name }}</span>
                                                </div>
                                                <span class="text-xs text-gray-500">{{ $rosterPlayer->position }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @else
                            <p class="text-gray-500 text-sm">{{ __('No lineup submitted yet.') }}</p>
                        @endif

                        <!-- Lineup Signature Status -->
                        <div class="mt-4 p-3 bg-gray-50 rounded">
                            @if($matchSheet->home_team_lineup_signature)
                                <div class="flex items-center text-green-600">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="text-sm font-medium">{{ __('Lineup Signed') }}</span>
                                </div>
                                <div class="text-xs text-gray-500 mt-1">{{ $matchSheet->home_team_lineup_signed_at->format('M j, Y g:i A') }}</div>
                            @else
                                <div class="flex items-center text-yellow-600">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="text-sm font-medium">{{ __('Lineup Not Signed') }}</span>
                                </div>
                            @endif
                        </div>

                        <!-- Sign Lineup Button -->
                        @if($canSignLineup && $matchSheet->canSignLineup(Auth::user(), 'home'))
                        <form method="POST" action="{{ route('competition-management.matches.match-sheet.sign-lineup', $match) }}" class="mt-4">
                            @csrf
                            <input type="hidden" name="team_type" value="home">
                            <input type="hidden" name="signature" value="{{ Auth::user()->name . ' - ' . now()->format('Y-m-d H:i:s') }}">
                            <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                {{ __('Sign Lineup') }}
                            </button>
                        </form>
                        @endif
                    </div>

                    <!-- Away Team -->
                    <div>
                        <h4 class="font-medium text-gray-700 mb-4">{{ $match->awayTeam->club->name ?? $match->awayTeam->name }}</h4>
                        
                        @if($awayTeamRoster)
                            <div class="space-y-3">
                                <div>
                                    <h5 class="text-sm font-medium text-gray-600 mb-2">{{ __('Starting XI') }}</h5>
                                    <div class="grid grid-cols-1 gap-2">
                                        @foreach($awayTeamRoster->players->where('is_starter', true) as $rosterPlayer)
                                            <div class="flex items-center justify-between p-2 bg-gray-50 rounded">
                                                <div class="flex items-center">
                                                    <span class="w-8 h-8 bg-blue-500 text-white text-xs font-medium rounded-full flex items-center justify-center mr-3">
                                                        {{ $rosterPlayer->jersey_number }}
                                                    </span>
                                                    <span class="text-sm font-medium text-gray-900">{{ $rosterPlayer->player->name }}</span>
                                                </div>
                                                <span class="text-xs text-gray-500">{{ $rosterPlayer->position }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <div>
                                    <h5 class="text-sm font-medium text-gray-600 mb-2">{{ __('Substitutes') }}</h5>
                                    <div class="grid grid-cols-1 gap-2">
                                        @foreach($awayTeamRoster->players->where('is_starter', false) as $rosterPlayer)
                                            <div class="flex items-center justify-between p-2 bg-gray-50 rounded">
                                                <div class="flex items-center">
                                                    <span class="w-8 h-8 bg-green-500 text-white text-xs font-medium rounded-full flex items-center justify-center mr-3">
                                                        {{ $rosterPlayer->jersey_number }}
                                                    </span>
                                                    <span class="text-sm font-medium text-gray-900">{{ $rosterPlayer->player->name }}</span>
                                                </div>
                                                <span class="text-xs text-gray-500">{{ $rosterPlayer->position }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @else
                            <p class="text-gray-500 text-sm">{{ __('No lineup submitted yet.') }}</p>
                        @endif

                        <!-- Lineup Signature Status -->
                        <div class="mt-4 p-3 bg-gray-50 rounded">
                            @if($matchSheet->away_team_lineup_signature)
                                <div class="flex items-center text-green-600">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="text-sm font-medium">{{ __('Lineup Signed') }}</span>
                                </div>
                                <div class="text-xs text-gray-500 mt-1">{{ $matchSheet->away_team_lineup_signed_at->format('M j, Y g:i A') }}</div>
                            @else
                                <div class="flex items-center text-yellow-600">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="text-sm font-medium">{{ __('Lineup Not Signed') }}</span>
                                </div>
                            @endif
                        </div>

                        <!-- Sign Lineup Button -->
                        @if($canSignLineup && $matchSheet->canSignLineup(Auth::user(), 'away'))
                        <form method="POST" action="{{ route('competition-management.matches.match-sheet.sign-lineup', $match) }}" class="mt-4">
                            @csrf
                            <input type="hidden" name="team_type" value="away">
                            <input type="hidden" name="signature" value="{{ Auth::user()->name . ' - ' . now()->format('Y-m-d H:i:s') }}">
                            <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                {{ __('Sign Lineup') }}
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Match Events Section (Stage 2) -->
        @if($matchSheet->isBeforeGameSigned() || $matchSheet->isAfterGameSigned() || $matchSheet->isFaValidated())
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6 bg-white border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Match Events') }}</h3>
                
                @if($events->count() > 0)
                    <div class="space-y-4">
                        @foreach($events as $event)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded">
                                <div class="flex items-center">
                                    <span class="w-8 h-8 bg-blue-500 text-white text-xs font-medium rounded-full flex items-center justify-center mr-3">
                                        {{ $event->minute }}'
                                    </span>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $event->player->name }}</div>
                                        <div class="text-xs text-gray-500">{{ ucfirst(str_replace('_', ' ', $event->type)) }}</div>
                                    </div>
                                </div>
                                <div class="text-xs text-gray-500">{{ $event->team->club->name ?? $event->team->name }}</div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-sm">{{ __('No match events recorded yet.') }}</p>
                @endif
            </div>
        </div>
        @endif

        <!-- Detailed Goals Section -->
        @if($matchSheet->isBeforeGameSigned() || $matchSheet->isAfterGameSigned() || $matchSheet->isFaValidated())
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6 bg-white border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Match Goals') }}</h3>
                
                @php
                    $goalEvents = $events->filter(function($event) {
                        return $event->isGoalEvent();
                    })->sortBy('minute');
                @endphp
                
                @if($goalEvents->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Time') }}
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Team') }}
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Scorer') }}
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Assist') }}
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Type') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($goalEvents as $goal)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    {{ $goal->display_time }}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $goal->team->club->name ?? $goal->team->name }}
                                                </div>
                                                @if($goal->team_id == $match->home_team_id)
                                                    <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                        {{ __('Home') }}
                                                    </span>
                                                @else
                                                    <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                                        {{ __('Away') }}
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $goal->player->name ?? 'Unknown Player' }}
                                                </div>
                                                @if($goal->player)
                                                    <div class="text-xs text-gray-500 ml-2">
                                                        #{{ $goal->player->jersey_number ?? 'N/A' }}
                                                    </div>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($goal->assistedByPlayer)
                                                <div class="flex items-center">
                                                    <div class="text-sm text-gray-900">
                                                        {{ $goal->assistedByPlayer->name }}
                                                    </div>
                                                    <div class="text-xs text-gray-500 ml-2">
                                                        #{{ $goal->assistedByPlayer->jersey_number ?? 'N/A' }}
                                                    </div>
                                                </div>
                                            @else
                                                <span class="text-sm text-gray-400">{{ __('No assist') }}</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                @if($goal->event_type === 'penalty_goal') bg-purple-100 text-purple-800
                                                @elseif($goal->event_type === 'own_goal') bg-red-100 text-red-800
                                                @elseif($goal->event_type === 'free_kick_goal') bg-yellow-100 text-yellow-800
                                                @elseif($goal->event_type === 'header_goal') bg-indigo-100 text-indigo-800
                                                @elseif($goal->event_type === 'volley_goal') bg-pink-100 text-pink-800
                                                @elseif($goal->event_type === 'long_range_goal') bg-orange-100 text-orange-800
                                                @else bg-green-100 text-green-800
                                                @endif">
                                                {{ $goal->event_type_label }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Goals Summary -->
                    <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <div class="text-sm font-medium text-blue-800">{{ __('Home Team Goals') }}</div>
                            <div class="text-2xl font-bold text-blue-600">
                                {{ $goalEvents->where('team_id', $match->home_team_id)->count() }}
                            </div>
                        </div>
                        <div class="bg-red-50 p-4 rounded-lg">
                            <div class="text-sm font-medium text-red-800">{{ __('Away Team Goals') }}</div>
                            <div class="text-2xl font-bold text-red-600">
                                {{ $goalEvents->where('team_id', $match->away_team_id)->count() }}
                            </div>
                        </div>
                        <div class="bg-green-50 p-4 rounded-lg">
                            <div class="text-sm font-medium text-green-800">{{ __('Total Goals') }}</div>
                            <div class="text-2xl font-bold text-green-600">
                                {{ $goalEvents->count() }}
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">{{ __('No Goals Scored') }}</h3>
                        <p class="mt-1 text-sm text-gray-500">{{ __('No goals have been recorded for this match yet.') }}</p>
                    </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Add Event Form Section -->
        @if($matchSheet->canEdit(Auth::user()))
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6 bg-white border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Add Match Event') }}</h3>
                
                <form method="POST" action="{{ route('competition-management.matches.match-sheet.add-event', $match) }}" class="space-y-6">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Event Type -->
                        <div>
                            <label for="event_type" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Event Type') }}</label>
                            <select id="event_type" name="event_type" required class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="">{{ __('Select Event Type') }}</option>
                                <optgroup label="{{ __('Goals') }}">
                                    <option value="goal">{{ __('Goal') }}</option>
                                    <option value="penalty_goal">{{ __('Penalty Goal') }}</option>
                                    <option value="free_kick_goal">{{ __('Free Kick Goal') }}</option>
                                    <option value="header_goal">{{ __('Header Goal') }}</option>
                                    <option value="volley_goal">{{ __('Volley Goal') }}</option>
                                    <option value="long_range_goal">{{ __('Long Range Goal') }}</option>
                                    <option value="own_goal">{{ __('Own Goal') }}</option>
                                </optgroup>
                                <optgroup label="{{ __('Cards') }}">
                                    <option value="yellow_card">{{ __('Yellow Card') }}</option>
                                    <option value="red_card">{{ __('Red Card') }}</option>
                                </optgroup>
                                <optgroup label="{{ __('Substitutions') }}">
                                    <option value="substitution_in">{{ __('Substitution In') }}</option>
                                    <option value="substitution_out">{{ __('Substitution Out') }}</option>
                                </optgroup>
                                <optgroup label="{{ __('Other') }}">
                                    <option value="injury">{{ __('Injury') }}</option>
                                    <option value="save">{{ __('Save') }}</option>
                                    <option value="missed_penalty">{{ __('Missed Penalty') }}</option>
                                    <option value="penalty_saved">{{ __('Penalty Saved') }}</option>
                                    <option value="var_decision">{{ __('VAR Decision') }}</option>
                                </optgroup>
                            </select>
                        </div>

                        <!-- Team -->
                        <div>
                            <label for="team_id" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Team') }}</label>
                            <select id="team_id" name="team_id" required class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="">{{ __('Select Team') }}</option>
                                <option value="{{ $match->home_team_id }}">{{ $match->homeTeam->club->name ?? $match->homeTeam->name }}</option>
                                <option value="{{ $match->away_team_id }}">{{ $match->awayTeam->club->name ?? $match->awayTeam->name }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Player -->
                        <div>
                            <label for="player_id" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Player') }}</label>
                            <select id="player_id" name="player_id" required class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="">{{ __('Select Player') }}</option>
                            </select>
                        </div>

                        <!-- Assist Player (for goals) -->
                        <div id="assist_section" class="hidden">
                            <label for="assisted_by_player_id" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Assist') }}</label>
                            <select id="assisted_by_player_id" name="assisted_by_player_id" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="">{{ __('No Assist') }}</option>
                            </select>
                        </div>

                        <!-- Substituted Player (for substitutions) -->
                        <div id="substitution_section" class="hidden">
                            <label for="substituted_player_id" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Substituted Player') }}</label>
                            <select id="substituted_player_id" name="substituted_player_id" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="">{{ __('Select Player') }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Minute -->
                        <div>
                            <label for="minute" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Minute') }}</label>
                            <input type="number" id="minute" name="minute" min="1" max="120" required class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <!-- Extra Time Minute -->
                        <div>
                            <label for="extra_time_minute" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Extra Time (Optional)') }}</label>
                            <input type="number" id="extra_time_minute" name="extra_time_minute" min="1" max="30" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <!-- Period -->
                        <div>
                            <label for="period" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Period') }}</label>
                            <select id="period" name="period" required class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="first_half">{{ __('First Half') }}</option>
                                <option value="second_half">{{ __('Second Half') }}</option>
                                <option value="extra_time_first">{{ __('Extra Time First Half') }}</option>
                                <option value="extra_time_second">{{ __('Extra Time Second Half') }}</option>
                                <option value="penalty_shootout">{{ __('Penalty Shootout') }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Location -->
                        <div>
                            <label for="location" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Location (Optional)') }}</label>
                            <input type="text" id="location" name="location" maxlength="100" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="{{ __('e.g., Penalty Area, Center Circle') }}">
                        </div>

                        <!-- Severity -->
                        <div>
                            <label for="severity" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Severity (Optional)') }}</label>
                            <select id="severity" name="severity" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="">{{ __('Select Severity') }}</option>
                                <option value="low">{{ __('Low') }}</option>
                                <option value="medium">{{ __('Medium') }}</option>
                                <option value="high">{{ __('High') }}</option>
                            </select>
                        </div>
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Description (Optional)') }}</label>
                        <textarea id="description" name="description" rows="3" maxlength="500" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="{{ __('Additional details about the event...') }}"></textarea>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            {{ __('Add Event') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @endif

        <!-- Referee Report and Match Details Section -->
        @if($matchSheet->match_status === 'completed' && $matchSheet->referee_report)
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6 bg-white border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Referee Report & Match Details') }}</h3>
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Referee Report -->
                    <div>
                        <h4 class="font-medium text-gray-700 mb-3">{{ __('Referee Report') }}</h4>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $matchSheet->referee_report }}</p>
                        </div>
                        @if($matchSheet->referee_signed_at)
                            <div class="text-xs text-gray-500 mt-2">
                                Signed by referee on {{ $matchSheet->referee_signed_at->format('M j, Y g:i A') }}
                            </div>
                        @endif
                    </div>

                    <!-- Match Statistics -->
                    <div>
                        <h4 class="font-medium text-gray-700 mb-3">{{ __('Match Statistics') }}</h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="text-center p-3 bg-blue-50 rounded">
                                <div class="text-lg font-bold text-blue-600">{{ $matchSheet->home_team_score ?? 0 }}</div>
                                <div class="text-xs text-gray-600">{{ $match->homeTeam->club->name ?? $match->homeTeam->name }}</div>
                            </div>
                            <div class="text-center p-3 bg-red-50 rounded">
                                <div class="text-lg font-bold text-red-600">{{ $matchSheet->away_team_score ?? 0 }}</div>
                                <div class="text-xs text-gray-600">{{ $match->awayTeam->club->name ?? $match->awayTeam->name }}</div>
                            </div>
                        </div>
                        
                        @if($matchSheet->match_statistics)
                            <div class="mt-4">
                                <h5 class="text-sm font-medium text-gray-600 mb-2">{{ __('Additional Statistics') }}</h5>
                                <div class="text-xs text-gray-500">
                                    @if(is_array($matchSheet->match_statistics))
                                        @foreach($matchSheet->match_statistics as $stat => $value)
                                            <div class="flex justify-between">
                                                <span>{{ ucfirst(str_replace('_', ' ', $stat)) }}:</span>
                                                <span>{{ is_array($value) ? json_encode($value) : $value }}</span>
                                            </div>
                                        @endforeach
                                    @elseif(is_string($matchSheet->match_statistics))
                                        {{ $matchSheet->match_statistics }}
                                    @elseif(!empty($matchSheet->match_statistics))
                                        {{ json_encode($matchSheet->match_statistics) }}
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                @if($matchSheet->notes)
                <div class="mt-6">
                    <h4 class="font-medium text-gray-700 mb-2">{{ __('Additional Notes') }}</h4>
                    <div class="bg-yellow-50 p-3 rounded">
                        <p class="text-sm text-gray-700">{{ $matchSheet->notes }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Post-Match Signatures Section (Stage 3) -->
        @if($matchSheet->isAfterGameSigned() || $matchSheet->isFaValidated())
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6 bg-white border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Post-Match Signatures') }}</h3>
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Home Team Post-Match -->
                    <div>
                        <h4 class="font-medium text-gray-700 mb-4">{{ $match->homeTeam->club->name ?? $match->homeTeam->name }}</h4>
                        
                        @if($matchSheet->home_team_post_match_signature)
                            <div class="p-3 bg-green-50 border border-green-200 rounded">
                                <div class="flex items-center text-green-600 mb-2">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="text-sm font-medium">{{ __('Signed') }}</span>
                                </div>
                                <div class="text-xs text-gray-500 mb-2">{{ $matchSheet->home_team_post_match_signed_at->format('M j, Y g:i A') }}</div>
                                @if($matchSheet->home_team_post_match_comments)
                                    <div class="text-sm text-gray-700">
                                        <strong>{{ __('Comments') }}:</strong> {{ $matchSheet->home_team_post_match_comments }}
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="p-3 bg-yellow-50 border border-yellow-200 rounded">
                                <div class="flex items-center text-yellow-600">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="text-sm font-medium">{{ __('Not Signed') }}</span>
                                </div>
                            </div>
                        @endif

                        <!-- Sign Post-Match Button -->
                        @if($canSignPostMatch && $matchSheet->canSignPostMatch(Auth::user(), 'home'))
                        <form method="POST" action="{{ route('competition-management.matches.match-sheet.sign-post-match', $match) }}" class="mt-4">
                            @csrf
                            <input type="hidden" name="team_type" value="home">
                            <input type="hidden" name="signature" value="{{ Auth::user()->name . ' - ' . now()->format('Y-m-d H:i:s') }}">
                            <div class="mb-3">
                                <label for="home_comments" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Comments (Optional)') }}</label>
                                <textarea name="comments" id="home_comments" rows="3" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"></textarea>
                            </div>
                            <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                {{ __('Sign Post-Match') }}
                            </button>
                        </form>
                        @endif
                    </div>

                    <!-- Away Team Post-Match -->
                    <div>
                        <h4 class="font-medium text-gray-700 mb-4">{{ $match->awayTeam->club->name ?? $match->awayTeam->name }}</h4>
                        
                        @if($matchSheet->away_team_post_match_signature)
                            <div class="p-3 bg-green-50 border border-green-200 rounded">
                                <div class="flex items-center text-green-600 mb-2">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="text-sm font-medium">{{ __('Signed') }}</span>
                                </div>
                                <div class="text-xs text-gray-500 mb-2">{{ $matchSheet->away_team_post_match_signed_at->format('M j, Y g:i A') }}</div>
                                @if($matchSheet->away_team_post_match_comments)
                                    <div class="text-sm text-gray-700">
                                        <strong>{{ __('Comments') }}:</strong> {{ $matchSheet->away_team_post_match_comments }}
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="p-3 bg-yellow-50 border border-yellow-200 rounded">
                                <div class="flex items-center text-yellow-600">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="text-sm font-medium">{{ __('Not Signed') }}</span>
                                </div>
                            </div>
                        @endif

                        <!-- Sign Post-Match Button -->
                        @if($canSignPostMatch && $matchSheet->canSignPostMatch(Auth::user(), 'away'))
                        <form method="POST" action="{{ route('competition-management.matches.match-sheet.sign-post-match', $match) }}" class="mt-4">
                            @csrf
                            <input type="hidden" name="team_type" value="away">
                            <input type="hidden" name="signature" value="{{ Auth::user()->name . ' - ' . now()->format('Y-m-d H:i:s') }}">
                            <div class="mb-3">
                                <label for="away_comments" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Comments (Optional)') }}</label>
                                <textarea name="comments" id="away_comments" rows="3" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"></textarea>
                            </div>
                            <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                {{ __('Sign Post-Match') }}
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- FA Validation Section (Stage 4) -->
        @if($canValidate && $matchSheet->isAfterGameSigned())
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6 bg-white border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('FA Validation') }}</h3>
                
                <form method="POST" action="{{ route('competition-management.matches.match-sheet.fa-validate', $match) }}" class="space-y-4">
                    @csrf
                    <div>
                        <label for="validation_notes" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Validation Notes (Optional)') }}</label>
                        <textarea name="validation_notes" id="validation_notes" rows="3" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"></textarea>
                    </div>
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        {{ __('Validate Match Sheet') }}
                    </button>
                </form>
            </div>
        </div>
        @endif

        <!-- FA Validation Status (Stage 4) -->
        @if($matchSheet->isFaValidated())
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6 bg-white border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('FA Validation Status') }}</h3>
                
                <div class="p-4 bg-green-50 border border-green-200 rounded">
                    <div class="flex items-center text-green-600 mb-2">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-lg font-medium">{{ __('Match Sheet Validated') }}</span>
                    </div>
                    <div class="text-sm text-gray-700 mb-2">
                        <strong>{{ __('Validated by') }}:</strong> {{ $matchSheet->faValidator->name ?? 'Unknown' }}
                    </div>
                    <div class="text-sm text-gray-700 mb-2">
                        <strong>{{ __('Validated on') }}:</strong> {{ $matchSheet->stage_fa_validated_at->format('M j, Y g:i A') }}
                    </div>
                    @if($matchSheet->fa_validation_notes)
                        <div class="text-sm text-gray-700">
                            <strong>{{ __('Notes') }}:</strong> {{ $matchSheet->fa_validation_notes }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
        @endif

        <!-- Export Button -->
        @if($matchSheet->isFaValidated())
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6 bg-white border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Export Match Sheet') }}</h3>
                
                <a href="{{ route('competition-management.matches.match-sheet.export', $match) }}" 
                   class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    {{ __('Export as JSON') }}
                </a>
            </div>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Get form elements
    const eventTypeSelect = document.getElementById('event_type');
    const teamSelect = document.getElementById('team_id');
    const playerSelect = document.getElementById('player_id');
    const assistSection = document.getElementById('assist_section');
    const assistSelect = document.getElementById('assisted_by_player_id');
    const substitutionSection = document.getElementById('substitution_section');
    const substitutedPlayerSelect = document.getElementById('substituted_player_id');

    // Player data for both teams
    const homeTeamPlayers = @json($match->homeTeam->players ?? []);
    const awayTeamPlayers = @json($match->awayTeam->players ?? []);

    // Function to populate player select
    function populatePlayerSelect(teamId) {
        const players = teamId == {{ $match->home_team_id }} ? homeTeamPlayers : awayTeamPlayers;
        
        // Clear existing options
        playerSelect.innerHTML = '<option value="">{{ __('Select Player') }}</option>';
        assistSelect.innerHTML = '<option value="">{{ __('No Assist') }}</option>';
        substitutedPlayerSelect.innerHTML = '<option value="">{{ __('Select Player') }}</option>';
        
        // Add player options
        players.forEach(player => {
            const option = document.createElement('option');
            option.value = player.id;
            option.textContent = `${player.name}${player.jersey_number ? ' (#' + player.jersey_number + ')' : ''}`;
            playerSelect.appendChild(option);
            
            // Clone for assist and substitution selects
            const assistOption = option.cloneNode(true);
            assistSelect.appendChild(assistOption);
            
            const subOption = option.cloneNode(true);
            substitutedPlayerSelect.appendChild(subOption);
        });
    }

    // Function to show/hide assist section based on event type
    function toggleAssistSection(eventType) {
        const isGoalEvent = ['goal', 'penalty_goal', 'free_kick_goal', 'header_goal', 'volley_goal', 'long_range_goal', 'own_goal'].includes(eventType);
        
        if (isGoalEvent) {
            assistSection.classList.remove('hidden');
        } else {
            assistSection.classList.add('hidden');
            assistSelect.value = '';
        }
    }

    // Function to show/hide substitution section based on event type
    function toggleSubstitutionSection(eventType) {
        const isSubstitutionEvent = ['substitution_in', 'substitution_out'].includes(eventType);
        
        if (isSubstitutionEvent) {
            substitutionSection.classList.remove('hidden');
        } else {
            substitutionSection.classList.add('hidden');
            substitutedPlayerSelect.value = '';
        }
    }

    // Event listeners
    if (eventTypeSelect) {
        eventTypeSelect.addEventListener('change', function() {
            toggleAssistSection(this.value);
            toggleSubstitutionSection(this.value);
        });
    }

    if (teamSelect) {
        teamSelect.addEventListener('change', function() {
            if (this.value) {
                populatePlayerSelect(this.value);
            } else {
                playerSelect.innerHTML = '<option value="">{{ __('Select Player') }}</option>';
                assistSelect.innerHTML = '<option value="">{{ __('No Assist') }}</option>';
                substitutedPlayerSelect.innerHTML = '<option value="">{{ __('Select Player') }}</option>';
            }
        });
    }

    // Initialize form state
    if (eventTypeSelect && eventTypeSelect.value) {
        toggleAssistSection(eventTypeSelect.value);
        toggleSubstitutionSection(eventTypeSelect.value);
    }
});
</script>
@endpush
@endsection 