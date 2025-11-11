<x-guest-layout>
    <div class="min-vh-100 d-flex flex-column justify-content-center align-items-center bg-info" style="margin:0; padding:0; overflow:hidden;">
     {{--
        <!-- Logo -->
        <div class="text-center mb-4">
            <a href="/">
                <x-application-logo style="width: 100px; height: 100px;" class="text-secondary" />
            </a>
        </div> --}}

        <!-- Auth Card -->
        <div class="w-100 d-flex justify-content-center" style="max-width: 100%; padding: 20px;">
            <div class="shadow-sm rounded p-4 w-100" style="max-width: 400px; background-color: white;">
                <div class="text-center py-3">
                    <h3 style="font-family: 'Times New Roman', Times, serif; font-weight:bold; color:black">Forgot your password</h3>
                </div>

                <!-- Info Text -->
                <p class="text-muted mb-3" style="color: black">
                    {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
                </p>

                <!-- Session Status -->
                <x-auth-session-status class="mb-3" :status="session('status')" />

                <!-- Validation Errors -->
                <x-auth-validation-errors class="mb-3" :errors="$errors" />

                <!-- Form -->
                <form method="POST" action="{{ route('password.email') }}">
                    @csrf

                    <!-- Email Input -->
                    <div class="mb-3 text-start" style="color: black">
                        <x-label for="email" :value="__('Email')" />
                        <x-input id="email" class="form-control mt-1 w-100" type="email" name="email" :value="old('email')" required autofocus />
                    </div>

                    {{-- <!-- Button -->
                    <div class="d-flex justify-content-end">
                        <x-button>
                            {{ __('Email Password Reset Link') }}
                        </x-button>
                    </div> --}}
                </form>

            </div>
        </div>
    </div>
</x-guest-layout>
