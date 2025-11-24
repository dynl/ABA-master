<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\VaccineStock;
use Illuminate\Support\Facades\Validator;

class VaccineStockController extends Controller
{
    // Update or Create stock for a specific day
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
            'amount' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        // updateOrCreate checks if 'date' exists. If yes, updates 'quantity'. If no, creates new.
        $stock = VaccineStock::updateOrCreate(
            ['date' => $request->date],
            ['quantity' => $request->amount]
        );

        return response()->json([
            'success' => true,
            'message' => 'Vaccine stock updated',
            'data' => $stock
        ]);
    }

    // Get stock list
    public function index()
    {
        // FIX: Removed ->whereDate('date', '>=', now()) to solve timezone issues
        $stocks = VaccineStock::orderBy('date', 'desc')
            ->take(50) // Just take the last 50 entries
            ->get();

        return response()->json(['success' => true, 'data' => $stocks]);
    }
}
