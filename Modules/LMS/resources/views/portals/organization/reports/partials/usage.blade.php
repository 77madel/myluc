<div class="overflow-x-auto">
    <table class="table-auto w-full text-left">
        <thead>
            <tr class="border-b border-gray-200 dark:border-dark-border-four">
                <th class="py-3 px-2 text-sm text-gray-500">{{ translate('Date') }}</th>
                <th class="py-3 px-2 text-sm text-gray-500">{{ translate('Temps total (s)') }}</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-dark-border-four">
        @foreach(($series ?? []) as $p)
            <tr>
                <td class="py-3 px-2">{{ $p->d }}</td>
                <td class="py-3 px-2">{{ $p->seconds }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>


