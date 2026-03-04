<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LeaveBalanceController extends Controller
{
    public function calculate(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'years_of_service' => ['required', 'integer', 'min:1'],
            'months_worked' => ['required', 'integer', 'min:0', 'max:12'],
            'taken_leave_days' => ['nullable', 'numeric', 'min:0'],
            'previous_year_unused_days' => ['nullable', 'numeric', 'min:0'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $yearsOfService = (int) $request->integer('years_of_service');
        $monthsWorked = (int) $request->integer('months_worked');
        $takenLeaveDays = (float) $request->input('taken_leave_days', 0);
        $previousYearUnusedDays = (float) $request->input('previous_year_unused_days', 0);

        $annualEntitlement = min(20 + max(0, $yearsOfService - 1), 30);
        $earnedThisYear = round(($annualEntitlement * $monthsWorked) / 12, 2);
        $carriedForward = min($previousYearUnusedDays, 5);
        $totalAvailable = round($earnedThisYear + $carriedForward, 2);
        $currentBalance = round($totalAvailable - $takenLeaveDays, 2);

        return response()->json([
            'input' => [
                'years_of_service' => $yearsOfService,
                'months_worked' => $monthsWorked,
                'taken_leave_days' => $takenLeaveDays,
                'previous_year_unused_days' => $previousYearUnusedDays,
            ],
            'result' => [
                'annual_entitlement_days' => $annualEntitlement,
                'earned_this_year_days' => $earnedThisYear,
                'carried_forward_days' => $carriedForward,
                'total_available_days' => $totalAvailable,
                'current_balance_days' => $currentBalance,
            ],
        ]);
    }
}
