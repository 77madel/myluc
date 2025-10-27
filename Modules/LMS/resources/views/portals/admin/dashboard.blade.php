<x-dashboard-layout>
    <x-slot:title>{{ translate('Dashboard') }}</x-slot:title>
    <div class="grid grid-cols-12 gap-x-4">
        <!-- Start Intro -->
        <x-portal::admin.admin.intro name="{{ auth()->user()->name }}" courseLink="{{ route('course.create') }}" />
        <!-- End Intro -->

        <!-- Start Short Progress Card -->
        <x-portal::admin.admin.overview :data="$data" />
        <!-- End Short Progress Card -->

        <!-- Start Webinar Statistics -->
        <div class="col-span-full card">
            <div class="flex-center-between mb-6">
                <h6 class="card-title">{{ translate('Statistiques des Webinaires') }}</h6>
                <a href="{{ route('webinars.index') }}" class="btn b-solid btn-primary-solid btn-sm dk-theme-card-square">
                    {{ translate('Voir tous') }}
                </a>
            </div>
            <div class="grid grid-cols-12 gap-4 mb-4">
                <!-- Total Webinaires -->
                <div class="col-span-full sm:col-span-6 p-4 dk-border-one rounded-xl h-full dk-theme-card-square">
                    <div class="flex-center-between">
                        <h6 class="leading-none text-gray-500 dark:text-dark-text font-semibold">
                            {{ translate('Total Webinaires') }}
                        </h6>
                    </div>
                    <div class="pt-3 bg-[url('../../assets/images/card/pattern.png')] dark:bg-[url('../../assets/images/card/pattern-dark.png')] bg-no-repeat bg-100% flex gap-4 mt-3">
                        <div class="pb-8 shrink-0">
                            <div class="flex items-center gap-2 mb-3">
                                <div class="card-title text-2xl">
                                    <span class="counter-value" data-value="{{ $data['webinar_stats']['total'] }}">{{ translate('0') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Webinaires Publiés -->
                <div class="col-span-full sm:col-span-6 p-4 dk-border-one rounded-xl h-full dk-theme-card-square">
                    <div class="flex-center-between">
                        <h6 class="leading-none text-gray-500 dark:text-dark-text font-semibold">
                            {{ translate('Publiés') }}
                        </h6>
                    </div>
                    <div class="pt-3 bg-[url('../../assets/images/card/pattern.png')] dark:bg-[url('../../assets/images/card/pattern-dark.png')] bg-no-repeat bg-100% flex gap-4 mt-3">
                        <div class="pb-8 shrink-0">
                            <div class="flex items-center gap-2 mb-3">
                                <div class="card-title text-2xl">
                                    <span class="counter-value" data-value="{{ $data['webinar_stats']['published'] }}">{{ translate('0') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Brouillons -->
                <div class="col-span-full sm:col-span-6 p-4 dk-border-one rounded-xl h-full dk-theme-card-square">
                    <div class="flex-center-between">
                        <h6 class="leading-none text-gray-500 dark:text-dark-text font-semibold">
                            {{ translate('Brouillons') }}
                        </h6>
                    </div>
                    <div class="pt-3 bg-[url('../../assets/images/card/pattern.png')] dark:bg-[url('../../assets/images/card/pattern-dark.png')] bg-no-repeat bg-100% flex gap-4 mt-3">
                        <div class="pb-8 shrink-0">
                            <div class="flex items-center gap-2 mb-3">
                                <div class="card-title text-2xl">
                                    <span class="counter-value" data-value="{{ $data['webinar_stats']['drafts'] }}">{{ translate('0') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Participants -->
                <div class="col-span-full sm:col-span-6 p-4 dk-border-one rounded-xl h-full dk-theme-card-square">
                    <div class="flex-center-between">
                        <h6 class="leading-none text-gray-500 dark:text-dark-text font-semibold">
                            {{ translate('Participants') }}
                        </h6>
                    </div>
                    <div class="pt-3 bg-[url('../../assets/images/card/pattern.png')] dark:bg-[url('../../assets/images/card/pattern-dark.png')] bg-no-repeat bg-100% flex gap-4 mt-3">
                        <div class="pb-8 shrink-0">
                            <div class="flex items-center gap-2 mb-3">
                                <div class="card-title text-2xl">
                                    <span class="counter-value" data-value="{{ $data['webinar_stats']['participants'] }}">{{ translate('0') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Webinar Statistics -->

        <!-- Start Statistics instructor & student  -->
        <x-portal::admin.admin.chat-instructor-student />
        <!-- End Average Statistics  Rate Chart -->

        <!-- Start Trending Category -->
        <x-portal::admin.admin.trending-category :topCategories="$data['top_category_courses']" />
        <!-- End Trending Category -->

        <div class="col-span-full 2xl:col-span-4 card">
            <div class="flex-center-between mb-6">
                <h6 class="card-title"> {{ translate('Top performing courses') }} </h6>
                @if (count($data['top_courses']) > 0)
                    <a href="{{ route('course.index') }}"
                        class="btn b-solid btn-primary-solid btn-sm dk-theme-card-square">
                        {{ translate('See all') }}
                    </a>
                @endif
            </div>
            <!-- Start Top Performing Course -->
            <x-portal::admin.admin.top-course :topCourses="$data['top_courses']" />
            <!-- End Top Performing Course -->
        </div>

        <!-- Start Support -->
        <x-portal::admin.admin.support :supports="$data['latest_supports']" />
        <!-- End Support -->
    </div>

    @php
        $instructorMonth = [];
        $getInstructorByMonth = [];
        if (count($data['instructor_reports']) > 0) {
            foreach ($data['instructor_reports'] as $key => $value) {
                $getInstructorByMonth[] = $value->total;
                $instructorMonth[] = "$value->dayMonthYears";
            }
        }
        $registerDate = [];
        $getStudentByDate = [];
        if (count($data['student_reports']) > 0) {
            foreach ($data['student_reports'] as $key => $value) {
                $getStudentByDate[] = $value->total;
                $registerDate[] = "$value->dayMonthYears";
            }
        }
    @endphp
    <input type="hidden" id="instructorMonth" value="{{ json_encode($instructorMonth) }}">
    <input type="hidden" id="getInstructorByMonth" value="{{ json_encode($getInstructorByMonth) }}">
    <input type="hidden" id="studentDate" value="{{ json_encode($registerDate) }}">
    <input type="hidden" id="getStudentByDate" value="{{ json_encode($getStudentByDate) }}">
    @push('js')
        <script src="{{ asset('lms/assets/js/vendor/apexcharts.min.js') }}"></script>
        <script src="{{ edulab_asset('lms/assets/js/pages/dashboard-admin.js') }}"></script>
    @endpush
</x-dashboard-layout>
