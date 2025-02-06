<?php

namespace App\Models;

use App\Enums\TraineesEducation;
use App\Enums\TraineesMaritalStatus;
use App\Enums\TraineesWorkExperience;
use App\Models\Agencies\Agency;
use App\Models\Country;
use App\Models\Documents\AgencyDocumentRequired;
use App\Models\Documents\Document;
use App\Models\Documents\DocumentTraineeOverride;
use App\Models\Exams\Exam;
use App\Models\Exams\ExamAttempt;
use App\Models\Exams\ExamTrainee;
use App\Models\Exams\ProficiencyTrainee;
use App\Models\Flag\Flag;
use App\Models\Flag\FlagTrainee;
use App\Models\Grouping\Group;
use App\Models\Grouping\GroupTrainee;
use App\Models\Meetings\Meeting;
use App\Models\Meetings\MeetingTrainee;
use App\Models\TraineesVerifiedRequest;
use App\Models\Traits\IsActiveTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class Trainee extends Model
{
    use HasFactory, Notifiable;
    use IsActiveTrait;

    protected $fillable = [
        'user_id',
        'agency_id',
        'first_name',
        'middle_name',
        'last_name',
        'date_of_birth',
        'sex',
        'country_of_residence_id',
        'country_of_citizenship_id',
        'active',
        'date_deployment',
        'date_of_training_start',
        'address',
        'phone_number',
        'occupation',
        'field_of_work',
        'work_experience',
        'marital_status',
        'education',
        'other_email'
    ];

    protected $appends = ['full_name'];

    protected $casts = [
        'work_experience' => TraineesWorkExperience::class,
        'marital_status' => TraineesMaritalStatus::class,
        'education' => TraineesEducation::class,
        'data' => 'array', // notifications table
    ];

    protected static function booted()
    {
        // This removes all active labels from group_trainee table if the trainee's entire profile becomes inactive
        static::updated(function ($trainee) {
            if ($trainee->isDirty('active') && !$trainee->active) {
                $trainee->group()->updateExistingPivot($trainee->group->pluck('id'), ['active' => false]);
            }
        });

        // Add all newly-created trainees to the beginners' course
        static::created(function ($trainee) {
            $groupTrainee = new GroupTrainee();
            $groupTrainee->trainee_id = $trainee->id;
            $groupTrainee->group_id = 1; // Kyl mä hoidan beginners' group
            $groupTrainee->notes = 'Initial beginners\' course placement';
            $groupTrainee->active = true;
            $groupTrainee->save();
        });
    }

    public function getFullNameAttribute() {
        return $this->first_name. ' ' .$this->middle_name. ' ' .$this->last_name;
    }

    // Scoped functions
    // Prefixing functions with scope allows chaining query constraints useful for code readability and DRY principle
    // Sample usage:
    // $agency_groups = Trainee::getGroupsByObserverAgency()->get();
    protected function scopeGetGroupsByObserverAgency($query)
    {
        if (Auth::user()->hasRole('Observer')) {
            $observer = Observer::select(['agency_id'])->where('user_id', Auth::user()->id)
                ->first();

            return $query->where('agency_id', $observer->agency_id);
        }
    }

    public function scopeHasFlag($query, $flag)
    {
        return $query->whereHas('flagsOfTrainee',
            fn (Builder $query) =>
                // isActive() is another scope function under FlagTrainee model
                // This is used to check if the flag was toggled active in the admin panel
                $query->isActive()
                    ->whereRaw('id IN (SELECT MAX(id) FROM flag_trainees GROUP BY trainee_id)')
                    ->whereHas('flag', fn (Builder $query) => $query->where('name', $flag))
        );
    }

    public function scopeDeployedWhen($query, $tense)
    {
        $operator = '';

        if ($tense == 'past') {
            $operator = '<=';
        } else if ($tense == 'future') {
            $operator = '>';
        }

        return $query->hasFlag('Deployed')
            ->whereDate('date_deployment', $operator, Carbon::today()->toDate());
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Country relationships

    public function countryOfCitizenship(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_of_citizenship_id');
    }

    public function countryOfResidence(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_of_residence_id');
    }


    // Group relationships

    public function activeGroup(): HasOne
    {
        return $this->hasOne(GroupTrainee::class)
            ->isActive()
            ->with('group');
    }

    public function activeGroups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class)
                    ->wherePivot('active', true);
    }

    public function latestActiveGroup(): BelongsToMany
    {
        return $this->belongsToMany(Group::class)
            ->wherePivot('active', true)
            ->with('courses')
            ->orderBy('created_at', 'desc');
    }

    public function group(): BelongsToMany
    {
        return $this->belongsToMany(Group::class)
            ->withTimestamps()
            ->withPivot('notes', 'active', 'added_by');
    }

    public function groupTrainees(): HasMany
    {
        return $this->hasMany(GroupTrainee::class);
    }


    // Flag relationships

    public function status(): BelongsToMany
    {
        return $this->belongsToMany(Flag::class, 'flag_trainees')
            ->withPivot('active', 'description', 'internal_notes')
            ->withTimestamps();
    }

    public function flagsOfTrainee(): HasMany
    {
        return $this->hasMany(FlagTrainee::class);
    }


    // Exam relationships

    public function proficiencyTrainees(): HasMany
    {
        return $this->hasMany(ProficiencyTrainee::class);
    }

    public function examAttempts(): HasMany
    {
        return $this->hasMany(ExamAttempt::class);
    }

    public function exams(): BelongsToMany 
    {
        return $this->belongsToMany(Exam::class)
            ->withPivot('trainee_alias', 'internal_notes', 'exam_location', 'status')
            ->using(ExamTrainee::class);
    }

    public function examTrainees(): HasMany
    {
        return $this->hasMany(ExamTrainee::class);
    }


    // Agency relationships

    public function agency(): BelongsTo
    {
        return $this->belongsTo(Agency::class);
    }


    // Document relationships

    public function documents(): BelongsToMany
    {
        return $this->belongsToMany(Document::class, 'document_trainees')
            ->withPivot('url', 'status', 'comments', 'internal_notes', 'created_at AS document_created_at', 'updated_at AS document_updated_at')
            ->withTimestamps();
    }

    public function documentOverrides(): HasMany
    {
        return $this->hasMany(DocumentTraineeOverride::class);
    }


    // Meeting relationships

    public function meetings(): BelongsToMany
    {
        return $this->belongsToMany(Meeting::class)
            ->withPivot('meeting_status_id', 'instructor_id', 'internal_notes', 'feedback', 'date')
            ->using(MeetingTrainee::class)
            ->withTimestamps();
    }

    public function meetingTrainees(): HasMany
    {
        return $this->hasMany(MeetingTrainee::class);
    }

    public function verified_requests(): HasMany
    {
        return $this->hasMany(TraineesVerifiedRequest::class, 'trainee_id');
    }

    // Custom model functions
    protected function get_required_documents_of_agency_for_trainee($trainee_id): Collection
    {
        $agency_documents = AgencyDocumentRequired::select(['documents.id', 'documents.name', 'document_trainees.status'])
            ->join('agencies', 'agency_document_requireds.agency_id', 'agencies.id')
            ->join('trainees', 'agencies.id', 'trainees.agency_id')
            ->join('documents', 'agency_document_requireds.document_id', 'documents.id')
            ->leftJoin('document_trainees', 'documents.id', 'document_trainees.document_id')
            ->where('trainees.id', $trainee_id)
            ->orderBy('agency_document_requireds.id')
            ->get()
            ->pluck('name', 'id');

        if ($agency_documents->isNotEmpty()) {
            return $agency_documents;
        } else {
            return collect([]);
        }
    }

    protected function get_required_documents_of_agency_for_trainee_count($trainee_id): int
    {
        $agency_documents = $this->get_required_documents_of_agency_for_trainee($trainee_id)->toArray();

        return count(array_values($agency_documents));
    }
}
