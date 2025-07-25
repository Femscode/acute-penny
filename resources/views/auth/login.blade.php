<x-guest-layout>
    <div class="flex items-center justify-center w-full transition-opacity opacity-100 duration-750">
        <main class="flex max-w-[335px] w-full flex-col-reverse lg:max-w-4xl lg:flex-row">
            <!-- Login Form Section -->
            <div class="text-[13px] leading-[20px] flex-1 p-6 pb-12 lg:p-20 bg-white dark:bg-[#161615] dark:text-[#EDEDEC] shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] rounded-bl-lg rounded-br-lg lg:rounded-tl-lg lg:rounded-br-none">
                <!-- Header -->
                  <a href="/">
                    <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
                </a>
                <h1 class="mb-1 font-medium text-2xl text-brand-blue dark:text-brand-off-white">Welcome Back</h1>
                <p class="mb-6 text-[#706f6c] dark:text-[#A1A09A]">Sign in to continue your savings journey with Synco Save</p>

                <!-- Session Status -->
                <x-auth-session-status class="mb-6" :status="session('status')" />

                <form method="POST" action="{{ route('login') }}" class="space-y-6">
                    @csrf

                    <!-- Email Address -->
                    <div class="space-y-2">
                        <x-input-label for="email" :value="__('Email Address')" class="text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC]" />
                        <x-text-input 
                            id="email" 
                            class="block w-full px-4 py-3 text-sm border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-sm bg-[#FDFDFC] dark:bg-[#161615] text-[#1b1b18] dark:text-[#EDEDEC] focus:ring-2 focus:ring-brand-blue focus:border-brand-blue transition-all duration-200 placeholder-[#706f6c] dark:placeholder-[#A1A09A]" 
                            type="email" 
                            name="email" 
                            :value="old('email')" 
                            required 
                            autofocus 
                            autocomplete="username"
                            placeholder="Enter your email address" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <!-- Password -->
                    <div class="space-y-2">
                        <x-input-label for="password" :value="__('Password')" class="text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC]" />
                        <x-text-input 
                            id="password" 
                            class="block w-full px-4 py-3 text-sm border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-sm bg-[#FDFDFC] dark:bg-[#161615] text-[#1b1b18] dark:text-[#EDEDEC] focus:ring-2 focus:ring-brand-blue focus:border-brand-blue transition-all duration-200 placeholder-[#706f6c] dark:placeholder-[#A1A09A]"
                            type="password"
                            name="password"
                            required 
                            autocomplete="current-password"
                            placeholder="Enter your password" />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <!-- Login Steps -->

                    <!-- Action Buttons -->
                    <ul class="flex gap-3 text-sm leading-normal">
                        <li>
                            <x-primary-button class="inline-block bg-brand-blue hover:bg-brand-blue/90 dark:bg-brand-orange dark:hover:bg-brand-orange/90 px-5 py-1.5 rounded-sm border border-brand-blue dark:border-brand-orange text-white text-sm leading-normal transition-colors">
                                {{ __('Sign In') }}
                            </x-primary-button>
                        </li>
                    </ul>

                    <!-- Sign Up Link -->
                    <div class="mt-6 pt-4 border-t border-[#e3e3e0] dark:border-[#3E3E3A]">
                        <p class="text-sm text-[#706f6c] dark:text-[#A1A09A]">
                            Don't have an account yet? 
                            <a href="/register" class="font-medium text-brand-blue dark:text-brand-orange hover:underline transition-colors duration-200">
                                Create your Synco Save account
                            </a>
                        </p>
                    </div>

                    <!-- Forgot Password Link -->
                    @if (Route::has('password.request'))
                        <div class="text-center">
                            <a class="text-sm text-[#706f6c] dark:text-[#A1A09A] hover:text-brand-blue dark:hover:text-brand-orange transition-colors duration-200 hover:underline" 
                               href="{{ route('password.request') }}">
                                {{ __('Forgot your password?') }}
                            </a>
                        </div>
                    @endif
                </form>
            </div>

          
        </main>
    </div>
</x-guest-layout>