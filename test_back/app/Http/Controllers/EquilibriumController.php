<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EquilibriumController extends Controller
{
    public function findEquilibrium(Request $request)
    {
        $arr = $request->input('array');

        // Validation
        if (!is_array($arr)) {
            return response()->json([
                'error' => 'Input must be an array'
            ], 400);
        }

        $totalSum = array_sum($arr);
        $leftSum = 0;
        $result = [];

        foreach ($arr as $index => $value) {
            $rightSum = $totalSum - $leftSum - $value;

            if ($leftSum == $rightSum) {
                $result[] = $index;
            }

            $leftSum += $value;
        }

        return response()->json([
            'equilibrium_indexes' => !empty($result) ? $result : null
        ]);
    }
}