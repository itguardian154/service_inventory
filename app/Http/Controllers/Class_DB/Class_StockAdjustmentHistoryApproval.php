<?php

namespace App\Http\Controllers\Class_DB;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Model\LogError;
use App\Models\stock_adjustment_history_approval;
use Carbon\Carbon;
use DateTime;

class Class_StockAdjustmentHistoryApproval
{
     /**
     * Read table
     */ 
    public function show($request)
    {
        // set value variable
        $noAdjustment=''; $ord=''; $pic=''; $name=''; $grade=''; $departemen=''; $signature=''; $status=''; $years='';

        if (isset($request['no_adjustment']) && $request['no_adjustment']!='' ) {$noAdjustment = $request['no_adjustment'];}
        if (isset($request['ord']) && $request['ord']!='' ) {$ord = $request['ord'];}
        if (isset($request['pic']) && $request['pic']!='' ) {$pic = $request['pic'];}
        if (isset($request['name']) && $request['name']!='' ) {$name = $request['name'];}
        if (isset($request['grade']) && $request['grade']!='' ) {$grade = $request['grade'];}
        if (isset($request['departemen']) && $request['departemen']!='' ) {$departemen = $request['departemen'];}
        if (isset($request['signature']) && $request['signature']!='' ) {$signature = $request['signature'];}
        if (isset($request['status']) && $request['status']!='' ) {$status = $request['status'];}
        if (isset($request['years']) && $request['years']!='' ) {$years = $request['years'];}

        try
        {
            $data_ = DB::table('stock_adjustment_history_approval');
            if($noAdjustment!='')
            {
                $data_->where('no_adjustment',$noAdjustment);
            }
            if($ord!='')
            {
                $data_->where('ord',$ord);
            }
            if($pic!='')
            {
                $data_->where('pic',$pic);
            }   
            if($name!='')
            {
                $data_->where('name',$name);
            }
            if($grade!='')
            {
                $data_->where('grade',$grade);
            }
            if($signature!='')
            {
                $data_->where('signature',$signature);
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
            $requestModule['class'] = 'Class_StockAdjustmentHistoryApproval';
            $requestModule['function'] = 'Show';
            $requestModule['message'] = $ex->getMessage();
            $requestModule['note'] = '-';
            $classModel = new LogError();
            $result = $classModel->insertLogError($requestModule);
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
        $noAdjustment='-'; $ord='0'; $pic='-';$idKaryawan='-'; $name='-'; $grade='-'; $departemen='-'; $signature='-'; $status='0'; $years='-';

        if (isset($request['no_adjustment']) && $request['no_adjustment']!='' ) {$noAdjustment = $request['no_adjustment'];}
        if (isset($request['ord']) && $request['ord']!='' ) {$ord = $request['ord'];}
        if (isset($request['pic']) && $request['pic']!='' ) {$pic = $request['pic'];}
        if (isset($request['id_karyawan']) && $request['id_karyawan']!='' ) {$idKaryawan = $request['id_karyawan'];}
        if (isset($request['name']) && $request['name']!='' ) {$name = $request['name'];}
        if (isset($request['grade']) && $request['grade']!='' ) {$grade = $request['grade'];}
        if (isset($request['departemen']) && $request['departemen']!='' ) {$departemen = $request['departemen'];}
        if (isset($request['signature']) && $request['signature']!='' ) {$signature = $request['signature'];}
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
                $data = new stock_adjustment_history_approval();
                $data->no_adjustment = $noAdjustment;
                $data->ord = $ord;
                $data->pic = $pic; 
                $data->id_karyawan = $idKaryawan;
                $data->name = $name; 
                $data->grade = $grade; 
                $data->departemen = $departemen; 
                $data->signature = $signature; 
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
            $result = $classModel->insertLogError($requestModule);
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

            if (isset($request['no_adjustment']) && $request['no_adjustment']!='' ) {$updateData['no_adjustment'] = $request['no_adjustment'];}
            if (isset($request['ord']) && $request['ord']!='' ) {$updateData['ord'] = $request['ord'];}
            if (isset($request['pic']) && $request['pic']!='' ) {$updateData['pic'] = $request['pic'];}
            if (isset($request['name']) && $request['name']!='' ) {$updateData['name'] = $request['name'];}
            if (isset($request['grade']) && $request['grade']!='' ) {$updateData['grade'] = $request['grade'];}
            if (isset($request['departemen']) && $request['departemen']!='' ) {$updateData['departemen'] = $request['departemen'];}
            if (isset($request['signature']) && $request['signature']!='' ) {$updateData['signature'] = $request['signature'];}
            if (isset($request['status']) && $request['status']!='' ) {$updateData['status'] = $request['status'];}
            if (isset($request['date']) && $request['date']!='' ) {$updateData['date'] = $request['date'];}
            if (isset($request['note']) && $request['note']!='' ) {$updateData['note'] = $request['note'];}
            if (isset($request['years']) && $request['years']!='' ) {$updateData['years'] = $request['years'];}

            DB::table('stock_adjustment_history_approval')
            ->where('id','=',$id)
            ->update($updateData);

            return $updateData;
        } catch (\Exception $ex) {
            # Insert Log Error
            $requestModule=[];
            $requestModule['reff'] = '-';
            $requestModule['service'] = 'Class';
            $requestModule['class'] = 'Class_StockAdjustmentHistoryApproval';
            $requestModule['function'] = 'Update';
            $requestModule['message'] = $ex->getMessage();
            $requestModule['note'] = '-';
            $classModel = new LogError();
            $result = $classModel->insertLogError($requestModule);
            # End Log Error
            return $ex;
        }
    }
}
