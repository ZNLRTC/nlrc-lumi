<?php

namespace App\Filament\Admin\Widgets;

use App\Filament\Admin\Resources\TraineeResource;
use App\Models\Trainee;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;

class StatsOverview extends BaseWidget
{
    protected static ?string $pollingInterval = null;

    public static function canView(): bool
    {
        return Auth::user()->hasAnyRole(['Admin', 'Manager', 'Staff']);
    }

    protected function getColumns(): int
    {
        return 2;
    }

    protected function getStats(): array
    {
        $common_classes = 'grid self-center justify-self-start rounded-md text-xs font-medium ring-1 ring-inset px-2 min-w-[theme(spacing.6)] py-1';

        return [
            Stat::make('Deployment', null)
                ->description(new HtmlString('
                    <div class="grid grid-cols-[minmax(30px,_50px)_1fr] gap-x-1 gap-y-3">
                        <span
                            style="--c-50: var(--success-50); --c-400: var(--success-400); --c-600: var(--success-600);"
                            class="' .$common_classes. ' bg-custom-50 text-custom-600 ring-custom-600/60 dark:bg-custom-400/10 dark:text-custom-400 dark:ring-custom-400/40"
                        >' .Trainee::deployedWhen('past')->count(). '</span>
                        <span class="text-xl text-gray-700 dark:text-gray-300">Total trainees deployed</span>

                        <span
                            style="--c-50: var(--warning-50); --c-400: var(--warning-400); --c-600: var(--warning-600);"
                            class="' .$common_classes. ' bg-custom-50 text-custom-600 ring-custom-600/60 dark:bg-custom-400/10 dark:text-custom-400 dark:ring-custom-400/40"
                        >' .Trainee::deployedWhen('future')->count(). '</span>
                        <span class="text-xl text-gray-700 dark:text-gray-300">Upcoming trainees to be deployed</span>
                    </div>
                ')),
            Stat::make('Flagged Trainees', null)
                ->description(new HtmlString('
                    <div class="grid grid-cols-[minmax(30px,_50px)_1fr] gap-x-1 gap-y-3">
                        <span
                            style="--c-50: var(--info-50); --c-400: var(--info-400); --c-600: var(--info-600);"
                            class="' .$common_classes. ' bg-custom-50 text-custom-600 ring-custom-600/60 dark:bg-custom-400/10 dark:text-custom-400 dark:ring-custom-400/40"
                        >' .Trainee::hasFlag('Active')->count(). '</span>
                        <span class="text-xl text-gray-700 dark:text-gray-300">Active</span>

                        <span
                            style="--c-50: var(--danger-50); --c-400: var(--danger-400); --c-600: var(--danger-600);"
                            class="' .$common_classes. ' bg-custom-50 text-custom-600 ring-custom-600/60 dark:bg-custom-400/10 dark:text-custom-400 dark:ring-custom-400/40"
                        >' .Trainee::hasFlag('Inactive')->count(). '</span>
                        <span class="text-xl text-gray-700 dark:text-gray-300">Inactive</span>

                        <span
                            class="' .$common_classes. ' bg-gray-50 text-gray-600 ring-gray-600/60 dark:bg-gray-400/10 dark:text-gray-400 dark:ring-gray-400/40"
                        >' .Trainee::hasFlag('On hold')->count(). '</span>
                        <span class="text-xl text-gray-700 dark:text-gray-300">On hold</span>

                        <span
                            style="--c-50: var(--warning-50); --c-400: var(--warning-400); --c-600: var(--warning-600);"
                            class="' .$common_classes. ' bg-custom-50 text-custom-600 ring-custom-600/60 dark:bg-custom-400/10 dark:text-custom-400 dark:ring-custom-400/40"
                        >' .Trainee::hasFlag('Quit')->count(). '</span>
                        <span class="text-xl text-gray-700 dark:text-gray-300">Quit</span>
                    </div>
                ')),
        ];
    }
}
