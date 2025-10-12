@php
    $class = $data['class'] ?? [];
    $btn = $data['btn'] ?? [];
@endphp

<form action="{{ route('auth.register') }}" class="mt-6 form">
    @csrf
    <input type="hidden" name="user_type" value="student">
    <div class="grid grid-cols-2 gap-x-3 gap-y-4">
        <div class="col-span-full lg:col-auto">
            <div class="relative">
                <input type="text" id="std_first_name" name="first_name"
                    class="{{ $class['input_class'] ?? 'form-input rounded-full peer' }}" placeholder="" />
                <label for="std_first_name" class="form-label floating-form-label">
                    {{ translate('First Name') }} <span class="text-danger"> *</span>
                </label>
            </div>
            <span class="text-danger error-text mt-1 d-block first_name_err"></span>
        </div>
        <div class="col-span-full lg:col-auto">
            <div class="relative">
                <input type="text" id="std_last_name" name="last_name"
                    class="{{ $class['input_class'] ?? 'form-input rounded-full peer' }}" placeholder="" />
                <label for="std_last_name" class="form-label floating-form-label">
                    {{ translate('Last Name') }} <span class="text-danger"> *</span>
                </label>
            </div>
            <span class="text-danger error-text mt-1 d-block last_name_err"></span>
        </div>
        <div class="col-span-full lg:col-auto">
            <div class="relative">
                <input type="email" id="std_email" name="email"
                    class="{{ $class['input_class'] ?? 'form-input rounded-full peer' }}" placeholder="" />
                <label for="std_email" class="form-label floating-form-label">
                    {{ translate('Email') }} <span class="text-danger"> *</span>
                </label>
            </div>
            <span class="text-danger error-text mt-1 d-block email_err"></span>
        </div>
        <div class="col-span-full lg:col-auto">
            <div class="relative">
                <input type="text" id="std_phone" name="phone"
                    class="{{ $class['input_class'] ?? 'form-input rounded-full peer' }}" placeholder="" />
                <label for="std_phone" class="form-label floating-form-label">
                    {{ translate('Phone') }} <span class="text-danger"> *</span>
                </label>
            </div>
            <span class="text-danger error-text mt-1 d-block phone_err"></span>
        </div>
        <div class="col-span-full lg:col-auto">
            <div class="relative">
                <input type="password" id="std_password" name="password"
                    class="{{ $class['input_class'] ?? 'form-input rounded-full peer' }}" placeholder="" />
                <label for="std_password" class="form-label floating-form-label">
                    {{ translate('Password') }} <span class="text-danger"> *</span>
                </label>
            </div>
            <span class="text-danger error-text mt-1 d-block password_err"></span>
        </div>
        <div class="col-span-full lg:col-auto">
            <div class="relative">
                <input type="password" id="std_password_confirmation" name="password_confirmation"
                    class="{{ $class['input_class'] ?? 'form-input rounded-full peer' }}" placeholder="" />
                <label for="std_password_confirmation" class="form-label floating-form-label">
                    {{ translate('Confirm Password') }} <span class="text-danger"> *</span>
                </label>
            </div>
            <span class="text-danger error-text mt-1 d-block password_confirmation_err"></span>
        </div>

        <!-- <div class="col-span-full">
            <div class="relative">
                <input type="text" id="designation" name="designation"
                    class="{{ $class['input_class'] ?? 'form-input rounded-full peer' }}" placeholder="" />
                <label for="designation" class="form-label floating-form-label">
                    {{ translate('Designation') }} <span class="text-danger"> *</span>
                </label>
            </div>
            <span class="text-danger error-text mt-1 d-block designation_err"></span>
        </div>
        <div class="col-span-full">
            <div class="relative">
                <textarea id="instructor-education" name="about" rows="5"
                    class="{{ $class['input_class'] ?? 'form-input peer' }} !rounded-2xl !h-auto" placeholder=""></textarea>
                <label for="instructor-education" class="form-label floating-form-label">
                    {{ translate('About') }}
                </label>
            </div>
        </div> -->
        <div class="col-span-full">
            <button type="submit"
                class=" {{ $class['btn_class'] ?? 'btn b-solid btn-secondary-solid !text-heading btn-xl !rounded-full font-bold w-full' }}"
                aria-label="Sign up">
                {{ translate($btn['text'] ?? 'Sign up') }}
                @if ($btn['is_show_icon'] ?? true)
                    <span class="hidden md:block">
                        <i class="ri-arrow-right-up-line text-[20px] rtl:before:content-['\ea66']"></i>
                    </span>
                @endif
            </button>
        </div>
    </div>
</form>
