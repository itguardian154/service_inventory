<?php

namespace App\Http\Controllers\Generate;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

use App\Models\stock;
use App\Models\stock_adjustment;
use App\Models\receive_order;
use App\Models\store_request;


class GenerateID extends Controller
{
    public function getIDAdjustment($request)
    {
        // $count = stock_adjustment::max('id') + 1;
        // $formattedNumber = str_pad($count, 5, '0', STR_PAD_LEFT);
        // $date = Carbon::now();
        // $monthNumber = $date->month; 
        // $yearsNumber = $date->year;
        $date = Carbon::now();
        $monthNumber = $date->month; 
        $yearsNumber = $date->year;
        $prefixSubs = 'SA-PIP-'.$yearsNumber;
      
        $count = DB::table('stock_adjustment')
        ->select(DB::raw('COUNT(ID) as count'))
        ->where('no_adjustment','like','%'.$prefixSubs.'%')
        ->first();
        $formattedNumber = str_pad($count->count, 6, '0', STR_PAD_LEFT);
       
        // Konversi angka bulan ke angka Romawi
        $romanMonth = $this->convertMonthToRoman($monthNumber);
        
        // format number/SA-PIP/month/years
        $id = 'SA-PIP-'.$yearsNumber.'-'.$romanMonth.'-'. $formattedNumber;
        return $id;
    }

    public function getIDGoodsReturn($request)
    {
        $date = Carbon::now();
        $monthNumber = $date->month; 
        $yearsNumber = $date->year;
        $prefixSubs = 'RT-PIP-'.$yearsNumber;
      
        $count = DB::table('stock_goods_return')
        ->select(DB::raw('COUNT(ID) as count'))
        ->where('no_receive','like','%'.$prefixSubs.'%')
        ->first();
        $formattedNumber = str_pad($count->count, 6, '0', STR_PAD_LEFT);
       
        // Konversi angka bulan ke angka Romawi
        $romanMonth = $this->convertMonthToRoman($monthNumber);
        
        // format number/SA-PIP/month/years
        $id = 'RT-PIP-'.$yearsNumber.'-'.$romanMonth.'-'. $formattedNumber;
        return $id;
    }

    public function getIDReceiveOrder($request)
    {
        $date = Carbon::now();
        $monthNumber = $date->month; 
        $yearsNumber = $date->year;
        $prefixSubs = 'RCV-PIP-'.$yearsNumber;
      
        $count = DB::table('receive_order')
        ->select(DB::raw('COUNT(ID) as count'))
        ->where('no_transaction','like','%'.$prefixSubs.'%')
        ->first();
    
        $formattedNumber = str_pad($count->count, 6, '0', STR_PAD_LEFT);
    
        // Konversi angka bulan ke angka Romawi
        $romanMonth = $this->convertMonthToRoman($monthNumber);
        
        // format RCV-PIP-years-romanMonth-sequenceNumberTable
        $id = 'RCV-PIP-'.$yearsNumber.'-'.$romanMonth.'-'. $formattedNumber;
        return $id;
    }

    public function getIDStoreRequest($request)
    {
        $date = Carbon::now();
        $monthNumber = $date->month; 
        $yearsNumber = $date->year;
        $prefixSubs = 'SRQ-PIP-'.$yearsNumber;
      
        $count = DB::table('store_request')
        ->select(DB::raw('COUNT(ID) as count'))
        ->where('no_transaction','like','%'.$prefixSubs.'%')
        ->first();
    
        $formattedNumber = str_pad($count->count, 6, '0', STR_PAD_LEFT);
    
        // Konversi angka bulan ke angka Romawi
        $romanMonth = $this->convertMonthToRoman($monthNumber);
        
        // format RCV-PIP-years-romanMonth-sequenceNumberTable
        $id = 'SRQ-PIP-'.$yearsNumber.'-'.$romanMonth.'-'. $formattedNumber;
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
