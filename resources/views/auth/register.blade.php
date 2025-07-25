<x-guest-layout>
    <div class="min-h-screen flex">
        <!-- Left Side - Branding & Info -->
        <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-brand-blue to-brand-orange relative overflow-hidden">
            <div class="absolute inset-0 bg-black bg-opacity-10"></div>
            <div class="relative z-10 flex flex-col justify-center px-12 text-white">
                  <a href="/">
                    <x-application-logo2 class="w-20 h-20 fill-current text-gray-500" />
                </a>
                <div class="mb-8">
                    <!-- <h1 class="text-4xl font-bold mb-4">Join Synco Save</h1> -->
                    <p class="text-xl text-brand-off-white mb-8">Start your savings journey with trusted group savings</p>
                </div>
                
                <div class="space-y-6">
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0 w-8 h-8 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                            <span class="text-sm font-semibold">1</span>
                        </div>
                        <div>
                            <h3 class="font-semibold text-lg">Create Your Account</h3>
                            <p class="text-brand-off-white">Quick and secure registration process</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0 w-8 h-8 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                            <span class="text-sm font-semibold">2</span>
                        </div>
                        <div>
                            <h3 class="font-semibold text-lg">Join or Create Groups</h3>
                            <p class="text-brand-off-white">Connect with trusted friends and family</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0 w-8 h-8 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                            <span class="text-sm font-semibold">3</span>
                        </div>
                        <div>
                            <h3 class="font-semibold text-lg">Start Saving Together</h3>
                            <p class="text-brand-off-white">Achieve your financial goals faster</p>
                        </div>
                    </div>
                </div>
                
                <div class="mt-12 p-6 bg-white bg-opacity-10 rounded-lg backdrop-blur-sm">
                    <div class="flex items-center space-x-3 mb-3">
                        <svg class="w-6 h-6 text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="font-semibold">Trusted Platform</span>
                    </div>
                    <p class="text-sm text-brand-off-white">Your financial data is protected with bank-level security and encryption.</p>
                </div>
            </div>
        </div>
        
        <!-- Right Side - Registration Form -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-8 bg-brand-off-white dark:bg-gray-900">
            <div class="w-full max-w-md">
                <!-- Mobile Logo -->
                <div class="lg:hidden text-center mb-8">
                    <x-application-logo class="w-16 h-16 mx-auto mb-4" />
                    <h2 class="text-2xl font-bold text-brand-blue dark:text-white">Join Synco Save</h2>
                    <p class="text-gray-600 dark:text-gray-400 mt-2">Create your account to get started</p>
                </div>
                
                <!-- Desktop Header -->
                <div class="hidden lg:block text-center mb-8">
                    <h2 class="text-3xl font-bold text-brand-blue dark:text-white mb-2">Create Account</h2>
                    <p class="text-gray-600 dark:text-gray-400">Join thousands of users saving together</p>
                </div>
                
                <form method="POST" action="{{ route('register') }}" class="space-y-6">
                    @csrf

                    <!-- Name -->
                    <div>
                        <x-input-label for="name" :value="__('Full Name')" class="text-gray-700 dark:text-gray-300 font-medium" />
                        <x-text-input id="name" 
                                    class="block mt-2 w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-2 focus:ring-brand-blue focus:border-brand-blue dark:bg-gray-800 dark:text-white transition-colors duration-200" 
                                    type="text" 
                                    name="name" 
                                    :value="old('name')" 
                                    required 
                                    autofocus 
                                    autocomplete="name" 
                                    placeholder="Enter your full name" />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <!-- Email Address -->
                    <div>
                        <x-input-label for="email" :value="__('Email Address')" class="text-gray-700 dark:text-gray-300 font-medium" />
                        <x-text-input id="email" 
                                    class="block mt-2 w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-2 focus:ring-brand-blue focus:border-brand-blue dark:bg-gray-800 dark:text-white transition-colors duration-200" 
                                    type="email" 
                                    name="email" 
                                    :value="old('email')" 
                                    required 
                                    autocomplete="username" 
                                    placeholder="Enter your email address" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="phone" :value="__('Phone Number')" class="text-gray-700 dark:text-gray-300 font-medium" />
                        <x-text-input id="phone" 
                                    class="block mt-2 w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-2 focus:ring-brand-blue focus:border-brand-blue dark:bg-gray-800 dark:text-white transition-colors duration-200" 
                                    type="number" 
                                    name="phone" 
                                    :value="old('phone')" 
                                    required 
                                    autocomplete="username" 
                                    placeholder="Enter your phone number" />
                        <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                    </div>

                    <!-- Password -->
                    <div>
                        <x-input-label for="password" :value="__('Password')" class="text-gray-700 dark:text-gray-300 font-medium" />
                        <x-text-input id="password" 
                                    class="block mt-2 w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-2 focus:ring-brand-blue focus:border-brand-blue dark:bg-gray-800 dark:text-white transition-colors duration-200"
                                    type="password"
                                    name="password"
                                    required 
                                    autocomplete="new-password" 
                                    placeholder="Create a strong password" />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <x-input-label for="password_confirmation" :value="__('Confirm Password')" class="text-gray-700 dark:text-gray-300 font-medium" />
                        <x-text-input id="password_confirmation" 
                                    class="block mt-2 w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-2 focus:ring-brand-blue focus:border-brand-blue dark:bg-gray-800 dark:text-white transition-colors duration-200"
                                    type="password"
                                    name="password_confirmation" 
                                    required 
                                    autocomplete="new-password" 
                                    placeholder="Confirm your password" />
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                    </div>

                    <!-- Terms Agreement -->
                    <div class="flex items-start space-x-3">
                        <input type="checkbox" 
                               id="terms" 
                               name="terms" 
                               required
                               class="mt-1 w-4 h-4 text-brand-blue bg-gray-100 border-gray-300 rounded focus:ring-brand-blue focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                        <label for="terms" class="text-sm text-gray-600 dark:text-gray-400">
                            I agree to the 
                            <a href="#" class="text-brand-blue hover:text-brand-orange underline">Terms of Service</a> 
                            and 
                            <a href="#" class="text-brand-blue hover:text-brand-orange underline">Privacy Policy</a>
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <div class="space-y-4">
                        <x-primary-button class="w-full justify-center py-3 text-lg font-semibold bg-brand-blue hover:bg-blue-700 focus:ring-brand-blue transition-colors duration-200">
                            {{ __('Create Account') }}
                        </x-primary-button>
                        
                        <div class="text-center">
                            <span class="text-gray-600 dark:text-gray-400">Already have an account?</span>
                            <a class="ml-2 text-brand-blue hover:text-brand-orange font-medium underline transition-colors duration-200" 
                               href="{{ route('login') }}">
                                {{ __('Sign in here') }}
                            </a>
                        </div>
                    </div>
                </form>
                
                <!-- Security Notice -->
                <div class="mt-8 p-4 bg-blue-50 dark:bg-gray-800 rounded-lg border border-blue-200 dark:border-gray-700">
                    <div class="flex items-start space-x-3">
                        <svg class="w-5 h-5 text-brand-blue mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        <div>
                            <h4 class="text-sm font-semibold text-brand-blue dark:text-blue-400">Secure Registration</h4>
                            <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">Your information is encrypted and protected with industry-standard security measures.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>