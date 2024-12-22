<?php

namespace App\Http\Controllers\Class_DB;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Log\LogError;
use App\Models\stock;
use Carbon\Carbon;
use DateTime;

class Class_Stock
{
    /**
     * Read table
     */
    public function show($request)
    {
        // set value variable
        $id=''; $itemGroups=''; $brand=''; $code=''; $items=''; $description=''; $unit=''; $haveExp='';

        if (isset($request['id']) && $request['id']!='' ) {$id = $request['id'];}
        if (isset($request['item_group']) && $request['item_group']!='' ) {$itemGroups = $request['item_group'];}
        if (isset($request['brand']) && $request['brand']!='' ) {$brand = $request['brand'];}
        if (isset($request['code']) && $request['code']!='' ) {$code = $request['code'];}
        if (isset($request['items']) && $request['items']!='' ) {$items = $request['items'];}
        if (isset($request['description']) && $request['description']!='' ) {$description = $request['description'];}
        if (isset($request['unit']) && $request['unit']!='' ) {$unit = $request['unit'];}
        if (isset($request['have_exp']) && $request['have_exp']!='' ) {$haveExp = $request['have_exp'];}
   
        try
        {
            $data_ = DB::table('stock');
            if($id!='')
            {
                $data_->where('id',$id);

                $data = $data_->get();
                return [
                    'success' => true,
                    'message' => 'Get successful',
                    'data' => $data
                ];
            }
            if($itemGroups!='')
            {
                $data_->where('item_group',$itemGroups);
            }
            if($code!='')
            {
                $data_->where('code',$code);
            }
            if($items!='')
            {
                $data_->where('items',$items);
            }
            if($description!='')
            {
                $data_->where('description',$description);
            }
            if($unit!='')
            {
                $data_->where('unit',$unit);
            }
            if($haveExp!='')
            {
                $data_->where('have_exp',$haveExp);
            }

            if($data_->exists())
            {
                $data = $data_->get();
                return [
                    'success' => true,
                    'message' => 'Get successful',
                    'data' => $data
                ];
            }
            else
            {
                $data = null;
                return [
                    'success' => false,
                    'message' => 'Data Not Found',
                    'data' => $data
                ];
            }
            return $data;
        } catch (\Exception $ex) {
            # Insert Log Error
            $requestModule=[];
            $requestModule['reff'] = '-';
            $requestModule['service'] = 'Class';
            $requestModule['class'] = 'Class_Stock';
            $requestModule['function'] = 'Show';
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

    /**
     * Create table
     */
    public function insert($request)
    {
        // set value variable
        $itemGroups=''; $brand=''; $code=''; $items=''; $description=''; $unit=''; $initialStock=0; $haveExp='';
        $stockIn=0; $stockOut=0; $finalStock=0; $lastPrice=0; $averagePrice=0; $totalPrice=0;
        
        if (isset($request['item_group']) && $request['item_group']!='' ) {$itemGroups = $request['item_group'];}
        if (isset($request['brand']) && $request['brand']!='' ) {$brand = $request['brand'];}
        if (isset($request['code']) && $request['code']!='' ) {$code = $request['code'];}
        if (isset($request['items']) && $request['items']!='' ) {$items = $request['items'];}
        if (isset($request['description']) && $request['description']!='' ) {$description = $request['description'];}
        if (isset($request['unit']) && $request['unit']!='' ) {$unit = $request['unit'];}
        if (isset($request['initial_stock']) && $request['initial_stock']!='' ) {$initialStock = $request['initial_stock'];}
        if (isset($request['have_exp']) && $request['have_exp']!='' ) {$haveExp = $request['have_exp'];}
        if (isset($request['stock_in']) && $request['stock_in']!='' ) {$stockIn = $request['stock_in'];}
        if (isset($request['stock_out']) && $request['stock_out']!='' ) {$stockOut = $request['stock_out'];}
        if (isset($request['final_stock']) && $request['final_stock']!='' ) {$finalStock = $request['final_stock'];}
        if (isset($request['last_price']) && $request['last_price']!='' ) {$lastPrice = $request['last_price'];}
        if (isset($request['average_price']) && $request['average_price']!='' ) {$averagePrice = $request['average_price'];}
        if (isset($request['total_price']) && $request['total_price']!='' ) {$totalPrice = $request['total_price'];}
 
        try
        {
            // cek data
            $request=[];
            $request['code'] = $code;
        
            $dataTransaction = $this->show($request);
     
            if($dataTransaction['success'])
            {
                return [
                    'success' => false,
                    'message' => 'Double Data',
                    'data' => $dataTransaction
                ];
            }
            else
            {
                $data = new stock();
                $data->item_group = $itemGroups;
                $data->brand = $brand;
                $data->code = $code; 
                $data->items = $items; 
                $data->description = $description; 
                $data->unit = $unit; 
                $data->initial_stock = $initialStock; 
                $data->have_exp = $haveExp; 
                $data->stock_in = $stockIn; 
                $data->stock_out = $stockOut; 
                $data->final_stock = $finalStock; 
                $data->last_price = $lastPrice; 
                $data->average_price = $averagePrice;
                $data->total_price = $totalPrice;
                $data->save();
                
                return [
                    'success' => true,
                    'message' => 'Insert successful',
                    'data' => $data
                ];
            }
            return $data;
        } catch (\Exception $ex) {
            # Insert Log Error
            $requestModule=[];
            $requestModule['reff'] = '-';
            $requestModule['service'] = 'Class';
            $requestModule['class'] = 'Class_Stock';
            $requestModule['function'] = 'Insert';
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

    /**
     * Update table
     */
    public function update($request)
    {
        // set value variable
        $id = '';
        $updateData =[];
        try
        {
            // declare variable set
            if (isset($request['id']) && $request['id']!='' ) {$id = $request['id'];}
            if (isset($request['item_group']) && $request['item_group']!='' ) {$updateData['item_group'] = $request['item_group'];}
            if (isset($request['brand']) && $request['brand']!='' ) {$updateData['brand'] = $request['brand'];}
            if (isset($request['code']) && $request['code']!='' ) {$updateData['code'] = $request['code'];}
            if (isset($request['items']) && $request['items']!='' ) {$updateData['items'] = $request['items'];}
            if (isset($request['description']) && $request['description']!='' ) {$updateData['description'] = $request['description'];}
            if (isset($request['unit']) && $request['unit']!='' ) {$updateData['unit'] = $request['unit'];}
            if (isset($request['initial_stock']) && $request['initial_stock']!='' ) {$updateData['initial_stock'] = $request['initial_stock'];}
            if (isset($request['have_exp']) && $request['have_exp']!='' ) {$updateData['have_exp'] = $request['have_exp'];}
            if (isset($request['stock_in']) && $request['stock_in']!='' ) {$updateData['stock_in'] = $request['stock_in'];}
            if (isset($request['stock_out']) && $request['stock_out']!='' ) {$updateData['stock_out'] = $request['stock_out'];}
            if (isset($request['final_stock']) && $request['final_stock']!='' ) {$updateData['final_stock'] = $request['final_stock'];}
            if (isset($request['last_price']) && $request['last_price']!='' ) {$updateData['last_price'] = $request['last_price'];}
            if (isset($request['average_price']) && $request['average_price']!='' ) {$updateData['average_price'] = $request['average_price'];}
            if (isset($request['total_price']) && $request['total_price']!='' ) {$updateData['total_price'] = $request['total_price'];}
     
            DB::table('stock')
            ->where('id','=',$id)
            ->update($updateData);
   
            return [
                'success' => true,
                'message' => 'Update successfuly',
                'data' => $updateData
            ];
        } catch (\Exception $ex) {
            # Insert Log Error
            $requestModule=[];
            $requestModule['reff'] = '-';
            $requestModule['service'] = 'Class';
            $requestModule['class'] = 'Class_Stock';
            $requestModule['function'] = 'Update';
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
