<?php

namespace App\Http\Controllers\Class_DB;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Log\LogError;
use App\Models\stock_goods_return_history_approval;
use Carbon\Carbon;
use DateTime;

class Class_StockGoodsReturnHistoryApproval
{
    /**
     * Read table
     */
    public function show($request)
    {
        // set value variable
        $noReceive=''; $ord=''; $idRoleAccess=''; $pic=''; $idKaryawan=''; $name=''; $grade=''; $departemen=''; $signature='';
        $status=''; $date=''; $note=''; $years='';

        if (isset($request['no_receive']) && $request['no_receive']!='' ) {$noReceive = $request['no_receive'];}
        if (isset($request['ord']) && $request['ord']!='' ) {$ord = $request['ord'];}
        if (isset($request['id_role_access']) && $request['id_role_access']!='' ) {$idRoleAccess = $request['id_role_access'];}
        if (isset($request['pic']) && $request['pic']!='' ) {$pic = $request['pic'];}
        if (isset($request['id_karyawan']) && $request['id_karyawan']!='' ) {$idKaryawan = $request['id_karyawan'];}
        if (isset($request['name']) && $request['name']!='' ) {$name = $request['name'];}
        if (isset($request['grade']) && $request['grade']!='' ) {$grade = $request['grade'];}
        if (isset($request['departemen']) && $request['departemen']!='' ) {$departemen = $request['departemen'];}
        if (isset($request['signature']) && $request['signature']!='' ) {$signature = $request['signature'];}
        if (isset($request['status']) && $request['status']!='' ) {$status = $request['status'];}
        if (isset($request['date']) && $request['date']!='' ) {$date = $request['date'];}
        if (isset($request['note']) && $request['note']!='' ) {$note = $request['note'];}
        if (isset($request['years']) && $request['years']!='' ) {$years = $request['years'];}

        try
        {
            $data_ = DB::table('stock_goods_return_history_approval');
            if($noReceive!='')
            {
                $data_->where('no_receive',$noReceive);
            }
            if($ord!='')
            {
                $data_->where('ord',$ord);
            }
            if($idRoleAccess!='')
            {
                $data_->where('id_role_access',$idRoleAccess);
            }
            if($pic!='')
            {
                $data_->where('pic',$pic);
            }
            if($idKaryawan!='')
            {
                $data_->where('id_karyawan',$idKaryawan);
            }
            if($name!='')
            {
                $data_->where('name',$name);
            }
            if($grade!='')
            {
                $data_->where('grade',$grade);
            }
            if($departemen!='')
            {
                $data_->where('departemen',$departemen);
            }
            if($signature!='')
            {
                $data_->where('signature',$signature);
            }
            if($status!='')
            {
                $data_->where('status',$status);
            }
            if($date!='')
            {
                $data_->where('date',$date);
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
            $requestModule['class'] = 'Class_StockGoodsReturnHistoryApproval';
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
        $noReceive=''; $ord=''; $idRoleAccess=''; $pic=''; $idKaryawan=''; $name=''; $grade=''; $departemen=''; $signature='';
        $status='0'; $date=null; $note=''; $years=carbon::now()->format('Y');

        if (isset($request['no_receive']) && $request['no_receive']!='' ) {$noReceive = $request['no_receive'];}
        if (isset($request['ord']) && $request['ord']!='' ) {$ord = $request['ord'];}
        if (isset($request['id_role_access']) && $request['id_role_access']!='' ) {$idRoleAccess = $request['id_role_access'];}
        if (isset($request['pic']) && $request['pic']!='' ) {$pic = $request['pic'];}
        if (isset($request['id_karyawan']) && $request['id_karyawan']!='' ) {$idKaryawan = $request['id_karyawan'];}
        if (isset($request['name']) && $request['name']!='' ) {$name = $request['name'];}
        if (isset($request['grade']) && $request['grade']!='' ) {$grade = $request['grade'];}
        if (isset($request['departemen']) && $request['departemen']!='' ) {$departemen = $request['departemen'];}
        if (isset($request['signature']) && $request['signature']!='' ) {$signature = $request['signature'];}
        if (isset($request['status']) && $request['status']!='' ) {$status = $request['status'];}
        if (isset($request['date']) && $request['date']!='' ) {$date = $request['date'];}
        if (isset($request['note']) && $request['note']!='' ) {$note = $request['note'];}
        if (isset($request['years']) && $request['years']!='' ) {$years = $request['years'];}
 
        try
        {
            // cek data
            $request=[];
            $request['no_receive'] = $noReceive;
            $request['ord'] = $ord;
            $request['id_role_access'] = $idRoleAccess;
            $request['pic'] = $pic;
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
                $data = new stock_goods_return_history_approval();
                $data->no_receive = $noReceive;
                $data->ord = $ord;
                $data->id_role_access = $idRoleAccess; 
                $data->pic = $pic; 
                $data->id_karyawan = $idKaryawan; 
                $data->name = $name; 
                $data->grade = $grade; 
                $data->departemen = $departemen; 
                $data->signature = $signature; 
                $data->status = $status; 
                $data->date = $date; 
                $data->note = $note; 
                $data->years = $years; 
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
            $requestModule['class'] = 'Class_StockGoodsReturnHistoryApproval';
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
            if (isset($request['no_receive']) && $request['no_receive']!='' ) {$updateData['no_receive'] = $request['no_receive'];}
            if (isset($request['ord']) && $request['ord']!='' ) {$updateData['ord'] = $request['ord'];}
            if (isset($request['id_role_access']) && $request['id_role_access']!='' ) {$updateData['id_role_access'] = $request['id_role_access'];}
            if (isset($request['pic']) && $request['pic']!='' ) {$updateData['pic'] = $request['pic'];}
            if (isset($request['id_karyawan']) && $request['id_karyawan']!='' ) {$updateData['id_karyawan'] = $request['id_karyawan'];}
            if (isset($request['price']) && $request['price']!='' ) {$updateData['price'] = $request['price'];}
            if (isset($request['sub_total']) && $request['sub_total']!='' ) {$updateData['sub_total'] = $request['sub_total'];}
            if (isset($request['expired_date']) && $request['expired_date']!='' ) {$updateData['expired_date'] = $request['expired_date'];}
            if (isset($request['note']) && $request['note']!='' ) {$updateData['note'] = $request['note'];}
            if (isset($request['years']) && $request['years']!='' ) {$updateData['years'] = $request['years'];}
     

            DB::table('Class_StockGoodsReturnHistoryApproval')
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
            $requestModule['class'] = 'Class_StockGoodsReturnHistoryApproval';
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
