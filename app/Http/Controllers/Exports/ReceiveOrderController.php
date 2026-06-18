<?php

namespace App\Http\Controllers\Exports;

use App\Exports\ReceiveOrderExport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReceiveOrderController extends Controller
{
    public function export(Request $request)
    {
        $request->validate([
            'date_from' => 'required|date',
            'date_to'   => 'required|date',
        ]);

        $fileName = 'receive_order_' .
                    $request->date_from .
                    '_to_' .
                    $request->date_to .
                    '.xlsx';

        return Excel::download(
            new ReceiveOrderExport(
                $request->date_from,
                $request->date_to
            ),
            $fileName
        );
    }
}