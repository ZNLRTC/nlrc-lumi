<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Grouping\GroupTrainee;
use Carbon\CarbonPeriod;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Support\Facades\Auth;

class NewGroupTransfersChart extends ChartWidget
{
    protected static ?string $heading = 'New Batch/Group Transfers';

    protected static ?string $pollingInterval = null; // Avoid refreshing the chart data

    public ?string $filter = 'this_week';

    public $start_value;
    public $end_value;
    public $extra_description;

    protected function getData(): array
    {
        $today = now();

        $data = Trend::query(GroupTrainee::isActive());

        if (in_array($this->filter, ['last_week', 'this_week', 'last_month', 'this_month'])) {
            if ($this->filter == 'last_week') {
                $this->start_value = $today->copy()->subWeek(1)->startOfWeek();
                $this->end_value = $today->copy()->subWeek(1)->endOfWeek();
            } else if ($this->filter == 'last_month') {
                $days_of_previous_month = $today->copy()->subMonth(1)->daysInMonth;

                $this->start_value = $today->copy()->subDays($days_of_previous_month)->startOfMonth();
                $this->end_value = $today->copy()->subDays($days_of_previous_month)->endOfMonth();
            } else {
                if ($this->filter == 'this_week') {
                    $this->start_value = $today->copy()->startOfWeek();
                    $this->end_value = $today->copy()->endOfWeek();
                } else if ($this->filter == 'this_month') {
                    $this->start_value = $today->copy()->startOfMonth();
                    $this->end_value = $today->copy()->endOfMonth();
                }
            }

            $data = $data->between(start: $this->start_value, end: $this->end_value)
                ->perDay();

            $period_iterator = 'day';

            if (in_array($this->filter, ['last_week', 'this_week'])) {
                $this->extra_description = 'between ' .$this->start_value->format('M d, Y'). ' - ' .$this->end_value->format('M d, Y'). '.';
            } else if (in_array($this->filter, ['last_month', 'this_month'])) {
                $this->extra_description = 'for ' .$this->start_value->format('F Y'). '.';
            }
        } else if (in_array($this->filter, ['last_year', 'this_year'])) {
            $this->start_value = $today->copy()->startOfYear();
            $this->end_value = $today->copy()->endOfYear();

            if ($this->filter == 'last_year') {
                $this->start_value = $this->start_value->copy()->subYear(1);
                $this->end_value = $this->end_value->copy()->subYear(1);
            }

            $data = $data->between(start: $this->start_value, end: $this->end_value)
                ->perMonth();

            $period_iterator = 'month';
            $this->extra_description = 'per month for ' .$this->start_value->format('Y'). '.';
        }

        $data = $data->count();

        $period = CarbonPeriod::create($this->start_value, '1 ' .$period_iterator, $this->end_value);
        $labels = [];

        foreach ($period as $date) {
            if (in_array($this->filter, ['last_week', 'this_week'])) {
                $labels[] = $date->format('M d');
            } else if (in_array($this->filter, ['last_month', 'this_month'])) {
                $labels[] = $date->format('d');
            } else if (in_array($this->filter, ['last_year', 'this_year'])) {
                $labels[] = $date->format('M Y');
            }
        }

        return [
            'datasets' => [
                [
                    'label' => 'Transferred trainees',
                    'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
                    'borderColor' => '#4ade80', // success-400
                    'backgroundColor' => '#f0fdf4', // success-50
                ],
            ],
            'labels' => $labels
        ];
    }

    public static function canView(): bool
    {
        return Auth::user()->hasAnyRole(['Admin', 'Manager', 'Staff']);
    }

    public function getColumnSpan(): int | string | array
    {
        return 2;
    }

    public function getDescription(): ?string
    {
        return 'Trainees transferred to new batches/groups ' .$this->extra_description;
    }

    protected function getFilters(): ?array
    {
        return [
            'last_year' => 'Last year',
            'this_year' => 'This year',
            'last_month' => 'Last month',
            'this_month' => 'This month',
            'last_week' => 'Last week',
            'this_week' => 'This week'
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
