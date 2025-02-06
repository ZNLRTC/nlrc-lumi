<?php

namespace App\Livewire\Dashboard;

use App\Enums\DocumentTraineesStatus;
use App\Models\Documents\DocumentTrainee;
use App\Models\Trainee;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class DocumentCompletionProgress extends Component
{
    public $trainee_documents = [];
    public $completion_progress = 0;
    public $completion_progress_text = 0;

    public function render()
    {
        $trainee = Auth::user()->trainee;
        if ($trainee) {
            $agency_documents = Trainee::get_required_documents_of_agency_for_trainee($trainee->id)
                ->toArray();

            $uploaded_documents = DocumentTrainee::where('trainee_id', $trainee->id)
                ->select(['document_trainees.document_id', 'document_trainees.status', 'documents.name'])
                ->join('documents', 'document_trainees.document_id', 'documents.id')
                ->get()
                ->keyBy('document_id');

            $uploaded_docs = $uploaded_documents->toArray();

            foreach ($agency_documents as $doc_name) {
                $filtered = array_values(array_filter($uploaded_docs, function($val) use($doc_name) {
                    return $val['name'] == $doc_name;
                }));

                if ($filtered) {
                    array_push($this->trainee_documents, [
                        'name' => $filtered[0]['name'],
                        'status' => $filtered[0]['status']
                    ]);
                } else {
                    array_push($this->trainee_documents, [
                        'name' => $doc_name,
                        'status' => null
                    ]);
                }
            }

            $completed_documents_count = count($uploaded_documents->filter(fn (DocumentTrainee $doc) =>
                $doc->status == DocumentTraineesStatus::APPROVED
            ));
            $required_documents_for_agency_count = Trainee::get_required_documents_of_agency_for_trainee_count($trainee->id);

            if ($completed_documents_count > 0 && $required_documents_for_agency_count > 0) {
                $this->completion_progress = round(($completed_documents_count / $required_documents_for_agency_count) * 100);
            } else {
                $this->completion_progress = 100; // This avoids division by zero when no required docs is set, but I'm not sure if the result should be 0 or 100 --Mikko
            }

            $this->completion_progress_text = $completed_documents_count. ' of ' .$required_documents_for_agency_count. ' documents completed';

            return view('livewire.dashboard.document-completion-progress');
        }
    }
}
