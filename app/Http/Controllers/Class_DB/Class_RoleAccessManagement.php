<?php

namespace App\Http\Controllers\Class_DB;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Log\LogError;
use App\Models\role_access_management;
use Carbon\Carbon;
use DateTime;

class Class_RoleAccessManagement
{
    /**
     * Read table
     */
    public function show($request)
    {
        // set value variable
        $idAccessManagement=''; $rolleName=''; $formName=''; $status='';

        if (isset($request['id_access_management']) && $request['id_access_management']!='' ) {$idAccessManagement = $request['id_access_management'];}
        if (isset($request['rolle_name']) && $request['rolle_name']!='' ) {$rolleName = $request['rolle_name'];}
        if (isset($request['form_name']) && $request['form_name']!='' ) {$formName = $request['form_name'];}
        if (isset($request['status']) && $request['status']!='' ) {$status = $request['status'];}

        try
        {
            $data_ = DB::table('role_access_management');
            if($idAccessManagement!='')
            {
                $data_->where('id_access_management',$idAccessManagement);
            }
            if($rolleName!='')
            {
                $data_->where('rolle_name',$rolleName);
            }
            if($formName!='')
            {
                $data_->where('form_name',$formName);
            }
            if($status!='')
            {
                $data_->where('status',$status);
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
            $requestModule['class'] = 'Class_RoleAccessManagement';
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
        $idAccessManagement=''; $rolleName=''; $formName=''; $status='';

        if (isset($request['id_access_management']) && $request['id_access_management']!='' ) {$idAccessManagement = $request['id_access_management'];}
        if (isset($request['rolle_name']) && $request['rolle_name']!='' ) {$rolleName = $request['rolle_name'];}
        if (isset($request['form_name']) && $request['form_name']!='' ) {$formName = $request['form_name'];}
        if (isset($request['status']) && $request['status']!='' ) {$status = $request['status'];}
 
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

                $data = new role_access_management();
                $data->id_access_management = $idAccessManagement;
                $data->rolle_name = $rolleName;
                $data->form_name = $formName; 
                $data->status = $status; 
                $data->save();

                return [
                    'success' => true,
                    'message' => 'Insert successful',
                    'data' => $data
                ];
            // }
       
        } catch (\Exception $ex) {
            # Insert Log Error
            $requestModule=[];
            $requestModule['reff'] = '-';
            $requestModule['service'] = 'Class';
            $requestModule['class'] = 'Class_RoleAccessManagement';
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
            if (isset($request['id_access_management']) && $request['id_access_management']!='' ) {$updateData['id_access_management'] = $request['id_access_management'];}
            if (isset($request['rolle_name']) && $request['rolle_name']!='' ) {$updateData['rolle_name'] = $request['rolle_name'];}
            if (isset($request['form_name']) && $request['form_name']!='' ) {$updateData['form_name'] = $request['form_name'];}
            if (isset($request['status']) && $request['status']!='' ) {$updateData['status'] = $request['status'];}

            DB::table('role_access_management')
            ->where('id','=',$id)
            ->update($updateData);

            return $updateData;
        } catch (\Exception $ex) {
            # Insert Log Error
            $requestModule=[];
            $requestModule['reff'] = '-';
            $requestModule['service'] = 'Class';
            $requestModule['class'] = 'Class_RoleAccessManagement';
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
