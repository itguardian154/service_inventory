<?php

namespace App\Http\Controllers\Class_DB;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Log\LogError;
use App\Models\users_access_management;
use Carbon\Carbon;
use DateTime;

class Class_UsersAccessManagement
{
        /**
     * Read table
     */
    public function show($request)
    {
        // set value variable
        $idKaryawan=''; $name=''; $departemen=''; $grade=''; $telephone=''; $idRoleAccess=''; $pic='';

        if (isset($request['id_karyawan']) && $request['id_karyawan']!='' ) {$idKaryawan = $request['id_karyawan'];}
        if (isset($request['name']) && $request['name']!='' ) {$name = $request['name'];}
        if (isset($request['departemen']) && $request['departemen']!='' ) {$departemen = $request['departemen'];}
        if (isset($request['grade']) && $request['grade']!='' ) {$grade = $request['grade'];}
        if (isset($request['telephone']) && $request['telephone']!='' ) {$telephone = $request['telephone'];}
        if (isset($request['id_role_access']) && $request['id_role_access']!='' ) {$idRoleAccess = $request['id_role_access'];}
        if (isset($request['pic']) && $request['pic']!='' ) {$pic = $request['pic'];}

        try
        {
            $data_ = DB::table('users_access_management');
            if($idKaryawan!='')
            {
                $data_->where('id_karyawan',$idKaryawan);
            }
            if($name!='')
            {
                $data_->where('name',$name);
            }
            if($departemen!='')
            {
                $data_->where('departemen',$departemen);
            }
            if($grade!='')
            {
                $data_->where('grade',$grade);
            }
            if($telephone!='')
            {
                $data_->where('telephone',$telephone);
            }
            if($idRoleAccess!='')
            {
                $data_->where('id_role_access',$idRoleAccess);
            }
            if($pic!='')
            {
                $data_->where('pic',$pic);
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
            $requestModule['class'] = 'Class_UsersAccessManagement';
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
        $idKaryawan=''; $name=''; $departemen=''; $grade=''; $telephone=''; $idRoleAccess=''; $pic='';

        if (isset($request['id_karyawan']) && $request['id_karyawan']!='' ) {$idKaryawan = $request['id_karyawan'];}
        if (isset($request['name']) && $request['name']!='' ) {$name = $request['name'];}
        if (isset($request['departemen']) && $request['departemen']!='' ) {$departemen = $request['departemen'];}
        if (isset($request['grade']) && $request['grade']!='' ) {$grade = $request['grade'];}
        if (isset($request['telephone']) && $request['telephone']!='' ) {$telephone = $request['telephone'];}
        if (isset($request['id_role_access']) && $request['id_role_access']!='' ) {$idRoleAccess = $request['id_role_access'];}
        if (isset($request['pic']) && $request['pic']!='' ) {$pic = $request['pic'];}
 
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

                $data = new users_access_management();
                $data->id_karyawan = $idKaryawan;
                $data->name = $name;
                $data->departemen = $departemen; 
                $data->grade = $grade; 
                $data->telephone = $telephone; 
                $data->id_role_access = $idRoleAccess; 
                $data->pic = $pic; 
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
            $requestModule['class'] = 'Class_UsersAccessManagement';
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
            if (isset($request['id_karyawan']) && $request['id_karyawan']!='' ) {$updateData['id_karyawan'] = $request['id_karyawan'];}
            if (isset($request['name']) && $request['name']!='' ) {$updateData['name'] = $request['name'];}
            if (isset($request['departemen']) && $request['departemen']!='' ) {$updateData['departemen'] = $request['departemen'];}
            if (isset($request['grade']) && $request['grade']!='' ) {$updateData['grade'] = $request['grade'];}
            if (isset($request['telephone']) && $request['telephone']!='' ) {$updateData['telephone'] = $request['telephone'];}
            if (isset($request['id_role_access']) && $request['id_role_access']!='' ) {$updateData['id_role_access'] = $request['id_role_access'];}
            if (isset($request['pic']) && $request['pic']!='' ) {$updateData['pic'] = $request['pic'];}

            DB::table('users_access_management')
            ->where('id','=',$id)
            ->update($updateData);

            return $updateData;
        } catch (\Exception $ex) {
            # Insert Log Error
            $requestModule=[];
            $requestModule['reff'] = '-';
            $requestModule['service'] = 'Class';
            $requestModule['class'] = 'Class_UsersAccessManagement';
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
