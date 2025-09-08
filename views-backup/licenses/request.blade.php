@extends('layouts.app')

@section('title', 'Request License')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h2 class="text-2xl font-bold mb-6">Request Player License</h2>
                
                <form method="POST" action="/api/licenses/request" enctype="multipart/form-data" data-secure="true" data-validate-on-input="true">
                    @csrf
                    
                    <div class="mb-4">
                        <label for="player_id" class="block text-sm font-medium text-gray-700">Player</label>
                        <select name="player_id" id="player_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                            <option value="">Select a player</option>
                            @foreach($players ?? [] as $player)
                                <option value="{{ $player->id }}">{{ $player->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="mb-4">
                        <label for="license_type" class="block text-sm font-medium text-gray-700">License Type</label>
                        <select name="license_type" id="license_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                            <option value="">Select license type</option>
                            <option value="amateur">Amateur</option>
                            <option value="professional">Professional</option>
                            <option value="youth">Youth</option>
                            <option value="international">International</option>
                        </select>
                    </div>
                    
                    <div class="mb-4">
                        <label for="document" class="block text-sm font-medium text-gray-700">Document</label>
                        <input type="file" name="document" id="document" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" required>
                    </div>
                    
                    <div class="mb-4">
                        <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                        <textarea name="notes" id="notes" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                    </div>
                    
                    <div class="flex items-center justify-end">
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                            Submit Request
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 