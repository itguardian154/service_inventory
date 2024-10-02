<?php

namespace App\Http\Controllers\Model\Stock;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class Stock extends Controller
{
    public function getStock($request)
    {
        try
        {
            $requestModule=[];
            if (isset($request['id_departemen']) && $request['id_departemen']!='' ) {$requestModule['id_departemen'] = $request['id_departemen'];}
            if (isset($request['departemen']) && $request['departemen']!='' ) {$requestModule['departemen'] = $request['departemen'];}
            if (isset($request['id_departemen_sub']) && $request['id_departemen_sub']!='' ) {$requestModule['id_departemen_sub'] = $request['id_departemen_sub'];}
            if (isset($request['departemen_sub']) && $request['departemen_sub']!='' ) {$requestModule['departemen_sub'] = $request['departemen_sub'];}
            if (isset($request['name']) && $request['name']!='' ) {$requestModule['name'] = $request['name'];}
            if (isset($request['pos']) && $request['pos']!='' ) {$requestModule['pos'] = $request['pos'];}
            if (isset($request['grade']) && $request['grade']!='' ) {$requestModule['grade'] = $request['grade'];}
            if (isset($request['id_Stock']) && $request['id_Stock']!='' ) {$requestModule['id_Stock'] = $request['id_Stock'];}
            if (isset($request['nik']) && $request['nik']!='' ) {$requestModule['nik'] = $request['nik'];}
            if (isset($request['phone']) && $request['phone']!='' ) {$requestModule['phone'] = $request['phone'];}
            if (isset($request['date_of_birth']) && $request['date_of_birth']!='' ) {$requestModule['date_of_birth'] = $request['date_of_birth'];}
            if (isset($request['age']) && $request['age']!='' ) {$requestModule['age'] = $request['age'];}
            if (isset($request['date_of_join']) && $request['date_of_join']!='' ) {$requestModule['date_of_join'] = $request['date_of_join'];}
            if (isset($request['years_of_service']) && $request['years_of_service']!='' ) {$requestModule['years_of_service'] = $request['years_of_service'];}
            if (isset($request['address']) && $request['address']!='' ) {$requestModule['address'] = $request['address'];}
            if (isset($request['city']) && $request['city']!='' ) {$requestModule['city'] = $request['city'];}
            if (isset($request['province']) && $request['province']!='' ) {$requestModule['province'] = $request['province'];}
            if (isset($request['email']) && $request['email']!='' ) {$requestModule['email'] = $request['email'];}
            if (isset($request['status']) && $request['status']!='' ) {$requestModule['status'] = $request['status'];}

            $result=[];
            $classModel = new Class_Stock();
            $result = $classModel->show($requestModule);

            return $result;
        } catch (\Exception $ex) {
            # Insert Log Error
            $requestModule=[];
            $requestModule['reff'] = '-';
            $requestModule['service'] = 'Model';
            $requestModule['class'] = 'Stock';
            $requestModule['function'] = 'getStock';
            $requestModule['message'] = $ex->getMessage();
            $requestModule['note'] = '-';
            $classModel = new LogError();
            $result = $classModel->insertLogError($request);
            # End Log Error
            return $ex;
        }
    }

    public function insertStock($request)
    {
        try
        {
            $requestModule=[];
            if (isset($request['id_departemen']) && $request['id_departemen']!='' ) {$requestModule['id_departemen'] = $request['id_departemen'];}
            if (isset($request['departemen']) && $request['departemen']!='' ) {$requestModule['departemen'] = $request['departemen'];}
            if (isset($request['id_departemen_sub']) && $request['id_departemen_sub']!='' ) {$requestModule['id_departemen_sub'] = $request['id_departemen_sub'];}
            if (isset($request['departemen_sub']) && $request['departemen_sub']!='' ) {$requestModule['departemen_sub'] = $request['departemen_sub'];}
            if (isset($request['name']) && $request['name']!='' ) {$requestModule['name'] = $request['name'];}
            if (isset($request['pos']) && $request['pos']!='' ) {$requestModule['pos'] = $request['pos'];}
            if (isset($request['grade']) && $request['grade']!='' ) {$requestModule['grade'] = $request['grade'];}
            if (isset($request['id_Stock']) && $request['id_Stock']!='' ) {$requestModule['id_Stock'] = $request['id_Stock'];}
            if (isset($request['nik']) && $request['nik']!='' ) {$requestModule['nik'] = $request['nik'];}
            if (isset($request['phone']) && $request['phone']!='' ) {$requestModule['phone'] = $request['phone'];}
            if (isset($request['date_of_birth']) && $request['date_of_birth']!='' ) {$requestModule['date_of_birth'] = $request['date_of_birth'];}
            if (isset($request['age']) && $request['age']!='' ) {$requestModule['age'] = $request['age'];}
            if (isset($request['date_of_join']) && $request['date_of_join']!='' ) {$requestModule['date_of_join'] = $request['date_of_join'];}
            if (isset($request['years_of_service']) && $request['years_of_service']!='' ) {$requestModule['years_of_service'] = $request['years_of_service'];}
            if (isset($request['address']) && $request['address']!='' ) {$requestModule['address'] = $request['address'];}
            if (isset($request['city']) && $request['city']!='' ) {$requestModule['city'] = $request['city'];}
            if (isset($request['province']) && $request['province']!='' ) {$requestModule['province'] = $request['province'];}
            if (isset($request['email']) && $request['email']!='' ) {$requestModule['email'] = $request['email'];}
            if (isset($request['status']) && $request['status']!='' ) {$requestModule['status'] = $request['status'];}

            $result=[];
            $classModel = new Class_Stock();
            $result = $classModel->insert($requestModule);

            return $result;
        } catch (\Exception $ex) {
            # Insert Log Error
            $requestModule=[];
            $requestModule['reff'] = '-';
            $requestModule['service'] = 'Model';
            $requestModule['class'] = 'Stock';
            $requestModule['function'] = 'insertStock';
            $requestModule['message'] = $ex->getMessage();
            $requestModule['note'] = '-';
            $classModel = new LogError();
            $result = $classModel->insertLogError($request);
            # End Log Error
            return $ex;
        }
    }

    public function updateStock($request)
    {
        try
        {
            $requestModule=[];
            if (isset($request['id']) && $request['id']!='' ) {$requestModule['id'] = $request['id'];}
            if (isset($request['id_departemen']) && $request['id_departemen']!='' ) {$requestModule['id_departemen'] = $request['id_departemen'];}
            if (isset($request['departemen']) && $request['departemen']!='' ) {$requestModule['departemen'] = $request['departemen'];}

            $result=[];
            $classModel = new Class_Stock();
            $result = $classModel->update($requestModule);

            return $result;
        } catch (\Exception $ex) {
            # Insert Log Error
            $requestModule=[];
            $requestModule['reff'] = '-';
            $requestModule['service'] = 'Model';
            $requestModule['class'] = 'Stock';
            $requestModule['function'] = 'updateStock';
            $requestModule['message'] = $ex->getMessage();
            $requestModule['note'] = '-';
            $classModel = new LogError();
            $result = $classModel->insertLogError($request);
            # End Log Error
            return $ex;
        }
    }
}
