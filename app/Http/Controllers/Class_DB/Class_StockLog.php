<?php

namespace App\Http\Controllers\Class_DB;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Log\LogError;
use App\Http\Controllers\Class_DB\Class_StockLog;
use App\Models\stock_log;
use Carbon\Carbon;
use DateTime;

class Class_StockLog
{
    /**
     * Read table
     */ 
    public function show($request)
    {
        // set value variable
        $idItem=''; $reff=''; $activity=''; $detailAct=''; $years='';

        if (isset($request['id_item']) && $request['id_item']!='' ) {$idItem = $request['id_item'];}
        if (isset($request['reff']) && $request['reff']!='' ) {$reff = $request['reff'];}
        if (isset($request['activity']) && $request['activity']!='' ) {$activity = $request['activity'];}
        if (isset($request['detail_act']) && $request['detail_act']!='' ) {$detailAct = $request['detail_act'];}
        if (isset($request['years']) && $request['years']!='' ) {$years = $request['years'];}

        try
        {
            $data_ = DB::table('stock_log');
            if($idItems!='')
            {
                $data_->where('id_item',$idItems);
            }
            if($reff!='')
            {
                $data_->where('reff',$reff);
            }
            if($activity!='')
            {
                $data_->where('activity',$activity);
            }
            if($detailAct!='')
            {
                $data_->where('detail_act',$detailAct);
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
            $requestModule['class'] = 'Class_StockLog';
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
        $idItem=''; $reff=''; $activity=''; $detailAct=''; $years='';

        if (isset($request['id_item']) && $request['id_item']!='' ) {$idItem = $request['id_item'];}
        if (isset($request['reff']) && $request['reff']!='' ) {$reff = $request['reff'];}
        if (isset($request['activity']) && $request['activity']!='' ) {$activity = $request['activity'];}
        if (isset($request['detail_act']) && $request['detail_act']!='' ) {$detailAct = $request['detail_act'];}
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
                $data = new stock_log();
                $data->id_item = $idItem;
                $data->reff = $reff;
                $data->activity = $activity;
                $data->detail_act = $detailAct; 
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
            $requestModule['class'] = 'Class_StockLog';
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
            if (isset($request['reff']) && $request['reff']!='' ) {$updateData['reff'] = $request['reff'];}
            if (isset($request['activity']) && $request['activity']!='' ) {$updateData['activity'] = $request['activity'];}
            if (isset($request['detail_act']) && $request['detail_act']!='' ) {$updateData['detail_act'] = $request['detail_act'];}
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
            $requestModule['class'] = 'Class_StockLog';
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
