<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 dark:text-slate-200">
    <x-action-message class="me-3" on="request-update-created">
        <div class="text-lg px-4 py-2 bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-400">Request update successfully submitted! Please wait for the staff's approval.</div>
    </x-action-message>

    <p>We need certain documents from you. You can upload and check their status here.</p>

    <x-button wire:click="$toggle('show_filter_modal')" class="p-2">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-4">
            <path fill-rule="evenodd" d="M2.628 1.601C5.028 1.206 7.49 1 10 1s4.973.206 7.372.601a.75.75 0 0 1 .628.74v2.288a2.25 2.25 0 0 1-.659 1.59l-4.682 4.683a2.25 2.25 0 0 0-.659 1.59v3.037c0 .684-.31 1.33-.844 1.757l-1.937 1.55A.75.75 0 0 1 8 18.25v-5.757a2.25 2.25 0 0 0-.659-1.591L2.659 6.22A2.25 2.25 0 0 1 2 4.629V2.34a.75.75 0 0 1 .628-.74Z" clip-rule="evenodd" />
        </svg>
    </x-button>

    @if ($is_filtered)
        <span class="block mt-4 p-2 border-2 border-solid rounded-xl w-fit bg-nlrc-blue-100 border-nlrc-blue-400 dark:bg-nlrc-blue-800">
            <span class="mr-1">Show: {{ $document_trainee_status_stored }}</span>

            <x-loading-indicator
                :loader_color_bg="'fill-slate-900 dark:fill-white'"
                :loader_color_spin="'fill-red-500'"
                :showText="false"
                :size="4"
                :target="'reset_filters'"
            />

            <button wire:click.prevent="reset_filters" wire:loading.remove wire:target="reset_filters" class="text-red-600 dark:text-red-300" title="Reset filters">&times;</button>
        </span>
    @endif

    <x-modal wire:model="show_filter_modal">
        <div class="flex justify-between px-6 py-2 border-b-2 border-b-slate-600 text-xl">
            <h2 class="dark:text-white">Filter Documents</h2>

            <button wire:click="$toggle('show_filter_modal')" class="dark:text-white">&times;</button>
        </div>

        <form wire:submit.prevent="filter_documents" class="px-6 py-2">
            <div class="flex items-center gap-2 my-2">
                <label class="dark:text-white" for="filter-status">Status</label>
                <select wire:model="document_trainee_status" wire:loading.attr="disabled" wire:target="show_filter_modal" class="dark:text-white dark:bg-nlrc-blue-900 dark:border-nlrc-blue-600" id="filter-status">
                    <option value="show-all">Show all</option>

                    @foreach (\App\Enums\DocumentTraineesStatus::cases() as $status)
                        <option value="{{ $status->value }}">{{ $status->value }}</option>
                    @endforeach
                </select>
            </div>

            <x-button wire.loading.attr="disabled" class="block bg-green-600 active:bg-green-400 focus:bg-green-400 hover:bg-green-400 dark:bg-green-400 dark:active:bg-green-600 dark:focus:bg-green-600 dark:hover:bg-green-600" type="submit">
                <span wire:loading wire:target="filter_documents">Filtering</span>
                <span wire:loading.remove wire:target="filter_documents">Filter</span>
            </x-button>
        </form>
    </x-modal>

    <div class="flex flex-col items-center gap-y-4 my-4">
        <h2>
            <label class="text-xl" for="progress-percent">Progress</label>
        </h2>

        <x-custom.document-upload-completion-progress
            class="h-40 w-40"
            :completion_progress_text="$completion_progress_text"
            :completion_progress="$completion_progress"
            :progress_percent_classes="'text-4xl'"
        />
    </div>

    <div wire:loading.flex wire:target="filter_documents" class="flex-col items-center my-4 dark:text-white">
        <x-loading-indicator
            :loader_color_bg="'fill-slate-900 dark:fill-white'"
            :loader_color_spin="'fill-slate-900 dark:fill-white'"
            :size="20"
            :text="'Filtering. Please wait...'"
            :text_color="'dark:text-slate-100'"
        />
    </div>

    <div
        x-data="{deletedDocumentId: 0, uploadedDocumentId: 0}"
        x-init="
            $wire.on('document-deleted', function(dispatchedData) {
                deletedDocumentId = dispatchedData.documentId;
                uploadedDocumentId = 0;
            });

            $wire.on('document-uploaded', function(dispatchedData) {
                deletedDocumentId = 0;
                uploadedDocumentId = dispatchedData.documentId;
            });
        "
        wire:loading.remove wire:target="filter_documents"
        class="grid grid-flow-row grid-cols-1 md:grid-cols-2 gap-x-4"
    >

    @if (count($documents) > 0)
        @foreach ($documents as $document)
            <div class="p-4 border border-nlrc-blue-200 dark:border-nlrc-blue-600 dark:bg-nlrc-blue-800 rounded mt-4 w-full">
                <div
                    x-show="deletedDocumentId == {{ $document->document->id }}"
                    x-bind:class="{'hidden': deletedDocumentId == 0, 'block': deletedDocumentId == {{ $document->document->id }}}"
                    class="hidden px-4 py-2 mb-4 text-lg text-green-800 bg-green-100 dark:bg-green-800 dark:text-green-100"
                >
                    Document deleted successfully.
                </div>

                <div
                    x-show="uploadedDocumentId == {{ $document->document->id }}"
                    x-bind:class="{'hidden': uploadedDocumentId == 0, 'block': uploadedDocumentId == {{ $document->document->id }}}"
                    class="hidden px-4 py-2 mb-4 text-lg text-green-800 bg-green-100 dark:bg-green-800 dark:text-green-100"
                >
                    Document uploaded successfully.
                </div>

                @if (!$this->hasUploadedFile($document->document->id))
                    <form wire:submit.prevent="saveDocument({{ $document->id }})">
                        <label for="documentFiles.{{ $document->id }}">{{ $document->document->name }}</label>
                        <p class="text-sm">{{ $document->document->description }}</p>
                        {{-- Progress bar --}}
                        <div class="flex justify-start items-center"
                            x-data="{ uploading: false, progress: 0 }"
                            x-on:livewire-upload-start="uploading = true"
                            x-on:livewire-upload-finish="uploading = false"
                            x-on:livewire-upload-cancel="uploading = false"
                            x-on:livewire-upload-error="uploading = false"
                            x-on:livewire-upload-progress="progress = $event.detail.progress"
                        >
                            {{-- Slap the progress bar behind the input field for extra coolness --}}
                            <div class="relative bg-nlrc-blue-100 p-2 border border-nlrc-blue-200 dark:bg-nlrc-blue-900 dark:border-nlrc-blue-600 rounded my-2 me-4 w-full">
                                <div class="absolute inset-0 flex items-center" wire:loading.delay.longer wire:target="documentFiles.{{ $document->id }}">
                                    <progress class="w-full h-full bg-nlrc-blue-200" max="100" x-bind:value="progress"></progress>
                                </div>
                                <div class="relative">
                                    <input class="block opacity-75 w-full dark:text-slate-300" type="file" wire:model="documentFiles.{{ $document->id }}" />

                                    <small class="dark:text-slate-300">Accepted file extensions: .pdf, .gif, .jpg, .jpeg, .png</small>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-start mb-2">
                            <x-loading-indicator
                                :loader_color_bg="'fill-slate-900 dark:fill-white'"
                                :loader_color_spin="'fill-slate-900 dark:fill-white'"
                                :showText="true"
                                :size="4"
                                :target="'documentFiles.' .$document->id"
                                :text="'Uploading'"
                                :text_color="'dark:text-slate-100'"
                            />
                        </div>

                        @error ('documentFiles.' .$document->id)
                            <div class="text-red-500 text-sm mb-2">{{ $message }}</div>
                        @enderror

                        <div wire:loading.remove wire:target="documentFiles.{{ $document->id }}">
                            <x-button wire:loading.attr="disabled" class="block gap-1.5">
                                <x-loading-indicator
                                    :loader_color_bg="'fill-white'"
                                    :loader_color_spin="'fill-white'"
                                    :showText="true"
                                    :size="4"
                                    :target="'saveDocument(' .$document->id. ')'"
                                    :text="'Saving'"
                                    :text_color="'dark:text-slate-100'"
                                />

                                <div wire:loading.remove wire:target="saveDocument({{ $document->id }})" class="flex gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5" />
                                    </svg>
                                    <span>Upload</span>
                                </div>
                            </x-button>
                        </div>
                    </form>
                @else
                    {{-- Store them in vars so that the code isn't too long --}}
                    @php
                        $document_id = $document->document->id;
                        $document_trainee_record = $this->uploadedDocuments->get($document_id);
                        $document_trainee_request_update = $this->document_trainees_request_updates->where('document_trainee_id', $document_trainee_record->id)->first();

                        switch ($document_trainee_record->status) {
                            case \App\Enums\DocumentTraineesStatus::APPROVED:
                                $status_markup = '<span class="text-green-500 dark:text-green-300">Approved</span>';

                                break;
                            case \App\Enums\DocumentTraineesStatus::RE_UPLOAD_NEEDED:
                                $status_markup = '<span class="text-red-500 dark:text-red-300">Re-upload needed</span>';

                                break;
                            case \App\Enums\DocumentTraineesStatus::PENDING_CHECKING:
                            default:
                                $status_markup = '<span class="text-orange-500 dark:text-orange-300">Pending checking</span>';

                                break;
                        }
                    @endphp

                    <h2 class="text-lg">{{ $document->document->name }}</h2>

                    <p class="mt-8">Document uploaded.</p>
                    {{-- Storing the stuff below in a variable doesn't seem to work because Livewire updates the view when a file is selected but before the save button is pushed. It would then look for these even though they aren't in the database yet --}}

                    <p class="mt-2">Status: {!! $status_markup !!}</p>

                    @if ($document_trainee_record->comments)
                        <div class="mt-2 rounded border border-nlrc-blue-200 dark:border-nlrc-blue-900">
                            <div class="px-2 py-3 font-bold bg-nlrc-blue-200 border-nlrc-blue-200 dark:bg-nlrc-blue-900 dark:border-nlrc-blue-900">Comments:</div>
                            <div class="p-2 italic indent-2">{{ $document_trainee_record->comments }}</div>
                        </div>
                    @endif

                    @if (isset($temporaryUrls[$document->document_id]))
                        <a href="{{ $temporaryUrls[$document->document_id] }}" target="_blank">
                            <x-secondary-button class="flex items-center gap-1.5 mt-8">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                </svg>
                                View Document
                            </x-button>
                        </a>
                    @endif

                    {{-- They can request an update if they haven't made any existing request for update and that the document has been approved by staff --}}
                    @if (!$document_trainee_request_update && $document_trainee_record->status == \App\Enums\DocumentTraineesStatus::APPROVED)
                        <x-secondary-button wire:click="request_update_modal({{ $document_trainee_record->id }})" class="bg-orange-300 dark:bg-orange-700 dark:text-white flex items-center gap-1.5 mt-8">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m3.75 13.5 10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75Z" />
                            </svg>
                            Request Update
                        </x-secondary-button>

                        <x-modal wire:model="show_request_update_modal" :maxWidth="'xl'">
                            <div class="my-4 mx-6">
                                <h2 class="mb-6 text-2xl text-slate-900 dark:text-slate-200">Request Update Form</h3>

                                <p class="my-6 text-justify text-slate-700 dark:text-slate-400"><strong>Note:</strong> To update your document, you need to request its removal. This request will undergo review and approval by our team. Once approved, you will have the option to delete the document, enabling you to upload your new document with the correct information.</p>

                                <p class="mb-8">Please provide us with more information by filling out the textarea below.</p>
                            </div>

                            <form wire:submit.prevent="create_request_update" class="my-4 mx-6">
                                <x-label value="{{ __('Reason') }}" is_required="true" for="reason_update_reason" />
                                <x-textarea wire:model="request_update_reason" class="placeholder-slate-700 dark:placeholder-slate-400" id="reason_update_reason" placeholder="Please state your reason here" />
                                <input wire:model="document_trainee_id" type="hidden" />

                                @error ('request_update_reason')
                                    <div class="text-red-500 text-sm mb-2">{{ $message }}</div>
                                @enderror

                                <x-button class="my-4 hover:cursor-pointer">
                                    <span wire:loading.flex wire:target="create_request_update" class="items-center">
                                        <x-loading-indicator
                                            :loader_color_bg="'fill-white'"
                                            :loader_color_spin="'fill-white'"
                                            :showText="false"
                                            :size="4"
                                        />

                                        <span class="ml-2">Submitting</span>
                                    </span>

                                    <span wire:loading.remove wire:target="create_request_update">Submit</span>
                                </x-button>
                            </form>
                        </x-modal>
                    {{-- They can delete if the document hasn't been approved yet or that their request for update is approved --}}
                    @elseif ($document_trainee_record->status != \App\Enums\DocumentTraineesStatus::APPROVED ||
                        ($document_trainee_request_update && $document_trainee_request_update->approval_status == App\Enums\DocumentTraineesRequestUpdatesApprovalStatus::APPROVED)
                    )
                        <x-danger-button wire:click="confirmDelete({{ $document->document->id }})" class="gap-1.5 mt-8">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                            </svg>
                            Delete
                        </x-danger-button>

                        <x-confirmation-modal wire:model="showDeleteModal">
                            <x-slot name="title">
                                Delete your {{ $document_name_to_be_deleted }}?
                            </x-slot>

                            <x-slot name="content">
                                If you delete this file, you can re-upload it. You cannot delete the file once our staff has checked it. Do you want to delete the {{ strtolower($document_name_to_be_deleted) }} file?
                            </x-slot>

                            <x-slot name="footer">
                                <x-secondary-button wire:click="$toggle('showDeleteModal')" wire:loading.attr="disabled">
                                    Nevermind
                                </x-secondary-button>

                                <x-danger-button class="ms-2" wire:click="deleteDocument" wire:loading.attr="disabled">
                                    Delete
                                </x-danger-button>
                            </x-slot>
                        </x-confirmation-modal>
                    @endif

                    @if ($document_trainee_request_update)
                        <p class="pt-4 text-green-500 dark:text-green-300">
                            @if ($document_trainee_request_update->approval_status == App\Enums\DocumentTraineesRequestUpdatesApprovalStatus::APPROVED)
                                Your request for removal/update has been approved.
                            @elseif ($document_trainee_request_update->approval_status == App\Enums\DocumentTraineesRequestUpdatesApprovalStatus::DISAPPROVED)
                                This document is okay. There's no need to update.
                            @elseif ($document_trainee_request_update->approval_status == App\Enums\DocumentTraineesRequestUpdatesApprovalStatus::PENDING_APPROVAL)
                                You have requested an update for this document. Kindly wait for the staff's approval.
                            @endif
                        </p>
                    @endif
                @endif
            </div>
        @endforeach
    @else
        <p class="dark:text-white">No documents found.</p>
    @endif
    </div>
</div>
