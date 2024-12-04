<?php

namespace App\Http\Controllers\Generate;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\stock_adjustment;
use App\Models\stock;

use Carbon\Carbon;

class GenerateID extends Controller
{
    public function getIDAdjustment($request)
    {
        $count = stock_adjustment::max('id') + 1;
        $formattedNumber = str_pad($count, 5, '0', STR_PAD_LEFT);
        $date = Carbon::now();
        $monthNumber = $date->month; 
        $yearsNumber = $date->year;
       
        // Konversi angka bulan ke angka Romawi
        $romanMonth = $this->convertMonthToRoman($monthNumber);
        
        // format number/SA-PIP/month/years
        $id = $formattedNumber.'/'.'SA-PIP'.$romanMonth.'/'.$yearsNumber;
        return $id;
    }

    public function getIDGoodsReturn($request)
    {
        $count = stock_goods_return::max('id') + 1;
        $formattedNumber = str_pad($count, 5, '0', STR_PAD_LEFT);
        $date = Carbon::now();
        $monthNumber = $date->month; 
        $yearsNumber = $date->year;
       
        // Konversi angka bulan ke angka Romawi
        $romanMonth = $this->convertMonthToRoman($monthNumber);
        
        // format number/SA-PIP/month/years
        $id = 'RT-PIP-'.$yearsNumber.'-'.$romanMonth.'-'. $formattedNumber;
        return $id;
    }

    // function Optioanl
    private function convertMonthToRoman($monthNumber) {
        $romanNumerals = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
            7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'
        ];
        return $romanNumerals[$monthNumber] ?? '';
    }
}
