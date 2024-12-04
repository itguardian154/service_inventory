<?php

namespace App\Http\Controllers\Model\Stock;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Log\LogError;

use App\Http\Controllers\Class_DB\Class_StockExpired;
// use App\Http\Controllers\Class_DB\Class_StockTransaction;
// use App\Http\Controllers\Class_DB\Class_StockLog;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use DateTime;
use Exception;

class StockExpired extends Controller
{
    public function getStockExpired($request)
    {
        try
        {
            $result=[];
            $classDB = new Class_StockExpired();
            $resultClassDB = $classDB->show($request);
            $result = $resultClassDB['data'];

            return [
                'success' => true,
                'message' => 'Get successful',
                'data'=> $result
            ];
        } catch (\Exception $ex) {
            # Insert Log Error
            $requestModule=[];
            $requestModule['reff'] = '-';
            $requestModule['service'] = 'Model';
            $requestModule['class'] = 'StockExpired';
            $requestModule['function'] = 'getStockExpired';
            $requestModule['message'] = $ex->getMessage();
            $requestModule['note'] = '-';
            $classModel = new LogError();
            $result = $classModel->insertLogError($requestModule);
            # End Log Error
            return [
                'success' => false,
                'message' => $ex->getMessage()
            ];
        }
    }

    public function insertStockExpired($request)
    {
        try
        {
      
            // Validate the incoming request data
            $request->validate([
                'code'            => 'required|string',      // Required
                'item'            => 'required|string',      // Required
                'qty'            => 'required|string',      // Required
            ]);
        
            // declare variable 
            $noTransaction=''; $itemGroup='-'; $code=''; $item=''; $unit=''; $stock=''; $qty=''; $dateExpired=''; 
            $status='1'; $years=Carbon::now()->format('Y');

            $result=[];
            # declare variable from request
            if (isset($request['no_transaction']) && $request['no_transaction']!='' ) {$noTransaction = $request['no_transaction'];}
            if (isset($request['item_group']) && $request['item_group']!='' ) {$itemGroup = $request['item_group'];}
            if (isset($request['code']) && $request['code']!='' ) {$code = $request['code'];}
            if (isset($request['item']) && $request['item']!='' ) {$item = $request['item'];}
            if (isset($request['unit']) && $request['unit']!='' ) {$unit = $request['unit'];}
            if (isset($request['stock']) && $request['stock']!='' ) {$stock = $request['stock'];}
            if (isset($request['qty']) && $request['qty']!='' ) {$qty = $request['qty'];}
            if (isset($request['date_expired']) && $request['date_expired']!='' ) {$dateExpired = $request['date_expired'];}
            if (isset($request['status']) && $request['status']!='' ) {$status = $request['status'];}
            if (isset($request['years']) && $request['years']!='' ) {$years = $request['years'];}
            # end declare variable request
      
            # declare variable request adjustment
            $requestModuleAdjustment=[];
            $requestModuleAdjustment['no_transaction'] = $noTransaction;
            $requestModuleAdjustment['item_group'] = $itemGroup;
            $requestModuleAdjustment['code'] = $code;
            $requestModuleAdjustment['item'] = $item;
            $requestModuleAdjustment['unit'] = $unit;
            $requestModuleAdjustment['stock'] = $stock;
            $requestModuleAdjustment['qty'] = $qty;
            $requestModuleAdjustment['date_expired'] = $dateExpired;
            $requestModuleAdjustment['status'] = $status;
            $requestModuleAdjustment['years'] = $years;
            # end declare variable request adjustment
    
            $classDB = new Class_StockExpired();
            $resultClassDB = $classDB->insert($requestModuleAdjustment);
            if(!$resultClassDB['success'])
            {
                DB::rollBack();
                return $resultClassDB;
            }
            $result['insert_stockExpired'] = $resultClassDB['data'];

            DB::commit();
            return $result;
        } catch (\Exception $ex) {
            DB::rollBack();
            # Insert Log Error
            $requestModule=[];
            $requestModule['reff'] = '-';
            $requestModule['service'] = 'Model';
            $requestModule['class'] = 'StockExpired';
            $requestModule['function'] = 'insertStockExpired';
            $requestModule['message'] = $ex->getMessage();
            $requestModule['note'] = '-';
            $classModel = new LogError();
            $result = $classModel->insertLogError($requestModule);
            # End Log Error
            return $ex;
        }
    }

    public function updateStockExpired($request)
    {
        try
        {
            DB::beginTransaction();
            // Validate the incoming request data
            $request->validate([
                'id'            => 'required|string',      // Required
            ]);
        
            // declare variable 
            $id=''; $noTransaction=''; $itemGroup='-'; $code=''; $item=''; $unit=''; $stock=''; $qty=''; $dateExpired=''; 
            $status=''; $years='';

            $result=[];
            $requestClassModel=[];
            # declare variable from request
            if (isset($request['id']) && $request['id']!='' ) {$requestClassModel['id'] = $request['id'];}
            if (isset($request['no_transaction']) && $request['no_transaction']!='' ) {$requestClassModel['no_transaction'] = $request['no_transaction'];}
            if (isset($request['item_group']) && $request['item_group']!='' ) {$requestClassModel['item_group'] = $request['item_group'];}
            if (isset($request['code']) && $request['code']!='' ) {$requestClassModel['code'] = $request['code'];}
            if (isset($request['item']) && $request['item']!='' ) {$requestClassModel['item'] = $request['item'];}
            if (isset($request['unit']) && $request['unit']!='' ) {$requestClassModel['unit'] = $request['unit'];}
            if (isset($request['stock']) && $request['stock']!='' ) {$requestClassModel['stock'] = $request['stock'];}
            if (isset($request['qty']) && $request['qty']!='' ) {$requestClassModel['qty'] = $request['qty'];}
            if (isset($request['date_expired']) && $request['date_expired']!='' ) {$requestClassModel['date_expired'] = $request['date_expired'];}
            if (isset($request['status']) && $request['status']!='' ) {$requestClassModel['status'] = $request['status'];}
            if (isset($request['years']) && $request['years']!='' ) {$requestClassModel['years'] = $request['years'];}
            # end declare variable request

            // update data Adjustment
            $classDB = new Class_StockExpired();
            $resultClassDB = $classDB->update($requestClassModel);
            if(!$resultClassDB['success'])
            {
                DB::rollBack();
                return $resultClassDB;
            }
            $result = $resultClassDB['data'];

            DB::commit();
            return $result;
        } catch (\Exception $ex) {
            DB::rollBack();
            # Insert Log Error
            $requestModule=[];
            $requestModule['reff'] = '-';
            $requestModule['service'] = 'Model';
            $requestModule['class'] = 'StockExpired';
            $requestModule['function'] = 'updateStockExpired';
            $requestModule['message'] = $ex->getMessage();
            $requestModule['note'] = '-';
            $classModel = new LogError();
            $result = $classModel->insertLogError($requestModule);
            # End Log Error
            return $ex;
        }
    }
}
