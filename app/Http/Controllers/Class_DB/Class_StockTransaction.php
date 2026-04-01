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
        $itemGroup=''; $brand=''; $code=''; $items=''; $description=''; $typeTransaction=''; $in=''; $out=''; $noTransaction=''; $qty=''; $origionOfGoods=''; $date=''; $years='';
        $dateStart=''; $dateEnd='';
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
        if (isset($request['date_start']) && $request['date_start']!='' ) {$dateStart = $request['date_start'];}
        if (isset($request['date_end']) && $request['date_end']!='' ) {$dateEnd = $request['date_end'];}
        

        try
        {
            $data_ = DB::table('stock_transaction');
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
                if($origionOfGoods=='1')
                {
                    $origionOfGoods_ = 'Good Receive';
                }
                if($origionOfGoods=='2')
                {
                    $origionOfGoods_= 'Store Request;';
                }
                if($origionOfGoods_=='3')
                {
                    $origionOfGoods_='Adjustment';
                }
                $data_->where('origin_of_goods',$origionOfGoods_);
            }
            if($date!='')
            {
                $data_->where('date',$date);
            }
            if($years!='')
            {
                $data_->where('years',$years);
            }
            if($dateStart!='')
            {
                $data_->whereBetween('date', [$dateStart, $dateEnd]);
            }
            
            if($data_->exists())
            {
                $data_->orderBy('id', 'asc');
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
            $requestModule['class'] = 'Class_StockTransaction';
            $requestModule['function'] = 'Show';
            $requestModule['message'] = $ex->getMessage();
            $requestModule['note'] = '-';
            $classModel = new LogError();
            $result = $classModel->insertLogError($request);
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
        $itemGroup=''; $brand=''; $code=''; $items=''; $description=''; $typeTransaction=''; $in=0; $out=0; $noTransaction=''; $qty=0; $origionOfGoods=''; $date=''; $years='';

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
        if (isset($request['origin_of_goods']) && $request['origin_of_goods']!='' ) {$origionOfGoods = $request['origin_of_goods'];}
        if (isset($request['date']) && $request['date']!='' ) {$date = $request['date'];}
        if (isset($request['years']) && $request['years']!='' ) {$years = $request['years'];}

        try
        {
            // cek data
            $request=[];
            $request['type_transaction'] = $typeTransaction;
            $request['no_transaction'] = $noTransaction;
            $request['code'] = $code;
            $request['qty'] = $qty;
        
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
                $data = new stock_transaction();
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

                return [
                    'success' => true,
                    'message' => 'Insert successful',
                    'data' => $data
                ];
            }

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
            $requestModule['class'] = 'Class_StockTransaction';
            $requestModule['function'] = 'Update';
            $requestModule['message'] = $ex->getMessage();
            $requestModule['note'] = '-';
            $classModel = new LogError();
            $result = $classModel->insertLogError($request);
            # End Log Error
            return [
                'success' => false,
                'message' => $ex->getMessage()
            ];
        }
    }
}
