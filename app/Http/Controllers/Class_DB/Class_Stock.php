<?php

namespace App\Http\Controllers\Class_DB;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Model\LogError;
use App\Models\stock;
use Carbon\Carbon;
use DateTime;

class Class_Stock extends Controller
{
    /**
     * Read table
     */
    public function show($request)
    {
        // set value variable
        $idDepartemen = ''; $departemen=''; $idDepartemenSub=''; $departemenSub=''; $name=''; 
        $pos=''; $grade=''; $idEmployee=''; $nik=''; $phone=''; $dateOfBirth=''; $age=''; $dateOfJoin=''; $yearsOfService='';
        $address=''; $city=''; $province=''; $email=''; $status='';
        
        if (isset($request['id_departemen']) && $request['id_departemen']!='' ) {$idDepartemen = $request['id_departemen'];}
        if (isset($request['departemen']) && $request['departemen']!='' ) {$departemen = $request['departemen'];}
        if (isset($request['id_departemen_sub']) && $request['id_departemen_sub']!='' ) {$idDepartemenSub = $request['id_departemen_sub'];}
        if (isset($request['departemen_sub']) && $request['departemen_sub']!='' ) {$departemenSub = $request['departemen_sub'];}
        if (isset($request['name']) && $request['name']!='' ) {$name = $request['name'];}
        if (isset($request['pos']) && $request['pos']!='' ) {$pos= $request['pos'];}
        if (isset($request['grade']) && $request['grade']!='' ) {$grade = $request['grade'];}
        if (isset($request['id_employee']) && $request['id_employee']!='' ) {$idEmployee = $request['id_employee'];}
        if (isset($request['nik']) && $request['nik']!='' ) {$nik = $request['nik'];}
        if (isset($request['phone']) && $request['phone']!='' ) {$phone = $request['phone'];}
        if (isset($request['date_of_birth']) && $request['date_of_birth']!='' ) {$dateOfBirth = $request['date_of_birth'];}
        if (isset($request['age']) && $request['age']!='' ) {$age = $request['age'];}
        if (isset($request['date_of_join']) && $request['date_of_join']!='' ) {$dateOfJoin = $request['date_of_join'];}
        if (isset($request['years_of_service']) && $request['years_of_service']!='' ) {$yearsOfService = $request['years_of_service'];}
        if (isset($request['address']) && $request['address']!='' ) {$address = $request['address'];}
        if (isset($request['city']) && $request['city']!='' ) {$city = $request['city'];}
        if (isset($request['province']) && $request['province']!='' ) {$province = $request['province'];}
        if (isset($request['email']) && $request['email']!='' ) {$email = $request['email'];}
        if (isset($request['status']) && $request['status']!='' ) {$status = $request['status'];}

        try
        {
            $data_ = DB::table('employee');
            if($idDepartemen!='')
            {
                $data_->where('id_departemen',$idDepartemen);
            }
            if($departemen!='')
            {
                $data_->where('departemen',$departemen);
            }
            if($idDepartemenSub!='')
            {
                $data_->where('id_departemen_sub',$idDepartemenSub);
            }
            if($departemenSub!='')
            {
                $data_->where('departemen_sub',$departemenSub);
            }
            if($name!='')
            {
                $data_->where('name',$name);
            }
            if($pos!='')
            {
                $data_->where('pos',$pos);
            }
            if($grade!='')
            {
                $data_->where('grade',$grade);
            }
            if($idEmployee!='')
            {
                $data_->where('id_employee',$idEmployee);
            }
            if($nik!='')
            {
                $data_->where('nik',$nik);
            }
            if($phone!='')
            {
                $data_->where('phone',$phone);
            }
            if($dateOfBirth!='')
            {
                $data_->where('date_of_birth',$dateOfBirth);
            }
            if($age!='')
            {
                $data_->where('age',$age);
            }
            if($dateOfJoin!='')
            {
                $data_->where('date_of_join',$dateOfJoin);
            }
            if($yearsOfService!='')
            {
                $data_->where('years_of_service',$yearsOfService);
            }
            if($address!='')
            {
                $data_->where('address',$address);
            }
            if($city!='')
            {
                $data_->where('city',$city);
            }
            if($province!='')
            {
                $data_->where('province',$province);
            }
            if($email!='')
            {
                $data_->where('email',$email);
            }
            if($status!='')
            {
                $data_->where('status',$status);
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
            $requestModule['class'] = 'Class_Stock';
            $requestModule['function'] = 'Show';
            $requestModule['message'] = $ex->getMessage();
            $requestModule['note'] = '-';
            $classModel = new LogError();
            $result = $classModel->insertLogError($request);
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
        $idDepartemen = ''; $departemen=''; $idDepartemenSub=''; $departemenSub=''; $name=''; 
        $pos=''; $grade=''; $idEmployee=''; $password=''; $nik=''; $phone=''; $dateOfBirth=''; $age=''; $dateOfJoin=''; $yearsOfService='';
        $address=''; $city=''; $province=''; $email=''; $imageProfile=''; $status='1'; // 1=active;2=non active

        // declare variable set
        if (isset($request['id_departemen']) && $request['id_departemen']!='' ) {$idDepartemen = $request['id_departemen'];}
        if (isset($request['departemen']) && $request['departemen']!='' ) {$departemen = $request['departemen'];}
        if (isset($request['id_departemen_sub']) && $request['id_departemen_sub']!='' ) {$idDepartemenSub = $request['id_departemen_sub'];}
        if (isset($request['departemen_sub']) && $request['departemen_sub']!='' ) {$departemenSub = $request['departemen_sub'];}
        if (isset($request['name']) && $request['name']!='' ) {$name = $request['name'];}
        if (isset($request['pos']) && $request['pos']!='' ) {$pos= $request['pos'];}
        if (isset($request['grade']) && $request['grade']!='' ) {$grade = $request['grade'];}
        if (isset($request['id_employee']) && $request['id_employee']!='' ) {$idEmployee = $request['id_employee'];}
        if (isset($request['nik']) && $request['nik']!='' ) {$nik = $request['nik'];}
        if (isset($request['phone']) && $request['phone']!='' ) {$phone = $request['phone'];}
        if (isset($request['date_of_birth']) && $request['date_of_birth']!='' ) {$dateOfBirth = $request['date_of_birth'];}
        if (isset($request['age']) && $request['age']!='' ) {$age = $request['age'];}
        if (isset($request['date_of_join']) && $request['date_of_join']!='' ) {$dateOfJoin = $request['date_of_join'];}
        if (isset($request['years_of_service']) && $request['years_of_service']!='' ) {$yearsOfService = $request['years_of_service'];}
        if (isset($request['address']) && $request['address']!='' ) {$address = $request['address'];}
        if (isset($request['city']) && $request['city']!='' ) {$city = $request['city'];}
        if (isset($request['province']) && $request['province']!='' ) {$province = $request['province'];}
        if (isset($request['email']) && $request['email']!='' ) {$email = $request['email'];}
        if (isset($request['status']) && $request['status']!='' ) {$status = $request['status'];}
        
        try
        {
            // cek data
            $request=[];
            $request['id_employee'] = $idEmployee;
            $request['nik'] = $nik;
        
            $dataTransaction = $this->show($request);
            if(isset($dataTransaction))
            {
                // data sudah ada
                return 'double data';
            }
            else
            {
                $data = new employee();
                $data->id_departemen = $idDepartemen;
                $data->departemen = $departemen;
                $data->id_departemen_sub = $idDepartemenSub;
                $data->departemen_sub = $departemenSub; 
                $data->name = $name; 
                $data->pos = $pos; 
                $data->grade = $grade; 
                $data->id_employee = $idEmployee; 
                $data->password = $password; 
                $data->nik = $nik; 
                $data->phone = $phone; 
                $data->date_of_birth = $dateOfBirth; 
                $data->age = $age; 
                $data->date_of_join = $dateOfJoin;
                $data->years_of_service = $yearsOfService;
                $data->address = $address;
                $data->city = $city; 
                $data->province = $province; 
                $data->email = $email; 
                $data->image_profile = $imageProfile; 
                $data->status = $status; 
                $data->save();
            }
            return $data;
        } catch (\Exception $ex) {
            # Insert Log Error
            $requestModule=[];
            $requestModule['reff'] = '-';
            $requestModule['service'] = 'Class';
            $requestModule['class'] = 'Class_Stock';
            $requestModule['function'] = 'Insert';
            $requestModule['message'] = $ex->getMessage();
            $requestModule['note'] = '-';
            $classModel = new LogError();
            $result = $classModel->insertLogError($request);
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
            if (isset($request['id_departemen']) && $request['id_departemen']!='' ) {$updateData['id_departemen'] = $request['id_departemen'];}
            if (isset($request['departemen']) && $request['departemen']!='' ) {$updateData['departemen'] = $request['departemen'];}
            if (isset($request['id_departemen_sub']) && $request['id_departemen_sub']!='' ) {$updateData['id_departemen_sub'] = $request['id_departemen_sub'];}
            if (isset($request['departemen_sub']) && $request['departemen_sub']!='' ) {$updateData['departemen_sub'] = $request['departemen_sub'];}
            if (isset($request['name']) && $request['name']!='' ) {$updateData['name'] = $request['name'];}
            if (isset($request['pos']) && $request['pos']!='' ) {$updateData['pos'] = $request['pos'];}
            if (isset($request['grade']) && $request['grade']!='' ) {$updateData['grade'] = $request['grade'];}
            if (isset($request['id_employee']) && $request['id_employee']!='' ) {$updateData['id_employee'] = $request['id_employee'];}
            if (isset($request['nik']) && $request['nik']!='' ) {$updateData['nik'] = $request['nik'];}
            if (isset($request['phone']) && $request['phone']!='' ) {$updateData['phone'] = $request['phone'];}
            if (isset($request['date_of_birth']) && $request['date_of_birth']!='' ) {$updateData['date_of_birth'] = $request['date_of_birth'];}
            if (isset($request['age']) && $request['age']!='' ) {$updateData['age'] = $request['age'];}
            if (isset($request['date_of_join']) && $request['date_of_join']!='' ) {$updateData['date_of_join'] = $request['date_of_join'];}
            if (isset($request['years_of_service']) && $request['years_of_service']!='' ) {$updateData['years_of_service'] = $request['years_of_service'];}
            if (isset($request['address']) && $request['address']!='' ) {$updateData['address'] = $request['address'];}
            if (isset($request['city']) && $request['city']!='' ) {$updateData['city'] = $request['city'];}
            if (isset($request['province']) && $request['province']!='' ) {$updateData['province'] = $request['province'];}
            if (isset($request['email']) && $request['email']!='' ) {$updateData['email'] = $request['email'];}
            if (isset($request['status']) && $request['status']!='' ) {$updateData['status'] = $request['status'];}
           
            DB::table('employee')
            ->where('id','=',$id)
            ->update($updateData);

            return $updateData;
        } catch (\Exception $ex) {
            # Insert Log Error
            $requestModule=[];
            $requestModule['reff'] = '-';
            $requestModule['service'] = 'Class';
            $requestModule['class'] = 'Class_Stock';
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
