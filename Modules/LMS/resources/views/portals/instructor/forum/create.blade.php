<x-dashboard-layout>
    <x-slot:title>{{ translate('Create Forum') }}</x-slot:title>
    <x-portal::admin.breadcrumb title="Create Forum" page-to="Forum" />

    <div class="card">
        <form method="post" class="form" action="{{ route('instructor.forum.store') }}">
            @csrf
            <div class="">
                <label for="forumTitle" class="form-label">{{ translate('Title') }} *</label>
                <input type="text" id="forumTitle" placeholder="{{ translate('Title') }}" name="title"
                    value="{{ old('title') }}" class="form-input">
                <span class="text-danger error-text title_err"></span>
            </div>
            <div class="mt-6">
                <label for="forumSlug" class="form-label">{{ translate('Slug') }}</label>
                <input type="text" id="forumSlug" placeholder="{{ translate('Slug') }}" name="slug"
                    value="{{ old('slug') }}" class="form-input">
                <span class="text-danger error-text slug_err"></span>
            </div>
            <div class="mt-6">
                <label for="forumDescription" class="form-label">{{ translate('Description') }}</label>
                <textarea class="summernote description" name="description">{{ old('description') }}</textarea>
                <span class="text-danger error-text description"></span>
            </div>
            <div class="mt-6">
                <label for="course_id" class="form-label">{{ translate('Associate with Course') }}</label>
                <select name="course_id" id="course_id" class="form-select">
                    <option value="">{{ translate('Select a Course') }}</option>
                    @foreach ($courses as $course)
                        <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>
                            {{ $course->title }}
                        </option>
                    @endforeach
                </select>
                <span class="text-danger error-text course_id_err"></span>
            </div>
            <button type="submit" class="btn b-solid btn-primary-solid px-5 cursor-pointer dk-theme-card-square mt-10">
                {{ translate('Submit') }}
            </button>
        </form>
    </div>
</x-dashboard-layout>
