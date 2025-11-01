<x-auth-layout>
    <div class="min-w-full min-h-screen flex items-center">
        <div class="grow min-h-screen h-full w-full lg:w-1/2 p-3 bg-primary-50 hidden lg:flex-center">
           <!-- <img data-src="{{ asset('lms/frontend/assets/images/auth/auth-loti.svg') }}" alt="loti"> -->
        </div>
        <div class="grow min-h-screen h-full w-full lg:w-1/2 pt-32 pb-12 px-3 lg:p-3 flex-center flex-col">
            <!-- Succès -->
            <div class="text-center">
                <div class="mx-auto h-16 w-16 flex items-center justify-center rounded-full mb-4 bg-primary-100 dark:bg-primary-500/20">
                    <i class="fas fa-check text-2xl text-primary-600 dark:text-primary-300"></i>
                </div>
                <h2 class="area-title text-heading dark:text-white">{{ translate('Enrollment Successful!') }}</h2>
                <p class="area-description max-w-screen-sm mx-auto text-center mt-3">
                    {{ translate('Welcome to') }} <strong>{{ $enrollmentLink->organization->name }}</strong>
                </p>
            </div>

            <!-- Informations -->
            <div class="card dk-theme-card-square p-6 w-full max-w-md mt-8">
                <div class="space-y-4">
                    <div class="flex items-center">
                        <i class="fas fa-building mr-3 text-primary-600 dark:text-primary-300"></i>
                        <div>
                            <p class="text-sm font-medium text-heading dark:text-white/90">{{ translate('Organization') }}</p>
                            <p class="text-sm text-gray-600 dark:text-gray-300">{{ $enrollmentLink->organization->name }}</p>
                        </div>
                    </div>

                    @if($enrollmentLink->course)
                    <div class="flex items-center">
                        <i class="fas fa-book mr-3 text-success-600 dark:text-success-300"></i>
                        <div>
                            <p class="text-sm font-medium text-heading dark:text-white/90">{{ translate('Course') }}</p>
                            <p class="text-sm text-gray-600 dark:text-gray-300">{{ $enrollmentLink->course->title }}</p>
                        </div>
                    </div>
                    @endif

                    <div class="flex items-center">
                        <i class="fas fa-calendar mr-3 text-purple-600 dark:text-purple-300"></i>
                        <div>
                            <p class="text-sm font-medium text-heading dark:text-white/90">{{ translate('Enrollment Date') }}</p>
                            <p class="text-sm text-gray-600 dark:text-gray-300">{{ now()->format('d/m/Y à H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Instructions -->
            <div class="rounded-lg p-4 w-full max-w-md mt-6 bg-primary-50 dark:bg-primary-500/10 border border-primary-200 dark:border-primary-900">
                <h3 class="text-sm font-semibold mb-2 text-primary-700 dark:text-primary-300">
                    <i class="fas fa-info-circle mr-1"></i>
                    {{ translate('Next Steps') }}
                </h3>
                <ul class="text-sm text-primary-700 dark:text-primary-200 space-y-1">
                    <li>• {{ translate('Login with your existing credentials') }}</li>
                    <li>• {{ translate('Access your course from your dashboard') }}</li>
                    <li>• {{ translate('Start learning immediately') }}</li>
                </ul>
            </div>

            <!-- Actions -->
            <div class="space-y-4 w-full max-w-md mt-6">
                <a href="{{ route('login') }}" class="btn b-solid btn-primary-solid w-full h-12 flex items-center justify-center gap-2 block">
                    <i class="fas fa-sign-in-alt"></i>
                    {{ translate('Login') }}
                </a>

                <a href="{{ route('home.index') }}" class="btn b-light btn-primary-light w-full h-12 flex items-center justify-center gap-2 block mt-4">
                    <i class="fas fa-home"></i>
                    {{ translate('Back to Home') }}
                </a>
            </div>

            <!-- Contact -->
            <div class="text-center mt-6">
                <p class="text-xs text-heading/70 dark:text-white/70">
                    {{ translate('Need help? Contact') }} 
                    <strong>{{ $enrollmentLink->organization->name }}</strong>
                </p>
            </div>
        </div>
    </div>
</x-auth-layout>












