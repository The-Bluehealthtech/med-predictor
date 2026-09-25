@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h2 class="font-bold text-2xl text-gray-900 leading-tight mb-4">
                    Edit License for {{ $license->player->name }}
                    @if(isset($club))
                        <span class="text-lg text-gray-600 font-normal">({{ $club->name }})</span>
                    @else
                        <span class="text-lg text-gray-600 font-normal">({{ $license->club->name }})</span>
                    @endif
                </h2>
                <form method="POST" action="{{ route('club-management.licenses.update', $license) }}">
                    @csrf
                    @method('PUT')
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">License Type</label>
                        <select name="license_type" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full">
                            <option value="professional" {{ $license->license_type == 'professional' ? 'selected' : '' }}>Professional</option>
                            <option value="amateur" {{ $license->license_type == 'amateur' ? 'selected' : '' }}>Amateur</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Status</label>
                        <select name="status" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full">
                            <option value="active" {{ $license->status == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="pending" {{ $license->status == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="expired" {{ $license->status == 'expired' ? 'selected' : '' }}>Expired</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Issue Date</label>
                        <input type="date" name="issue_date" value="{{ $license->issue_date ? $license->issue_date->format('Y-m-d') : '' }}" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full">
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Expiry Date</label>
                        <input type="date" name="expiry_date" value="{{ $license->expiry_date ? $license->expiry_date->format('Y-m-d') : '' }}" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full">
                    </div>
                    <div class="flex items-center justify-between">
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Update License
                        </button>
                        <a href="{{ route('club-management.licenses.index') }}" class="text-gray-600 hover:text-gray-900">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 