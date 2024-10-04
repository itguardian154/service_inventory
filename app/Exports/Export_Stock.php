<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

use App\Http\Controllers\Model\Stock\Stock;

class Export_Stock implements FromView,WithColumnWidths,WithColumnFormatting
{
    protected $param = array();

    function __construct($param) 
    {  
            $this->param = $param;
    }

    public function view(): View
    {
        $idItem = $this->param['id_item'];

        $requestModule = [];
        if (isset($this->param['id_item']) && $this->param['id_item']!='' ) {$requestModule['id_item'] = $request['id_item'];}
        if (isset($this->param['item_group']) && $this->param['item_group']!='' ) {$requestModule['item_group'] = $this->param['item_group'];}
        if (isset($this->param['brand']) && $this->param['brand']!='' ) {$requestModule['brand'] = $this->param['brand'];}
        if (isset($this->param['code']) && $this->param['code']!='' ) {$requestModule['code'] = $this->param['code'];}
        if (isset($this->param['items']) && $this->param['items']!='' ) {$requestModule['items'] = $this->param['items'];}
        if (isset($this->param['unit']) && $this->param['unit']!='' ) {$requestModule['unit'] = $this->param['unit'];}
        if (isset($this->param['have_exp']) && $this->param['have_exp']!='' ) {$requestModule['have_exp'] = $this->param['have_exp'];}

        $model = new Stock();
        $data['stock']= $model->getStock($requestModule);
        return view('Stock.Stock', $data);
    }
  
    public function columnWidths(): array
    {
        return [
            'A' => 4,
            'B' => 15,
            'C' => 25,
            'D' => 20,   
            'E' => 25, 
            'F' => 20,
            'G' => 15, 
            'H' => 15, 
            'I' => 10,    
            'J' => 15,
            'K' => 40,
            'L' => 40,
            'M' => 20,
            'N' => 20,
            'O' => 20,
            'P' => 20,
            'Q' => 20,
            'R' => 20,
            'S' => 20,
            'T' => 20,
            'U' => 20,
        ];
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_TEXT,
            'B' => NumberFormat::FORMAT_TEXT,
            'C' => NumberFormat::FORMAT_TEXT,
            'D' => NumberFormat::FORMAT_TEXT,
            'E' => NumberFormat::FORMAT_TEXT,
            'F' => NumberFormat::FORMAT_TEXT,
            'G' => NumberFormat::FORMAT_TEXT,
            'H' => NumberFormat::FORMAT_TEXT,
            'I' => NumberFormat::FORMAT_TEXT,
            'J' => NumberFormat::FORMAT_TEXT,
            'K' => NumberFormat::FORMAT_TEXT,
            'L' => NumberFormat::FORMAT_TEXT,
            'M' => NumberFormat::FORMAT_TEXT,
            'N' => NumberFormat::FORMAT_TEXT,
            'O' => NumberFormat::FORMAT_TEXT,
            'P' => NumberFormat::FORMAT_TEXT,
            'Q' => NumberFormat::FORMAT_TEXT,
            'R' => NumberFormat::FORMAT_TEXT,
            'S' => NumberFormat::FORMAT_TEXT,
            'T' => NumberFormat::FORMAT_TEXT,
            'U' => NumberFormat::FORMAT_TEXT,
        ];
    }
}
