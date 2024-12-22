<?php

namespace App\Http\Controllers\Model\Stock;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Log\LogError;

use App\Http\Controllers\Class_DB\Class_Stock;
use App\Http\Controllers\Class_DB\Class_StockTransaction;
use App\Http\Controllers\Class_DB\Class_StockLog;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use DateTime;
use Exception;

class Stock extends Controller
{
    public function getStock($request)
    {
        try
        {
            $result=[];
            $classDB = new Class_Stock();
            $resultClassDB = $classDB->show($request);
            $result = $resultClassDB['data'];

            if (isset($request['code']) && $request['code']!='') 
            {
                $result=[];
                $result['get_stock'] = $resultClassDB['data'];

                $classDB = new Class_StockTransaction();
                $resultClassDB = $classDB->show($request);
                $result['get_stock_transaction'] = $resultClassDB['data'];
            }

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
            $requestModule['class'] = 'Stock';
            $requestModule['function'] = 'getStock';
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

    public function insertStock($request)
    {
        try
        {
            DB::beginTransaction();
            // Validate the incoming request data
            $request->validate([
                'item_group'      => 'sometimes|nullable|string',   // Optional, can be null
                'brand'           => 'sometimes|nullable|string',   // Optional, can be null
                'code'            => 'required|string',             // Required
                'items'           => 'required|string',             // Required
                'description'     => 'sometimes|nullable|string',   // Optional, can be null
                'unit'            => 'required|string',             // Required
                'have_exp'        => 'sometimes|nullable|string',   // Optional, can be null
                'initial_stock'   => 'sometimes|nullable|int',      // Optional, can be null, integer
                'reff'            => 'required|string',             // Required
            ]);
         
            $requestModule=[];
            if (isset($request['item_group']) && $request['item_group']!='' ) {$requestModule['item_group'] = $request['item_group'];}
            if (isset($request['brand']) && $request['brand']!='' ) {$requestModule['brand'] = $request['brand'];}
            if (isset($request['code']) && $request['code']!='' ) {$requestModule['code'] = $request['code'];}
            if (isset($request['items']) && $request['items']!='' ) {$requestModule['items'] = $request['items'];}
            if (isset($request['description']) && $request['description']!='' ) {$requestModule['description'] = $request['description'];}
            if (isset($request['unit']) && $request['unit']!='' ) {$requestModule['unit'] = $request['unit'];}
            if (isset($request['have_exp']) && $request['have_exp']!='' ) {$requestModule['have_exp'] = $request['have_exp'];}
            if (isset($request['initial_stock']) && $request['initial_stock']!='' ) {$requestModule['initial_stock'] = $request['initial_stock'];}
           
            $result=[];
            // insert class stock
            $classDB = new Class_Stock();
            $resultClassDB = $classDB->insert($requestModule);
            if(!$resultClassDB['success'])
            {
                DB::rollBack();
                return $resultClassDB;
            }
            $result['insert_stock'] = $resultClassDB['data'];
      
            // insert class transaction if have initial stock
            if (isset($request['initial_stock']) && $request['initial_stock']!='' && $request['initial_stock']!='0' )
            {
                $requestClassDB=[];
                if (isset($request['item_group']) && $request['item_group']!='' ) {$requestClassDB['item_group'] = $request['item_group'];}
                if (isset($request['brand']) && $request['brand']!='' ) {$requestClassDB['brand'] = $request['brand'];}
                if (isset($request['code']) && $request['code']!='' ) {$requestClassDB['code'] = $request['code'];}
                if (isset($request['items']) && $request['items']!='' ) {$requestClassDB['items'] = $request['items'];}
                if (isset($request['description']) && $request['description']!='' ) {$requestClassDB['description'] = $request['description'];}
                $requestClassDB['type_transaction'] = '1';
                $requestClassDB['in'] = $request['initial_stock'];
                $requestClassDB['out'] =0;
                $requestClassDB['no_transaction'] = '-';
                $requestClassDB['qty'] = $request['initial_stock'];
                $requestClassDB['origin_of_goods'] = 'Initial Stock';
                $requestClassDB['date'] = Carbon::now()->format('Y-m-d');
                $requestClassDB['years'] = Carbon::now()->format('Y');
             
                $classDB = new Class_StockTransaction();
                $resultClassDBStockTransaction = $classDB->insert($requestClassDB);
                if(!$resultClassDBStockTransaction['success'])
                {
                    DB::rollBack();
                    return $resultClassDBStockTransaction;
                }
                $result['insert_stockTransaction'] = $resultClassDBStockTransaction['data'];
            }
            
            $requestClassDB = [];
            $requestClassDB['id_item'] = $resultClassDB['data']->id;
            $requestClassDB['reff'] = $request['reff'];
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
            $requestModule['class'] = 'Stock';
            $requestModule['function'] = 'insertStock';
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

    public function updateStock($request)
    {
        try
        {
            DB::beginTransaction();
            // Validate the incoming request data
            $request->validate([
                'id'              => 'required|string',             // Required
                'item_group'      => 'sometimes|nullable|string',   // Optional, can be null
                'brand'           => 'sometimes|nullable|string',   // Optional, can be null
                'code'            => 'required|string',             // Required
                'brand'           => 'sometimes|nullable|string',   // Optional, can be null
                'description'     => 'sometimes|nullable|string',   // Optional, can be null
                'unit'            => 'sometimes|nullable|string',   // Optional, can be null
                'have_exp'        => 'sometimes|nullable|string',   // Optional, can be null
                'initial_stock'   => 'sometimes|nullable|string',   // Optional, can be null
                'reff'            => 'required|string',             // Required
            ]);

            $requestModule=[];
            if (isset($request['id']) && $request['id']!='' ) {$requestModule['id'] = $request['id'];}
            if (isset($request['item_group']) && $request['item_group']!='' ) {$requestModule['item_group'] = $request['item_group'];}
            if (isset($request['brand']) && $request['brand']!='' ) {$requestModule['brand'] = $request['brand'];}
            if (isset($request['code']) && $request['code']!='' ) {$requestModule['code'] = $request['code'];}
            if (isset($request['items']) && $request['items']!='' ) {$requestModule['items'] = $request['items'];}
            if (isset($request['description']) && $request['description']!='' ) {$requestModule['description'] = $request['description'];}
            if (isset($request['unit']) && $request['unit']!='' ) {$requestModule['unit'] = $request['unit'];}
            if (isset($request['initial_stock']) && $request['initial_stock']!='' ) {$requestModule['initial_stock'] = $request['initial_stock'];}
            if (isset($request['have_exp']) && $request['have_exp']!='' ) {$requestModule['have_exp'] = $request['have_exp'];}

            $result=[];
            $classModel = new Class_Stock();
            $resultClassDB = $classModel->update($requestModule);

            $result['update_stock'] = $resultClassDB['data'];
            if(!$resultClassDB['success'])
            {
                DB::rollBack();
                return $resultClassDB;
            }
 
            $requestClassDB = [];
            $requestClassDB['id_item'] = $request['id'];
            $requestClassDB['reff'] = $request['reff'];
            $requestClassDB['activity'] = 'Update Stock';
            $requestClassDB['detail_act'] = json_encode($requestModule);
            $requestClassDB['years'] = Carbon::now()->format('Y');
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
            $requestModule['class'] = 'Stock';
            $requestModule['function'] = 'updateStock';
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

    public function updateStockFromTransaction($request)
    {
        try
        {
            $idItem = ''; $code ='';
            if (isset($request['id_item']) && $request['id_item']!='' ) {$idItem = $request['id_item'];}
            if (isset($request['code']) && $request['code']!='' ) {$code= $request['code'];}

            // cek stock transaction in
            $stockIn = DB::table('stock_transaction')
            ->select(DB::raw('sum(`in`) as total'))
            ->where('code', $code)
            ->groupBy('code')
            ->first();

            // cek stock transaction Out
            $stockOut = DB::table('stock_transaction')
            ->select(DB::raw('sum(`out`) as total'))
            ->where('code', $code)
            ->groupBy('code')
            ->first();
            // final_stock = initial_stock + stock_in - stock_out
            $finalStock = 0;
            $finalStock = $stockIn->total - $stockOut->total;
            // update stock
            $requestClassDB=[];
            $requestClassDB['id'] = $idItem;
            $requestClassDB['stock_in'] = $stockIn->total;
            $requestClassDB['stock_out'] = $stockOut->total;
            $requestClassDB['final_stock'] = $finalStock;
            $classDB = new Class_Stock();
            $requestClassDB = $classDB->update($requestClassDB);

            return [
                'success' => true,
                'message' => 'Successfuly Update Stock',
                'data' => $requestClassDB
            ];
        } catch (\Exception $ex) {
            # Insert Log Error
            $requestModule=[];
            $requestModule['reff'] = '-';
            $requestModule['service'] = 'Model';
            $requestModule['class'] = 'Stock';
            $requestModule['function'] = 'updateStockFromTransaction';
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
