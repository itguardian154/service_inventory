<?php

namespace App\Http\Controllers\Class_DB;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Log\LogError;
use App\Models\stock_adjustment;
use Carbon\Carbon;
use DateTime;

class Class_StockAdjustment
{
    /**
     * Read table
     */ 
    public function show($request)
    {
        // set value variable
        $noAdjustment=''; $date=''; $totalItem=''; $totalQty=''; $totalPrice=''; $status=''; $years=''; $reff='';

        if (isset($request['no_adjustment']) && $request['no_adjustment']!='' ) {$noAdjustment = $request['no_adjustment'];}
        if (isset($request['date']) && $request['date']!='' ) {$date = $request['date'];}
        if (isset($request['total_item']) && $request['total_item']!='' ) {$totalItem = $request['total_item'];}
        if (isset($request['total_qty']) && $request['total_qty']!='' ) {$totalQty = $request['total_qty'];}
        if (isset($request['total_price']) && $request['total_price']!='' ) {$totalPrice = $request['total_price'];}
        if (isset($request['status']) && $request['status']!='' ) {$status = $request['status'];}
        if (isset($request['years']) && $request['years']!='' ) {$years = $request['years'];}
        if (isset($request['reff']) && $request['reff']!='' ) {$reff = $request['reff'];}

        try
        {
            $data_ = DB::table('stock_adjustment');
            if($noAdjustment!='')
            {
                $data_->where('no_adjustment',$noAdjustment);
            }
            if($date!='')
            {
                $data_->where('date',$date);
            }
            if($totalItem!='')
            {
                $data_->where('total_item',$totalItem);
            }   
            if($totalQty!='')
            {
                $data_->where('total_qty',$totalQty);
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
            $requestModule['class'] = 'Class_StockAdjustment';
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
        $noAdjustment=''; $date=''; $totalItem=''; $totalQty=''; $totalPrice=''; $status='0'; $years=''; $reff='';

        if (isset($request['no_adjustment']) && $request['no_adjustment']!='' ) {$noAdjustment = $request['no_adjustment'];}
        if (isset($request['date']) && $request['date']!='' ) {$date = $request['date'];}
        if (isset($request['total_item']) && $request['total_item']!='' ) {$totalItem = $request['total_item'];}
        if (isset($request['total_qty']) && $request['total_qty']!='' ) {$totalQty = $request['total_qty'];}
        if (isset($request['total_price']) && $request['total_price']!='' ) {$totalPrice = $request['total_price'];}
        if (isset($request['status']) && $request['status']!='' ) {$status = $request['status'];}
        if (isset($request['years']) && $request['years']!='' ) {$years = $request['years'];}
        if (isset($request['reff']) && $request['reff']!='' ) {$reff = $request['reff'];}
        
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
                $data->no_adjustment = $noAdjustment;
                $data->date = $date;
                $data->total_item = $totalItem; 
                $data->total_qty = $totalQty; 
                $data->total_price = $totalPrice; 
                $data->status = $status; 
                $data->years = $years; 
                $data->reff = $reff; 
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
            $requestModule['class'] = 'Class_StockAdjustment';
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
            if (isset($request['no_adjustment']) && $request['no_adjustment']!='' ) {$updateData['no_adjustment'] = $request['no_adjustment'];}
            if (isset($request['date']) && $request['date']!='' ) {$updateData['date'] = $request['date'];}
            if (isset($request['total_item']) && $request['total_item']!='' ) {$updateData['total_item'] = $request['total_item'];}
            if (isset($request['total_qty']) && $request['total_qty']!='' ) {$updateData['total_qty'] = $request['total_qty'];}
            if (isset($request['total_price']) && $request['total_price']!='' ) {$updateData['total_price'] = $request['total_price'];}
            if (isset($request['status']) && $request['status']!='' ) {$updateData['status'] = $request['status'];}
            if (isset($request['years']) && $request['years']!='' ) {$updateData['years'] = $request['years'];}
            if (isset($request['reff']) && $request['reff']!='' ) {$updateData['reff'] = $request['reff'];}

            DB::table('stock_adjustment')
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
            $requestModule['class'] = 'Class_StockAdjustment';
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
