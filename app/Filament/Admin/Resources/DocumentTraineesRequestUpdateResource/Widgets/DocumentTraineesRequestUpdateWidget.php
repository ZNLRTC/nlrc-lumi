<?php

namespace App\Filament\Admin\Resources\DocumentTraineesRequestUpdateResource\Widgets;

use App\Enums\DocumentTraineesRequestUpdatesApprovalStatus;
use App\Models\Documents\DocumentTraineesRequestUpdate;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class DocumentTraineesRequestUpdateWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $stats = [];

        $user = User::findOrFail(Auth::user()->id);

        if ($user->can('viewWidgetStats', DocumentTraineesRequestUpdate::class)) {
            $pending_document_request_updates = DocumentTraineesRequestUpdate::select(['id'])
                ->where('approval_status', DocumentTraineesRequestUpdatesApprovalStatus::PENDING_APPROVAL)
                ->get();

            if ($pending_document_request_updates->isNotEmpty()) {
                $stats = [Stat::make('Pending document request updates', $pending_document_request_updates->count())];
            }
        }

        return $stats;
    }
}
