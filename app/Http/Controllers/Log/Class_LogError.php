<?php

namespace App\Http\Controllers\Log;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\log_error;
use Carbon\Carbon;
use DateTime;

class Class_LogError
{
    /**
     * Read table
     */
    public function show($request)
    {
        // set value variable
        $reff = ''; $service=''; $class=''; $function=''; $message='';
            
        if (isset($request['reff']) && $request['reff']!='' ) {$reff = $request['reff'];}
        if (isset($request['service']) && $request['service']!='' ) {$service = $request['service'];}
        if (isset($request['class']) && $request['class']!='' ) {$class = $request['class'];}
        if (isset($request['function']) && $request['function']!='' ) {$function = $request['function'];}

        try
        {
            $data_ = DB::table('log_error');
            if($reff!='')
            {
                $data_->where('reff',$reff);
            }
            if($service!='')
            {
                $data_->where('service',$service);
            }
            if($class!='')
            {
                $data_->where('class',$class);
            }
            if($function!='')
            {
                $data_->where('function',$function);
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
        $reff = ''; $service=''; $class=''; $function=''; $message=''; $note=''; 
        
        if (isset($request['reff']) && $request['reff']!='' ) {$reff = $request['reff'];}
        if (isset($request['service']) && $request['service']!='' ) {$service = $request['service'];}
        if (isset($request['class']) && $request['class']!='' ) {$class = $request['class'];}
        if (isset($request['function']) && $request['function']!='' ) {$function = $request['function'];}
        if (isset($request['message']) && $request['message']!='' ) {$message = $request['message'];}
        if (isset($request['note']) && $request['note']!='' ) {$note = $request['note'];}
        
        try
        {
            $data = new log_error();
            $data->reff = $reff;
            $data->service = $service;
            $data->class = $class;
            $data->function = $function; 
            $data->message = $message; 
            $data->note = $note; 
            $data->save();
            
            return $data;
        } catch (\Exception $ex) {
            return $ex;
        }
    }
}
