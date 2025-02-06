<?php

namespace App\Filament\Admin\Resources\TraineeResource\Pages;

use App\Filament\Admin\Resources\TraineeResource;
use App\Filament\Imports\TraineeImporter;
use App\Models\Trainee;
use App\Models\TraineesVerifiedRequest;
use Filament\Actions\CreateAction;
use Filament\Actions\ImportAction;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class ListTrainees extends ListRecords
{
    protected static string $resource = TraineeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            ImportAction::make('import_trainees_from_csv')
                ->importer(TraineeImporter::class)
                ->label('Import trainees from CSV')
                ->color('primary')
                ->hidden(fn () => Auth::user()->hasRole('Observer')),
        ];
    }

    public function getTabs(): array
    {
        $available_tabs = [
            'active-training' => Tab::make()
                ->label('Active in training')
                ->modifyQueryUsing(fn (Builder $query) => $query->isActive()
                    ->whereDoesntHave('activeGroups', fn (Builder $query) =>
                        $query->where('name', 'Kyl mä hoidan')
                    )
                )
                // The number in the badge is cached because it's calculated allllllll the time and it's not that crucial to have it up-to-date alll the time
                ->badge(function () {
                    if (Auth::user()->hasAnyRole(['Admin', 'Manager', 'Staff'])) {
                        return Cache::remember('activeTrainingCount', 60, fn () =>
                            Trainee::isActive()
                                ->whereDoesntHave('activeGroups', fn (Builder $query) => $query->where('name', 'Kyl mä hoidan'))
                                ->count()
                        );
                    }
                }),
            'non-KMH' => Tab::make()
                ->label('Active in KMH')
                ->modifyQueryUsing(fn (Builder $query) => $query->isActive()
                    ->whereHas('activeGroups', fn (Builder $query) =>
                        $query->where('name', 'Kyl mä hoidan')
                    )
                )
                ->badge(function () {
                    if (Auth::user()->hasAnyRole(['Admin', 'Manager', 'Staff'])) {
                        return Cache::remember('non-KMHCount', 60, fn () =>
                            Trainee::isActive()
                                ->whereHas('activeGroups', fn (Builder $query) => $query->where('name', 'Kyl mä hoidan'))
                                ->count()
                        );
                    }
                }),
            'active-all' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->isActive())
                ->label('Active (all)'),
            'inactive' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->isNotActive())
                ->label('Inactive (all)'),
            'all' => Tab::make(),
        ];

        $verified_request_tabs = [];

        if (!Auth::user()->hasRole('Observer')) {
            $verified_request_tabs = [
                'no-verification-submitted' => Tab::make()
                    ->modifyQueryUsing(fn (Builder $query) => $query->whereDoesntHave('verified_requests')),
                'pending-verification' => Tab::make()
                    ->modifyQueryUsing(fn (Builder $query) => $query->whereHas('verified_requests',
                        fn (Builder $query) => $query->latest()->where('is_verified', 0)
                    ))
                    ->badgeColor('danger')
                    ->badge(
                        Cache::remember('pendingVerifiedRequestsCount', 60, fn () =>
                            TraineesVerifiedRequest::query()->where('is_verified', 0)
                                ->count()
                        )
                    )
            ];
        }

        return array_merge($available_tabs, $verified_request_tabs);
    }
}
