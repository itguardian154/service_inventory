<?php

namespace App\Http\Controllers\Class_DB;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Model\LogError;
use App\Models\stock_goods_return;
use Carbon\Carbon;
use DateTime;


class Class_StockGoodsReturn
{
    /**
     * Read table
     */ 
    public function show($request)
    {
        // set value variable
        $idItem=''; $noReceive=''; $date=''; $type=''; $itemGroup=''; $brand=''; $code=''; $item=''; $detail=''; $qty=''; $uni=''; $status=''; $years='';

        if (isset($request['id_item']) && $request['id_item']!='' ) {$idItem = $request['id_item'];}
        if (isset($request['no_receive']) && $request['no_receive']!='' ) {$noReceive = $request['no_receive'];}
        if (isset($request['date']) && $request['date']!='' ) {$date = $request['date'];}
        if (isset($request['type']) && $request['type']!='' ) {$type = $request['type'];}
        if (isset($request['item_group']) && $request['item_group']!='' ) {$itemGroup = $request['item_group'];}
        if (isset($request['brand']) && $request['brand']!='' ) {$brand = $request['brand'];}
        if (isset($request['code']) && $request['code']!='' ) {$code = $request['code'];}
        if (isset($request['item']) && $request['item']!='' ) {$item = $request['item'];}
        if (isset($request['detail']) && $request['detail']!='' ) {$detail = $request['detail'];}
        if (isset($request['qty']) && $request['qty']!='' ) {$qty = $request['qty'];}
        if (isset($request['unit']) && $request['unit']!='' ) {$unit = $request['unit'];}
        if (isset($request['status']) && $request['status']!='' ) {$status = $request['status'];}
        if (isset($request['years']) && $request['years']!='' ) {$years = $request['years'];}

        try
        {
            $data_ = DB::table('stock_goods_return');
            if($idItems!='')
            {
                $data_->where('id_item',$idItems);
            }
            if($noReceive!='')
            {
                $data_->where('no_receive',$noReceive);
            }
            if($date!='')
            {
                $data_->where('date',$date);
            }
            if($type!='')
            {
                $data_->where('type',$type);
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
            if($detail!='')
            {
                $data_->where('detail',$detail);
            }
            if($qty!='')
            {
                $data_->where('qty',$qty);
            }
            if($unit!='')
            {
                $data_->where('unit',$unit);
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
            $requestModule['class'] = 'Class_StockGoodsReturn';
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
        $idItem=''; $noReceive=''; $date=''; $type=''; $itemGroup=''; $brand=''; $code=''; $item=''; $detail=''; $qty=''; $uni=''; $status=''; $years='';

        if (isset($request['id_item']) && $request['id_item']!='' ) {$idItem = $request['id_item'];}
        if (isset($request['no_receive']) && $request['no_receive']!='' ) {$noReceive = $request['no_receive'];}
        if (isset($request['date']) && $request['date']!='' ) {$date = $request['date'];}
        if (isset($request['type']) && $request['type']!='' ) {$type = $request['type'];}
        if (isset($request['item_group']) && $request['item_group']!='' ) {$itemGroup = $request['item_group'];}
        if (isset($request['brand']) && $request['brand']!='' ) {$brand = $request['brand'];}
        if (isset($request['code']) && $request['code']!='' ) {$code = $request['code'];}
        if (isset($request['item']) && $request['item']!='' ) {$item = $request['item'];}
        if (isset($request['detail']) && $request['detail']!='' ) {$detail = $request['detail'];}
        if (isset($request['qty']) && $request['qty']!='' ) {$qty = $request['qty'];}
        if (isset($request['unit']) && $request['unit']!='' ) {$unit = $request['unit'];}
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
                $data = new stock_goods_return();
                $data->id_item = $idItem;
                $data->no_receive = $noReceive;
                $data->date = $date;
                $data->type = $type; 
                $data->item_group = $itemGroup; 
                $data->brand = $brand; 
                $data->code = $code; 
                $data->item = $item; 
                $data->detail = $detail; 
                $data->qty = $qty; 
                $data->unit = $unit; 
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
            $requestModule['class'] = 'Class_StockGoodsReturn';
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
            if (isset($request['no_receive']) && $request['no_receive']!='' ) {$updateData['no_receive'] = $request['no_receive'];}
            if (isset($request['date']) && $request['date']!='' ) {$updateData['date'] = $request['date'];}
            if (isset($request['type']) && $request['type']!='' ) {$updateData['type'] = $request['type'];}
            if (isset($request['item_group']) && $request['item_group']!='' ) {$updateData['item_group'] = $request['item_group'];}
            if (isset($request['brand']) && $request['brand']!='' ) {$updateData['brand'] = $request['brand'];}
            if (isset($request['code']) && $request['code']!='' ) {$updateData['code'] = $request['code'];}
            if (isset($request['item']) && $request['item']!='' ) {$updateData['item'] = $request['item'];}
            if (isset($request['detail']) && $request['detail']!='' ) {$updateData['detail'] = $request['detail'];}
            if (isset($request['qty']) && $request['qty']!='' ) {$updateData['qty'] = $request['qty'];}
            if (isset($request['unit']) && $request['unit']!='' ) {$updateData['unit'] = $request['unit'];}
            if (isset($request['status']) && $request['status']!='' ) {$updateData['status'] = $request['status'];}
            if (isset($request['years']) && $request['years']!='' ) {$updateData['years'] = $request['years'];}

            DB::table('stock_goods_return')
            ->where('id','=',$id)
            ->update($updateData);

            return $updateData;
        } catch (\Exception $ex) {
            # Insert Log Error
            $requestModule=[];
            $requestModule['reff'] = '-';
            $requestModule['service'] = 'Class';
            $requestModule['class'] = 'Class_StockGoodsReturn';
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
