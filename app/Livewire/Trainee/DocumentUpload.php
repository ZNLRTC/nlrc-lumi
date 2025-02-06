<?php

namespace App\Livewire\Trainee;

use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\WithFileUploads;
use App\Models\Documents\Document;
use Illuminate\Support\Facades\Auth;
use App\Enums\DocumentTraineesStatus;
use Illuminate\Support\Facades\Storage;
use App\Models\Documents\DocumentTrainee;
use App\Models\Documents\AgencyDocumentRequired;
use App\Models\Documents\DocumentTraineesRequestUpdate;

class DocumentUpload extends Component
{
    use WithFileUploads;

    public $show_filter_modal = false;
    public $is_filtered = false;
    public $document_trainee_status = 'show-all';

    // This property is used to retain the last value of the filter select
    public $document_trainee_status_stored = 'show-all';

    public $documents;
    public $uploadedDocuments;
    public $documentFiles = [];
    public $temporaryUrls = [];
    
    public $completion_progress = 0;
    public $completion_progress_text = '';

    public $showDeleteModal = false;
    public $documentIdToDelete;
    public $document_name_to_be_deleted;

    // Request update
    public $document_trainees_request_updates;
    public $document_trainee_ids = [];
    public $document_trainee_id = 0;
    public $show_request_update_modal = false;
    public $request_update_reason = '';
    public $is_update_requested = false;

    // Listen to dispatched file upload events and call the method
    // REF: https://laracasts.com/discuss/channels/livewire/laravel-livewire-show-success-message-after-file-upload?page=1&replyId=884269
    public $listeners = ['upload:generatedSignedUrl' => 'clear_error_messages'];

    public function mount()
    {
        $trainee = Auth::user()->trainee;
        $this->documents = AgencyDocumentRequired::where('agency_id', $trainee->agency_id)
            // TODO: Something like this would implement the document overrides but it's work in progress
            // ->leftJoin('document_trainee_overrides', function ($join) use ($trainee) {
            //     $join->on('agency_document_required.document_id', '=', 'document_trainee_overrides.document_id')
            //         ->where('document_trainee_overrides.trainee_id', '=', $trainee->id);
            // })
            // ->selectRaw('agency_document_required.*, COALESCE(document_trainee_overrides.required, agency_document_required.required) as required')
            ->get();
        $this->uploadedDocuments = DocumentTrainee::where('trainee_id', $trainee->id)
            ->get()
            ->keyBy('document_id');

        $approved_documents = $this->uploadedDocuments->select(['id', 'status'])
            ->filter(fn (array $doc) => $doc['status'] == DocumentTraineesStatus::APPROVED);

        $this->get_document_completion_progress($approved_documents);
    }

    public function get_document_completion_progress($approved_documents)
    {
        $completed_documents_count = count($approved_documents);
        $required_documents_for_agency_count = count($this->documents->toArray());

        if ($completed_documents_count > 0) {
            $this->completion_progress = round(($completed_documents_count / $required_documents_for_agency_count) * 100);
        }

        $this->completion_progress_text = $completed_documents_count. ' of ' .$required_documents_for_agency_count. ' documents completed';

        $this->generateTemporaryUrls();
    }

    // Check fi the doc is already in the database
    public function hasUploadedFile($documentId)
    {
        $trainee = Auth::user()->trainee;
        return DocumentTrainee::where('trainee_id', $trainee->id)
            ->where('document_id', $documentId)
            ->exists();
    }

    public function saveDocument($documentId)
    {
        $agency_document = AgencyDocumentRequired::findOrFail($documentId, ['document_id']);
        $document = Document::findOrFail($agency_document->document_id);

        $this->validate(
            ['documentFiles.' .$documentId => 'required|file|max:2048|mimes:pdf,gif,jpg,jpeg,png'],
            ['documentFiles.' .$documentId. '.mimes' => 'The :attribute field accepts .pdf, .gif, .jpg, .jpeg, and .png files only.'],
            ['documentFiles.' .$documentId => $document->name]
        );

        $file = $this->documentFiles[$documentId];
        $trainee = Auth::user()->trainee;

        $document_name_shorthand = '';

        switch ($document->id) {
            case 1: $document_name_shorthand = 'passport'; break;
            case 2: $document_name_shorthand = 'visa'; break;
            case 3: $document_name_shorthand = 'cv'; break;
            case 4: $document_name_shorthand = 'tor'; break;
            case 5: $document_name_shorthand = 'diploma'; break;
            case 6: $document_name_shorthand = 'coe'; break;
            case 7: $document_name_shorthand = 'board'; break;
            case 8: $document_name_shorthand = 'license'; break;
            case 9: $document_name_shorthand = 'nbi'; break;
            case 10: $document_name_shorthand = 'marriage'; break;
            case 11: $document_name_shorthand = 'medical'; break;
            case 12: $document_name_shorthand = 'covid'; break;
            case 13: $document_name_shorthand = 'immunization'; break;
            case 14: $document_name_shorthand = 'premed'; break;
            case 15: $document_name_shorthand = 'nc2'; break;
        }

        // The file goes to S3
        $file_name = strtolower($trainee->first_name. '_' .$trainee->last_name. '_' .$document_name_shorthand. '_' .date('YmdHis'). '.' .$file->getClientOriginalExtension());
        $file_path = $file->storeAs('/', $file_name, 'documents');

        DocumentTrainee::create([
            'trainee_id' => $trainee->id,
            'document_id' => $document->id,
            'url' => $file_path,
        ]);

        // Clear uploaded file and refresh the lists
        unset($this->documentFiles[$documentId]);
        $this->documents = AgencyDocumentRequired::where('agency_id', $trainee->agency_id)
            ->get();
        $this->uploadedDocuments = DocumentTrainee::where('trainee_id', $trainee->id)
            ->get()
            ->keyBy('document_id');

        $this->generateTemporaryUrls();

        $this->dispatch('document-uploaded', documentId: $agency_document->document_id);
    }

    public function deleteDocument()
    {
        $trainee = Auth::user()->trainee;
        $documentTrainee = $this->uploadedDocuments->get($this->documentIdToDelete);
    
        // There's an event in the DocumentTrainee model (App\Models\Documents) that will delete the file in the storage when an entry is deleted here
        $documentTrainee->delete();
    
        // Refresh stuff
        $this->uploadedDocuments = DocumentTrainee::where('trainee_id', $trainee->id)
            ->get()
            ->keyBy('document_id');
    
        $this->showDeleteModal = false;

        $this->dispatch('document-deleted', documentId: $this->documentIdToDelete);
    }

    public function confirmDelete($documentId)
    {
        $document = Document::findOrFail($documentId);

        $this->showDeleteModal = true;
        $this->documentIdToDelete = $documentId;
        $this->document_name_to_be_deleted = $document->name;
    }

    // Temporary pre-signed URLs for uploaded documents so we won't be accused of leaking 4000 person's passport pages via S3
    public function generateTemporaryUrls()
    {
        $this->temporaryUrls = $this->uploadedDocuments->mapWithKeys(function ($document) {
            return [$document->document_id => Storage::disk('documents')->temporaryUrl($document->url, now()->addMinutes(5))];
        })->toArray();
    }

    public function request_update_modal($document_trainee_id)
    {
        $this->show_request_update_modal = true;
        $this->document_trainee_id = $document_trainee_id;
    }

    public function create_request_update()
    {
        $this->validate([
            'document_trainee_id' => 'required|exists:document_trainees,id',
            'request_update_reason' => 'required'
        ]);

        DocumentTraineesRequestUpdate::create([
            'document_trainee_id' => $this->document_trainee_id,
            'reason' => $this->request_update_reason
        ]);

        $this->reset();
        $this->show_request_update_modal = false;
        $this->is_update_requested = true;

        $this->dispatch('request-update-created');
    }

    public function filter_documents(): void
    {
        $this->document_trainee_status_stored = $this->document_trainee_status;
        $this->is_filtered = $this->document_trainee_status == 'show-all' ? false : true;
        $this->is_update_requested = false;

        $this->dispatch('filtered-documents');
    }

    public function reset_filters(): void
    {
        $this->document_trainee_status = 'show-all';
        $this->document_trainee_status_stored = $this->document_trainee_status;
        $this->is_filtered = false;
        $this->is_update_requested = false;

        $this->dispatch('filtered-documents');
    }

    public function clear_error_messages($name)
    {
        // $name is the string 'documentFiles.' .$documentId, eg. 'documentFiles.1'
        $this->resetValidation($name); // Clear error message
    }

    #[On('document-deleted')]
    #[On('document-uploaded')]
    #[On('filtered-documents')]
    #[On('request-update-created')]
    public function render()
    {
        $trainee = Auth::user()->trainee;

        $document_trainee_instance = new DocumentTrainee;
        $document_trainees = $document_trainee_instance->where('trainee_id', $trainee->id);

        $agency_document_required_instance = new AgencyDocumentRequired;
        $agency_document_requireds = $agency_document_required_instance->where('agency_id', $trainee->agency_id);

        if ($this->is_filtered) {
            $document_trainees = $document_trainees->where('status', $this->document_trainee_status_stored);
            $document_trainees_array = $document_trainees
                ->get()
                ->keyBy('document_id')
                ->toArray();
            $document_ids = array_column($document_trainees_array, 'document_id');

            $agency_document_requireds = $agency_document_requireds->whereIn('document_id', $document_ids);
        }

        $this->uploadedDocuments = $document_trainees->get()
            ->keyBy('document_id');
        $this->documents = $agency_document_requireds->get();

        $approved_documents = $this->uploadedDocuments->select(['id', 'status'])
            ->filter(fn (array $doc) => $doc['status'] == DocumentTraineesStatus::APPROVED);

        $this->document_trainee_ids = array_column($approved_documents->toArray(), 'id');
            
        if ($this->is_update_requested) {
            $this->get_document_completion_progress($approved_documents);
        }

        $this->document_trainees_request_updates = DocumentTraineesRequestUpdate::select(['document_trainee_id', 'approval_status'])
            ->whereIn('document_trainee_id', $this->document_trainee_ids)
            ->get();

        // Retain selected states of filter dropdown from the last time the user pressed Filter
        $this->document_trainee_status = $this->document_trainee_status_stored;

        return view('livewire.trainee.document-upload');
    }

}