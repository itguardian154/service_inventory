<?php

namespace App\Http\Controllers\Class_DB;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Model\LogError;
use App\Models\stock_adjustment;
use Carbon\Carbon;
use DateTime;

class Class_StockAdjustmentAdjustment
{
    /**
     * Read table
     */ 
    public function show($request)
    {
        // set value variable
        $idItem=''; $noAdjustment=''; $date=''; $itemGroup=''; $brand=''; $code=''; $item=''; $description=''; $qty='';
        $typeTransaction=''; $totalPrice=''; $status=''; $years='';
        
        if (isset($request['id_item']) && $request['id_item']!='' ) {$idItem = $request['id_item'];}
        if (isset($request['no_adjustment']) && $request['no_adjustment']!='' ) {$noAdjustment = $request['no_adjustment'];}
        if (isset($request['date']) && $request['date']!='' ) {$date = $request['date'];}
        if (isset($request['item_group']) && $request['item_group']!='' ) {$itemGroup = $request['item_group'];}
        if (isset($request['brand']) && $request['brand']!='' ) {$brand = $request['brand'];}
        if (isset($request['code']) && $request['code']!='' ) {$code = $request['code'];}
        if (isset($request['item']) && $request['item']!='' ) {$item = $request['item'];}
        if (isset($request['description']) && $request['description']!='' ) {$description = $request['description'];}
        if (isset($request['qty']) && $request['qty']!='' ) {$qty = $request['qty'];}
        if (isset($request['type_transaction']) && $request['type_transaction']!='' ) {$typeTransaction = $request['type_transaction'];}
        if (isset($request['total_price']) && $request['total_price']!='' ) {$totalPrice = $request['total_price'];}
        if (isset($request['status']) && $request['status']!='' ) {$status = $request['status'];}
        if (isset($request['years']) && $request['years']!='' ) {$years = $request['years'];}

        try
        {
            $data_ = DB::table('stock_adjustment');
            if($idItems!='')
            {
                $data_->where('id_item',$idItems);
            }
            if($noAdjustment!='')
            {
                $data_->where('no_adjustment',$noAdjustment);
            }
            if($date!='')
            {
                $data_->where('date',$date);
            }
            if($itemGroup!='')
            {
                $data_->where('item_group',$itemGroup);
            }
            if($brand!='')
            {
                $data_->where('brand',$brand);
            }
            if($code!='')
            {
                $data_->where('code',$code);
            }
            if($item!='')
            {
                $data_->where('item',$item);
            }
            if($description!='')
            {
                $data_->where('description',$description);
            }
            if($qty!='')
            {
                $data_->where('qty',$qty);
            }
            if($typeTransaction!='')
            {
                $data_->where('type_transaction',$typeTransaction);
            }
            if($totalPrice!='')
            {
                $data_->where('total_price',$totalPrice);
            }
            if($status!='')
            {
                $data_->where('status',$status);
            }
            if($years!='')
            {
                $data_->where('years',$years);
            }
           

            if($data_->exists())
            {
                $data = $data_->get();
            }
            else
            {
                $data = null;
            }
            return $data;
        } catch (\Exception $ex) {
            # Insert Log Error
            $requestModule=[];
            $requestModule['reff'] = '-';
            $requestModule['service'] = 'Class';
            $requestModule['class'] = 'Class_StockAdjustment';
            $requestModule['function'] = 'Show';
            $requestModule['message'] = $ex->getMessage();
            $requestModule['note'] = '-';
            $classModel = new LogError();
            $result = $classModel->insertLogError($request);
            # End Log Error
            return $ex;
        }
    }

    /**
     * Create table
     */
    public function insert($request)
    {
        // set value variable
        $idItem=''; $noAdjustment=''; $date=''; $itemGroup=''; $brand=''; $code=''; $item=''; $description=''; $qty='';
        $typeTransaction=''; $totalPrice=''; $status=''; $years='';
        
        if (isset($request['id_item']) && $request['id_item']!='' ) {$idItem = $request['id_item'];}
        if (isset($request['no_adjustment']) && $request['no_adjustment']!='' ) {$noAdjustment = $request['no_adjustment'];}
        if (isset($request['date']) && $request['date']!='' ) {$date = $request['date'];}
        if (isset($request['item_group']) && $request['item_group']!='' ) {$itemGroup = $request['item_group'];}
        if (isset($request['brand']) && $request['brand']!='' ) {$brand = $request['brand'];}
        if (isset($request['code']) && $request['code']!='' ) {$code = $request['code'];}
        if (isset($request['item']) && $request['item']!='' ) {$item = $request['item'];}
        if (isset($request['description']) && $request['description']!='' ) {$description = $request['description'];}
        if (isset($request['qty']) && $request['qty']!='' ) {$qty = $request['qty'];}
        if (isset($request['type_transaction']) && $request['type_transaction']!='' ) {$typeTransaction = $request['type_transaction'];}
        if (isset($request['total_price']) && $request['total_price']!='' ) {$totalPrice = $request['total_price'];}
        if (isset($request['status']) && $request['status']!='' ) {$status = $request['status'];}
        if (isset($request['years']) && $request['years']!='' ) {$years = $request['years'];}
        
        try
        {
            // cek data
            // $request=[];
            // $request['id_item'] = $idItems;
            // $request['code'] = $code;
        
            // $dataTransaction = $this->show($request);
            // if(isset($dataTransaction))
            // {
            //     // data sudah ada
            //     return 'double data';
            // }
            // else
            // {
                $data = new stock_adjustment();
                $data->id_item = $idItem;
                $data->no_adjustment = $noAdjustment;
                $data->date = $date;
                $data->item_group = $itemGroup; 
                $data->brand = $brand; 
                $data->code = $code; 
                $data->item = $item; 
                $data->description = $description; 
                $data->qty = $qty; 
                $data->type_transaction = $typeTransaction; 
                $data->total_price = $totalPrice; 
                $data->status = $status; 
                $data->years = $years; 
                $data->save();
            // }
            return $data;
        } catch (\Exception $ex) {
            # Insert Log Error
            $requestModule=[];
            $requestModule['reff'] = '-';
            $requestModule['service'] = 'Class';
            $requestModule['class'] = 'Class_StockAdjustment';
            $requestModule['function'] = 'Insert';
            $requestModule['message'] = $ex->getMessage();
            $requestModule['note'] = '-';
            $classModel = new LogError();
            $result = $classModel->insertLogError($request);
            # End Log Error
            return $ex;
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
            if (isset($request['id_item']) && $request['id_item']!='' ) {$updateData['id_item'] = $request['id_item'];}
            if (isset($request['no_adjustment']) && $request['no_adjustment']!='' ) {$updateData['no_adjustment'] = $request['no_adjustment'];}
            if (isset($request['date']) && $request['date']!='' ) {$updateData['date'] = $request['date'];}
            if (isset($request['item_group']) && $request['item_group']!='' ) {$updateData['item_group'] = $request['item_group'];}
            if (isset($request['brand']) && $request['brand']!='' ) {$brand = $request['brand'];}
            if (isset($request['code']) && $request['code']!='' ) {$code = $request['code'];}
            if (isset($request['item']) && $request['item']!='' ) {$item = $request['item'];}
            if (isset($request['description']) && $request['description']!='' ) {$description = $request['description'];}
            if (isset($request['qty']) && $request['qty']!='' ) {$qty = $request['qty'];}
            if (isset($request['type_transaction']) && $request['type_transaction']!='' ) {$typeTransaction = $request['type_transaction'];}
            if (isset($request['total_price']) && $request['total_price']!='' ) {$totalPrice = $request['total_price'];}
            if (isset($request['status']) && $request['status']!='' ) {$status = $request['status'];}
            if (isset($request['years']) && $request['years']!='' ) {$years = $request['years'];}

            DB::table('stock_adjustment')
            ->where('id','=',$id)
            ->update($updateData);

            return $updateData;
        } catch (\Exception $ex) {
            # Insert Log Error
            $requestModule=[];
            $requestModule['reff'] = '-';
            $requestModule['service'] = 'Class';
            $requestModule['class'] = 'Class_StockAdjustment';
            $requestModule['function'] = 'Update';
            $requestModule['message'] = $ex->getMessage();
            $requestModule['note'] = '-';
            $classModel = new LogError();
            $result = $classModel->insertLogError($request);
            # End Log Error
            return $ex;
        }
    }
}
