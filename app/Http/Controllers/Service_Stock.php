<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Log\LogError;
use App\Http\Controllers\Model\Stock\Stock;
use App\Http\Controllers\Model\Stock\StockAdjustment;
use App\Http\Controllers\Model\Stock\StockLog;
use App\Models\stock as model_stock;

use App\Exports\Export_Stock;
use App\Exports\Export_InventoryValuationReport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use DateTime;

// nanti di detele
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Class_DB\Class_StockExpired;
use App\Http\Controllers\Class_DB\Class_StockTransaction;


class Service_Stock extends Controller
{
    public function getStock(Request $request)
    {
        try
        {
            $module = new Stock();
            $resultModel = $module->getStock($request); 
            
            if($resultModel['success'])
            {
                $result=response()->json([
                    'status' => 'success',
                    'message' => 'Get Data Successfuly',
                    'data' => $resultModel['data']
                ]);
            }
            else
            {
                $result=response()->json([
                    'status' => 'failed',
                    'message' => 'Error Get Data',
                    'data' => $resultModel
                ]);
            }

            return $result;
        } catch (\Exception $ex) {
            # Insert Log Error
            $requestModule=[];
            $requestModule['reff'] = 'Service';
            $requestModule['service'] = 'Get-Stock';
            $requestModule['class'] = 'Service_Stock';
            $requestModule['function'] = 'getStock';
            $requestModule['message'] = $ex->getMessage();
            $requestModule['note'] = '-';
            $classModel = new LogError();
            $result = $classModel->insertLogError($requestModule);
            # End Log Error
            return $ex;
        }  
    }

    public function insertStock(Request $request)
    {
        try
        {
          
            $result = [];
            $classModel = new Stock();
            $resultModel = $classModel->insertStock($request); 
            
            if($resultModel['success'])
            {
                $result=response()->json([
                    'status' => 'success',
                    'message' => 'Created Transaction Items Successfuly',
                    'data' => $resultModel['data']
                ]);
            }
            else
            {
                $result=response()->json([
                    'status' => 'failed',
                    'message' => 'Error Created Transaction Items',
                    'data' => $resultModel['message']
                ]);
            }
            return $result;
        } catch (\Exception $ex) {
        
            # Insert Log Error
            $requestModule=[];
            $requestModule['reff'] = 'Service';
            $requestModule['service'] = 'Insert-Stock';
            $requestModule['class'] = 'Service_Stock';
            $requestModule['function'] = 'InsertStock';
            $requestModule['message'] = $ex->getMessage();
            $requestModule['note'] = '-';
            $classModel = new LogError();
            $result = $classModel->insertLogError($requestModule);
            # End Log Error
            return $ex;
        }  
    }

    public function exportStock(Request $request)
    {
        try
        {
          
            $param = array_filter([
                'date_start'   => $request['date_start'] ?? now()->startOfMonth(),
                'date_end'     => $request['date_end'] ?? now()->endOfMonth(),
                'export_type'  => $request['export_type'] ?? 'have_stock', 
                'item_group'   => $request['item_group'] ?? null,
                'have_exp'     => $request['have_exp'] ?? null,
            ]);
          
            return Excel::download(new Export_Stock($param), 'Stock-' . now()->format('Y-m-d_His') .'(' . ($request['date_start'] ?? now()->startOfMonth()) . '-' . ($request['date_end'] ?? now()->endOfMonth()) . ').xlsx');
        } catch (\Exception $ex) {
            # Insert Log Error
            $requestModule=[];
            $requestModule['reff'] = 'Service';
            $requestModule['service'] = 'Export-Stock';
            $requestModule['class'] = 'Service_Stock';
            $requestModule['function'] = 'ExportStock';
            $requestModule['message'] = $ex->getMessage();
            $requestModule['note'] = '-';
            $classModel = new LogError();
            $result = $classModel->insertLogError($requestModule);
            # End Log Error
            return $ex;
        }  
    }

    public function exportInventoryValuationReport(Request $request)
    {
        try
        {
          
            $param = array_filter([
                'date_start'   => $request['date_start'] ?? now()->startOfMonth(),
                'date_end'     => $request['date_end'] ?? now()->endOfMonth(),
                'export_type'  => $request['export_type'] ?? 'have_stock', 
                'item_group'   => $request['item_group'] ?? null,
                'have_exp'     => $request['have_exp'] ?? null,
            ]);
  
            return Excel::download(new Export_InventoryValuationReport($param), 'Inventory Valuation Report-' . now()->format('Y-m-d_His') .'(' . ($request['date_start'] ?? now()->startOfMonth()) . '-' . ($request['date_end'] ?? now()->endOfMonth()) . ').xlsx');
        } catch (\Exception $ex) {
            # Insert Log Error
            $requestModule=[];
            $requestModule['reff'] = 'Service';
            $requestModule['service'] = 'Export-Stock';
            $requestModule['class'] = 'Service_Stock';
            $requestModule['function'] = 'exportInventoryValuationReport';
            $requestModule['message'] = $ex->getMessage();
            $requestModule['note'] = '-';
            $classModel = new LogError();
            $result = $classModel->insertLogError($requestModule);
            # End Log Error
            return $ex;
        }  
    }

    public function updateStock(Request $request)
    {
        try
        {
            $stock = new Stock();
            $result['update_stock'] = $stock->updateStock($request); 

            // insert History
            $requestHistory = [];
            $requestHistory['id_item'] = $request['id'];
            $requestHistory['reff'] = $request['reff'];
            $requestHistory['activity'] = 'Update Stock';
            $requestHistory['detail_act'] = json_encode($request->all());

            $history = new StockLog();
            $result['insert_history'] = $history->insertHistoryStock($requestHistory);
            
            $result=response()->json([
                'status' => 'success',
                'message' => 'Update Stock Successfuly',
                'data' => $result
            ]);

            return $result;
        } catch (\Exception $ex) {
            # Insert Log Error
            $requestModule=[];
            $requestModule['reff'] = 'Service';
            $requestModule['service'] = 'Update-Stock';
            $requestModule['class'] = 'Service_Stock';
            $requestModule['function'] = 'UpdateStock';
            $requestModule['message'] = $ex->getMessage();
            $requestModule['note'] = '-';
            $classModel = new LogError();
            $result = $classModel->insertLogError($requestModule);
            # End Log Error
            return $ex;
        }  
    } 

    // adjust item dari excel, boleh di hapus
    public function insertStockDB(Request $request)
    {
        try
        {
            $codeItems=''; $stock=0; $haveExp=''; $dateExpired=''; $yars='';
            if (isset($request['code_items']) && $request['code_items']!='' ) {$codeItems = $request['code_items'];}
            if (isset($request['stock']) && $request['stock']!='' ) {$stock = $request['stock'];}
            if (isset($request['have_exp']) && $request['have_exp']!='' ) {$haveExp = $request['have_exp'];}
            if (isset($request['date_expired']) && $request['date_expired']!='' ) {$dateExpired = $request['date_expired'];}
            if (isset($request['years']) && $request['years']!='' ) {$years = $request['years'];}
       
            $data = DB::table('stock')
            ->select('id','item_group','brand','code','items','description','unit')
            ->where('code',$codeItems)
            ->first();
        
            $id = $data->id;
            $itemGroup= $data->item_group;
            $brand= $data->brand;
            $code= $data->code;
            $items= $data->items;
            $description = $data->description;
            $unit= $data->unit;

            $date = Carbon::now();
            $monthNumber = $date->month; 
            $yearsNumber = $date->year;
            $prefixSubs = 'SRQ-PIP-'.$yearsNumber;
        
            $formattedNumber = str_pad($id, 6, '0', STR_PAD_LEFT);
        
            // Konversi angka bulan ke angka Romawi
            $romanMonth = $this->convertMonthToRoman($monthNumber);
            
            // format RCV-PIP-years-romanMonth-sequenceNumberTable
            $noTransaction = 'SRQ-PIP-'.$yearsNumber.'-'.$romanMonth.'-'. $formattedNumber;

            // update table stock
            DB::table('stock')
            ->where('code',$codeItems)
            ->update([
                'initial_stock' => $stock,
                'final_stock' => $stock,
            ]);
               
            // if have expired insert stock expired
            if($haveExp !='')
            {
             
                DB::table('stock')
                ->where('code',$codeItems)
                ->update([
                    'have_exp' => '1',
                ]);

                $requestClass =[];
                $requestClass['no_transaction'] = $noTransaction;
                $requestClass['item_group'] = $itemGroup;
                $requestClass['code'] = $codeItems;
                $requestClass['item'] = $items;
                $requestClass['unit'] = $unit;
                $requestClass['stock'] = $stock;
                $requestClass['qty'] = $stock;
                $requestClass['status'] = '1';
                $requestClass['date_expired'] = $haveExp;
                $requestClass['years'] = $years;
                $classDB = new Class_StockExpired();
                $resultClass = $classDB->insert($requestClass);
               
            }
            // insert item transaction
            $requestClassDB = [];
            $requestClassDB['id_item'] = $codeItems;
            $requestClassDB['item_group'] = $itemGroup;
            $requestClassDB['brand'] =$brand;
            $requestClassDB['code'] = $code;
            $requestClassDB['items'] = $items;
            $requestClassDB['description'] = $description;
            $requestClassDB['type_transaction'] = '1';
            $requestClassDB['in'] = $stock;
            $requestClassDB['out'] = '0';
            $requestClassDB['no_transaction'] = $noTransaction;
            $requestClassDB['qty'] = $stock;
            $requestClassDB['origin_of_goods'] = 'Adjustment';
            $requestClassDB['date'] = Carbon::now()->format('Y-m-d');
            $requestClassDB['years'] = Carbon::now()->format('Y');
            $classDB = new Class_StockTransaction();
    
            $resultClassDB = $classDB->insert($requestClassDB);
        
            if(!$resultClassDB['success'])
            {
                DB::rollBack();
                return $resultClassDB;
            }
            $result['insert_stockTransaction'] = $resultClassDB['data'];

            $result=response()->json([
                'status' => 'success',
                'message' => 'Update Stock Successfuly'
            ]);

            return $result;
        } catch (\Exception $ex) {
            # Insert Log Error
            $requestModule=[];
            $requestModule['reff'] = 'Service';
            $requestModule['service'] = 'Update-Stock';
            $requestModule['class'] = 'Service_Stock';
            $requestModule['function'] = 'UpdateStock';
            $requestModule['message'] = $ex->getMessage();
            $requestModule['note'] = '-';
            $classModel = new LogError();
            $result = $classModel->insertLogError($requestModule);
            # End Log Error
            return $ex;
        }  
    } 

    private function convertMonthToRoman($monthNumber) {
        $romanNumerals = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
            7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'
        ];
        return $romanNumerals[$monthNumber] ?? '';
    }
    // batas hapus
}
