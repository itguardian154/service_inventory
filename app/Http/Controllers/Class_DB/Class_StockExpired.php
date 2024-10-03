<?php

namespace App\Http\Controllers\Class_DB;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Model\LogError;
use App\Models\stock_expired;
use Carbon\Carbon;
use DateTime;

class Class_StockExpired 
{
    /**
     * Read table
     */ 
    public function show($request)
    {
        // set value variable
        $idItem=''; $itemGroup=''; $code=''; $item=''; $unit=''; $stock=''; $qty=''; $dateExpired=''; $years='';

        if (isset($request['id_item']) && $request['id_item']!='' ) {$idItem = $request['id_item'];}
        if (isset($request['item_group']) && $request['item_group']!='' ) {$itemGroup = $request['item_group'];}
        if (isset($request['code']) && $request['code']!='' ) {$code = $request['code'];}
        if (isset($request['item']) && $request['item']!='' ) {$item = $request['item'];}
        if (isset($request['unit']) && $request['unit']!='' ) {$unit = $request['unit'];}
        if (isset($request['stock']) && $request['stock']!='' ) {$stock = $request['stock'];}
        if (isset($request['qty']) && $request['qty']!='' ) {$qty = $request['qty'];}
        if (isset($request['date_expired']) && $request['date_expired']!='' ) {$dateExpired = $request['date_expired'];}
        if (isset($request['years']) && $request['years']!='' ) {$years = $request['years'];}

        try
        {
            $data_ = DB::table('stock_expired');
            if($idItems!='')
            {
                $data_->where('id_item',$idItems);
            }
            if($itemGroup!='')
            {
                $data_->where('item_group',$itemGroup);
            }
            if($code!='')
            {
                $data_->where('code',$code);
            }
            if($item!='')
            {
                $data_->where('item',$item);
            }
            if($unit!='')
            {
                $data_->where('unit',$unit);
            }
            if($stock!='')
            {
                $data_->where('stock',$stock);
            }
            if($qty!='')
            {
                $data_->where('qty',$qty);
            }
            if($dateExpired!='')
            {
                $data_->where('date_expired',$dateExpired);
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
            $requestModule['class'] = 'Class_StockExpired';
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
        $idItem=''; $itemGroup=''; $code=''; $item=''; $unit=''; $stock=''; $qty=''; $dateExpired=''; $years='';

        if (isset($request['id_item']) && $request['id_item']!='' ) {$idItem = $request['id_item'];}
        if (isset($request['item_group']) && $request['item_group']!='' ) {$itemGroup = $request['item_group'];}
        if (isset($request['code']) && $request['code']!='' ) {$code = $request['code'];}
        if (isset($request['item']) && $request['item']!='' ) {$item = $request['item'];}
        if (isset($request['unit']) && $request['unit']!='' ) {$unit = $request['unit'];}
        if (isset($request['stock']) && $request['stock']!='' ) {$stock = $request['stock'];}
        if (isset($request['qty']) && $request['qty']!='' ) {$qty = $request['qty'];}
        if (isset($request['date_expired']) && $request['date_expired']!='' ) {$dateExpired = $request['date_expired'];}
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
                $data = new stock_expired();
                $data->id_item = $idItem;
                $data->item_group = $itemGroup;
                $data->code = $code;
                $data->item = $item; 
                $data->unit = $unit; 
                $data->stock = $stock; 
                $data->qty = $qty; 
                $data->date_expired = $dateExpired; 
                $data->years = $years; 
                $data->save();
            // }
            return $data;
        } catch (\Exception $ex) {
            # Insert Log Error
            $requestModule=[];
            $requestModule['reff'] = '-';
            $requestModule['service'] = 'Class';
            $requestModule['class'] = 'Class_StockExpired';
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
            if (isset($request['item_group']) && $request['item_group']!='' ) {$updateData['item_group'] = $request['item_group'];}
            if (isset($request['code']) && $request['code']!='' ) {$updateData['code'] = $request['code'];}
            if (isset($request['item']) && $request['item']!='' ) {$updateData['item'] = $request['item'];}
            if (isset($request['unit']) && $request['unit']!='' ) {$updateData['unit'] = $request['unit'];}
            if (isset($request['stock']) && $request['stock']!='' ) {$updateData['stock'] = $request['stock'];}
            if (isset($request['qty']) && $request['qty']!='' ) {$updateData['qty'] = $request['qty'];}
            if (isset($request['date_expired']) && $request['date_expired']!='' ) {$updateData['date_expired'] = $request['date_expired'];}
            if (isset($request['years']) && $request['years']!='' ) {$updateData['years'] = $request['years'];}

            DB::table('stock_expired')
            ->where('id','=',$id)
            ->update($updateData);

            return $updateData;
        } catch (\Exception $ex) {
            # Insert Log Error
            $requestModule=[];
            $requestModule['reff'] = '-';
            $requestModule['service'] = 'Class';
            $requestModule['class'] = 'Class_StockExpired';
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
