<div class="max-w-7xl sm:px-6 lg:px-8 dark:text-slate-200 {{ auth()->user()->hasRole('Trainee') ? 'px-4 py-6 mx-auto' : '' }}">
    @if (count($announcement_recipients) > 0)
        <div class="border-b border-nlrc-blue-200 dark:border-nlrc-blue-900">
            <p>List of trainees that received this announcement</p>

            <table class="w-full my-4">
                <thead class="bg-nlrc-blue-200 dark:bg-nlrc-blue-900">
                    <th class="px-2 py-4">Trainee</th>
                    <th class="px-2 py-4">Group</th>
                    <th class="px-2 py-4">Sent on</th>
                    <th class="px-2 py-4">Read on</th>
                </thead>

                <tbody class="bg-white dark:bg-nlrc-blue-800">
                    @foreach ($announcement_recipients as $recipient)
                        <tr>
                            <td class="p-2 text-center dark:text-slate-300">{{ $recipient->last_name }}, {{ $recipient->first_name }} {{ $recipient->middle_name }}</td>
                            <td class="p-2 text-center dark:text-slate-300">{{ $recipient->trainee_group }}</td>
                            <td class="p-2 text-center dark:text-slate-300">{{ \Carbon\Carbon::parse($recipient->created_at)->format('M j, Y h:i A') }}</td>
                            <td class="p-2 text-center dark:text-slate-300">{{ $recipient->read_at ? \Carbon\Carbon::parse($recipient->read_at)->format('M j, Y h:i A') : 'N/A' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{ $announcement_recipients->withQueryString()->links() }}
        </div>
    @else
        <p class="px-2 dark:text-white">This announcement has no recipients.</p>
    @endif
</div>
