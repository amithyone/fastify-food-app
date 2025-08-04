<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Upgrade to Manager') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Upgrade Your Account</h3>
                        <p class="text-gray-600">Upgrade your account to become a restaurant manager and start creating restaurants.</p>
                    </div>

                    <form method="POST" action="{{ route('user.upgrade') }}" class="space-y-6">
                        @csrf

                        <!-- Business Information -->
                        <div class="bg-gray-50 p-6 rounded-lg">
                            <h4 class="text-md font-medium text-gray-900 mb-4">Business Information</h4>
                            <p class="text-sm text-gray-600 mb-4">Please provide your business registration details for verification.</p>
                            
                            <div class="space-y-4">
                                <div>
                                    <label for="business_name" class="block text-sm font-medium text-gray-700">Business Name</label>
                                    <input id="business_name" name="business_name" type="text" required 
                                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-orange-500 focus:border-orange-500"
                                        value="{{ old('business_name') }}">
                                    @error('business_name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="business_registration_number" class="block text-sm font-medium text-gray-700">Business Registration Number</label>
                                    <input id="business_registration_number" name="business_registration_number" type="text" required 
                                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-orange-500 focus:border-orange-500"
                                        value="{{ old('business_registration_number') }}">
                                    @error('business_registration_number')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="cac_number" class="block text-sm font-medium text-gray-700">CAC Number</label>
                                    <input id="cac_number" name="cac_number" type="text" required 
                                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-orange-500 focus:border-orange-500"
                                        value="{{ old('cac_number') }}">
                                    @error('cac_number')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="business_address" class="block text-sm font-medium text-gray-700">Business Address</label>
                                    <textarea id="business_address" name="business_address" rows="3" required 
                                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-orange-500 focus:border-orange-500">{{ old('business_address') }}</textarea>
                                    @error('business_address')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="business_phone" class="block text-sm font-medium text-gray-700">Business Phone</label>
                                    <input id="business_phone" name="business_phone" type="tel" required 
                                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-orange-500 focus:border-orange-500"
                                        value="{{ old('business_phone') }}">
                                    @error('business_phone')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Information Notice -->
                        <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-blue-800">Important Information</h3>
                                    <div class="mt-2 text-sm text-blue-700">
                                        <ul class="list-disc pl-5 space-y-1">
                                            <li>Your account will be upgraded to manager role</li>
                                            <li>Business verification is required before creating restaurants</li>
                                            <li>Verification process typically takes 1-3 business days</li>
                                            <li>You'll receive email notifications about your verification status</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-between">
                            <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                Cancel
                            </a>
                            <button type="submit" 
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                                Upgrade to Manager
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 