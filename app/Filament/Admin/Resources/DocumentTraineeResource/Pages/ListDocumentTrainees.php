<?php

namespace App\Filament\Admin\Resources\DocumentTraineeResource\Pages;

use App\Enums\DocumentTraineesRequestUpdatesApprovalStatus;
use App\Filament\Admin\Resources\DocumentTraineeResource;
use App\Models\Documents\AgencyDocumentRequired;
use App\Models\Documents\DocumentTrainee;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use Filament\Resources\Pages\ListRecords;

class ListDocumentTrainees extends ListRecords
{
    protected static string $resource = DocumentTraineeResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make(),
            // Users with completed documents
            'completed-all-requirements' => Tab::make()
                ->modifyQueryUsing(function (Builder $query) {
                    $approved_document_trainees = DocumentTrainee::select(['trainee_id', 'trainees.agency_id'])
                        ->join('trainees', 'document_trainees.trainee_id', '=', 'trainees.id')
                        ->where('status', DocumentTraineesRequestUpdatesApprovalStatus::APPROVED)
                        ->get();
                    $approved_document_trainees_as_array = $approved_document_trainees->toArray();

                    $trainee_ids = array_unique($approved_document_trainees->pluck('trainee_id')->toArray());
                    $trainee_ids_with_all_approved_documents = [];

                    // Filter only trainees with all approved documents required by agency
                    foreach ($trainee_ids as $trainee_id) {
                        $trainee_approved_documents = array_values(array_filter($approved_document_trainees_as_array, function($val) use ($trainee_id) {
                            return $trainee_id == $val['trainee_id'];
                        }));

                        $required_count_for_agency = AgencyDocumentRequired::where('agency_id', $trainee_approved_documents[0]['agency_id'])
                            ->get()
                            ->count();

                        if ($required_count_for_agency == count($trainee_approved_documents)) {
                            array_push($trainee_ids_with_all_approved_documents, $trainee_id);
                        }
                    }

                    return $query->whereIn('trainee_id', $trainee_ids_with_all_approved_documents)
                        ->where('status', DocumentTraineesRequestUpdatesApprovalStatus::APPROVED);
                }),
            'recently-uploaded' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->whereRaw('created_at = updated_at')),
        ];
    }
}
