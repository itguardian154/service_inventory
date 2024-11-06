<?php

namespace App\Http\Controllers\Class_DB;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Log\LogError;
use App\Models\stock;
use Carbon\Carbon;
use DateTime;
class Class_RoleAccessDetail
{
    /**
     * Read table
     */
    public function show($request)
    {
        // set value variable
        $idAccessManagement=''; $ord=''; $pic=''; $name=''; $grade=''; $telephone='';

        if (isset($request['id_access_management']) && $request['id_access_management']!='' ) {$idAccessManagement = $request['id_access_management'];}
        if (isset($request['ord']) && $request['ord']!='' ) {$ord = $request['ord'];}
        if (isset($request['pic']) && $request['pic']!='' ) {$pic = $request['pic'];}
        if (isset($request['name']) && $request['name']!='' ) {$name = $request['name'];}
        if (isset($request['grade']) && $request['grade']!='' ) {$grade = $request['grade'];}
        if (isset($request['telephone']) && $request['telephone']!='' ) {$telephone = $request['telephone'];}

        try
        {
            $data_ = DB::table('role_access_detail');
            if($idAccessManagement!='')
            {
                $data_->where('id_access_management',$idAccessManagement);
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
            if($telephone!='')
            {
                $data_->where('telephone',$telephone);
            }

            if($data_->exists())
            {
                $data_->orderBy('ord','asc');
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
            $requestModule['class'] = 'Class_RoleAccessDetail';
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
        $idAccessManagement=''; $ord=''; $pic=''; $name=''; $grade=''; $telephone='';

        if (isset($request['id_access_management']) && $request['id_access_management']!='' ) {$idAccessManagement = $request['id_access_management'];}
        if (isset($request['ord']) && $request['ord']!='' ) {$ord = $request['ord'];}
        if (isset($request['pic']) && $request['pic']!='' ) {$pic = $request['pic'];}
        if (isset($request['name']) && $request['name']!='' ) {$name = $request['name'];}
        if (isset($request['grade']) && $request['grade']!='' ) {$grade = $request['grade'];}
        if (isset($request['telephone']) && $request['telephone']!='' ) {$telephone = $request['telephone'];}
 
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

                $data = new role_access_detail();
                $data->id_access_management = $idAccessManagement;
                $data->ord = $ord;
                $data->pic = $pic; 
                $data->name = $name; 
                $data->grade = $grade; 
                $data->telephone = $telephone; 
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
            $requestModule['class'] = 'Class_RoleAccessDetail';
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
            if (isset($request['ord']) && $request['ord']!='' ) {$updateData['ord'] = $request['ord'];}
            if (isset($request['pic']) && $request['pic']!='' ) {$updateData['pic'] = $request['pic'];}
            if (isset($request['name']) && $request['name']!='' ) {$updateData['name'] = $request['name'];}
            if (isset($request['grade']) && $request['grade']!='' ) {$updateData['grade'] = $request['grade'];}
            if (isset($request['telephone']) && $request['telephone']!='' ) {$updateData['telephone'] = $request['telephone'];}

            DB::table('role_access_detail')
            ->where('id','=',$id)
            ->update($updateData);

            return $updateData;
        } catch (\Exception $ex) {
            # Insert Log Error
            $requestModule=[];
            $requestModule['reff'] = '-';
            $requestModule['service'] = 'Class';
            $requestModule['class'] = 'Class_RoleAccessDetail';
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
