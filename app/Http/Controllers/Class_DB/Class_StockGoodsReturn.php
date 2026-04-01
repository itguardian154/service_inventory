<?php

namespace App\Http\Controllers\Class_DB;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Log\LogError;
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
        $noReceive=''; $date=''; $type=''; $supplier=''; $detailGoodsReturn=''; $status=''; $years=''; $reff='';

        if (isset($request['no_receive']) && $request['no_receive']!='' ) {$noReceive = $request['no_receive'];}
        if (isset($request['date']) && $request['date']!='' ) {$date = $request['date'];}
        if (isset($request['type']) && $request['type']!='' ) {$type = $request['type'];}
        if (isset($request['supplier']) && $request['supplier']!='' ) {$supplier = $request['supplier'];}
        if (isset($request['detail_goods_return']) && $request['detail_goods_return']!='' ) {$detailG = $request['detail_goods_return'];}
        if (isset($request['status']) && $request['status']!='' ) {$status = $request['status'];}
        if (isset($request['years']) && $request['years']!='' ) {$years = $request['years'];}
        if (isset($request['reff']) && $request['reff']!='' ) {$reff = $request['reff'];}

        try
        {
            $data_ = DB::table('stock_goods_return');
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
            if($supplier!='')
            {
                $data_->where('supplier',$supplier);
            }
            if($detailGoodsReturn!='')
            {
                $data_->where('detail_goods_return',$detailGoodsReturn);
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
        $noReceive='-'; $date=Carbon::now()->format('Y-m-d'); $type='-'; $supplier='-'; $detailGoodsReturn=''; $status='-'; $years=Carbon::now()->format('Y'); $reff='-';

        if (isset($request['no_receive']) && $request['no_receive']!='' ) {$noReceive = $request['no_receive'];}
        if (isset($request['date']) && $request['date']!='' ) {$date = $request['date'];}
        if (isset($request['type']) && $request['type']!='' ) {$type = $request['type'];}
        if (isset($request['supplier']) && $request['supplier']!='' ) {$supplier = $request['supplier'];}
        if (isset($request['detail_goods_return']) && $request['detail_goods_return']!='' ) {$detailGoodsReturn = $request['detail_goods_return'];}
        if (isset($request['status']) && $request['status']!='' ) {$status = $request['status'];}
        if (isset($request['years']) && $request['years']!='' ) {$years = $request['years'];}
        if (isset($request['reff']) && $request['reff']!='' ) {$reff = $request['reff'];}
        
        try
        {
            // cek data
            $request=[];
            $request['no_receive'] = $noReceive;
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
                $data = new stock_goods_return();
                $data->no_receive = $noReceive;
                $data->date = $date;
                $data->type = $type; 
                $data->supplier = $supplier; 
                $data->detail_goods_return = $detailGoodsReturn; 
                $data->status = $status; 
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
            $requestModule['class'] = 'Class_StockGoodsReturn';
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
            if (isset($request['no_receive']) && $request['no_receive']!='' ) {$updateData['no_receive'] = $request['no_receive'];}
            if (isset($request['date']) && $request['date']!='' ) {$updateData['date'] = $request['date'];}
            if (isset($request['type']) && $request['type']!='' ) {$updateData['type'] = $request['type'];}
            if (isset($request['supplier']) && $request['supplier']!='' ) {$updateData['supplier'] = $request['supplier'];}
            if (isset($request['detail_goods_return']) && $request['detail_goods_return']!='' ) {$updateData['detail_goods_return'] = $request['detail_goods_return'];}
            if (isset($request['status']) && $request['status']!='' ) {$updateData['status'] = $request['status'];}
            if (isset($request['years']) && $request['years']!='' ) {$updateData['years'] = $request['years'];}
            if (isset($request['reff']) && $request['reff']!='' ) {$updateData['reff'] = $request['reff'];}

            DB::table('stock_goods_return')
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
            $requestModule['class'] = 'Class_StockGoodsReturn';
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
