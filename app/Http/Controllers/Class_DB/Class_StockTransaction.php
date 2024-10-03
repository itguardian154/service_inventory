<?php

namespace App\Http\Controllers\Class_DB;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Log\LogError;
use App\Models\stock_transaction;
use Carbon\Carbon;
use DateTime;

class Class_StockTransaction
{
   /**
     * Read table
     */ 
    public function show($request)
    {
        // set value variable
        $idItem=''; $itemGroup=''; $brand=''; $code=''; $items=''; $description=''; $typeTransaction=''; $in=''; $out=''; $noTransaction=''; $qty=''; $origionOfGoods=''; $date=''; $years='';
        if (isset($request['id_item']) && $request['id_item']!='' ) {$idItem = $request['id_item'];}
        if (isset($request['item_group']) && $request['item_group']!='' ) {$itemGroup = $request['item_group'];}
        if (isset($request['brand']) && $request['brand']!='' ) {$brand = $request['brand'];}
        if (isset($request['code']) && $request['code']!='' ) {$code = $request['code'];}
        if (isset($request['items']) && $request['items']!='' ) {$items = $request['items'];}
        if (isset($request['description']) && $request['description']!='' ) {$description = $request['description'];}
        if (isset($request['type_transaction']) && $request['type_transaction']!='' ) {$typeTransaction = $request['type_transaction'];}
        if (isset($request['in']) && $request['in']!='' ) {$in = $request['in'];}
        if (isset($request['out']) && $request['out']!='' ) {$out = $request['out'];}
        if (isset($request['no_transaction']) && $request['no_transaction']!='' ) {$noTransaction = $request['no_transaction'];}
        if (isset($request['qty']) && $request['qty']!='' ) {$qty = $request['qty'];}
        if (isset($request['origin_of_goods']) && $request['origin_of_goods']!='' ) {$items = $request['origin_of_goods'];}
        if (isset($request['date']) && $request['date']!='' ) {$date = $request['date'];}
        if (isset($request['years']) && $request['years']!='' ) {$years = $request['years'];}


        try
        {
            $data_ = DB::table('stock_transaction');
            if($idItems!='')
            {
                $data_->where('id_item',$idItems);
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
            if($items!='')
            {
                $data_->where('items',$items);
            }
            if($description!='')
            {
                $data_->where('description',$description);
            }
            if($typeTransaction!='')
            {
                $data_->where('type_transaction',$typeTransaction);
            }
            if($in!='')
            {
                $data_->where('in',$in);
            }
            if($out!='')
            {
                $data_->where('out',$out);
            }
            if($noTransaction!='')
            {
                $data_->where('no_transaction',$noTransaction);
            }
            if($qty!='')
            {
                $data_->where('qty',$qty);
            }
            if($origionOfGoods!='')
            {
                $data_->where('origin_of_goods',$origionOfGoods);
            }
            if($date!='')
            {
                $data_->where('date',$date);
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
            $requestModule['class'] = 'Class_StockTransaction';
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
        $idItem=''; $itemGroup=''; $brand=''; $code=''; $items=''; $description=''; $typeTransaction=''; $in=''; $out=''; $noTransaction=''; $qty=''; $origionOfGoods=''; $date=''; $years='';
        
        if (isset($request['id_item']) && $request['id_item']!='' ) {$idItem = $request['id_item'];}
        if (isset($request['item_group']) && $request['item_group']!='' ) {$itemGroup = $request['item_group'];}
        if (isset($request['brand']) && $request['brand']!='' ) {$brand = $request['brand'];}
        if (isset($request['code']) && $request['code']!='' ) {$code = $request['code'];}
        if (isset($request['items']) && $request['items']!='' ) {$items = $request['items'];}
        if (isset($request['description']) && $request['description']!='' ) {$description = $request['description'];}
        if (isset($request['type_transaction']) && $request['type_transaction']!='' ) {$typeTransaction = $request['type_transaction'];}
        if (isset($request['in']) && $request['in']!='' ) {$in = $request['in'];}
        if (isset($request['out']) && $request['out']!='' ) {$out = $request['out'];}
        if (isset($request['no_transaction']) && $request['no_transaction']!='' ) {$noTransaction = $request['no_transaction'];}
        if (isset($request['qty']) && $request['qty']!='' ) {$qty = $request['qty'];}
        if (isset($request['origin_of_goods']) && $request['origin_of_goods']!='' ) {$items = $request['origin_of_goods'];}
        if (isset($request['date']) && $request['date']!='' ) {$date = $request['date'];}
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
                $data = new stock_transaction();
                $data->id_item = $idItem;
                $data->item_group = $itemGroup;
                $data->brand = $brand;
                $data->code = $code; 
                $data->items = $items; 
                $data->description = $description; 
                $data->type_transaction = $typeTransaction; 
                $data->in = $in; 
                $data->out = $out; 
                $data->no_transaction = $noTransaction; 
                $data->qty = $qty; 
                $data->origin_of_goods = $origionOfGoods; 
                $data->date = $date;
                $data->years = $years;  
                $data->save();
            // }
            return $data;
        } catch (\Exception $ex) {
            # Insert Log Error
            $requestModule=[];
            $requestModule['reff'] = '-';
            $requestModule['service'] = 'Class';
            $requestModule['class'] = 'Class_StockTransaction';
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
            if (isset($request['brand']) && $request['brand']!='' ) {$updateData['brand'] = $request['brand'];}
            if (isset($request['code']) && $request['code']!='' ) {$updateData['code'] = $request['code'];}
            if (isset($request['items']) && $request['items']!='' ) {$updateData['items'] = $request['items'];}
            if (isset($request['description']) && $request['description']!='' ) {$updateData['description'] = $request['description'];}
            if (isset($request['type_transaction']) && $request['type_transaction']!='' ) {$updateData['type_transaction'] = $request['type_transaction'];}
            if (isset($request['in']) && $request['in']!='' ) {$updateData['in'] = $request['in'];}
            if (isset($request['out']) && $request['out']!='' ) {$updateData['out'] = $request['out'];}
            if (isset($request['no_transaction']) && $request['no_transaction']!='' ) {$updateData['no_transaction'] = $request['no_transaction'];}
            if (isset($request['qty']) && $request['qty']!='' ) {$updateData['qty'] = $request['qty'];}
            if (isset($request['origin_of_goods']) && $request['origin_of_goods']!='' ) {$updateData['origin_of_goods'] = $request['origin_of_goods'];}
            if (isset($request['date']) && $request['date']!='' ) {$updateData['date'] = $request['date'];}
            if (isset($request['years']) && $request['years']!='' ) {$updateData['years'] = $request['years'];}
            

            DB::table('stock_log')
            ->where('id','=',$id)
            ->update($updateData);

            return $updateData;
        } catch (\Exception $ex) {
            # Insert Log Error
            $requestModule=[];
            $requestModule['reff'] = '-';
            $requestModule['service'] = 'Class';
            $requestModule['class'] = 'Class_StockTransaction';
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
