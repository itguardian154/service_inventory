<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\stock;

class UpdateStockDataController extends Controller
{
    public function updateStock(Request $request)
    {
        try {
            // Validasi input
            $request->validate([
                'code' => 'required|string',
                'name' => 'required|string'
            ]);

            $code = $request->code;
            $name = $request->name;

            // Cek apakah stock ada
            $stock = stock::where('code', $code)->first();

            if (!$stock) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Stock code not found'
                ], 404);
            }

            // Update
            $stock->items = $name;
            $stock->save();

            return response()->json([
                'status'  => 'success',
                'message' => 'Stock updated successfully',
                'data'    => $stock
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Server error',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
