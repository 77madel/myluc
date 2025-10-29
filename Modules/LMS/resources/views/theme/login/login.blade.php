@php
    $settings = [
        'components' => [
            'inner-header-top' => '',
        ],
    ];
@endphp

<x-auth-layout class="home-online-education" :data="$settings">
    <div class="min-w-full min-h-screen flex items-center">
        <div class="grow min-h-screen h-full w-full lg:w-1/2 p-3 bg-primary-50 hidden lg:flex-center">
           <!-- <img data-src="{{ asset('lms/frontend/assets/images/auth/auth-loti.svg') }}" alt="loti"> -->
        </div>
        <div class="grow min-h-screen h-full w-full lg:w-1/2 pt-32 pb-12 px-3 lg:p-3 flex-center flex-col">
            <h2 class="area-title">{{ translate('Sign In') }}!</h2>
            <p class="area-description max-w-screen-sm mx-auto text-center mt-5">
                {{ translate('Discover, learn, and thrive with us. Experience a smooth and rewarding educational adventure. Let\'s get started') }}!
            </p>

            <div class="dashkit-tab flex-center gap-2 flex-wrap mt-10" id="userRegisterTab">
                <button type="button" aria-label="Login tab for Student"
                    class="dashkit-tab-btn login-credentials btn b-light btn-primary-light btn-lg h-11 !rounded-full text-[14px] sm:text-[16px] md:text-[18px] [&.active]:bg-primary [&.active]:text-white active"
                    id="asStudent" data-role="student">
                    {{ translate('Student') }}
                </button>
                <button type="button" aria-label="Login tab for Instructor"
                    class="dashkit-tab-btn login-credentials btn b-light btn-primary-light btn-lg h-11 !rounded-full text-[14px] sm:text-[16px] md:text-[18px] [&.active]:bg-primary [&.active]:text-white"
                    id="asInstructor" data-role="instructor">
                    {{ translate('Instructor') }}
                </button>
                <button type="button" aria-label="Login tab for Organization"
                    class="dashkit-tab-btn login-credentials btn b-light btn-primary-light btn-lg h-11 !rounded-full text-[14px] sm:text-[16px] md:text-[18px] [&.active]:bg-primary [&.active]:text-white"
                    id="asOrganization" data-role="organization">
                    {{ translate('Organization') }}
                </button>
                <button type="button" aria-label="Login tab for Admin"
                    class="dashkit-tab-btn btn b-light btn-primary-light btn-lg h-11 !rounded-full text-[14px] sm:text-[16px] md:text-[18px] [&.active]:bg-primary [&.active]:text-white"
                    id="admin" data-role="admin">
                    {{ translate('Admin') }}
                </button>
            </div>

            <div class="dashkit-tab-content w-full max-w-screen-sm *:hidden" id="userRegisterTabContent">
                <div class="dashkit-tab-pane !block" data-tab="asStudent">
                    <form action="{{ route('auth.login') }}" class="w-full max-w-screen-sm mt-10 form" method="POST">
                        @csrf
                        <input type="hidden" name="selected_role" value="student">
                        <div class="grid grid-cols-2 gap-x-3 gap-y-5">
                            <div class="col-span-full">
                                <div class="relative">
                                    <input type="email" name="email" id="role_email" class="form-input rounded-full peer" placeholder=""
                                        required />
                                    <label for="role_email" class="form-label floating-form-label"> {{ translate('Email') }} <span
                                            class="text-danger">*</span></label>
                                </div>
                                <span class="error-text email_err"></span>
                            </div>
                            <div class="col-span-full">
                                <div class="relative">
                                    <input type="password" name="password" id="role_password" class="form-input rounded-full peer"
                                        placeholder="" required />
                                    <label for="role_password" class="form-label floating-form-label">
                                        {{ translate('Password') }}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <!-- type toggler -->
                                    <label
                                        class="size-8 rounded-full cursor-pointer flex-center hover:bg-gray-200 focus:bg-gray-200 absolute top-1/2 -translate-y-1/2 right-2 rtl:right-auto rtl:left-2">
                                        <input type="checkbox" class="inputTypeToggle peer/it" hidden>
                                        <i
                                            class="ri-eye-off-line text-gray-500 dark:text-dark-text peer-checked/it:before:content-['\ecb5']"></i>
                                    </label>
                                </div>
                                <span class="error-text password_err"></span>
                            </div>
                            <div class="col-span-full">
                                <div class="flex-center-between px-4">
                                    <label class="flex items-center gap-2.5 cursor-pointer py-2.5 select-none">
                                        <input type="checkbox" name="remember_me" class="checkbox checkbox-primary rounded-sm">
                                        <span class="text-heading dark:text-white font-medium leading-none">
                                            {{ translate('Remember me') }}
                                        </span>
                                    </label>
                                    <div class="text-heading dark:text-white text-sm">
                                        <a href="{{ route('password.request') }}" class="text-primary underline">
                                            {{ translate('Forgot Password?') }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-span-full">
                                <button type="submit"
                                    class="btn b-solid btn-secondary-solid !text-heading dark:text-white btn-xl !rounded-full font-bold w-full h-12"
                                    aria-label="Login">
                                    {{ translate('Log in') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- JOIN AS INSTRUCTOR -->
                <div class="dashkit-tab-pane" data-tab="asInstructor">
                    <form action="{{ route('auth.login') }}" class="w-full max-w-screen-sm mt-10 form" method="POST">
                        @csrf
                        <input type="hidden" name="selected_role" value="instructor">
                        <div class="grid grid-cols-2 gap-x-3 gap-y-5">
                            <div class="col-span-full">
                                <div class="relative">
                                    <input type="email" name="email" id="instructor_email" class="form-input rounded-full peer" placeholder=""
                                        required />
                                    <label for="instructor_email" class="form-label floating-form-label"> {{ translate('Email') }} <span
                                            class="text-danger">*</span></label>
                                </div>
                                <span class="error-text email_err"></span>
                            </div>
                            <div class="col-span-full">
                                <div class="relative">
                                    <input type="password" name="password" id="instructor_password" class="form-input rounded-full peer"
                                        placeholder="" required />
                                    <label for="instructor_password" class="form-label floating-form-label">
                                        {{ translate('Password') }}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <!-- type toggler -->
                                    <label
                                        class="size-8 rounded-full cursor-pointer flex-center hover:bg-gray-200 focus:bg-gray-200 absolute top-1/2 -translate-y-1/2 right-2 rtl:right-auto rtl:left-2">
                                        <input type="checkbox" class="inputTypeToggle peer/it" hidden>
                                        <i
                                            class="ri-eye-off-line text-gray-500 dark:text-dark-text peer-checked/it:before:content-['\ecb5']"></i>
                                    </label>
                                </div>
                                <span class="error-text password_err"></span>
                            </div>
                            <div class="col-span-full">
                                <div class="flex-center-between px-4">
                                    <label class="flex items-center gap-2.5 cursor-pointer py-2.5 select-none">
                                        <input type="checkbox" name="remember_me" class="checkbox checkbox-primary rounded-sm">
                                        <span class="text-heading dark:text-white font-medium leading-none">
                                            {{ translate('Remember me') }}
                                        </span>
                                    </label>
                                    <div class="text-heading dark:text-white text-sm">
                                        <a href="{{ route('password.request') }}" class="text-primary underline">
                                            {{ translate('Forgot Password?') }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-span-full">
                                <button type="submit"
                                    class="btn b-solid btn-secondary-solid !text-heading dark:text-white btn-xl !rounded-full font-bold w-full h-12"
                                    aria-label="Login">
                                    {{ translate('Log in') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- JOIN AS ORGANIZATION -->
                <div class="dashkit-tab-pane" data-tab="asOrganization">
                    <form action="{{ route('auth.login') }}" class="w-full max-w-screen-sm mt-10 form" method="POST">
                        @csrf
                        <input type="hidden" name="selected_role" value="organization">
                        <div class="grid grid-cols-2 gap-x-3 gap-y-5">
                            <div class="col-span-full">
                                <div class="relative">
                                    <input type="email" name="email" id="organization_email" class="form-input rounded-full peer" placeholder=""
                                        required />
                                    <label for="organization_email" class="form-label floating-form-label"> {{ translate('Email') }} <span
                                            class="text-danger">*</span></label>
                                </div>
                                <span class="error-text email_err"></span>
                            </div>
                            <div class="col-span-full">
                                <div class="relative">
                                    <input type="password" name="password" id="organization_password" class="form-input rounded-full peer"
                                        placeholder="" required />
                                    <label for="organization_password" class="form-label floating-form-label">
                                        {{ translate('Password') }}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <!-- type toggler -->
                                    <label
                                        class="size-8 rounded-full cursor-pointer flex-center hover:bg-gray-200 focus:bg-gray-200 absolute top-1/2 -translate-y-1/2 right-2 rtl:right-auto rtl:left-2">
                                        <input type="checkbox" class="inputTypeToggle peer/it" hidden>
                                        <i
                                            class="ri-eye-off-line text-gray-500 dark:text-dark-text peer-checked/it:before:content-['\ecb5']"></i>
                                    </label>
                                </div>
                                <span class="error-text password_err"></span>
                            </div>
                            <div class="col-span-full">
                                <div class="flex-center-between px-4">
                                    <label class="flex items-center gap-2.5 cursor-pointer py-2.5 select-none">
                                        <input type="checkbox" name="remember_me" class="checkbox checkbox-primary rounded-sm">
                                        <span class="text-heading dark:text-white font-medium leading-none">
                                            {{ translate('Remember me') }}
                                        </span>
                                    </label>
                                    <div class="text-heading dark:text-white text-sm">
                                        <a href="{{ route('password.request') }}" class="text-primary underline">
                                            {{ translate('Forgot Password?') }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-span-full">
                                <button type="submit"
                                    class="btn b-solid btn-secondary-solid !text-heading dark:text-white btn-xl !rounded-full font-bold w-full h-12"
                                    aria-label="Login">
                                    {{ translate('Log in') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- JOIN AS ADMIN -->
                <div class="dashkit-tab-pane" data-tab="admin">
                    <form action="{{ route('admin.login') }}" class="w-full max-w-screen-sm mt-10 form" method="POST">
                        @csrf
                        <input type="hidden" name="selected_role" value="admin">
                        <div class="grid grid-cols-2 gap-x-3 gap-y-5">
                            <div class="col-span-full">
                                <div class="relative">
                                    <input type="email" name="email" id="admin_email"
                                        class="form-input rounded-full peer" placeholder="" />
                                    <label for="admin_email"
                                        class="form-label floating-form-label">{{ translate('Email') }} <span
                                            class="text-danger">*</span></label>
                                </div>
                                <span class="error-text content_err"></span>
                            </div>
                            <div class="col-span-full">
                                <div class="relative">
                                    <input type="password" name="password" id="admin_password"
                                        class="form-input rounded-full peer" placeholder="" />
                                    <label for="admin_password"
                                        class="form-label floating-form-label">{{ translate('Password') }} <span
                                            class="text-danger">*</span></label>
                                    <!-- type toggler -->
                                    <label
                                        class="size-8 rounded-full cursor-pointer flex-center hover:bg-gray-200 focus:bg-gray-200 absolute top-1/2 -translate-y-1/2 right-2 rtl:right-auto rtl:left-2">
                                        <input type="checkbox" class="inputTypeToggle peer/it" hidden>
                                        <i
                                            class="ri-eye-off-line text-gray-500 dark:text-dark-text peer-checked/it:before:content-['\ecb5']"></i>
                                    </label>
                                </div>
                                <span class="error-text content_err"></span>
                            </div>

                            <div class="col-span-full">
                                <button type="submit" aria-label="Login"
                                    class="btn b-solid btn-secondary-solid !text-heading dark:text-white btn-xl !rounded-full font-bold w-full h-12">
                                    {{ translate('Log in') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div
                class="flex-center w-full max-w-screen-sm py-6 h-max relative text-heading dark:text-white font-normal before:absolute inset-0 before:w-full before:h-px before:bg-border">
                <span class="relative z-10 px-5 bg-white text-sm">{{ translate('OR') }}</span>
            </div>
            <div class="text-heading">
                {{ translate('Don\'t have an account yet') }}?
                <a href="{{ route('register.page') }}" class="text-primary hover:underline"
                    aria-label="Sign up page">{{ translate('Sign up') }}</a>
            </div>
        </div>
    </div>
    @push('js')
        <script>
            var loginPortals = $('.login-credentials');

            loginPortals.each(function() {
                let loginPortal = $(this);

                if (loginPortal.hasClass('active')) {
                    let email = loginPortal.data('email');
                    let password = loginPortal.data('password');
                    $("#email").val(email);
                    $("#password").val(password);
                }
            });

            $(document).on('click', '.login-credentials', function() {
                let email = $(this).data('email');
                let password = $(this).data('password');

                $('.dashkit-tab-btn').removeClass('active');
                $(this).addClass('active');

                $("#role_email").val(email);
                $("#role_password").val(password);
            })
        </script>
    @endpush
</x-auth-layout>
