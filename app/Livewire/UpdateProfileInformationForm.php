<?php

namespace App\Livewire;

use App\Enums\TraineesEducation;
use App\Enums\TraineesMaritalStatus;
use App\Enums\TraineesWorkExperience;
use App\Models\Country;
use App\Models\TraineesVerifiedRequest;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UpdateProfileInformationForm extends Component
{
    use WithFileUploads;

    public $user;
    public $trainee;
    public $trainees_verified_request;
    public $countries;
    public $profile_photo_path;
    public $first_name;
    public $middle_name;
    public $last_name;
    public $date_of_birth;
    public $sex;
    public $country_of_residence_id;
    public $country_of_residence_name;
    public $country_of_citizenship_id;
    public $country_of_citizenship_nationality;
    public $address;
    public $phone_number;
    public $occupation;
    public $field_of_work;
    public $work_experience;
    public $marital_status;
    public $education;
    public $email;
    public $show_confirm_save_modal = false;

    public $is_same_country_of_residence_and_citizenship = false;

    // Properties below are used for profile_photo_path field
    public $profile_photo;
    public $cropped_image;
    public $profile_photo_uploaded_data;
    public $show_profile_photo_preview_modal = false;
    
    // Properties below are used for website_photo_path field
    public $website_photo;
    public $cropped_image_website;
    public $website_photo_uploaded_data;
    public $show_website_photo_preview_modal = false;

    // For both preview modals
    public $is_preview_modal_opening = false;
    public $preview_modal_to_use;

    public function mount()
    {
        $this->user = Auth::user();
        $this->trainee = $this->user->trainee;
        $this->countries = Country::all();

        $country_of_residence = Country::find($this->trainee->country_of_residence_id);
        $country_of_citizenship = Country::find($this->trainee->country_of_citizenship_id);

        $this->first_name = $this->trainee->first_name;
        $this->middle_name = $this->trainee->middle_name;
        $this->last_name = $this->trainee->last_name;
        $this->date_of_birth = $this->trainee->date_of_birth;
        $this->sex = !is_null($this->trainee->sex) ? $this->trainee->sex : 'female';

        $this->country_of_residence_id = $country_of_residence->id;
        $this->country_of_citizenship_id = $country_of_citizenship->id;
        $this->country_of_residence_name = $country_of_residence->name;
        $this->country_of_citizenship_nationality = $country_of_citizenship->nationality;
        $this->address = $this->trainee->address;
        $this->phone_number = $this->trainee->phone_number;
        $this->occupation = $this->trainee->occupation;
        $this->field_of_work = $this->trainee->field_of_work;
        $this->work_experience = !is_null($this->trainee->work_experience) ? $this->trainee->work_experience : TraineesWorkExperience::NO_WORK_EXPERIENCE;
        $this->marital_status = !is_null($this->trainee->marital_status) ? $this->trainee->marital_status : TraineesMaritalStatus::SINGLE;
        $this->education = !is_null($this->trainee->education) ? $this->trainee->education : TraineesEducation::CAREGIVER;
        $this->email = Auth::user()->email;
    }

    // Lifecycle hook based on property $profile_photo
    // REF: https://laravel-livewire.com/docs/2.x/lifecycle-hooks#class-hooks
    public function updatedProfilePhoto()
    {
        $this->validate(['profile_photo' => ['max:2048', 'mimes:gif,jpg,jpeg,png']]);

        $this->preview_modal_to_use = 'profile_photo';
        $this->is_preview_modal_opening = true;

        $this->dispatch('set-photo', [
            'path' => $this->profile_photo->getRealPath(),
            'preview_modal' => 'profile_photo',
            'open_modal' => true
        ]);
    }

    public function updatedWebsitePhoto()
    {
        $this->validate(['website_photo' => ['max:2048', 'mimes:gif,jpg,jpeg,png']]);

        $this->preview_modal_to_use = 'website_photo';
        $this->is_preview_modal_opening = true;

        $this->dispatch('set-photo', [
            'path' => $this->website_photo->getRealPath(),
            'preview_modal' => 'website_photo',
            'open_modal' => true
        ]);
    }

    #[On('set-photo')]
    public function set_photo($dispatched_params)
    {
        if ($dispatched_params) {
            $image_path = $dispatched_params['path'];
            if (!$image_path) {
                if ($dispatched_params['preview_modal'] == 'profile_photo') {
                    $this->profile_photo_uploaded_data = null;
                } else {
                    $this->website_photo_uploaded_data = null;
                }

                return;
            }

            if ($dispatched_params['open_modal'] == true) {
                if ($dispatched_params['preview_modal'] == 'profile_photo') {
                    $this->show_profile_photo_preview_modal = true;
                } else {
                    $this->show_website_photo_preview_modal = true;
                }
            } else {
                if ($dispatched_params['preview_modal'] == 'profile_photo') {
                    $this->show_profile_photo_preview_modal = false;
                    $this->profile_photo_uploaded_data = str_replace('data:image/png;base64,', '', $image_path);
                } else {
                    $this->show_website_photo_preview_modal = false;
                    $this->website_photo_uploaded_data = str_replace('data:image/png;base64,', '', $image_path);
                }
            }

            $this->is_preview_modal_opening = false;
        }
    }

    #[On('closed-preview-modals')]
    public function close_photo_preview_modals()
    {
        $this->show_profile_photo_preview_modal = false;
        $this->show_website_photo_preview_modal = false;
    }

    #[On('finalized-cropped-image')]
    public function handle_cropped_image($cropped_image, $type)
    {
        if ($type == 'profile_photo') {
            $this->cropped_image = $cropped_image;
        } else {
            $this->cropped_image_website = $cropped_image;
        }

        // Dispatch the event again to set the newly cropped image based on cropped image URL
        $this->dispatch('set-photo', [
            'path' => $cropped_image,
            'preview_modal' => $type,
            'open_modal' => false
        ]);
    }

    public function toggle_same_country_of_residence_and_citizenship()
    {
        if ($this->is_same_country_of_residence_and_citizenship) {
            $this->country_of_citizenship_id = $this->country_of_residence_id;
        }
    }

    public function open_confirmation_modal()
    {
        $this->show_confirm_save_modal = true;
    }

    public function update_profile()
    {
        // Trim inputs first
        $this->first_name = trim($this->first_name);
        $this->middle_name = trim($this->middle_name);
        $this->last_name = trim($this->last_name);
        $this->address = trim($this->address);
        $this->phone_number = trim($this->phone_number);
        $this->occupation = trim($this->occupation);
        $this->field_of_work = trim($this->field_of_work);

        $this->show_confirm_save_modal = false;

        $fields_to_validate = [
            'first_name' => ['required', 'min:2', 'max:255'],
            'middle_name' => ['sometimes', 'min:2', 'max:255'],
            'last_name' => ['required', 'min:2', 'max:255'],
            'date_of_birth' => ['required', 'date', 'date_format:Y-m-d', 'before:today', 'after:1900-01-01'],
            'country_of_residence_id' => ['required', 'exists:countries,id'],
            'country_of_citizenship_id' => ['required', 'exists:countries,id'],
            'address' => ['required', 'max:255'],
            'phone_number' => ['required', 'regex:/[^a-zA-Z]+$/', 'max:32'],
            'occupation' => ['required', 'min:2', 'max:64'],
            'field_of_work' => ['required', 'min:2', 'max:64'],
        ];

        // Extra validations are placed on a separate function when uploading photo
        if (!Auth::user()->profile_photo_path) {
            $fields_to_validate['profile_photo'] = ['required'];
        }

        $this->validate($fields_to_validate, ['regex' => 'The :attribute field accepts all characters except letters.']);

        $user_fields_to_update = [];

        if ($this->profile_photo) {
            // Remove old profile image to save space
            if (Auth::user()->profile_photo_path) {
                Storage::disk('avatars')->delete(Auth::user()->profile_photo_path);
            }

            $decoded_photo = base64_decode(str_replace(' ', '+', $this->profile_photo_uploaded_data));

            $file_name = strtolower($this->first_name. '_' .$this->last_name. '_' .date('YmdHis'). '.' .$this->profile_photo->getClientOriginalExtension());

            // The file goes to S3
            Storage::disk('avatars')->put($file_name, $decoded_photo);

            $user_fields_to_update['profile_photo_path'] = $file_name;
        }

        if ($this->website_photo) {
            // Remove old website image to save space
            if (Auth::user()->website_photo_path) {
                Storage::disk('avatars_website')->delete(Auth::user()->website_photo_path);
            }

            $decoded_photo = base64_decode(str_replace(' ', '+', $this->website_photo_uploaded_data));

            $file_name = strtolower('website_' .date('YmdHis'). '.' .$this->website_photo->getClientOriginalExtension());

            // The file goes to S3
            Storage::disk('avatars_website')->put($file_name, $decoded_photo);

            $user_fields_to_update['website_photo_path'] = $file_name;
        }

        if (count($user_fields_to_update) > 0) {
            $this->user->update($user_fields_to_update);
        }

        $this->trainee->update([
            'first_name' => $this->first_name,
            'middle_name' => strlen($this->middle_name) > 0 ? $this->middle_name : NULL,
            'last_name' => $this->last_name,
            'date_of_birth' => $this->date_of_birth,
            'sex' => $this->sex,
            'country_of_residence_id' => $this->country_of_residence_id,
            'country_of_citizenship_id' => $this->country_of_citizenship_id,
            'address' => $this->address,
            'phone_number' => $this->phone_number,
            'occupation' => $this->occupation,
            'field_of_work' => $this->field_of_work,
            'work_experience' => $this->work_experience,
            'marital_status' => $this->marital_status,
            'education' => $this->education
        ]);

        if (!$this->trainees_verified_request ||
            ($this->trainees_verified_request->is_checked_by_staff == 1 && $this->trainees_verified_request->is_verified == 0)
        ) {
            TraineesVerifiedRequest::create(['trainee_id' => $this->trainee->id]);
        }

        if ($this->profile_photo) {
            $this->profile_photo = false;
        }

        if ($this->website_photo) {
            $this->website_photo = false;
        }

        $this->dispatch('trainee-profile-updated');
    }

    #[On('trainee-profile-updated')]
    public function render()
    {
        $this->trainees_verified_request = $this->trainee->verified_requests->last();

        return view('profile.update-profile-information-form');
    }
}
