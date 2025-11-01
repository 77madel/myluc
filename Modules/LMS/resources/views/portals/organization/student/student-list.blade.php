<x-dashboard-layout>
    <x-slot:title>{{ translate('Étudiants de l\'Organisation') }}</x-slot:title>

    <div class="card">
        <form method="get">
            <div class="grid grid-cols-4 gap-4">
                <div class="col-span-full md:col-span-2 lg:col-auto">
                    <input type="text" class="form-input" placeholder="{{ translate('Rechercher par nom') }}" name="name_search" autocomplete="off" value="{{ Request()->name_search ?? '' }}">
                </div>
                <div class="col-span-full md:col-span-2 lg:col-auto">
                    <select class="singleSelect" name="status">
                        @php
                            $status = Request()->status ?? '';
                        @endphp
                        <option value="all" {{ $status == 'all' ? 'selected' : '' }}>{{ translate('Tous') }}</option>
                        <option value="active" {{ $status == 'active' ? 'selected' : '' }}>{{ translate('Actif') }}</option>
                        <option value="inactive" {{ $status == 'inactive' ? 'selected' : '' }}>{{ translate('Inactif') }}</option>
                    </select>
                </div>
{{--
                <div class="col-span-full md:col-span-2 lg:col-auto">
                    <select class="singleSelect" name="verify">
                        @php
                            $verify = Request()->verify ?? '';
                        @endphp
                        <option value="all" {{ $verify == 'all' ? 'selected' : '' }}>{{ translate('Tous') }}</option>
                       --}}
{{-- <option value="verified" {{ $verify == 'verified' ? 'selected' : '' }}>{{ translate('Vérifié') }}</option>--}}{{--

                        <option value="unverified" {{ $verify == 'unverified' ? 'selected' : '' }}>{{ translate('Non Vérifié') }}</option>
                    </select>
                </div>
--}}
                <div class="col-span-full md:col-span-2 lg:col-auto">
                    <div class="flex items-end">
                        <button class="btn b-solid btn-info-solid mr-3">{{ translate('Filtrer') }}</button>
                        <a href="{{ route('organization.students.index') }}" class="btn b-solid btn-info-solid">
                            {{ translate('Actualiser') }}
                        </a>
                    </div>
                </div>
            </div>
        </form>
        </div>

    @if ($students->count() > 0)
        <div class="card">
            <div class="overflow-x-auto scrollbar-table">
                <table class="table-auto border-collapse w-full whitespace-nowrap text-left text-gray-500 dark:text-dark-text">
                <thead>
                        <tr class="text-primary-500">
                            <th class="px-3.5 py-4 bg-[#F2F4F9] dark:bg-dark-card-two first:rounded-l-lg last:rounded-r-lg first:dk-theme-card-square-left last:dk-theme-card-square-right">
                                {{ translate('Profil') }}
                            </th>
                            <th class="px-3.5 py-4 bg-[#F2F4F9] dark:bg-dark-card-two first:rounded-l-lg last:rounded-r-lg first:dk-theme-card-square-left last:dk-theme-card-square-right">
                                {{ translate('Email') }}
                            </th>
                            <th class="px-3.5 py-4 bg-[#F2F4F9] dark:bg-dark-card-two first:rounded-l-lg last:rounded-r-lg first:dk-theme-card-square-left last:dk-theme-card-square-right">
                                {{ translate('Téléphone') }}
                            </th>
                            <th class="px-3.5 py-4 bg-[#F2F4F9] dark:bg-dark-card-two first:rounded-l-lg last:rounded-r-lg first:dk-theme-card-square-left last:dk-theme-card-square-right">
                                {{ translate('Cours Inscrits') }}
                            </th>
                           {{-- <th class="px-3.5 py-4 bg-[#F2F4F9] dark:bg-dark-card-two first:rounded-l-lg last:rounded-r-lg first:dk-theme-card-square-left last:dk-theme-card-square-right">
                                {{ translate('Email Vérifié') }}
                            </th>--}}
                            <th class="px-3.5 py-4 bg-[#F2F4F9] dark:bg-dark-card-two first:rounded-l-lg last:rounded-r-lg first:dk-theme-card-square-left last:dk-theme-card-square-right">
                                {{ translate('Statut') }}
                            </th>
                            <th class="px-3.5 py-4 bg-[#F2F4F9] dark:bg-dark-card-two first:rounded-l-lg last:rounded-r-lg first:dk-theme-card-square-left last:dk-theme-card-square-right w-10">
                                {{ translate('Actions') }}
                            </th>
                    </tr>
                </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-dark-border-three">
                        @foreach ($students as $student)
                            @php
                                $userInfo = $student?->userable ?? null;
                                $userableTranslations = [];

                                if ($userInfo) {
                                    $userableTranslations = parse_translation($userInfo);
                                }

                                $firstName = $userableTranslations['first_name'] ?? $userInfo?->first_name ?? '';
                                $lastName = $userableTranslations['last_name'] ?? $userInfo?->last_name ?? '';

                                $profileImg = $userInfo && fileExists('lms/students', $userInfo?->profile_img) == true
                                    ? asset("storage/lms/students/{$userInfo?->profile_img}")
                                    : asset('lms/assets/images/placeholder/profile.jpg');
                            @endphp
                            <tr>
                                <td class="px-4 py-4">
                                    <div class="flex items-center gap-3.5">
                                        <a href="#" class="size-12 rounded-50 overflow-hidden dk-theme-card-square">
                                            <img src="{{ $profileImg }}" alt="student" class="size-full object-cover">
                                        </a>
                                        <div>
                                            <h6 class="leading-none text-heading dark:text-white font-semibold capitalize">
                                                <a href="#">
                                                    {{ $firstName . ' ' . $lastName }}
                                                </a>
                                            </h6>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-4">{{ $student?->email }}</td>
                                <td class="px-4 py-4">{{ $userInfo?->phone }}</td>
                                <td class="px-4 py-4">{{ $student?->enrollments?->count() }}</td>
                               {{-- <td class="px-4 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $student?->is_verify == 1 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $student?->is_verify == 1 ? translate('Vérifié') : translate('Non Vérifié') }}
                                    </span>
                                </td>--}}
                                <td class="px-4 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $userInfo && $userInfo?->status == 1 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $userInfo && $userInfo?->status == 1 ? translate('Actif') : translate('Inactif') }}
                                    </span>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="flex items-center gap-1">
                                        <a href="{{ route('organization.students.progress', $student->id) }}"
                                            class="btn-icon btn-primary-icon-light size-8">
                                            <i class="ri-eye-line text-inherit text-base"></i>
                                        </a>
                                        <a href="{{ route('organization.student.profile', $student->id) }}"
                                            class="btn-icon btn-primary-icon-light size-8">
                                            <i class="ri-user-line text-inherit text-base"></i>
                                        </a>
                                    </div>
                            </td>
                        </tr>
                        @endforeach
                </tbody>
            </table>
                <!-- Start Pagination -->
                {{ $students->links('portal::admin.pagination.paginate') }}
                <!-- End Pagination -->
        </div>
        </div>
    @else
        <x-portal::admin.empty-card title="Aucun Étudiant" />
    @endif
</x-dashboard-layout>
