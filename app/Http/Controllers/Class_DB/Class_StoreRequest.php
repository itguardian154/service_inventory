<?php

namespace App\Http\Controllers\Class_DB;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Log\LogError;
use App\Models\store_request;
use Carbon\Carbon;
use DateTime;

class Class_StoreRequest 
{
   /**
     * Read table
     */
    public function show($request)
    {
        // set value variable
        $noTransaction=''; $idDepartemen=''; $departemen=''; $idSubDepartemen=''; $subDepartemen=''; $dateTransaction=''; 
        $detailItems=''; $totalItems=''; $status=''; $note=''; $reff=''; $years='';

        if (isset($request['no_transaction']) && $request['no_transaction']!='' ) {$noTransaction = $request['no_transaction'];}
        if (isset($request['id_departemen']) && $request['id_departemen']!='' ) {$idDepartemen = $request['id_departemen'];}
        if (isset($request['departemen']) && $request['departemen']!='' ) {$departemen = $request['departemen'];}
        if (isset($request['id_sub_departemen']) && $request['id_sub_departemen']!='' ) {$idSubDepartemen = $request['id_sub_departemen'];}
        if (isset($request['sub_departemen']) && $request['sub_departemen']!='' ) {$subDepartemen = $request['sub_departemen'];}
        if (isset($request['date_transaction']) && $request['date_transaction']!='' ) {$dateTransaction = $request['date_transaction'];}
        if (isset($request['detail_items']) && $request['detail_items']!='' ) {$detailItem = $request['detail_items'];}
        if (isset($request['total_items']) && $request['total_items']!='' ) {$totalItems = $request['total_items'];}
        if (isset($request['status']) && $request['status']!='' ) {$status = $request['status'];}
        if (isset($request['note']) && $request['note']!='' ) {$note = $request['note'];}
        if (isset($request['reff']) && $request['reff']!='' ) {$reff = $request['reff'];}
        if (isset($request['years']) && $request['years']!='' ) {$years = $request['years'];}

        try
        {
            $data_ = DB::table('store_request');
            if($noTransaction!='')
            {
                $data_->where('no_transaction',$noTransaction);
            }
            if($idDepartemen!='')
            {
                $data_->where('id_departemen',$idDepartemen);
            }
            if($departemen!='')
            {
                $data_->where('departemen',$departemen);
            }
            if($idSubDepartemen!='')
            {
                $data_->where('id_sub_departemen',$idSubDepartemen);
            }
            if($subDepartemen!='')
            {
                $data_->where('sub_departemen',$subDepartemen);
            }
            if($dateTransaction!='')
            {
                $data_->where('date_transaction',$dateTransaction);
            }
            if($detailItems!='')
            {
                $data_->where('detail_items',$detailItems);
            }
            if($totalItems!='')
            {
                $data_->where('total_items',$totalItems);
            }
            if($status!='')
            {
                $data_->where('status',$status);
            }
            if($note!='')
            {
                $data_->where('note',$note);
            }
            if($reff!='')
            {
                $data_->where('reff',$reff);
            }
            if($years!='')
            {
                $data_->where('years',$years);
            }

            if($data_->exists())
            {
                $data_->orderBy('id', 'desc');
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
            $requestModule['class'] = 'Class_StoreRequest';
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
        $noTransaction=''; $idDepartemen=''; $departemen=''; $idSubDepartemen=''; $subDepartemen=''; 
        $dateTransaction=Carbon::now()->format('Y'); 
        $detailItems=''; $totalItems='0'; $status='0'; $note=''; $reff=''; $years=Carbon::now()->format('Y');

        if (isset($request['no_transaction']) && $request['no_transaction']!='' ) {$noTransaction = $request['no_transaction'];}
        if (isset($request['id_departemen']) && $request['id_departemen']!='' ) {$idDepartemen = $request['id_departemen'];}
        if (isset($request['departemen']) && $request['departemen']!='' ) {$departemen = $request['departemen'];}
        if (isset($request['id_sub_departemen']) && $request['id_sub_departemen']!='' ) {$idSubDepartemen = $request['id_sub_departemen'];}
        if (isset($request['sub_departemen']) && $request['sub_departemen']!='' ) {$subDepartemen = $request['sub_departemen'];}
        if (isset($request['date_transaction']) && $request['date_transaction']!='' ) {$dateTransaction = $request['date_transaction'];}
        if (isset($request['detail_items']) && $request['detail_items']!='' ) {$detailItems = $request['detail_items'];}
        if (isset($request['total_items']) && $request['total_items']!='' ) {$totalItems = $request['total_items'];}
        if (isset($request['status']) && $request['status']!='' ) {$status = $request['status'];}
        if (isset($request['note']) && $request['note']!='' ) {$note = $request['note'];}
        if (isset($request['reff']) && $request['reff']!='' ) {$reff = $request['reff'];}
        if (isset($request['years']) && $request['years']!='' ) {$years = $request['years'];}
 
        try
        {
            // cek data
            // $request=[];
            // $request['no_transaction'] = $noTransaction;
            // $request['type_transaction'] = $typeTransaction;
            // $request['detail_item'] = $detailItem;
            // $dataTransaction = $this->show($request);
            // if($dataTransaction['success'])
            // {
            //     return [
            //         'success' => false,
            //         'message' => 'Double Data',
            //         'data' => $dataTransaction
            //     ];
            // }
            // else
            // {
    
                $data = new store_request();
                $data->no_transaction = $noTransaction;
                $data->id_departemen = $idDepartemen;
                $data->departemen = $departemen; 
                $data->id_sub_departemen = $idSubDepartemen; 
                $data->sub_departemen = $subDepartemen; 
                $data->date_transaction = $dateTransaction; 
                $data->detail_items = $detailItems; 
                $data->total_items = $totalItems; 
                $data->status = $status; 
                $data->note = $note; 
                $data->reff = $reff; 
                $data->years = $years; 
                $data->save();
              
                return [
                    'success' => true,
                    'message' => 'Insert successful',
                    'data' => $data
                ];
            // }
            return $data;
        } catch (\Exception $ex) {
            # Insert Log Error
            $requestModule=[];
            $requestModule['reff'] = '-';
            $requestModule['service'] = 'Class';
            $requestModule['class'] = 'Class_StoreRequest';
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
            if (isset($request['no_transaction']) && $request['no_transaction']!='' ) {$updateData['no_transaction'] = $request['no_transaction'];}
            if (isset($request['id_departemen']) && $request['id_departemen']!='' ) {$updateData['id_departemen'] = $request['id_departemen'];}
            if (isset($request['departemen']) && $request['departemen']!='' ) {$updateData['departemen'] = $request['departemen'];}
            if (isset($request['id_sub_departemen']) && $request['id_sub_departemen']!='' ) {$updateData['id_sub_departemen'] = $request['id_sub_departemen'];}
            if (isset($request['sub_departemen']) && $request['sub_departemen']!='' ) {$updateData['sub_departemen'] = $request['sub_departemen'];}
            if (isset($request['date_transaction']) && $request['date_transaction']!='' ) {$updateData['date_transaction'] = $request['date_transaction'];}
            if (isset($request['detail_items']) && $request['detail_items']!='' ) {$updateData['detail_items'] = $request['detail_items'];}
            if (isset($request['total_items']) && $request['total_items']!='' ) {$updateData['total_items'] = $request['total_items'];}
            if (isset($request['status']) && $request['status']!='' ) {$updateData['status'] = $request['status'];}
            if (isset($request['note']) && $request['note']!='' ) {$updateData['note'] = $request['note'];}
            if (isset($request['reff']) && $request['reff']!='' ) {$updateData['reff'] = $request['reff'];}
            if (isset($request['years']) && $request['years']!='' ) {$updateData['yearss'] = $request['years'];}

            DB::table('store_request')
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
            $requestModule['class'] = 'Class_StoreRequest';
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