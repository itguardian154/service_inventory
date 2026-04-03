<?php

namespace App\Http\Controllers\Whatsapp;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\API\API_Service;

class SentMessage
{
    # Get Name Apps
    private function getNamaApps()
    {
        return 'INVENTORY';
    }
    # End Name Apps

    # Watermark Footer
    private function getWatermarkFooter()
    {   
        return "[sent by Bot Loka]";
    }
    # End Watermark Footer
    
    # Access
    private function getAccess()
    {   
        return "https://salokapark.app";
    }
    # End Access

    # Body
    private function getBodyRequestReceiveOrder($request)
    {
        $name=''; $noTransaction=''; $departemen=''; $subDepartemen=''; $dateTransaction='';
        if (isset($request['name'])) {$name = $request['name'];}
        if (isset($request['no_transaction'])) {$noTransaction = $request['no_transaction'];}
        if (isset($request['date_transaction'])) {$dateTransaction = $request['date_transaction'];}
      
        $message = $this->getNamaApps()." \n\n".
        "Dear Bapak/Ibu *".$name."* \n".
        "Request Receive Order sudah dibuat dengan data sebagai berikut:"." \n\n".
        "Nomor : \n*".$noTransaction."* \n".
        "Tanggal Pengajuan : *".$dateTransaction."* \n";

        return $message;
    }

    private function getBodyRequestStoreRequest($request)
    {
   
        $name=''; $noTransaction=''; $departemen=''; $subDepartemen=''; $dateTransaction='';
        if (isset($request['name'])) {$name = $request['name'];}
        if (isset($request['no_transaction'])) {$noTransaction = $request['no_transaction'];}
        if (isset($request['departemen'])) {$departemen = $request['departemen'];}
        if (isset($request['sub_departemen'])) {$subDepartemen = $request['sub_departemen'];}
        if (isset($request['date_transaction'])) {$dateTransaction = $request['date_transaction'];}
      
        $message = $this->getNamaApps()." \n\n".
        "Dear Bapak/Ibu *".$name."* \n".
        "Request Store Request sudah dibuat dengan data sebagai berikut:"." \n\n".
        "Nomor : \n*".$noTransaction."* \n".
        // "Departemen : *".$departemen."* \n".
        // "Sub Departemen : *".$subDepartemen."* \n".
        "Tanggal Pengajuan : \n*".$dateTransaction."* \n";
        return $message;
    }
    # End Body

    # Sent Message
    public function sentWhatsappRequest($request)
    {
        try
        {
            $type=''; $telephone='';
            if (isset($request['telephone']) && $request['telephone']!='') {$telephone = $request['telephone'];}
            if (isset($request['type']) && $request['type']!='') {$type = $request['type'];}
            if (isset($request['name']) && $request['name']!='') {$name = $request['name'];}
           
            if($telephone=='')
            {
                return null;   
            }
            if($type=='')
            {
                return null;   
            }

            $message='';
            if($type=='receive_order')
            {
                $message = $this->getBodyRequestReceiveOrder($request)." \n".
                "Mohon untuk dapat melakukan pengecekan dan Approval/Reject permintaan tersebut."." \n".
                "Matur Nuwum."." \n\n".
                "Access : ".$this->getAccess(). " \n".
                $this->getWatermarkFooter();
            }
            if($type=='store_request')
            {
                $message = $this->getBodyRequestStoreRequest($request)." \n".
                "Mohon untuk dapat melakukan pengecekan dan Approval/Reject permintaan tersebut."." \n".
                "Matur Nuwum."." \n\n".
                "Access : ".$this->getAccess(). " \n".
                $this->getWatermarkFooter();
              
            }
        
            $requestClassAPI = [];
            $requestClassAPI['name'] = $name;
            $requestClassAPI['telephone'] = $telephone;
            $requestClassAPI['message'] = $message;
   
            $classAPI = new API_Service();
            $resultClassAPI = $classAPI->sentWhatsapp($requestClassAPI);
            return $resultClassAPI;
        }
        catch(\Exception $e)
        {
            return null;
        }
    }
    # End Sent Message
}
