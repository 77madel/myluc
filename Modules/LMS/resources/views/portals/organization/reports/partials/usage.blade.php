<div class="overflow-x-auto scrollbar-table">
    <table class="table-auto border-collapse w-full whitespace-nowrap text-left text-gray-500 dark:text-dark-text">
        <thead>
            <tr class="text-primary-500">
                <th class="px-3.5 py-4 bg-[#F2F4F9] dark:bg-dark-card-two first:rounded-l-lg last:rounded-r-lg first:dk-theme-card-square-left last:dk-theme-card-square-right">{{ translate('Date') }}</th>
                <th class="px-3.5 py-4 bg-[#F2F4F9] dark:bg-dark-card-two first:rounded-l-lg last:rounded-r-lg first:dk-theme-card-square-left last:dk-theme-card-square-right">{{ translate('Temps total (s)') }}</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-dark-border-three">
        @foreach(($series ?? []) as $p)
            <tr>
                <td class="px-4 py-4">{{ $p->d }}</td>
                <td class="px-4 py-4">{{ $p->seconds }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>


