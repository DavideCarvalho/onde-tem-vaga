<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Controller;
use App\Data\GetEarningsData;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GetEarningsController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $user = $request->user();
        $today = now()->startOfDay();
        $weekStart = now()->startOfWeek();
        $monthStart = now()->startOfMonth();

        $calculateEarnings = function ($startDate) use ($user) {
            return $user->parking->parkingSpots()
                ->whereHas('parkingRecords', function ($query) use ($startDate) {
                    $query->whereDate('exit_time', '>=', $startDate)
                        ->whereNotNull('exit_time')
                        ->where('is_paid', true);
                })
                ->withSum('parkingRecords', 'total_amount')
                ->get()
                ->sum('parking_records_sum_total_amount');
        };

        // Daily chart data (by hour)
        $dailyEarnings = $user->parking->parkingSpots()
            ->whereHas('parkingRecords', function ($query) use ($today) {
                $query->whereDate('exit_time', '>=', $today)
                    ->whereNotNull('exit_time')
                    ->where('is_paid', true);
            })
            ->join('parking_records', 'parking_spots.id', '=', 'parking_records.parking_spot_id')
            ->select(
                DB::raw('EXTRACT(HOUR FROM exit_time) as hour'),
                DB::raw('SUM(total_amount) as value')
            )
            ->groupBy('hour')
            ->orderBy('hour')
            ->get()
            ->keyBy('hour');

        // Create array with all hours (0-23) and fill with earnings data
        $dailyChart = collect(range(0, 23))->map(function ($hour) use ($dailyEarnings) {
            return [
                'hour' => $hour,
                'value' => (float) ($dailyEarnings->get($hour)?->value ?? 0),
            ];
        })->values()->all();

        // Weekly chart data (by day)
        $weeklyChartRaw = $user->parking->parkingSpots()
            ->whereHas('parkingRecords', function ($query) use ($weekStart) {
                $query->whereDate('exit_time', '>=', $weekStart)
                    ->whereNotNull('exit_time')
                    ->where('is_paid', true);
            })
            ->join('parking_records', 'parking_spots.id', '=', 'parking_records.parking_spot_id')
            ->select(
                DB::raw('DATE(exit_time) as day'),
                DB::raw('SUM(total_amount) as value')
            )
            ->groupBy('day')
            ->orderBy('day')
            ->get()
            ->map(fn ($record) => [
                'day' => $record->day,
                'value' => (float) $record->value,
            ]);

        // Fill all days of the current week
        $daysInWeek = collect();
        $start = $weekStart->copy();
        $end = now();
        while ($start->lte($end)) {
            $daysInWeek->push($start->format('Y-m-d'));
            $start->addDay();
        }
        $weeklyChartIndexed = $weeklyChartRaw->keyBy('day');
        $weeklyChart = $daysInWeek->map(fn($day) => [
            'day' => $day,
            'value' => (float) ($weeklyChartIndexed->get($day)['value'] ?? 0),
        ])->values()->all();

        // Monthly chart data (by day)
        $monthlyChartRaw = $user->parking->parkingSpots()
            ->whereHas('parkingRecords', function ($query) use ($monthStart) {
                $query->whereDate('exit_time', '>=', $monthStart)
                    ->whereNotNull('exit_time')
                    ->where('is_paid', true);
            })
            ->join('parking_records', 'parking_spots.id', '=', 'parking_records.parking_spot_id')
            ->select(
                DB::raw('DATE(exit_time) as day'),
                DB::raw('SUM(total_amount) as value')
            )
            ->groupBy('day')
            ->orderBy('day')
            ->get()
            ->map(fn ($record) => [
                'day' => $record->day,
                'value' => (float) $record->value,
            ]);

        // Fill all days of the current month
        $daysInMonth = collect();
        $start = $monthStart->copy();
        $end = now()->endOfMonth();
        while ($start->lte($end)) {
            $daysInMonth->push($start->format('Y-m-d'));
            $start->addDay();
        }
        $monthlyChartIndexed = $monthlyChartRaw->keyBy('day');
        $monthlyChart = $daysInMonth->map(fn($day) => [
            'day' => $day,
            'value' => (float) ($monthlyChartIndexed->get($day)['value'] ?? 0),
        ])->values()->all();

        return response()->json(GetEarningsData::make(
            today: $calculateEarnings($today),
            week: $calculateEarnings($weekStart),
            month: $calculateEarnings($monthStart),
            chart: [
                'day' => $dailyChart,
                'week' => $weeklyChart,
                'month' => $monthlyChart,
            ],
        ));
    }
}
