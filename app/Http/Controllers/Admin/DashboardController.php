<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Form;
use App\Models\Submission;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $user = auth()->user();
        $isAdmin = $user->isAdmin();
        $reports = $isAdmin
            ? Submission::query()
            : $user->submissions();
        $startDate = now()->subDays(6)->startOfDay();
        $dailyCounts = (clone $reports)
            ->where('created_at', '>=', $startDate)
            ->selectRaw('DATE(created_at) as report_date, COUNT(*) as total')
            ->groupBy('report_date')
            ->pluck('total', 'report_date');
        $chartDays = collect(range(6, 0))->map(function (int $daysAgo) use ($dailyCounts) {
            $date = now()->subDays($daysAgo)->startOfDay();

            return [
                'label' => $date->translatedFormat('D'),
                'date' => $date->toDateString(),
                'total' => (int) $dailyCounts->get($date->toDateString(), 0),
            ];
        });
        $chartMaximum = max(1, (int) $chartDays->max('total'));
        $chartDays = $chartDays->map(function (array $day) use ($chartMaximum) {
            $day['height'] = $day['total'] > 0
                ? max(8, (int) round($day['total'] / $chartMaximum * 100))
                : 0;

            return $day;
        });
        $totalReports = (clone $reports)->count();
        $weeklyReports = (clone $reports)->where('created_at', '>=', $startDate)->count();
        $latestReports = (clone $reports)->with('form')->latest()->limit(6)->get();

        if ($isAdmin) {
            return view('dashboard', [
                'formCount' => Form::count(),
                'submissionCount' => $totalReports,
                'recentSubmissionCount' => $weeklyReports,
                'latestSubmissions' => $latestReports,
                'chartDays' => $chartDays,
            ]);
        }

        return view('dashboard-user', [
            'submissionCount' => $totalReports,
            'recentSubmissionCount' => $weeklyReports,
            'latestSubmissions' => $latestReports,
            'chartDays' => $chartDays,
        ]);
    }
}
