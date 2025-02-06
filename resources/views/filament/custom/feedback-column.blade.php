@php
    $record = $getRecord();
@endphp

<div class="w-full px-3 text-sm">
    @if ($record->internal_notes)
        <table>
            <tr class='border-b border-nlrc-blue-200 dark:border-nlrc-blue-800'>
                <td class="text-slate-500 dark:text-slate-400">feedback:</td>
                <td class="ps-2 w-full">{{ $record->feedback }}</td>
            </tr>
            <tr>
                <td class="text-slate-500 dark:text-slate-400">notes:</td>
                <td class="ps-2">{{ $record->internal_notes }}</td>
            </tr>
        </table>
    @else
        <p>{{ $record->feedback }}</p>
    @endif
</div>