<?php

namespace App\Models\Meetings;

use App\Enums\MeetingsOnCallsMeetingStatus;
use App\Models\Meetings\MeetingsOnCallsOptIn;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class MeetingsOnCall extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'meeting_link',
        'meeting_date',
        'meeting_status',
        'start_time',
        'end_time'
    ];

    protected $casts = ['meeting_status' => MeetingsOnCallsMeetingStatus::class];

    protected $appends = [
        'start_time_meeting_date',
        'start_time_hours_mins',
        'start_time_am_pm',
        'end_time_meeting_date',
        'end_time_hours_mins',
        'end_time_am_pm'
    ];

    public function getStartTimeMeetingDateAttribute() {
        return strtolower(Carbon::parse($this->start_time)->format('Y-m-d'));
    }

    public function getStartTimeHoursMinsAttribute() {
        return strtolower(Carbon::parse($this->start_time)->format('h:i:s'));
    }

    public function getStartTimeAmPmAttribute() {
        return strtolower(Carbon::parse($this->start_time)->format('A'));
    }

    public function getEndTimeMeetingDateAttribute() {
        return strtolower(Carbon::parse($this->end_time)->format('Y-m-d'));
    }

    public function getEndTimeHoursMinsAttribute() {
        return strtolower(Carbon::parse($this->end_time)->format('h:i:s'));
    }

    public function getEndTimeAmPmAttribute() {
        return strtolower(Carbon::parse($this->end_time)->format('A'));
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function meetings_on_calls_opt_ins(): HasMany
    {
        return $this->hasMany(MeetingsOnCallsOptIn::class, 'meetings_on_call_id');
    }

    // Custom model functions
    protected function parse_utc_timestamp_to_user_timezone($timestamp, $format, $user_timezone = false): string
    {
        $formatted_timestamp = Carbon::parse($timestamp, 'UTC');

        if (!$user_timezone) { // Used in instructor's dashboard
            $formatted_timestamp = $formatted_timestamp->setTimezone(Auth::user()->timezone);
        } else { // Used in admin panel
            $formatted_timestamp = $formatted_timestamp->setTimezone($user_timezone);
        }

        $formatted_timestamp = $formatted_timestamp->format($format);

        return $formatted_timestamp;
    }

    protected function parse_meeting_times_to_utc($am_or_pm, $time, $meeting_date)
    {
        $instructor = User::find(Auth::user()->id);

        $utc_time = Carbon::parse($meeting_date. ' ' .$time. ' ' .strtoupper($am_or_pm), $instructor->timezone)->setTimezone('UTC');

        return $utc_time;
    }

    protected function is_current_time_behind_instructor_current_time($am_or_pm, $time, $meeting_date, $instructor_timezone): bool
    {
        $parsed_time_for_instructor_timezone = Carbon::parse($meeting_date. ' ' .$time. ' ' .strtoupper($am_or_pm), $instructor_timezone);

        return $parsed_time_for_instructor_timezone < Carbon::now($instructor_timezone);
    }

    protected function get_times(): array
    {
        $times = [];

        for ($i = 1; $i <= 12; $i++) {
            $key = $i;
            if ($i < 10) {
                $key = '0' .$i;
            }
            
            $times[$key. ':00:00'] = $i. ':00';
            $times[$key. ':30:00'] = $i. ':30';
        }

        return $times;
    }
}
