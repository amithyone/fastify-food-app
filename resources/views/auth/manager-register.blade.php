<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div>
                <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                    Register as Restaurant Manager
                </h2>
                <p class="mt-2 text-center text-sm text-gray-600">
                    Create your manager account to start managing restaurants
                </p>
            </div>

            <form class="mt-8 space-y-6" method="POST" action="{{ route('manager.register') }}">
                @csrf

                <!-- Personal Information -->
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Personal Information</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
                            <input id="name" name="name" type="text" required 
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-orange-500 focus:border-orange-500"
                                value="{{ old('name') }}">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                            <input id="email" name="email" type="email" required 
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-orange-500 focus:border-orange-500"
                                value="{{ old('email') }}">
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="phone_number" class="block text-sm font-medium text-gray-700">Phone Number</label>
                            <input id="phone_number" name="phone_number" type="tel" required 
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-orange-500 focus:border-orange-500"
                                value="{{ old('phone_number') }}">
                            @error('phone_number')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                            <input id="password" name="password" type="password" required 
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-orange-500 focus:border-orange-500">
                            @error('password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                            <input id="password_confirmation" name="password_confirmation" type="password" required 
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-orange-500 focus:border-orange-500">
                        </div>
                    </div>
                </div>

                <!-- Business Information -->
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Business Information</h3>
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

                <div>
                    <button type="submit" 
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                        Register as Manager
                    </button>
                </div>

                <div class="text-center">
                    <p class="text-sm text-gray-600">
                        Already have an account? 
                        <a href="{{ route('login') }}" class="font-medium text-orange-600 hover:text-orange-500">
                            Sign in here
                        </a>
                    </p>
                    <p class="text-sm text-gray-600 mt-2">
                        Want to register as a regular user? 
                        <a href="{{ route('register') }}" class="font-medium text-orange-600 hover:text-orange-500">
                            Register here
                        </a>
                    </p>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout> 