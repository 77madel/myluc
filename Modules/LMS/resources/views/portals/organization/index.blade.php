@php
    $user = authCheck()?->userable;
    $translations = parse_translation($user);
    $organization = Auth::user()->organization;

    $backendSetting = get_theme_option(key: 'backend_general') ?? null;
    $currency = $backendSetting['currency'] ?? 'USD-$';
    $currencySymbol = get_currency_symbol($currency);

    // Statistiques pour l'organisation
    $totalPurchasedCourses = $organization ? DB::table('purchase_details')
        ->where('organization_id', $organization->id)
        ->where('purchase_type', 'organization_course')
        ->count() : 0;

    $totalStudents = $organization ? \Modules\LMS\Models\User::where('organization_id', $organization->id)
        ->where('userable_type', 'Modules\LMS\Models\Auth\Student')
        ->count() : 0;
    $totalEnrollmentLinks = $organization ? $organization->enrollmentLinks()->count() : 0;

    // Récupérer les cours achetés avec leurs détails
    $purchasedCourses = $organization ? DB::table('purchase_details')
        ->join('purchases', 'purchase_details.purchase_id', '=', 'purchases.id')
        ->join('courses', 'purchase_details.course_id', '=', 'courses.id')
        ->where('purchase_details.organization_id', $organization->id)
        ->where('purchase_details.purchase_type', 'organization_course')
        ->select('purchases.*', 'courses.title as course_title', 'courses.thumbnail', 'courses.id as course_id', 'purchase_details.price')
        ->latest('purchases.created_at')
        ->take(5)
        ->get() : collect();

    $recentEnrollmentLinks = $organization ? $organization->enrollmentLinks()->with('course')->latest()->take(5)->get() : collect();
@endphp

<x-dashboard-layout>
    <x-slot:title> {{ translate('dashboard') }} </x-slot:title>


    <div class="grid grid-cols-12 gap-x-4">
        <!-- Start Instructor Profile -->
        <div class="col-span-full lg:col-span-4 card p-0">
            <div class="rounded-15 overflow-hidden dk-theme-card-square">
                <x-portal::admin.show-card-profile-img />
                <div class="p-7 mt-6 text-center">
                    <h4 class="text-[22px] leading-none text-heading dark:text-white font-semibold">
                        {{ $translations['name'] ?? $user?->name }}
                    </h4>
                    <div
                        class="flex-center gap-3.5 font-spline_sans mt-5 border-b border-gray-200 dark:border-dark-border pb-4 flex-wrap">
                        <div class="flex items-center gap-1.5">
                            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="13" viewBox="0 0 15 13"
                                fill="none">
                                <path
                                    d="M13.6111 0H1.38889C1.02053 0 0.667263 0.146329 0.406796 0.406796C0.146329 0.667263 0 1.02053 0 1.38889V8.61111C0 8.97947 0.146329 9.33274 0.406796 9.5932C0.667263 9.85367 1.02053 10 1.38889 10H13.6111C13.9795 10 14.3327 9.85367 14.5932 9.5932C14.8537 9.33274 15 8.97947 15 8.61111V1.38889C15 1.02053 14.8537 0.667263 14.5932 0.406796C14.3327 0.146329 13.9795 0 13.6111 0ZM13.3333 8.33333H1.66667V1.66667H13.3333V8.33333ZM15 11.9444C15 12.1655 14.9122 12.3774 14.7559 12.5337C14.5996 12.69 14.3877 12.7778 14.1667 12.7778H0.833333C0.61232 12.7778 0.400358 12.69 0.244078 12.5337C0.0877974 12.3774 0 12.1655 0 11.9444C0 11.7234 0.0877974 11.5115 0.244078 11.3552C0.400358 11.1989 0.61232 11.1111 0.833333 11.1111H14.1667C14.3877 11.1111 14.5996 11.1989 14.7559 11.3552C14.9122 11.5115 15 11.7234 15 11.9444ZM5.83333 6.38889V3.61111C5.83328 3.46233 5.87306 3.31625 5.94855 3.18805C6.02403 3.05984 6.13246 2.95417 6.26257 2.88203C6.39269 2.80989 6.53974 2.77389 6.68847 2.77778C6.8372 2.78168 6.98217 2.82531 7.10833 2.90417L9.33056 4.29306C9.45053 4.36794 9.54948 4.47212 9.61808 4.5958C9.68668 4.71947 9.72267 4.85857 9.72267 5C9.72267 5.14143 9.68668 5.28053 9.61808 5.4042C9.54948 5.52788 9.45053 5.63206 9.33056 5.70694L7.10833 7.09583C6.98217 7.17468 6.8372 7.21832 6.68847 7.22222C6.53974 7.22611 6.39269 7.19011 6.26257 7.11797C6.13246 7.04582 6.02403 6.94016 5.94855 6.81195C5.87306 6.68374 5.83328 6.53767 5.83333 6.38889Z"
                                    fill="#795DED" />
                            </svg>
                            <div class="text-sm leading-none text-gray-500 dark:text-dark-text">
                                {{ $totalPurchasedCourses }} {{ translate('Cours Achetés') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Instructor Profile -->

        <!-- Start Instructor Earning Overview -->


        <div class="col-span-full lg:col-span-8 card">
            <div class="grid grid-cols-12 gap-4 mb-4">
                <!-- Statistiques pour l'organisation -->
                <div class="col-span-full sm:col-span-4 p-4 dk-border-one rounded-xl h-full dk-theme-card-square">
                    <div class="flex-center-between">
                        <h6 class="leading-none text-gray-500 dark:text-dark-text font-semibold">
                            {{ translate('Total Étudiants') }} </h6>
                        <div class="flex items-center justify-center w-10 h-10 bg-blue-100 dark:bg-blue-900 rounded-full">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-blue-600 dark:text-blue-400">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                            </svg>
                        </div>
                    </div>
                    <div
                        class="pt-3 bg-[url('../../assets/images/card/pattern.png')] dark:bg-[url('../../assets/images/card/pattern-dark.png')] bg-no-repeat bg-100% flex gap-4 mt-3">
                        <div class="pb-8 shrink-0">
                            <div class="flex items-center gap-2 mb-3">
                                <div class="card-title text-2xl">
                                    <span class="counter-value"
                                        data-value="{{ $totalStudents }}">{{ translate('0') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-span-full sm:col-span-4 p-4 dk-border-one rounded-xl h-full dk-theme-card-square">
                    <div class="flex-center-between">
                        <h6 class="leading-none text-gray-500 dark:text-dark-text font-semibold">
                            {{ translate('Cours Achetés') }} </h6>
                        <div class="flex items-center justify-center w-10 h-10 bg-green-100 dark:bg-green-900 rounded-full">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-green-600 dark:text-green-400">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                <polyline points="14,2 14,8 20,8"></polyline>
                                <line x1="16" y1="13" x2="8" y2="13"></line>
                                <line x1="16" y1="17" x2="8" y2="17"></line>
                                <polyline points="10,9 9,9 8,9"></polyline>
                            </svg>
                        </div>
                    </div>
                    <div
                        class="pt-3 bg-[url('../../assets/images/card/pattern.png')] dark:bg-[url('../../assets/images/card/pattern-dark.png')] bg-no-repeat bg-100% flex gap-4 mt-3">
                        <div class="pb-8 shrink-0">
                            <div class="flex items-center gap-2 mb-3">
                                <div class="card-title text-2xl">
                                    <span class="counter-value"
                                        data-value="{{ $totalPurchasedCourses }}">{{ translate('0') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-span-full sm:col-span-4 p-4 dk-border-one rounded-xl h-full dk-theme-card-square">
                    <div class="flex-center-between">
                        <h6 class="leading-none text-gray-500 dark:text-dark-text font-semibold">
                            {{ translate('Total Profit') }} </h6>
                    </div>
                    <div
                        class="pt-3 bg-[url('../../assets/images/card/pattern.png')] dark:bg-[url('../../assets/images/card/pattern-dark.png')] bg-no-repeat bg-100% flex gap-4 mt-3">
                        <div class="pb-8 shrink-0">
                            <div class="flex items-center gap-2 mb-3">
                                <div class="card-title text-2xl">
                                    {{ $currencySymbol }}<span class="counter-value"
                                        data-value="{{ $data['total_amount'] - $data['total_platform_fee'] }}">{{ translate('0') }}</span>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-span-full sm:col-span-4 p-4 dk-border-one rounded-xl h-full dk-theme-card-square">
                    <div class="flex-center-between">
                        <h6 class="leading-none text-gray-500 dark:text-dark-text font-semibold">
                            {{ translate('Liens d\'Inscription') }} </h6>
                        <div class="flex items-center justify-center w-10 h-10 bg-purple-100 dark:bg-purple-900 rounded-full">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-purple-600 dark:text-purple-400">
                                <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path>
                                <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path>
                            </svg>
                        </div>
                    </div>
                    <div
                        class="pt-3 bg-[url('../../assets/images/card/pattern.png')] dark:bg-[url('../../assets/images/card/pattern-dark.png')] bg-no-repeat bg-100% flex gap-4 mt-3">
                        <div class="pb-8 shrink-0">
                            <div class="flex items-center gap-2 mb-3">
                                <div class="card-title text-2xl">
                                    <span class="counter-value"
                                        data-value="{{ $totalEnrollmentLinks }}">{{ translate('0') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Liens d'inscription récents -->
        @if($recentEnrollmentLinks->count() > 0)
            <div class="col-span-full card">
                <div class="flex-center-between mb-6">
                    <h6 class="card-title">{{ translate('Liens d\'Inscription Récents') }}</h6>
                    <a href="{{ route('organization.enrollment-links.index') }}" class="btn b-solid btn-primary-solid btn-sm">
                        {{ translate('Voir Tous') }}
                    </a>
                </div>
                <div class="overflow-x-auto scrollbar-table">
                    <table class="table-auto border-collapse w-full whitespace-nowrap text-left text-gray-500 dark:text-dark-text">
                        <thead>
                            <tr class="text-primary-500">
                                <th class="px-3.5 py-4 bg-[#F2F4F9] dark:bg-dark-card-two first:rounded-l-lg last:rounded-r-lg first:dk-theme-card-square-left last:dk-theme-card-square-right">{{ translate('Nom') }}</th>
                                <th class="px-3.5 py-4 bg-[#F2F4F9] dark:bg-dark-card-two first:rounded-l-lg last:rounded-r-lg first:dk-theme-card-square-left last:dk-theme-card-square-right">{{ translate('Cours') }}</th>
                                <th class="px-3.5 py-4 bg-[#F2F4F9] dark:bg-dark-card-two first:rounded-l-lg last:rounded-r-lg first:dk-theme-card-square-left last:dk-theme-card-square-right">{{ translate('Lien') }}</th>
                                <th class="px-3.5 py-4 bg-[#F2F4F9] dark:bg-dark-card-two first:rounded-l-lg last:rounded-r-lg first:dk-theme-card-square-left last:dk-theme-card-square-right">{{ translate('Statut') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-dark-border-three">
                            @foreach($recentEnrollmentLinks as $link)
                                <tr>
                                    <td class="px-4 py-4">{{ $link->name }}</td>
                                    <td class="px-4 py-4">{{ $link->course->title ?? 'N/A' }}</td>
                                    <td class="px-4 py-4">
                                        <input type="text" value="{{ url('/enroll/' . $link->slug) }}" readonly
                                            class="form-input text-sm w-48  dark:bg-gray-700 border-none">
                                    </td>
                                    <td class="px-4 py-4">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $link->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ ucfirst($link->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <!-- Cours Achetés Récents -->
        @if($purchasedCourses->count() > 0)
            <div class="col-span-full card">
                <div class="flex-center-between mb-6">
                    <h6 class="card-title">{{ translate('Cours Achetés Récents') }}</h6>
                    <a href="{{ route('organization.courses.index') }}" class="btn b-solid btn-primary-solid btn-sm">
                        {{ translate('Acheter Plus') }}
                    </a>
                </div>
                <div class="overflow-x-auto scrollbar-table">
                    <table class="table-auto border-collapse w-full whitespace-nowrap text-left text-gray-500 dark:text-dark-text">
                        <thead>
                            <tr class="text-primary-500">
                                <th class="px-3.5 py-4 bg-[#F2F4F9] dark:bg-dark-card-two first:rounded-l-lg last:rounded-r-lg first:dk-theme-card-square-left last:dk-theme-card-square-right">{{ translate('Cours') }}</th>
                                <th class="px-3.5 py-4 bg-[#F2F4F9] dark:bg-dark-card-two first:rounded-l-lg last:rounded-r-lg first:dk-theme-card-square-left last:dk-theme-card-square-right">{{ translate('Prix') }}</th>
                                <th class="px-3.5 py-4 bg-[#F2F4F9] dark:bg-dark-card-two first:rounded-l-lg last:rounded-r-lg first:dk-theme-card-square-left last:dk-theme-card-square-right">{{ translate('Date d\'Achat') }}</th>
                                <th class="px-3.5 py-4 bg-[#F2F4F9] dark:bg-dark-card-two first:rounded-l-lg last:rounded-r-lg first:dk-theme-card-square-left last:dk-theme-card-square-right">{{ translate('Statut') }}</th>
                                <th class="px-3.5 py-4 bg-[#F2F4F9] dark:bg-dark-card-two first:rounded-l-lg last:rounded-r-lg first:dk-theme-card-square-left last:dk-theme-card-square-right">{{ translate('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-dark-border-three">
                            @foreach($purchasedCourses as $purchase)
                                <tr>
                                    <td class="px-4 py-4">
                                        <div class="flex items-center gap-3.5">
                                            <img src="{{ fileExists('lms/courses/thumbnails', $purchase->thumbnail) ? asset('storage/lms/courses/thumbnails/' . $purchase->thumbnail) : asset('lms/assets/images/placeholder/thumbnail612.jpg') }}"
                                                alt="{{ $purchase->course_title }}" class="w-10 h-10 rounded-md object-cover">
                                            <div>
                                                <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $purchase->course_title }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-900 dark:text-white">
                                        {{ $currencySymbol }}{{ number_format($purchase->price, 0) }}
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-500 dark:text-gray-400">
                                        {{ \Carbon\Carbon::parse($purchase->created_at)->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="px-4 py-4">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $purchase->status === 'success' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                            {{ ucfirst($purchase->status) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 text-sm font-medium">
                                        <a href="{{ route('organization.courses.show', $purchase->course_id) }}" class="text-primary-600 hover:text-primary-900 dark:text-primary-400">
                                            {{ translate('Voir') }}
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</x-dashboard-layout>
