<x-form-section submit="update_profile">
    <x-slot name="title">
        {{ __('Profile Information') }}
    </x-slot>

    <x-slot name="description">
        {{ __('Update your account\'s profile information. Once the form is submitted, you cannot change your name but you may change other information while verification is pending.') }}
    </x-slot>

    <x-slot name="form">
        <x-custom.trainees-is-verified-message :trainees_verified_request="$trainees_verified_request" />

        {{-- Profile Photo --}}
        <div class="col-span-6">
            <x-label value="{{ __('Profile Photo') }}" is_required="true" for="profile_photo" />

            <div class="flex items-center space-x-4 mb-4">
                <img src="{{ $cropped_image ? $cropped_image : auth()->user()->profilePhotoUrl() }}"
                    class="rounded-lg size-28"
                    alt="User profile photo"
                    title="User profile photo"
                />

                <x-loading-indicator
                    :loader_color_bg="'fill-slate-900 dark:fill-white'"
                    :loader_color_spin="'fill-slate-900 dark:fill-white'"
                    :showText="true"
                    :size="8"
                    :target="'profile_photo'"
                    :text="'Uploading'"
                    :text_color="'dark:text-slate-100'"
                />

                @if ($is_preview_modal_opening && $preview_modal_to_use == 'profile_photo')
                    <x-loading-indicator
                        :loader_color_bg="'fill-slate-900 dark:fill-white'"
                        :loader_color_spin="'fill-slate-900 dark:fill-white'"
                        :showText="true"
                        :size="8"
                        :text="'Opening modal. Please wait...'"
                        :text_color="'dark:text-slate-100'"
                    />
                @endif
            </div>

            @if (!$trainees_verified_request || ($trainees_verified_request && $trainees_verified_request->is_verified == 0))
                <x-input wire:model="profile_photo" wire:loading.attr="disabled" wire:target="profile_photo" class="sr-only" type="file" id="profile_photo" name="profile_photo" />

                <label class="text-xs text-white tracking-widest font-semibold uppercase cursor-pointer px-4 py-2 rounded-md bg-nlrc-blue-500 transition ease-in-out duration-150 hover:bg-nlrc-blue-600 dark:bg-nlrc-blue-600 dark:hover:bg-nlrc-blue-500" for="profile_photo">Choose profile photo</label>

                <small class="block mt-2 text-slate-700 dark:text-slate-300">Accepted file extensions: .gif, .jpg, .jpeg, .png</small>
            @endif

            <x-input-error class="mt-2" for="profile_photo" />
        </div>

        @if ($show_profile_photo_preview_modal)
            <x-modal wire:model="show_profile_photo_preview_modal" id="show_profile_photo_preview_modal">
                <div
                    x-data="{cropper: null,
                        isFinalizing: false,
                        minCroppedWidth: 80,
                        minCroppedHeight: 80,
                        maxCroppedWidth: 640,
                        maxCroppedHeight: 640,
                        dispatchCroppedImage() {
                            this.isFinalizing = true;

                            {{-- Returns data:image/png;base64, --}}
                            const canvas = this.cropper.getCroppedCanvas({
                                imageSmoothingEnabled: true,
                                imageSmoothingQuality: 'medium'
                            }).toDataURL();
                            $dispatch('finalized-cropped-image', { cropped_image: canvas, type: 'profile_photo' });
                        },
                        closePreviewModal() {
                            this.cropper.destroy();
                            $dispatch('closed-preview-modals');
                        }
                    }"
                    x-init="console.log('LOG: Initialized cropperjs');
                        console.log($refs.uploadedProfilePhoto);
                        $nextTick(() => {
                            cropper = new Cropper($refs.uploadedProfilePhoto, {
                                aspectRatio: 1 / 1,
                                autoCropArea: 1,
                                background: false,
                                checkCrossOrigin: false,
                                viewMode: 1,
                                zoomable: false,
                                data: {
                                    height: (this.minCroppedHeight + this.maxCroppedHeight) / 2,
                                    width: (this.minCroppedWidth + this.maxCroppedWidth) / 2,
                                },
                                crop (event) {
                                    const width = Math.round(event.detail.width);
                                    const height = Math.round(event.detail.height);

                                    if (width < minCroppedWidth ||
                                    height < minCroppedHeight ||
                                    width > maxCroppedWidth ||
                                    height > maxCroppedHeight) {
                                        this.cropper.setData({
                                            width: Math.max(minCroppedWidth, Math.min(maxCroppedWidth, width)),
                                            height: Math.max(minCroppedHeight, Math.min(maxCroppedHeight, height)),
                                        });
                                    }
                                }
                            });
                        });
                    "
                    class="py-2"
                >
                    <div class="flex justify-between px-4 py-2 mb-4 border-b-2 border-black dark:border-white">
                        <h2 class="dark:text-slate-300">Profile Photo Preview</h2>

                        <span x-on:click="closePreviewModal" class="cursor-pointer dark:text-white" title="Close modal">&times;</span>
                    </div>

                    <div>
                        <img x-ref="uploadedProfilePhoto" class="w-full max-w-full" src="{{ $profile_photo->temporaryUrl() }}" crossorigin />

                        <div class="flex items-center justify-center flex-col gap-2 my-4">
                            <x-secondary-button x-bind:disabled="isFinalizing" x-on:click="dispatchCroppedImage" class="w-9/12 gap-2 justify-center mt-2">
                                <x-loading-indicator
                                    :loader_color_bg="'fill-slate-900 dark:fill-white'"
                                    :loader_color_spin="'fill-slate-900 dark:fill-white'"
                                    :showText="false"
                                    :size="4"
                                    x-show="isFinalizing"
                                />

                                <span>Finalize Cropping</span>
                            </x-secondary-button>
                        </div>
                    </div>
                </div>
            </x-modal>
        @endif

        {{-- Website Photo --}}
        <div class="col-span-6">
            <x-label value="{{ __('Website Photo') }}" for="website_photo" />

            <div class="flex items-center space-x-4 mb-4">
                <img src="{{ $cropped_image_website ? $cropped_image_website : auth()->user()->websitePhotoUrl() }}"
                    class="rounded-lg size-28"
                    alt="User website photo"
                    title="User website photo"
                />

                <x-loading-indicator
                    :loader_color_bg="'fill-slate-900 dark:fill-white'"
                    :loader_color_spin="'fill-slate-900 dark:fill-white'"
                    :showText="true"
                    :size="8"
                    :target="'website_photo'"
                    :text="'Uploading'"
                    :text_color="'dark:text-slate-100'"
                />

                @if ($is_preview_modal_opening && $preview_modal_to_use == 'website_photo')
                    <x-loading-indicator
                        :loader_color_bg="'fill-slate-900 dark:fill-white'"
                        :loader_color_spin="'fill-slate-900 dark:fill-white'"
                        :showText="true"
                        :size="8"
                        :text="'Opening modal. Please wait...'"
                        :text_color="'dark:text-slate-100'"
                    />
                @endif
            </div>

            <x-input wire:model="website_photo" wire:loading.attr="disabled" wire:target="website_photo" class="sr-only" type="file" id="website_photo" name="website_photo" />

            <label class="text-xs text-white tracking-widest font-semibold uppercase cursor-pointer px-4 py-2 rounded-md bg-nlrc-blue-500 transition ease-in-out duration-150 hover:bg-nlrc-blue-600 dark:bg-nlrc-blue-600 dark:hover:bg-nlrc-blue-500" for="website_photo">Choose website photo</label>

            <small class="block mt-2 text-slate-700 dark:text-slate-300">Accepted file extensions: .gif, .jpg, .jpeg, .png</small>

            <x-input-error class="mt-2" for="website_photo" />
        </div>

        @if ($show_website_photo_preview_modal)
            <x-modal wire:model="show_website_photo_preview_modal" id="show_website_photo_preview_modal">
                <div
                    x-data="{cropper: null,
                        isFinalizing: false,
                        minCroppedWidth: 80,
                        minCroppedHeight: 80,
                        maxCroppedWidth: 640,
                        maxCroppedHeight: 640,
                        dispatchCroppedImage() {
                            this.isFinalizing = true;

                            {{-- Returns data:image/png;base64, --}}
                            const canvas = this.cropper.getCroppedCanvas({
                                imageSmoothingEnabled: true,
                                imageSmoothingQuality: 'medium'
                            }).toDataURL();
                            $dispatch('finalized-cropped-image', { cropped_image: canvas, type: 'website_photo' });
                        },
                        closePreviewModal() {
                            this.cropper.destroy();
                            $dispatch('closed-preview-modals');
                        }
                    }"
                    x-init="console.log('LOG: Initialized cropperjs');
                        console.log($refs.uploadedWebsitePhoto);
                        $nextTick(() => {
                            cropper = new Cropper($refs.uploadedWebsitePhoto, {
                                aspectRatio: 1 / 1,
                                autoCropArea: 1,
                                background: false,
                                checkCrossOrigin: false,
                                viewMode: 1,
                                zoomable: false,
                                data: {
                                    height: (this.minCroppedHeight + this.maxCroppedHeight) / 2,
                                    width: (this.minCroppedWidth + this.maxCroppedWidth) / 2,
                                },
                                crop (event) {
                                    const width = Math.round(event.detail.width);
                                    const height = Math.round(event.detail.height);

                                    if (width < minCroppedWidth ||
                                    height < minCroppedHeight ||
                                    width > maxCroppedWidth ||
                                    height > maxCroppedHeight) {
                                        this.cropper.setData({
                                            width: Math.max(minCroppedWidth, Math.min(maxCroppedWidth, width)),
                                            height: Math.max(minCroppedHeight, Math.min(maxCroppedHeight, height)),
                                        });
                                    }
                                }
                            });
                        });
                    "
                    class="py-2"
                >
                    <div class="flex justify-between px-4 py-2 mb-4 border-b-2 border-black dark:border-white">
                        <h2 class="dark:text-slate-300">Website Photo Preview</h2>

                        <span x-on:click="closePreviewModal" class="cursor-pointer dark:text-white" title="Close modal">&times;</span>
                    </div>

                    <div>
                        <img x-ref="uploadedWebsitePhoto" class="w-full max-w-full" src="{{ $website_photo->temporaryUrl() }}" crossorigin />

                        <div class="flex items-center justify-center flex-col gap-2 my-4">
                            <x-secondary-button x-bind:disabled="isFinalizing" x-on:click="dispatchCroppedImage" class="w-9/12 gap-2 justify-center mt-2">
                                <x-loading-indicator
                                    :loader_color_bg="'fill-slate-900 dark:fill-white'"
                                    :loader_color_spin="'fill-slate-900 dark:fill-white'"
                                    :showText="false"
                                    :size="4"
                                    x-show="isFinalizing"
                                />

                                <span>Finalize Cropping</span>
                            </x-secondary-button>
                        </div>
                    </div>
                </div>
            </x-modal>
        @endif

        <div class="col-span-6">
            <x-label value="{{ __('First Name') }}" is_required="true" for="first_name" />

            @if ($trainees_verified_request || ($trainees_verified_request && $trainees_verified_request->is_verified == 1))
                <p class="mt-1 block w-full dark:text-slate-400">{{ $first_name }}</p>
            @else
                <x-input wire:model="first_name" class="mt-1 block w-full" type="text" id="first_name" autocomplete="first_name" />
            @endif

            <x-input-error class="mt-2" for="first_name" />
        </div>

        <div class="col-span-6">
            <x-label value="{{ __('Middle Name') }}" for="middle_name" />

            @if ($trainees_verified_request || ($trainees_verified_request && $trainees_verified_request->is_verified == 1))
                <p class="mt-1 block w-full dark:text-slate-400">{{ $middle_name }}</p>
            @else
                <x-input wire:model="middle_name" class="mt-1 block w-full" type="text" id="middle_name" autocomplete="middle_name" />
            @endif

            <x-input-error class="mt-2" for="middle_name" />
        </div>

        <div class="col-span-6">
            <x-label value="{{ __('Last Name') }}" is_required="true" for="last_name" />

            @if ($trainees_verified_request || ($trainees_verified_request && $trainees_verified_request->is_verified == 1))
                <p class="mt-1 block w-full dark:text-slate-400">{{ $last_name }}</p>
            @else
                <x-input wire:model="last_name" class="mt-1 block w-full" type="text" id="last_name" autocomplete="last_name" />
            @endif

            <x-input-error class="mt-2" for="last_name" />
        </div>

        <div class="col-span-6">
            <x-label value="{{ __('Date of Birth') }}" is_required="true" for="date_of_birth" />

            @if ($trainees_verified_request && $trainees_verified_request->is_verified == 1)
                <p class="mt-1 block w-full dark:text-slate-400">{{ \Carbon\Carbon::parse($date_of_birth)->format('F j, Y') }}</p>
            @else
                <x-input wire:model="date_of_birth" class="mt-1 block w-full" type="date" id="date_of_birth" autocomplete="date_of_birth" />
                <small class="text-slate-700 dark:text-slate-300">Format: DD/MM/YYYY</small>
            @endif

            <x-input-error class="mt-2" for="date_of_birth" />
        </div>

        <div class="col-span-6">
            <x-label value="{{ __('Sex') }}" is_required="true" for="sex" />

            @if ($trainees_verified_request && $trainees_verified_request->is_verified == 1)
                <p class="mt-1 block w-full dark:text-slate-400">{{ ucfirst($sex) }}</p>
            @else
                <x-select wire:model="sex" :inline_block="false">
                    <option value="female">Female</option>
                    <option value="male">Male</option>
                </x-select>
            @endif

            <x-input-error class="mt-2" for="sex" />
        </div>

        <div class="col-span-6">
            <x-label value="{{ __('Country of Residence') }}" is_required="true" for="country_of_residence_id" />

            @if ($trainees_verified_request && $trainees_verified_request->is_verified == 1)
                <p class="mt-1 block w-full dark:text-slate-400">{{ $country_of_residence_name }}</p>
            @else
                <x-select wire:model="country_of_residence_id" :inline_block="false">
                    @foreach ($countries as $country)
                        <option wire:key="{{ $country->id }}" value="{{ $country->id }}">{{ $country->name }}</option>
                    @endforeach
                </x-select>
            @endif

            <x-input-error class="mt-2" for="country_of_residence_id" />
        </div>

        <div class="col-span-6">
            <x-label value="{{ __('Nationality') }}" is_required="true" for="country_of_citizenship_id" />

            @if ($trainees_verified_request && $trainees_verified_request->is_verified == 1)
                <p class="mt-1 block w-full dark:text-slate-400">{{ $country_of_citizenship_nationality }}</p>
            @else
                <x-select wire:model="country_of_citizenship_id" wire:target="toggle_same_country_of_residence_and_citizenship" wire:loading.attr="disabled" :inline_block="false">
                    @foreach ($countries as $country)
                        <option value="{{ $country->id }}">{{ $country->nationality }}</option>
                    @endforeach
                </x-select>
            @endif

            @if (!$trainees_verified_request || ($trainees_verified_request && $trainees_verified_request->is_verified != 1))
                <div class="col-span-6 flex items-center gap-2">
                    <x-checkbox wire:model="is_same_country_of_residence_and_citizenship" wire:click="toggle_same_country_of_residence_and_citizenship()" wire:target="toggle_same_country_of_residence_and_citizenship" wire:loading.attr="disabled" /> <span class="dark:text-slate-300">Same with Country of Residence</span>

                    @if (!$is_same_country_of_residence_and_citizenship)
                        <x-loading-indicator
                            :loader_color_bg="'fill-slate-900 dark:fill-white'"
                            :loader_color_spin="'fill-slate-900 dark:fill-white'"
                            :showText="false"
                            :size="4"
                            :target="'toggle_same_country_of_residence_and_citizenship'"
                        />
                    @endif
                </div>
            @endif

            <x-input-error class="mt-2" for="country_of_citizenship_id" />
        </div>

        <div class="col-span-6">
            <x-label value="{{ __('Address') }}" is_required="true" for="address" />

            @if ($trainees_verified_request && $trainees_verified_request->is_verified == 1)
                <p class="mt-1 block w-full dark:text-slate-400">{{ $address }}</p>
            @else
                <x-input wire:model="address" class="mt-1 block w-full" type="text" id="address" autocomplete="address" />
            @endif

            <x-input-error class="mt-2" for="address" />
        </div>

        <div class="col-span-6">
            <x-label value="{{ __('Phone Number') }}" is_required="true" for="phone_number" />

            @if ($trainees_verified_request && $trainees_verified_request->is_verified == 1)
                <p class="mt-1 block w-full dark:text-slate-400">{{ $phone_number }}</p>
            @else
                <x-input wire:model="phone_number" class="mt-1 block w-full" type="text" id="phone_number" autocomplete="phone_number" />
            @endif

            <x-input-error class="mt-2" for="phone_number" />
        </div>

        <div class="col-span-6">
            <x-label value="{{ __('Occupation') }}" is_required="true" for="occupation" />

            @if ($trainees_verified_request && $trainees_verified_request->is_verified == 1)
                <p class="mt-1 block w-full dark:text-slate-400">{{ $occupation }}</p>
            @else
                <x-input wire:model="occupation" class="mt-1 block w-full placeholder-slate-800 focus:placeholder-slate-400 dark:placeholder-slate-400 dark:focus:placeholder-slate-200" type="text" id="occupation" autocomplete="occupation" placeholder="eg. Nurse" />
            @endif

            <x-input-error class="mt-2" for="occupation" />
        </div>

        <div class="col-span-6">
            <x-label value="{{ __('Field of Work') }}" is_required="true" for="field_of_work" />

            @if ($trainees_verified_request && $trainees_verified_request->is_verified == 1)
                <p class="mt-1 block w-full dark:text-slate-400">{{ $field_of_work }}</p>
            @else
                <x-input wire:model="field_of_work" class="mt-1 block w-full placeholder-slate-800 focus:placeholder-slate-400 dark:placeholder-slate-400 dark:focus:placeholder-slate-200" type="text" id="field_of_work" autocomplete="field_of_work" placeholder="eg. Healthcare Services" />
            @endif

            <x-input-error class="mt-2" for="field_of_work" />
        </div>

        <div class="col-span-6">
            <x-label value="{{ __('Work Experience') }}" is_required="true" for="work_experience" />

            @if ($trainees_verified_request && $trainees_verified_request->is_verified == 1)
                <p class="mt-1 block w-full dark:text-slate-400">{{ \App\Enums\TraineesWorkExperience::formLabel($work_experience) }}</p>
            @else
                <x-select wire:model="work_experience" :inline_block="false">
                    @foreach (\App\Enums\TraineesWorkExperience::cases() as $work_experience)
                        <option value="{{ $work_experience->value }}">{{ \App\Enums\TraineesWorkExperience::formLabel($work_experience) }}</option>
                    @endforeach
                </x-select>
            @endif

            <x-input-error class="mt-2" for="work_experience" />
        </div>

        <div class="col-span-6">
            <x-label value="{{ __('Marital Status') }}" is_required="true" for="marital_status" />

            @if ($trainees_verified_request && $trainees_verified_request->is_verified == 1)
                <p class="mt-1 block w-full dark:text-slate-400">{{ \App\Enums\TraineesMaritalStatus::formLabel($marital_status) }}</p>
            @else
                <x-select wire:model="marital_status" :inline_block="false">
                    @foreach (\App\Enums\TraineesMaritalStatus::cases() as $marital_status)
                        <option value="{{ $marital_status->value }}">{{ \App\Enums\TraineesMaritalStatus::formLabel($marital_status) }}</option>
                    @endforeach
                </x-select>
            @endif

            <x-input-error class="mt-2" for="marital_status" />
        </div>

        <div class="col-span-6">
            <x-label value="{{ __('Education') }}" is_required="true" for="education" />

            @if ($trainees_verified_request && $trainees_verified_request->is_verified == 1)
                <p class="mt-1 block w-full dark:text-slate-400">{{ \App\Enums\TraineesEducation::formLabel($education) }}</p>
            @else
                <x-select wire:model="education" :inline_block="false">
                    @foreach (\App\Enums\TraineesEducation::cases() as $education)
                        <option value="{{ $education->value }}">{{ \App\Enums\TraineesEducation::formLabel($education) }}</option>
                    @endforeach
                </x-select>
            @endif

            <x-input-error class="mt-2" for="education" />
        </div>

        <div class="col-span-6">
            <x-label value="{{ __('Email') }}" />
            <p class="mt-1 block w-full dark:text-slate-400">{{ $email }}</p>
        </div>

        @if ($errors->any())
            <div class="col-span-6">
                <p class="text-sm px-4 py-2 bg-red-100 text-red-800 dark:bg-red-800 dark:text-white">Trainee profile not saved. Please correct <span class="font-bold dark:text-red-300">{{ count($errors->keys()) }}</span> {{ count($errors->keys()) > 1 ? 'errors' : 'error' }}.</p>
            </div>
        @endif

        {{--
        <div class="col-span-6 sm:col-span-4">
            <x-label for="email" value="{{ __('Email') }}" />
            <x-input id="email" type="email" class="mt-1 block w-full" wire:model="state.email" required autocomplete="username" />
            <x-input-error for="email" class="mt-2" />

            @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::emailVerification()) && ! $this->user->hasVerifiedEmail())
                <p class="text-sm mt-2 dark:text-white">
                    {{ __('Your email address is unverified.') }}

                    <button type="button" class="underline text-sm text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-slate-800" wire:click.prevent="sendEmailVerification">
                        {{ __('Click here to re-send the verification email.') }}
                    </button>
                </p>

                @if ($this->verificationLinkSent)
                    <p class="mt-2 font-medium text-sm text-green-600 dark:text-green-400">
                        {{ __('A new verification link has been sent to your email address.') }}
                    </p>
                @endif
            @endif
        </div>
        --}}
    </x-slot>

    <x-slot name="actions">
        @if (!$trainees_verified_request || ($trainees_verified_request && $trainees_verified_request->is_verified != 1))
            <x-secondary-button wire:click="open_confirmation_modal" wire.loading.attr="disabled">Save</x-secondary-button>

            <x-loading-indicator
                :loader_color_bg="'fill-slate-900 dark:fill-white'"
                :loader_color_spin="'fill-slate-900 dark:fill-white'"
                :showText="true"
                :size="4"
                :target="'open_confirmation_modal'"
                :text="'Opening modal. Please wait...'"
                :text_color="'dark:text-slate-100'"
                class="pl-4"
            />
        @elseif ($trainees_verified_request && $trainees_verified_request->is_verified == 1)
            <x-button wire.loading.attr="disabled">
                <span wire:loading.flex wire:target="update_profile">
                    <x-loading-indicator
                        :loader_color_bg="'fill-white'"
                        :loader_color_spin="'fill-white'"
                        :showText="false"
                        :size="4"
                    />

                    <span class="ml-2">Saving</span>
                </span>
                <span wire:loading.remove wire:target="update_profile">Save</span>
            </x-button>
        @endif

        <x-action-message class="ms-3" on="trainee-profile-updated">
            <div class="text-sm px-4 py-2 me-2 bg-green-100 text-green-800 dark:bg-green-800 dark:text-white">Trainee profile updated.</div>
        </x-action-message>

        <x-confirmation-modal wire:model="show_confirm_save_modal">
            <x-slot name="title">Save changes?</x-slot>

            <x-slot name="content">Are you sure you want to submit your information? While pending verification, you can change your information except the first name, middle name, and last name to prevent impersonation. Please make sure they are correct and press Confirm Save to submit your info for verification.</x-slot>

            <x-slot name="footer">
                <x-secondary-button wire:click="$toggle('show_confirm_save_modal')" wire:loading.attr="disabled">No</x-secondary-button>

                <x-button wire.loading.attr="disabled" class="ml-4">
                    <span wire:loading.flex wire:target="update_profile">
                        <x-loading-indicator
                            :loader_color_bg="'fill-white'"
                            :loader_color_spin="'fill-white'"
                            :showText="false"
                            :size="4"
                        />

                        <span class="ml-2">Saving</span>
                    </span>
                    <span wire:loading.remove wire:target="update_profile">Confirm Save</span>
                </x-button>
            </x-slot>
        </x-confirmation-modal>
    </x-slot>
</x-form-section>
