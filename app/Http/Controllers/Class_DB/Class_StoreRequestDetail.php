<?php

namespace App\Http\Controllers\Class_DB;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Log\LogError;
use App\Models\store_request_detail;
use Carbon\Carbon;
use DateTime;

class Class_StoreRequestDetail
{
    /**
     * Read table
     */
    public function show($request)
    {
        // set value variable
        $noTransaction=''; $itemGroup=''; $brand=''; $codeItem=''; $items=''; $description=''; $unit=''; $qty='';
        $price=''; $subTotal=''; $expiredDate=''; $note=''; $years='';

        if (isset($request['no_transaction']) && $request['no_transaction']!='' ) {$noTransaction = $request['no_transaction'];}
        if (isset($request['item_group']) && $request['item_group']!='' ) {$itemGroup = $request['item_group'];}
        if (isset($request['brand']) && $request['brand']!='' ) {$brand = $request['brand'];}
        if (isset($request['code_item']) && $request['code_item']!='' ) {$codeItem = $request['code_item'];}
        if (isset($request['items']) && $request['items']!='' ) {$items = $request['items'];}
        if (isset($request['description']) && $request['description']!='' ) {$description = $request['description'];}
        if (isset($request['unit']) && $request['unit']!='' ) {$unit = $request['unit'];}
        if (isset($request['qty']) && $request['qty']!='' ) {$qty = $request['qty'];}
        if (isset($request['price']) && $request['price']!='' ) {$price = $request['price'];}
        if (isset($request['sub_total']) && $request['sub_total']!='' ) {$subTotal = $request['sub_total'];}
        if (isset($request['expired_date']) && $request['expired_date']!='' ) {$expiredDate = $request['expired_date'];}
        if (isset($request['note']) && $request['note']!='' ) {$note = $request['note'];}
        if (isset($request['years']) && $request['years']!='' ) {$years = $request['years'];}

        try
        {
            $data_ = DB::table('store_request_detail');
            if($noTransaction!='')
            {
                $data_->where('no_transaction',$noTransaction);
            }
            if($itemGroup!='')
            {
                $data_->where('item_group',$itemGroup);
            }
            if($brand!='')
            {
                $data_->where('brand',$brand);
            }
            if($codeItem!='')
            {
                $data_->where('code_item',$codeItem);
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
            if($qty!='')
            {
                $data_->where('qty',$qty);
            }
            if($price!='')
            {
                $data_->where('price',$price);
            }
            if($subTotal!='')
            {
                $data_->where('sub_total',$subTotal);
            }
            if($expiredDate!='')
            {
                $data_->where('expired_date',$expiredDate);
            }
            if($note!='')
            {
                $data_->where('note',$note);
            }
            if($years!='')
            {
                $data_->where('years',$years);
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
            $requestModule['class'] = 'Class_StoreRequestDetail';
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
        $noTransaction=''; $itemGroup=''; $brand=''; $codeItem=''; $items=''; $description=''; $unit=''; $qty='';
        $price=''; $subTotal=''; $expiredDate=''; $note=''; $years='';

        if (isset($request['no_transaction']) && $request['no_transaction']!='' ) {$noTransaction = $request['no_transaction'];}
        if (isset($request['item_group']) && $request['item_group']!='' ) {$itemGroup = $request['item_group'];}
        if (isset($request['brand']) && $request['brand']!='' ) {$brand = $request['brand'];}
        if (isset($request['code_item']) && $request['code_item']!='' ) {$codeItem = $request['code_item'];}
        if (isset($request['items']) && $request['items']!='' ) {$items = $request['items'];}
        if (isset($request['description']) && $request['description']!='' ) {$description = $request['description'];}
        if (isset($request['unit']) && $request['unit']!='' ) {$unit = $request['unit'];}
        if (isset($request['qty']) && $request['qty']!='' ) {$qty = $request['qty'];}
        if (isset($request['price']) && $request['price']!='' ) {$price = $request['price'];}
        if (isset($request['sub_total']) && $request['sub_total']!='' ) {$subTotal = $request['sub_total'];}
        if (isset($request['expired_date']) && $request['expired_date']!='' ) {$expiredDate = $request['expired_date'];}
        if (isset($request['note']) && $request['note']!='' ) {$note = $request['note'];}
        if (isset($request['years']) && $request['years']!='' ) {$years = $request['years'];}
        try
        {
            // cek data
            // $request=[];
            // $request['no_transaction'] = $noTransaction;
            // $request['type_transaction'] = $typeTransaction;
            // $request['detail_item'] = $detailItem;
            // $dataTransaction = $this->show($request);
            // if($dataTransaction['success'])
            // {
            //     return [
            //         'success' => false,
            //         'message' => 'Double Data',
            //         'data' => $dataTransaction
            //     ];
            // }
            // else
            // {
                $data = new store_request_detail();
                $data->no_transaction = $noTransaction;
                $data->item_group = $itemGroup;
                $data->brand = $brand; 
                $data->code_item = $codeItem; 
                $data->items = $items; 
                $data->description = $description; 
                $data->unit = $unit; 
                $data->qty = $qty; 
                $data->price = $price; 
                $data->sub_total = $subTotal; 
                $data->expired_date = $expiredDate; 
                $data->note = $note; 
                $data->years = $years; 
                $data->save();
                
                return [
                    'success' => true,
                    'message' => 'Insert successful',
                    'data' => $data
                ];
            // }
            return $data;
        } catch (\Exception $ex) {
            # Insert Log Error
            $requestModule=[];
            $requestModule['reff'] = '-';
            $requestModule['service'] = 'Class';
            $requestModule['class'] = 'Class_StoreRequestDetail';
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
            if (isset($request['no_transaction']) && $request['no_transaction']!='' ) {$updateData['no_transaction'] = $request['no_transaction'];}
            if (isset($request['item_group']) && $request['item_group']!='' ) {$updateData['item_group'] = $request['item_group'];}
            if (isset($request['brand']) && $request['brand']!='' ) {$updateData['brand'] = $request['brand'];}
            if (isset($request['code_item']) && $request['code_item']!='' ) {$updateData['code_item'] = $request['code_item'];}
            if (isset($request['items']) && $request['items']!='' ) {$updateData['items'] = $request['items'];}
            if (isset($request['description']) && $request['description']!='' ) {$updateData['description'] = $request['description'];}
            if (isset($request['unit']) && $request['unit']!='' ) {$updateData['unit'] = $request['unit'];}
            if (isset($request['qty']) && $request['qty']!='' ) {$updateData['qty'] = $request['qty'];}
            if (isset($request['price']) && $request['price']!='' ) {$updateData['price'] = $request['price'];}
            if (isset($request['sub_total']) && $request['sub_total']!='' ) {$updateData['sub_total'] = $request['sub_total'];}
            if (isset($request['expired_date']) && $request['expired_date']!='' ) {$updateData['expired_date'] = $request['expired_date'];}
            if (isset($request['note']) && $request['note']!='' ) {$updateData['note'] = $request['note'];}
            if (isset($request['years']) && $request['years']!='' ) {$updateData['years'] = $request['years'];}

            DB::table('store_request_detail')
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
            $requestModule['class'] = 'Class_StoreRequestDetail';
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