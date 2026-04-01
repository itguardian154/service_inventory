<?php

namespace App\Http\Controllers\Class_DB;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Log\LogError;
use App\Models\stock_goods_return_detail;
use Carbon\Carbon;
use DateTime;

class Class_StockGoodsReturnDetail
{
    /**
     * Read table
     */ 
    public function show($request)
    {
        // set value variable
        $noReceive=''; $itemGroup=''; $brand=''; $code=''; $items=''; $description=''; $typeTransaction=''; 
        $qty=''; $unit=''; $price=''; $totalPrice=''; $expDate=''; $remark=''; $years=''; $reff='';

        if (isset($request['no_receive']) && $request['no_receive']!='' ) {$noReceive = $request['no_receive'];}
        if (isset($request['item_group']) && $request['item_group']!='' ) {$itemGroup = $request['item_group'];}
        if (isset($request['brand']) && $request['brand']!='' ) {$brand = $request['brand'];}
        if (isset($request['code']) && $request['code']!='' ) {$code = $request['code'];}
        if (isset($request['items']) && $request['items']!='' ) {$items = $request['items'];}
        if (isset($request['description']) && $request['description']!='' ) {$description = $request['description'];}
        if (isset($request['type_transaction']) && $request['type_transaction']!='' ) {$typeTransaction = $request['type_transaction'];}
        if (isset($request['qty']) && $request['qty']!='' ) {$qty = $request['qty'];}
        if (isset($request['unit']) && $request['unit']!='' ) {$unit = $request['unit'];}
        if (isset($request['price']) && $request['price']!='' ) {$price = $request['price'];}
        if (isset($request['total_price']) && $request['total_price']!='' ) {$totalPrice = $request['total_price'];}
        if (isset($request['exp_date']) && $request['exp_date']!='' ) {$expDate = $request['exp_date'];}
        if (isset($request['remark']) && $request['remark']!='' ) {$remark = $request['remark'];}
        if (isset($request['years']) && $request['years']!='' ) {$years = $request['years'];}
        if (isset($request['reff']) && $request['reff']!='' ) {$reff = $request['reff'];}

        try
        {
            $data_ = DB::table('stock_goods_return_detail');
            if($noReceive!='')
            {
                $data_->where('no_receive',$noReceive);
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
            if($qty!='')
            {
                $data_->where('qty',$qty);
            }
            if($unit!='')
            {
                $data_->where('unit',$unit);
            }
            if($price!='')
            {
                $data_->where('price',$price);
            }
            if($totalPrice!='')
            {
                $data_->where('total_price',$totalPrice);
            }
            if($expDate!='')
            {
                $data_->where('exp_date',$expDate);
            }
            if($remark!='')
            {
                $data_->where('remark',$remark);
            }
            if($years!='')
            {
                $data_->where('years',$years);
            }
            if($reff!='')
            {
                $data_->where('reff',$reff);
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
            $requestModule['class'] = 'Class_StockGoodsReturnDetail';
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
        $noReceive=null; $itemGroup='-'; $brand='-'; $code='-'; $items='-'; $description='-'; 
        $typeTransaction='-'; $qty=0; $unit='-'; $price=0; $totalPrice=0; $expDate=null; $remark='-'; $years=null; $reff=null;

        if (isset($request['no_receive']) && $request['no_receive']!='' ) {$noReceive = $request['no_receive'];}
        if (isset($request['item_group']) && $request['item_group']!='' ) {$itemGroup = $request['item_group'];}
        if (isset($request['brand']) && $request['brand']!='' ) {$brand = $request['brand'];}
        if (isset($request['code']) && $request['code']!='' ) {$code = $request['code'];}
        if (isset($request['items']) && $request['items']!='' ) {$items = $request['items'];}
        if (isset($request['description']) && $request['description']!='' ) {$description = $request['description'];}
        if (isset($request['type_transaction']) && $request['type_transaction']!='' ) {$typeTransaction = $request['type_transaction'];}
        if (isset($request['qty']) && $request['qty']!='' ) {$qty = $request['qty'];}
        if (isset($request['unit']) && $request['unit']!='' ) {$unit = $request['unit'];}
        if (isset($request['price']) && $request['price']!='' ) {$price = $request['price'];}
        if (isset($request['total_price']) && $request['total_price']!='' ) {$totalPrice = $request['total_price'];}
        if (isset($request['exp_date']) && $request['exp_date']!='' ) {$expDate = $request['exp_date'];}
        if (isset($request['remark']) && $request['remark']!='' ) {$remark = $request['remark'];}
        if (isset($request['years']) && $request['years']!='' ) {$years = $request['years'];}
        if (isset($request['reff']) && $request['reff']!='' ) {$reff = $request['reff'];}
        
        try
        {
            // cek data
            $request=[];
            $request['no_receive'] = $noReceive;
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
                $data = new stock_goods_return_detail();
                $data->no_receive = $noReceive;
                $data->item_group = $itemGroup;
                $data->brand = $brand; 
                $data->code = $code; 
                $data->items = $items; 
                $data->description = $description; 
                $data->type_transaction = $typeTransaction; 
                $data->qty = $qty; 
                $data->unit = $unit; 
                $data->price = $price; 
                $data->total_price = $totalPrice; 
                $data->exp_date = $expDate; 
                $data->remark = $remark; 
                $data->years = $years; 
                $data->reff = $reff; 
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
            $requestModule['class'] = 'Class_StockGoodsReturnDetail';
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
            if (isset($request['no_recieve']) && $request['no_recieve']!='' ) {$updateData['no_recieve'] = $request['no_recieve'];}
            if (isset($request['item_group']) && $request['item_group']!='' ) {$updateData['item_group'] = $request['item_group'];}
            if (isset($request['brand']) && $request['brand']!='' ) {$updateData['brand'] = $request['brand'];}
            if (isset($request['code']) && $request['code']!='' ) {$updateData['code'] = $request['code'];}
            if (isset($request['items']) && $request['items']!='' ) {$updateData['items'] = $request['items'];}
            if (isset($request['description']) && $request['description']!='' ) {$updateData['description'] = $request['description'];}
            if (isset($request['type_transaction']) && $request['type_transaction']!='' ) {$updateData['type_transaction'] = $request['type_transaction'];}
            if (isset($request['qty']) && $request['qty']!='' ) {$updateData['qty'] = $request['qty'];}
            if (isset($request['unit']) && $request['unit']!='' ) {$updateData['unit'] = $request['unit'];}
            if (isset($request['price']) && $request['price']!='' ) {$updateData['price'] = $request['price'];}
            if (isset($request['total_price']) && $request['total_price']!='' ) {$updateData['total_price'] = $request['total_price'];}
            if (isset($request['exp_date']) && $request['exp_date']!='' ) {$updateData['exp_date'] = $request['exp_date'];}
            if (isset($request['remark']) && $request['remark']!='' ) {$updateData['remark'] = $request['remark'];}
            if (isset($request['years']) && $request['years']!='' ) {$updateData['years'] = $request['years'];}
            if (isset($request['reff']) && $request['reff']!='' ) {$updateData['reff'] = $request['reff'];}
            
            DB::table('stock_goods_return_detail')
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
            $requestModule['class'] = 'Class_StockGoodsReturnDetail';
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
