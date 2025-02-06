<x-mail::message>
# Greetings!
<br /><br />

Hi {{ $trainee->first_name }} {{ $trainee->last_name }},
<br /><br />

This is to inform you that an announcement was made through [lumi.nlrc.ph](https://lumi.nlrc.ph).
<br /><br />

## {{ $announcement->title }}
Announced on {{ \Carbon\Carbon::parse($announcement->created_at)->format('F j, Y g:i:s A') }}

<br />
{!! Markdown::parse($announcement->description) !!}
<br />

---
Login to [lumi.nlrc.ph](https://lumi.nlrc.ph) to view the announcement.

**Do not reply** to this email as it is automatically generated and this address is not monitored. If you have any issues, kindly contact your recruitment agency.
</x-mail::message>
