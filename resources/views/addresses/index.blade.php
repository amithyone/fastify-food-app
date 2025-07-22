@extends('layouts.app')

@section('title', 'My Addresses - Abuja Eat')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-4xl">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">My Addresses</h1>
        <a href="{{ route('addresses.create') }}" class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-lg font-semibold transition-colors">
            <i class="fas fa-plus mr-2"></i>Add New Address
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if($addresses->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach($addresses as $address)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 border-l-4 {{ $address->is_default ? 'border-orange-500' : 'border-gray-300' }}">
                    <div class="flex justify-between items-start mb-4">
                        <div class="flex items-center gap-2">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                {{ $address->label ?: 'Address' }}
                            </h3>
                            @if($address->is_default)
                                <span class="bg-orange-100 text-orange-800 text-xs px-2 py-1 rounded-full font-medium">
                                    Default
                                </span>
                            @endif
                        </div>
                        <div class="flex gap-2">
                            <a href="{{ route('addresses.edit', $address) }}" class="text-blue-500 hover:text-blue-700">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('addresses.destroy', $address) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this address?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="space-y-2 text-gray-600 dark:text-gray-300">
                        <p class="font-medium">{{ $address->address_line_1 }}</p>
                        @if($address->address_line_2)
                            <p>{{ $address->address_line_2 }}</p>
                        @endif
                        <p>{{ $address->city }}, {{ $address->state }}</p>
                        @if($address->postal_code)
                            <p>{{ $address->postal_code }}</p>
                        @endif
                        <p>{{ $address->country }}</p>
                        @if($address->phone_number)
                            <p class="flex items-center gap-2">
                                <i class="fas fa-phone text-sm"></i>
                                {{ $address->phone_number }}
                            </p>
                        @endif
                        @if($address->additional_instructions)
                            <div class="mt-3 p-3 bg-gray-50 dark:bg-gray-700 rounded">
                                <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Delivery Instructions:</p>
                                <p class="text-sm">{{ $address->additional_instructions }}</p>
                            </div>
                        @endif
                    </div>

                    @if(!$address->is_default)
                        <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <form action="{{ route('addresses.set-default', $address) }}" method="POST" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="text-orange-500 hover:text-orange-700 text-sm font-medium">
                                    Set as Default
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-12">
            <div class="text-gray-400 dark:text-gray-500 mb-4">
                <i class="fas fa-map-marker-alt text-6xl"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No addresses yet</h3>
            <p class="text-gray-500 dark:text-gray-400 mb-6">Add your first address to make ordering easier!</p>
            <a href="{{ route('addresses.create') }}" class="bg-orange-500 hover:bg-orange-600 text-white px-6 py-3 rounded-lg font-semibold transition-colors">
                <i class="fas fa-plus mr-2"></i>Add Your First Address
            </a>
        </div>
    @endif
</div>
@endsection 