<x-auth-layout>
    <div class="min-w-full min-h-screen flex items-center">
        <div class="grow min-h-screen h-full w-full lg:w-1/2 p-3 bg-primary-50 hidden lg:flex-center">
           <!-- <img data-src="{{ asset('lms/frontend/assets/images/auth/auth-loti.svg') }}" alt="loti"> -->
        </div>
        <div class="grow min-h-screen h-full w-full lg:w-1/2 pt-32 pb-12 px-3 lg:p-3 flex-center flex-col">
            <!-- Succès -->
            <div class="text-center">
                <div class="mx-auto h-16 w-16 flex items-center justify-center rounded-full bg-green-100 mb-4">
                    <i class="fas fa-check text-green-600 text-2xl"></i>
                </div>
                <h2 class="area-title">{{ translate('Enrollment Successful!') }}</h2>
                <p class="area-description max-w-screen-sm mx-auto text-center mt-5">
                    {{ translate('Welcome to') }} <strong>{{ $enrollmentLink->organization->name }}</strong>
                </p>
            </div>

            <!-- Informations -->
            <div class="bg-white shadow rounded-lg p-6 w-full max-w-md mt-8">
                <div class="space-y-4">
                    <div class="flex items-center">
                        <i class="fas fa-building text-blue-500 mr-3"></i>
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ translate('Organization') }}</p>
                            <p class="text-sm text-gray-600">{{ $enrollmentLink->organization->name }}</p>
                        </div>
                    </div>

                    @if($enrollmentLink->course)
                    <div class="flex items-center">
                        <i class="fas fa-book text-green-500 mr-3"></i>
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ translate('Course') }}</p>
                            <p class="text-sm text-gray-600">{{ $enrollmentLink->course->title }}</p>
                        </div>
                    </div>
                    @endif

                    <div class="flex items-center">
                        <i class="fas fa-calendar text-purple-500 mr-3"></i>
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ translate('Enrollment Date') }}</p>
                            <p class="text-sm text-gray-600">{{ now()->format('d/m/Y à H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Instructions -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 w-full max-w-md mt-6">
                <h3 class="text-sm font-medium text-blue-800 mb-2">
                    <i class="fas fa-info-circle mr-1"></i>
                    {{ translate('Next Steps') }}
                </h3>
                <ul class="text-sm text-blue-700 space-y-1">
                    <li>• {{ translate('Login with your existing credentials') }}</li>
                    <li>• {{ translate('Access your course from your dashboard') }}</li>
                    <li>• {{ translate('Start learning immediately') }}</li>
                </ul>
            </div>

            <!-- Actions -->
            <div class="space-y-3 w-full max-w-md mt-6">
                <a href="{{ route('login') }}" 
                   class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    <i class="fas fa-sign-in-alt mr-2"></i>
                    {{ translate('Login') }}
                </a>
                
                <a href="{{ route('home.index') }}" 
                   class="w-full flex justify-center py-3 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    <i class="fas fa-home mr-2"></i>
                    {{ translate('Back to Home') }}
                </a>
            </div>

            <!-- Contact -->
            <div class="text-center mt-6">
                <p class="text-xs text-gray-500">
                    {{ translate('Need help? Contact') }} 
                    <strong>{{ $enrollmentLink->organization->name }}</strong>
                </p>
            </div>
        </div>
    </div>
</x-auth-layout>
