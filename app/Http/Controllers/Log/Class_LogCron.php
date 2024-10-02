<?php

namespace App\Http\Controllers\Log;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\log_cron;
use Carbon\Carbon;
use DateTime;


class Class_LogCron extends Controller
{
    /**
     * Read table
     */
    public function show($request)
    {
        // set value variable
        $cron = ''; $signature=''; $message=''; $status=''; 
            
        if (isset($request['cron']) && $request['cron']!='' ) {$cron = $request['cron'];}
        if (isset($request['signature']) && $request['signature']!='' ) {$signature = $request['signature'];}
        if (isset($request['message']) && $request['message']!='' ) {$message = $request['message'];}
        if (isset($request['status']) && $request['status']!='' ) {$status = $request['status'];}

        try
        {
            $data_ = DB::table('log_cron');
            if($cron!='')
            {
                $data_->where('cron',$cron);
            }
            if($message!='')
            {
                $data_->where('message',$message);
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
            return $ex;
        }
    }

    /**
     * Create table
     */
    public function insert($request)
    {
        // set value variable
        $cron = ''; $signature=''; $message=''; $status=''; 
            
        if (isset($request['cron']) && $request['cron']!='' ) {$cron = $request['cron'];}
        if (isset($request['signature']) && $request['signature']!='' ) {$signature = $request['signature'];}
        if (isset($request['message']) && $request['message']!='' ) {$message = $request['message'];}
        if (isset($request['status']) && $request['status']!='' ) {$status = $request['status'];}
    
        try
        {
            $data = new log_cron();
            $data->cron = $cron;
            $data->signature = $signature;
            $data->message = $message;
            $data->status = $status;
            $data->save();
            
            return $data;
        } catch (\Exception $ex) {
            return $ex;
        }
    }
}