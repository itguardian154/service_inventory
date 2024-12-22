<?php

namespace App\Http\Controllers\Model\Stock;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Log\LogError;

use App\Http\Controllers\Class_DB\Class_Stock;
use App\Http\Controllers\Class_DB\Class_StockTransaction;
use App\Http\Controllers\Class_DB\Class_StockLog;

use App\Models\stock_transaction;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use DateTime;
use Exception;

class StockTransaction extends Controller
{
    public function getStockTransaction($request)
    {
        try
        {
            $result=[];
            $classDB = new Class_StockTransaction();
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
            $requestModule['class'] = 'StockTransaction';
            $requestModule['function'] = 'getStockTransaction';
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

    public function insertStockTransaction($request)
    {
        try
        {
            DB::beginTransaction();
            // Validate the incoming request data
            // $request->validate([
            //     'item_group'        => 'sometimes|nullable|string',   // Optional, can be null
            //     'brand'             => 'sometimes|nullable|string',   // Optional, can be null
            //     'code'              => 'required|string',             // Required
            //     'items'             => 'required|string',             // Required
            //     'description'       => 'sometimes|nullable|string',   // Optional, can be null
            //     'type_transaction'  => 'required|string',             // Required
            //     'qty'               => 'required|string',             // Required
            //     'no_transaction'    => 'sometimes|nullable|string',   // Optional, can be null
            // ]);
         
            $itemGroup=''; $brand=''; $code=''; $item=''; $description=''; $qty=''; $typeTransaction=''; $in=''; $out=''; $qty='';
            $noTransaction=''; $date=''; $price=''; $totalPrice=''; $originOfGoods=''; $expDate=''; $remark=''; $years='';
          
            if (isset($request['item_group']) && $request['item_group']!='' ) {$itemGroup = $request['item_group'];}
            if (isset($request['brand']) && $request['brand']!='' ) {$brand = $request['brand'];}
            if (isset($request['code']) && $request['code']!='' ) {$code = $request['code'];}
            if (isset($request['items']) && $request['items']!='' ) {$items = $request['items'];}
            if (isset($request['description']) && $request['description']!='' ) {$description = $request['description'];}
            if (isset($request['qty']) && $request['qty']!='' ) {$qty = $request['qty'];}
            if (isset($request['type_transaction']) && $request['type_transaction']!='' ) {$typeTransaction= $request['type_transaction'];}
            if (isset($request['in']) && $request['in']!='' ) {$in = $request['in'];}
            if (isset($request['out']) && $request['out']!='' ) {$out = $request['out'];}
            if (isset($request['no_transaction']) && $request['no_transaction']!='' ) {$noTransaction = $request['no_transaction'];}
            if (isset($request['date']) && $request['date']!='' ) {$date = $request['date'];}
            if (isset($request['price']) && $request['price']!='' ) {$price = $request['price'];}
            if (isset($request['total_price']) && $request['total_price']!='' ) {$totalPrice = $request['total_price'];}
            if (isset($request['origin_of_goods']) && $request['origin_of_goods']!='' ) {$originOfGoods = $request['origin_of_goods'];}
            if (isset($request['exp_date']) && $request['exp_date']!='' ) {$expDate = $request['exp_date'];}
            if (isset($request['remark']) && $request['remark']!='' ) {$remark = $request['remark'];}
            if (isset($request['years']) && $request['years']!='' ) {$years= $request['years'];}
     
            $result=[];

            $requestModule=[];
            $requestModule['item_group'] = $itemGroup;
            $requestModule['brand'] = $brand;
            $requestModule['code'] = $code;
            $requestModule['items'] = $items;
            $requestModule['description'] = $description;
            $requestModule['qty'] = $qty;
            $requestModule['type_transaction'] = $typeTransaction;
            if($typeTransaction=='1')
            {
                $requestModule['in'] = $qty;
                $requestModule['out'] = 0;
            }
            else
            {
                $requestModule['in'] = 0;
                $requestModule['out'] = $qty;
            }
            $requestModule['no_transaction'] = $noTransaction;
            $requestModule['date'] = $date;
            $requestModule['price'] = $price;
            $requestModule['total_price'] = $totalPrice;
            $requestModule['origin_of_goods'] = $originOfGoods;
            $requestModule['exp_date'] = $expDate;
            $requestModule['remark'] = $remark;
            $requestModule['years'] = $years;
            // insert class stock Transaction
            $classDB = new Class_StockTransaction();
            $resultClassDB = $classDB->insert($requestModule);
            if(!$resultClassDB['success'])
            {
                DB::rollBack();
                return $resultClassDB;
            }
            $result['insert_stockTransaction'] = $resultClassDB['data'];

            // update Class Stock
            // --GET ID Class Stock
            $requestModule=[];
            $requestModule['code'] = $code; 
            $classDB = new Class_Stock();
            $resultClassDB = $classDB->show($requestModule);
            if(!$resultClassDB['success'])
            {
                DB::rollBack();
                return $resultClassDB;
            }
            $idStockItem = $resultClassDB['data'][0]->id;
         
            $requestModule=[];
            $requestModule['id_item'] = $idStockItem;
            $requestModule['code'] = $code; 
            $classDB = new Stock();
            $resultClassDB = $classDB->updateStockFromTransaction($requestModule);
            if(!$resultClassDB['success'])
            {
                DB::rollBack();
                return $resultClassDB;
            }
            $result['update_stock'] = $resultClassDB['data'];
         
            $requestClassDB = [];
            $requestClassDB['id_item'] = $idStockItem;
            $requestClassDB['reff'] = '-';
            $requestClassDB['activity'] = 'Insert Stock';
            $requestClassDB['detail_act'] = json_encode($requestModule);
            $requestClassDB['years'] = Carbon::now()->format('Y');
            // insert class stock log
            $classDB = new Class_StockLog();
            $resultClassDB = $classDB->insert($requestClassDB);
            $result['insert_stockLog'] = $resultClassDB['data'];

            DB::commit();
            return [
                'success' => true,
                'message' => 'Insert successfuly',
                'data'=> $result
            ];
        } catch (\Exception $ex) {
            DB::rollBack();
            # Insert Log Error
            $requestModule=[];
            $requestModule['reff'] = '-';
            $requestModule['service'] = 'Model';
            $requestModule['class'] = 'StockTransaction';
            $requestModule['function'] = 'insertStockTransaction';
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

    public function updateStockTransaction($request)
    {
        try
        {
            DB::beginTransaction();
            // Validate the incoming request data
            $request->validate([
                'item_group'        => 'sometimes|nullable|string',   // Optional, can be null
                'brand'             => 'sometimes|nullable|string',   // Optional, can be null
                'code'              => 'required|string',             // Required
                'items'             => 'required|string',             // Required
                'description'       => 'sometimes|nullable|string',   // Optional, can be null
                'type_transaction'  => 'required|string',             // Required
                'qty'               => 'required|string',             // Required
                'no_transaction'    => 'sometimes|nullable|string',   // Optional, can be null
            ]);
         
            $itemGroup=''; $brand=''; $code=''; $item=''; $description=''; $qty=''; $typeTransaction=''; $in=''; $out=''; $qty='';
            $noTransaction=''; $date=''; $price=''; $totalPrice=''; $originOfGoods=''; $expDate=''; $remark=''; $years='';
           
            if (isset($request['item_group']) && $request['item_group']!='' ) {$itemGroup = $request['item_group'];}
            if (isset($request['brand']) && $request['brand']!='' ) {$brand = $request['brand'];}
            if (isset($request['code']) && $request['code']!='' ) {$code = $request['code'];}
            if (isset($request['items']) && $request['items']!='' ) {$items = $request['items'];}
            if (isset($request['description']) && $request['description']!='' ) {$description = $request['description'];}
            if (isset($request['qty']) && $request['qty']!='' ) {$qty = $request['qty'];}
            if (isset($request['type_transaction']) && $request['type_transaction']!='' ) {$typeTransaction= $request['type_transaction'];}
            if (isset($request['in']) && $request['in']!='' ) {$in = $request['in'];}
            if (isset($request['out']) && $request['out']!='' ) {$out = $request['out'];}
            if (isset($request['no_transaction']) && $request['no_transaction']!='' ) {$noTransaction = $request['no_transaction'];}
            if (isset($request['date']) && $request['date']!='' ) {$date = $request['date'];}
            if (isset($request['price']) && $request['price']!='' ) {$price = $request['price'];}
            if (isset($request['total_price']) && $request['total_price']!='' ) {$totalPrice = $request['total_price'];}
            if (isset($request['origin_of_goods']) && $request['origin_of_goods']!='' ) {$originOfGoods = $request['origin_of_goods'];}
            if (isset($request['exp_date']) && $request['exp_date']!='' ) {$expDate = $request['exp_date'];}
            if (isset($request['remark']) && $request['remark']!='' ) {$remark = $request['remark'];}
            if (isset($request['years']) && $request['years']!='' ) {$years= $request['years'];}
     
            $result=[];

            $requestModule=[];
            $requestModule['item_group'] = $itemGroup;
            $requestModule['brand'] = $brand;
            $requestModule['code'] = $code;
            $requestModule['items'] = $items;
            $requestModule['description'] = $description;
            $requestModule['qty'] = $qty;
            $requestModule['type_transaction'] = $typeTransaction;
            if($typeTransaction=='1')
            {
                $requestModule['in'] = $qty;
                $requestModule['out'] = 0;
            }
            else
            {
                $requestModule['in'] = 0;
                $requestModule['out'] = $qty;
            }
            $requestModule['no_transaction'] = $noTransaction;
            $requestModule['date'] = $date;
            $requestModule['price'] = $price;
            $requestModule['total_price'] = $totalPrice;
            $requestModule['origin_of_goods'] = $originOfGoods;
            $requestModule['exp_date'] = $expDate;
            $requestModule['remark'] = $remark;
            $requestModule['years'] = $years;
            // insert class stock Transaction
            $classDB = new Class_StockTransaction();
            $resultClassDB = $classDB->insert($requestModule);
            if(!$resultClassDB['success'])
            {
                DB::rollBack();
                return $resultClassDB;
            }
            $result['insert_stockTransaction'] = $resultClassDB['data'];

            // update Class Stock
            // --GET ID Class Stock
            $requestModule=[];
            $requestModule['code'] = $code; 
            $classDB = new Class_Stock();
            $resultClassDB = $classDB->show($requestModule);
            if(!$resultClassDB['success'])
            {
                DB::rollBack();
                return $resultClassDB;
            }
            $idStockItem = $resultClassDB['data'][0]->id;
         
            $requestModule=[];
            $requestModule['id_item'] = $idStockItem;
            $requestModule['code'] = $code; 
            $classDB = new Stock();
            $resultClassDB = $classDB->updateStockFromTransaction($requestModule);
            if(!$resultClassDB['success'])
            {
                DB::rollBack();
                return $resultClassDB;
            }
            $result['update_stock'] = $resultClassDB['data'];
         
            $requestClassDB = [];
            $requestClassDB['id_item'] = $idStockItem;
            $requestClassDB['reff'] = '-';
            $requestClassDB['activity'] = 'Insert Stock';
            $requestClassDB['detail_act'] = json_encode($requestModule);
            $requestClassDB['years'] = Carbon::now()->format('Y');
            // insert class stock log
            $classDB = new Class_StockLog();
            $resultClassDB = $classDB->insert($requestClassDB);
            $result['insert_stockLog'] = $resultClassDB['data'];

            DB::commit();
            return [
                'success' => true,
                'message' => 'Update successfuly',
                'data'=> $result
            ];
        } catch (\Exception $ex) {
            # Insert Log Error
            $requestModule=[];
            $requestModule['reff'] = '-';
            $requestModule['service'] = 'Model';
            $requestModule['class'] = 'StockTransaction';
            $requestModule['function'] = 'updateStockTransaction';
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

    public function jsonInsertStockTransaction($request)
    {
        try
        {
            DB::beginTransaction();
            // Validate the incoming request data
            $request->validate([
                'json_input'               => 'required|string',             // Required
            ]);

            $jsonInput = '';
            if (isset($request['json_input']) && $request['json_input']!='' ) {$jsonInput = $request['json_input'];}
            $jsonDecodeInput = json_decode($jsonInput);

            foreach($jsonDecodeInput as $v)
            {
           
                $requestModule=[];
                if (isset($v->item_group) && $v->item_group!='' ) {$requestModule['item_group'] = $v->item_group;}
                if (isset($v->brand) && $v->brand!='' ) {$requestModule['brand'] = $v->brand;}
                if (isset($v->code) && $v->code!='' ) {$requestModule['code'] = $v->code;}
                if (isset($v->items) && $v->items!='' ) {$requestModule['items'] = $v->items;}
                if (isset($v->description) && $v->description!='' ) {$requestModule['description'] = $v->description;}
                if (isset($v->qty) && $v->qty!='' ) {$requestModule['qty'] = $v->qty;}
                if (isset($v->type_transaction) && $v->type_transaction!='' ) {$requestModule['type_transaction'] = $v->type_transaction;}
                if (isset($v->in) && $v->in!='' ) {$requestModule['in'] = $v->in;}
                if (isset($v->out) && $v->out!='' ) {$requestModule['out'] = $v->out;}
                if (isset($v->in) && $v->in!='' ) {$requestModule['in'] = $v->in;}
                if (isset($v->no_transaction) && $v->no_transaction!='' ) {$requestModule['no_transaction'] = $v->no_transaction;}
                if (isset($v->date) && $v->date!='' ) {$requestModule['date'] = $v->date;}
                if (isset($v->price) && $v->price!='' ) {$requestModule['price'] = $v->price;}
                if (isset($v->total_price) && $v->total_price!='' ) {$requestModule['total_price'] = $v->total_price;}
                if (isset($v->origin_of_goods) && $v->origin_of_goods!='' ) {$requestModule['origin_of_goods'] = $v->origin_of_goods;}
                if (isset($v->exp_date) && $v->exp_date!='' ) {$requestModule['exp_date'] = $v->exp_date;}
                if (isset($v->remark) && $v->remark!='' ) {$requestModule['remark'] = $v->remark;}
                if (isset($v->years) && $v->years!='' ) {$requestModule['years'] = $v->years;}       
                $result = $this->insertStockTransaction($requestModule);
            }

            DB::commit();
            return [
                'success' => true,
                'message' => 'Insert successfuly',
                'data'=> $result
            ];
        } catch (\Exception $ex) {
            DB::rollBack();
            # Insert Log Error
            $requestModule=[];
            $requestModule['reff'] = '-';
            $requestModule['service'] = 'Model';
            $requestModule['class'] = 'StockTransaction';
            $requestModule['function'] = 'jsonInsertStockTransaction';
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

    public function jsonUpdateStockTransaction($request)
    {
        try
        {
            DB::beginTransaction();
            // Validate the incoming request data
            $request->validate([
                'no_transaction'           => 'required|string',             // Required
                'json_input'               => 'required|string',             // Required
            ]);

            $noTransaction=''; $jsonInput = '';
            if (isset($request['no_transaction']) && $request['no_transaction']!='' ) {$noTransaction = $request['no_transaction'];}
            if (isset($request['json_input']) && $request['json_input']!='' ) {$jsonInput = $request['json_input'];}
            
            if($noTransaction!='')
            {
                stock_transaction::where('no_transaction', $noTransaction)->delete();
            }
            $jsonDecodeInput = json_decode($jsonInput);
            foreach($jsonDecodeInput as $v)
            {
                $requestModule=[];
                if (isset($v->item_group) && $v->item_group!='' ) {$requestModule['item_group'] = $v->item_group;}
                if (isset($v->brand) && $v->brand!='' ) {$requestModule['brand'] = $v->brand;}
                if (isset($v->code) && $v->code!='' ) {$requestModule['code'] = $v->code;}
                if (isset($v->items) && $v->items!='' ) {$requestModule['items'] = $v->items;}
                if (isset($v->description) && $v->description!='' ) {$requestModule['description'] = $v->description;}
                if (isset($v->qty) && $v->qty!='' ) {$requestModule['qty'] = $v->qty;}
                if (isset($v->type_transaction) && $v->type_transaction!='' ) {$requestModule['type_transaction'] = $v->type_transaction;}
                if (isset($v->in) && $v->in!='' ) {$requestModule['in'] = $v->in;}
                if (isset($v->out) && $v->out!='' ) {$requestModule['out'] = $v->out;}
                if (isset($v->in) && $v->in!='' ) {$requestModule['in'] = $v->in;}
                if (isset($v->no_transaction) && $v->no_transaction!='' ) {$requestModule['no_transaction'] = $v->no_transaction;}
                if (isset($v->date) && $v->date!='' ) {$requestModule['date'] = $v->date;}
                if (isset($v->price) && $v->price!='' ) {$requestModule['price'] = $v->price;}
                if (isset($v->total_price) && $v->total_price!='' ) {$requestModule['total_price'] = $v->total_price;}
                if (isset($v->origin_of_goods) && $v->origin_of_goods!='' ) {$requestModule['origin_of_goods'] = $v->origin_of_goods;}
                if (isset($v->exp_date) && $v->exp_date!='' ) {$requestModule['exp_date'] = $v->exp_date;}
                if (isset($v->remark) && $v->remark!='' ) {$requestModule['remark'] = $v->remark;}
                if (isset($v->years) && $v->years!='' ) {$requestModule['years'] = $v->years;}       
                $result = $this->insertStockTransaction($requestModule);
            }

            DB::commit();
            return [
                'success' => true,
                'message' => 'Insert successfuly',
                'data'=> $result
            ];
        } catch (\Exception $ex) {
            DB::rollBack();
            # Insert Log Error
            $requestModule=[];
            $requestModule['reff'] = '-';
            $requestModule['service'] = 'Model';
            $requestModule['class'] = 'StockTransaction';
            $requestModule['function'] = 'jsonUpdateStockTransaction';
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
}
