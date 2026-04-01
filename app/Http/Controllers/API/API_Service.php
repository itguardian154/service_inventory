<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class API_Service
{
    public function getDataKaryawan($request)
    {
        $idKaryawan=''; $name=''; $departemen=''; $grade='';
        if (isset($request['id_karyawan'])) {$idKaryawan = $request['id_karyawan'];}
        if (isset($request['name'])) {$name = $request['name'];}
        if (isset($request['departemen'])) {$departemen = $request['departemen'];}
        if (isset($request['grade'])) {$grade = $request['grade'];}
        try
        {
            $result=[];
            $urlServerLokaHR = config('app.apiLokaHR');
            $client = new \GuzzleHttp\Client();
            $response = $client->request('GET', $urlServerLokaHR.'get_karyawan_byID', [
                'json' => [
                    'id_karyawan' => $idKaryawan,
                    'name' => $name,
                    'departemen' => $departemen,
                    'grade' => $grade,
                ],
            ]);  
            $jsonData = json_decode($response->getBody(), true);
     
            if($jsonData['status']=='success' && $jsonData['data']!=null)
            {
                return [
                    'success' => true,
                    'message' => 'Get successful',
                    'data' => $jsonData['data']
                ];
            }
            else
            {
                return [
                    'success' => true,
                    'message' => 'failed response API LOKAHR',
                    'data' =>''
                ];
            }
            return $result;
        } catch (\Exception $ex) {
            return [
                'success' => false,
                'message' => $ex->getMessage()
            ];
        }
    }

    public function getUsersAccessManagement($request)
    {
        try
        {
            $idKaryawan=''; $name=''; $departemen=''; $grade=''; $telephone=''; $idRoleAccess='';
            if (isset($request['id_karyawan']) && $request['id_karyawan']!='') {$idKaryawan = $request['id_karyawan'];}
            if (isset($request['name']) && $request['name']!='') {$name = $request['name'];}
            if (isset($request['departemen']) && $request['departemen']!='') {$departemen = $request['departemen'];}
            if (isset($request['grade']) && $request['grade']!='') {$grade = $request['grade'];}
            if (isset($request['telephone']) && $request['telephone']!='') {$idKaryawan = $request['telephone'];}
            if (isset($request['id_role_access']) && $request['id_role_access']!='') {$idRoleAccess = $request['id_role_access'];}

            $result=[];
            $urlServer = config('app.apiInventory');
            $client = new \GuzzleHttp\Client();
            $response = $client->request('GET', $urlServer.'get_user_access_management', [
                'json' => [
                    'id_karyawan' => $idKaryawan,
                    'name' => $name,
                    'departemen' => $departemen,
                    'grade' => $grade,
                    'telephone' => $telephone,
                    'id_role_access' => $idRoleAccess,
                ],
            ]);  
            $jsonData = json_decode($response->getBody(), true);
     
            if($jsonData['status']=='success' && $jsonData['data']!=null)
            {
                return [
                    'success' => true,
                    'message' => 'Get successful',
                    'data' => $jsonData['data']
                ];
            }
            else
            {
                return [
                    'success' => true,
                    'message' => 'failed response API Inventory',
                    'data' =>''
                ];
            }
            return $result;
        } catch (\Exception $ex) {
            return [
                'success' => false,
                'message' => $ex->getMessage()
            ];
        }
    }

    public function sentWhatsapp($request)
    {
        $telephone=''; $message='';
        if (isset($request['telephone']) && $request['telephone']!='') {$telephone = $request['telephone'];}
        if (isset($request['message']) && $request['message']!='') {$message = $request['message'];}

        try
        {
            $urlServer = config('app.apiWhatsapp');
            $client = new \GuzzleHttp\Client();

            $response = $client->request('POST', $urlServer, [
                'headers' => [
                    // 'Authorization' => '+PkfUaYYGfR1+gRCx9no',
                    'Authorization' => '2RhqkkL3Vrp8FkRFcRpi',
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'target' => $telephone,
                    'message' => $message
                ],
            ]);
            $data = json_decode($response->getBody(), true);
            if($data['status']==true)
            {
                return [
                    'success' => true,
                    'message' => 'Sent Message successful',
                    'data' => $data['detail']
                ];
            }
            else
            {
                return [
                    'success' => false,
                    'message' => 'failed response API Whatsapp',
                    'data' =>''
                ];
            }
            return $result;
        } catch (\Exception $ex) {
            return [
                'success' => false,
                'message' => $ex->getMessage()
            ];
        }
    }
}
