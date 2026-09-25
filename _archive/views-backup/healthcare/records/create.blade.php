<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Healthcare Record</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen">
        <div class="bg-white shadow">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <div class="flex-shrink-0 flex items-center">
                            <h1 class="text-xl font-semibold">Create Healthcare Record</h1>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <a href="{{ route('healthcare.index') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Back to Healthcare
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <div class="px-4 py-6 sm:px-0">
                @if(session('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        {{ session('error') }}
                    </div>
                @endif

                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h2 class="text-lg font-semibold mb-6">Create New Healthcare Record</h2>
                        
                        <form action="{{ route('healthcare.records.store') }}" method="POST" class="space-y-6">
                            @csrf
                            
                            <!-- Player Selection -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="player_id" class="block text-sm font-medium text-gray-700 mb-2">Player *</label>
                                    <select name="player_id" id="player_id" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="">Select a player</option>
                                        @foreach($players as $player)
                                            <option value="{{ $player->id }}" {{ old('player_id') == $player->id ? 'selected' : '' }}>
                                                {{ $player->first_name }} {{ $player->last_name }} 
                                                @if($player->club)
                                                    - {{ $player->club->name }}
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('player_id')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="record_date" class="block text-sm font-medium text-gray-700 mb-2">Record Date</label>
                                    <input type="date" name="record_date" id="record_date" value="{{ old('record_date', date('Y-m-d')) }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    @error('record_date')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Status -->
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
                                <select name="status" id="status" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">Select status</option>
                                    <option value="healthy" {{ old('status') == 'healthy' ? 'selected' : '' }}>Healthy</option>
                                    <option value="injured" {{ old('status') == 'injured' ? 'selected' : '' }}>Injured</option>
                                    <option value="recovering" {{ old('status') == 'recovering' ? 'selected' : '' }}>Recovering</option>
                                    <option value="unfit" {{ old('status') == 'unfit' ? 'selected' : '' }}>Unfit</option>
                                </select>
                                @error('status')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Symptoms -->
                            <div>
                                <label for="symptoms" class="block text-sm font-medium text-gray-700 mb-2">Symptoms</label>
                                <textarea name="symptoms" id="symptoms" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Describe the symptoms...">{{ old('symptoms') }}</textarea>
                                @error('symptoms')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Diagnosis and Treatment -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="diagnosis" class="block text-sm font-medium text-gray-700 mb-2">Diagnosis</label>
                                    <textarea name="diagnosis" id="diagnosis" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Medical diagnosis...">{{ old('diagnosis') }}</textarea>
                                    @error('diagnosis')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="treatment_plan" class="block text-sm font-medium text-gray-700 mb-2">Treatment Plan</label>
                                    <textarea name="treatment_plan" id="treatment_plan" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Treatment plan...">{{ old('treatment_plan') }}</textarea>
                                    @error('treatment_plan')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Medications -->
                            <div>
                                <label for="medications" class="block text-sm font-medium text-gray-700 mb-2">Medications</label>
                                <textarea name="medications" id="medications" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="List of medications...">{{ old('medications') }}</textarea>
                                @error('medications')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Dates -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="next_checkup_date" class="block text-sm font-medium text-gray-700 mb-2">Next Checkup Date</label>
                                    <input type="date" name="next_checkup_date" id="next_checkup_date" value="{{ old('next_checkup_date') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    @error('next_checkup_date')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="fifa_connect_id" class="block text-sm font-medium text-gray-700 mb-2">FIFA Connect ID (Optional)</label>
                                    <input type="text" name="fifa_connect_id" id="fifa_connect_id" value="{{ old('fifa_connect_id') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="FIFA Connect ID">
                                    @error('fifa_connect_id')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Submit Buttons -->
                            <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                                <a href="{{ route('healthcare.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                    Cancel
                                </a>
                                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                    Create Health Record
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 