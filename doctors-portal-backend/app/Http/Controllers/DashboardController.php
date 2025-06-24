<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\ShipmentUpdate;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{

public function stats(Request $request)
    {
        $today = now();
        $startOfToday = $today->copy()->startOfDay();
        $endOfToday = $today->copy()->endOfDay();

        $startOfThisMonth = $today->copy()->startOfMonth();
        $endOfLastMonth = $startOfThisMonth->copy()->subSecond();
        $startOfLastMonth = $startOfThisMonth->copy()->subMonth()->startOfMonth();

        // === Active Doctors ===
        $thisMonthDoctorCount = User::role('doctor')
            ->whereHas('shipmentUpdates', function ($q) use ($startOfThisMonth) {
                $q->where('created_at', '>=', $startOfThisMonth);
            })
            ->count();

        $lastMonthDoctorCount = User::role('doctor')
            ->whereHas('shipmentUpdates', function ($q) use ($startOfLastMonth, $endOfLastMonth) {
                $q->whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth]);
            })
            ->count();

        $activeDoctorsChange = $this->calculateChange($thisMonthDoctorCount, $lastMonthDoctorCount);

        // === Prescriptions Today ===
        $prescriptionsToday = ShipmentUpdate::whereBetween('created_at', [$startOfToday, $endOfToday])->count();

        // === Completed Today ===
        $completedToday = ShipmentUpdate::where('status', 'delivery_confirmation')
            ->whereBetween('updated_at', [$startOfToday, $endOfToday])
            ->count();

        // === Prescriptions This Month ===
        $prescriptionsThisMonth = ShipmentUpdate::whereBetween('created_at', [$startOfThisMonth, $today])->count();

        $prescriptionsLastMonth = ShipmentUpdate::whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])->count();
        $prescriptionsChange = $this->calculateChange($prescriptionsThisMonth, $prescriptionsLastMonth);

        return response()->json([
            'active_doctors' => [
                'count' => $thisMonthDoctorCount,
                'change' => $activeDoctorsChange
            ],
            'prescriptions_today' => [
                'count' => $prescriptionsToday,
                'change' => null // optional: compare to yesterday if needed
            ],
            'completed_today' => [
                'count' => $completedToday,
                'change' => null // optional: compare to yesterday if needed
            ],
            'this_month' => [
                'count' => $prescriptionsThisMonth,
                'change' => $prescriptionsChange
            ]
        ]);
    }

    private function calculateChange($current, $previous)
    {
        if ($previous == 0) {
            return $current > 0 ? 100 : 0;
        }

        return round((($current - $previous) / $previous) * 100, 2);
    }


}
