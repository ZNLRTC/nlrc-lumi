<?php

namespace App\Filament\Admin\Resources\DocumentTraineeResource\Widgets;

use App\Enums\DocumentTraineesStatus;
use App\Models\Documents\DocumentTrainee;
use App\Models\User;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class DocumentTraineeWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $base_query = DocumentTrainee::select(['id'])->submittedThisMonth();

        $user = User::findOrFail(Auth::user()->id);

        if ($user->can('viewWidgetStats', DocumentTrainee::class)) {
            $uploaded_documents_this_month = $base_query->clone()
                ->get()
                ->count();

            if ($uploaded_documents_this_month == 0) {
                return [];
            } else {
                $approved_documents_count = $base_query->clone()
                    ->where('status', DocumentTraineesStatus::APPROVED)
                    ->get()
                    ->count();

                $pending_documents_count = $base_query->clone()
                    ->where('status', DocumentTraineesStatus::PENDING_CHECKING)
                    ->get()
                    ->count();

                $re_upload_needed_documents_count = $base_query->clone()
                    ->where('status', DocumentTraineesStatus::RE_UPLOAD_NEEDED)
                    ->get()
                    ->count();

                return [
                    Stat::make('Approved documents this month', $approved_documents_count)
                        ->description(round(($approved_documents_count / $uploaded_documents_this_month * 100), 2). '%')
                        ->descriptionIcon('heroicon-m-clipboard-document', IconPosition::Before)
                        ->color('success'),
                    Stat::make('Pending documents this month', $pending_documents_count)
                        ->description(round(($pending_documents_count / $uploaded_documents_this_month * 100), 2). '%')
                        ->descriptionIcon('heroicon-m-clipboard-document', IconPosition::Before)
                        ->color('warning'),
                    Stat::make('Re-upload documents this month', $re_upload_needed_documents_count)
                        ->description(round(($re_upload_needed_documents_count / $uploaded_documents_this_month * 100), 2). '%')
                        ->descriptionIcon('heroicon-m-clipboard-document', IconPosition::Before)
                        ->color('danger'),
                ];
            }
        } else {
            return [];
        }
    }
}
