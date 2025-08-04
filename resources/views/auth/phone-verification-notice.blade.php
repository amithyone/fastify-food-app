@extends('layouts.guest')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-[#f1ecdc] dark:bg-gray-900 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <div class="mx-auto h-12 w-12 flex items-center justify-center rounded-full bg-orange-100 dark:bg-orange-900">
                <i class="fas fa-mobile-alt text-orange-600 dark:text-orange-400 text-xl"></i>
            </div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900 dark:text-white">
                Verify Your Phone Number
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600 dark:text-gray-400">
                We've sent a verification code to your phone number
            </p>
        </div>

        <div class="bg-white dark:bg-gray-800 py-8 px-4 shadow sm:rounded-lg sm:px-10">
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 dark:bg-green-900 mb-4">
                    <i class="fas fa-check text-green-600 dark:text-green-400 text-xl"></i>
                </div>
                
                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-2">
                    Verification Code Sent
                </h3>
                
                <div class="mt-2 max-w-xl text-sm text-gray-500 dark:text-gray-400">
                    <p>
                        We've sent a 6-digit verification code to your phone number. 
                        Please check your SMS messages and enter the code to complete your registration.
                    </p>
                </div>

                <div class="mt-6">
                    <a href="{{ route('phone.verification.show') }}" 
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                        <i class="fas fa-key mr-2"></i>
                        Enter Verification Code
                    </a>
                </div>

                <div class="mt-4">
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Didn't receive the code? 
                        <a href="{{ route('phone.verification.show') }}" class="font-medium text-orange-600 hover:text-orange-500">
                            Resend code
                        </a>
                    </p>
                </div>
            </div>

            <div class="mt-6 border-t border-gray-200 dark:border-gray-700 pt-6">
                <div class="text-center">
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Having trouble? 
                        <a href="{{ route('contact') }}" class="font-medium text-orange-600 hover:text-orange-500">
                            Contact support
                        </a>
                    </p>
                </div>
            </div>
        </div>

        <!-- Additional Information -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-3">Important Information</h4>
            <ul class="text-sm text-gray-600 dark:text-gray-400 space-y-2">
                <li class="flex items-start">
                    <i class="fas fa-clock text-orange-500 mt-0.5 mr-2"></i>
                    <span>Verification codes expire after 10 minutes</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-shield-alt text-orange-500 mt-0.5 mr-2"></i>
                    <span>Never share your verification code with anyone</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-mobile-alt text-orange-500 mt-0.5 mr-2"></i>
                    <span>Make sure your phone number is correct</span>
                </li>
            </ul>
        </div>
    </div>
</div>
@endsection 