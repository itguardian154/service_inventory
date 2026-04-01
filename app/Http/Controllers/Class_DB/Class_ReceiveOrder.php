<?php

namespace App\Http\Controllers\Class_DB;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Log\LogError;
use App\Models\receive_order;
use Carbon\Carbon;
use DateTime;

class Class_ReceiveOrder
{
    /**
     * Read table
     */
    public function show($request)
    {
        // set value variable
        $noTransaction=''; $idDepartemen=''; $departemen=''; $idSubDepartemen=''; $subDepartemen='';
        $typeTransaction=''; $dateTransaction=''; $noPo=''; $noInvoice=''; $supplier=''; $typeParent=''; $isSr='';
        $detailItem=''; $total=''; $status=''; $years='';

        if (isset($request['no_transaction']) && $request['no_transaction']!='' ) {$noTransaction = $request['no_transaction'];}
        if (isset($request['id_departemen']) && $request['id_departemen']!='' ) {$idDepartemen = $request['id_departemen'];}
        if (isset($request['departemen']) && $request['departemen']!='' ) {$departemen = $request['departemen'];}
        if (isset($request['id_sub_departemen']) && $request['id_sub_departemen']!='' ) {$idSubDepartemen = $request['id_sub_departemen'];}
        if (isset($request['sub_departemen']) && $request['sub_departemen']!='' ) {$subDepartemen = $request['sub_departemen'];}
        if (isset($request['type_transaction']) && $request['type_transaction']!='' ) {$typeTransaction = $request['type_transaction'];}
        if (isset($request['date_transaction']) && $request['date_transaction']!='' ) {$dateTransaction = $request['date_transaction'];}
        if (isset($request['no_po']) && $request['no_po']!='' ) {$noPo = $request['no_po'];}
        if (isset($request['no_invoice']) && $request['no_invoice']!='' ) {$noInvoice = $request['no_invoice'];}
        if (isset($request['supplier']) && $request['supplier']!='' ) {$supplier = $request['supplier'];}
        if (isset($request['type_parent']) && $request['type_parent']!='' ) {$typeParent = $request['type_parent'];}
        if (isset($request['is_sr']) && $request['is_sr']!='' ) {$isSr = $request['is_sr'];}
        if (isset($request['detail_item']) && $request['detail_item']!='' ) {$detailItem = $request['detail_item'];}
        if (isset($request['total']) && $request['total']!='' ) {$total = $request['total'];}
        if (isset($request['status']) && $request['status']!='' ) {$status = $request['status'];}
        if (isset($request['years']) && $request['years']!='' ) {$years = $request['years'];}

        try
        {
            $data_ = DB::table('receive_order');
            if($idDepartemen!='')
            {
                $data_->where('id_departemen',$idDepartemen);
            }
            if($idSubDepartemen!='')
            {
                $data_->where('id_sub_departemen',$idSubDepartemen);
            }
            if($noTransaction!='')
            {
                $data_->where('no_transaction',$noTransaction);
            }
            if($typeTransaction!='')
            {
                $data_->where('type_transaction',$typeTransaction);
            }
            if($dateTransaction!='')
            {
                $data_->where('date_transaction',$dateTransaction);
            }
            if($noPo!='')
            {
                $data_->where('no_po',$noPo);
            }
            if($noInvoice!='')
            {
                $data_->where('no_invoice',$noInvoice);
            }
            if($supplier!='')
            {
                $data_->where('supplier',$supplier);
            }
            if($typeParent!='')
            {
                $data_->where('type_parent',$typeParent);
            }
            if($isSr!='')
            {
                $data_->where('is_sr',$isSr);
            }
            if($detailItem!='')
            {
                $data_->where('detail_item',$detailItem);
            }
            if($total!='')
            {
                $data_->where('total',$total);
            }
            if($status!='')
            {
                $data_->where('status',$status);
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
            $requestModule['class'] = 'Class_ReceiveOrder';
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
        $typeTransaction=''; $dateTransaction=''; $noPo=''; $noInvoice=''; $supplier=''; $typeParent=''; $isSr='';
        $detailItem=''; $total=0; $status='0'; $years=Carbon::now()->format('Y'); $reff=''; $jsonPo='';

        if (isset($request['no_transaction']) && $request['no_transaction']!='' ) {$noTransaction = $request['no_transaction'];}
        if (isset($request['id_departemen']) && $request['id_departemen']!='' ) {$idDepartemen = $request['id_departemen'];}
        if (isset($request['departemen']) && $request['departemen']!='' ) {$departemen = $request['departemen'];}
        if (isset($request['id_sub_departemen']) && $request['id_sub_departemen']!='' ) {$idSubDepartemen = $request['id_sub_departemen'];}
        if (isset($request['sub_departemen']) && $request['sub_departemen']!='' ) {$subDepartemen = $request['sub_departemen'];}
        if (isset($request['type_transaction']) && $request['type_transaction']!='' ) {$typeTransaction = $request['type_transaction'];}
        if (isset($request['date_transaction']) && $request['date_transaction']!='' ) {$dateTransaction = $request['date_transaction'];}
        if (isset($request['no_po']) && $request['no_po']!='' ) {$noPo = $request['no_po'];}
        if (isset($request['no_invoice']) && $request['no_invoice']!='' ) {$noInvoice = $request['no_invoice'];}
        if (isset($request['supplier']) && $request['supplier']!='' ) {$supplier = $request['supplier'];}
        if (isset($request['type_parent']) && $request['type_parent']!='' ) {$typeParent = $request['type_parent'];}
        if (isset($request['is_sr']) && $request['is_sr']!='' ) {$isSr = $request['is_sr'];}
        if (isset($request['detail_item']) && $request['detail_item']!='' ) {$detailItem = $request['detail_item'];}
        if (isset($request['json_po_item']) && $request['json_po_item']!='' ) {$jsonPo = $request['json_po_item'];}
        if (isset($request['total']) && $request['total']!='' ) {$total = $request['total'];}
        if (isset($request['status']) && $request['status']!='' ) {$status = $request['status'];}
        if (isset($request['years']) && $request['years']!='' ) {$years = $request['years'];}
        if (isset($request['reff']) && $request['reff']!='' ) {$reff = $request['reff'];}
        if (isset($request['json_po_item']) && $request['json_po_item']!='' ) {$jsonPo = $request['json_po_item'];}
 
        try
        {
            // cek data
            $request=[];
            $request['no_transaction'] = $noTransaction;
            $request['type_transaction'] = $typeTransaction;
            $request['detail_item'] = $detailItem;
            $dataTransaction = $this->show($request);
            
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
                $data = new receive_order();
                $data->no_transaction = $noTransaction;
                $data->id_departemen = $idDepartemen;
                $data->departemen = $departemen;
                $data->id_sub_departemen = $idSubDepartemen;
                $data->sub_departemen = $subDepartemen;
                $data->type_transaction = $typeTransaction;
                $data->date_transaction = $dateTransaction; 
                $data->no_po = $noPo; 
                $data->no_invoice = $noInvoice; 
                $data->supplier = $supplier; 
                $data->type_parent = $typeParent; 
                $data->is_sr = $isSr; 
                $data->detail_item = $detailItem; 
                $data->json_po_item = $jsonPo; 
                $data->total = $total; 
                $data->status = $status; 
                $data->years = $years; 
                $data->reff = $reff; 
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
            $requestModule['class'] = 'Class_ReceiveOrder';
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
            if (isset($request['type_transaction']) && $request['type_transaction']!='' ) {$updateData['type_transaction'] = $request['type_transaction'];}
            if (isset($request['date_transaction']) && $request['date_transaction']!='' ) {$updateData['date_transaction'] = $request['date_transaction'];}
            if (isset($request['no_po']) && $request['no_po']!='' ) {$updateData['no_po'] = $request['no_po'];}
            if (isset($request['no_invoice']) && $request['no_invoice']!='' ) {$updateData['no_invoice'] = $request['no_invoice'];}
            if (isset($request['supplier']) && $request['supplier']!='' ) {$updateData['supplier'] = $request['supplier'];}
            if (isset($request['type_parent']) && $request['type_parent']!='' ) {$updateData['type_parent'] = $request['type_parent'];}
            if (isset($request['is_sr']) && $request['is_sr']!='' ) {$updateData['is_sr'] = $request['is_sr'];}
            if (isset($request['detail_item']) && $request['detail_item']!='' ) {$updateData['detail_item'] = $request['detail_item'];}
            if (isset($request['json_po_item']) && $request['json_po_item']!='' ) {$jsonPo = $request['json_po_item'];}
            if (isset($request['total']) && $request['total']!='' ) {$updateData['total'] = $request['total'];}
            if (isset($request['status']) && $request['status']!='' ) {$updateData['status'] = $request['status'];}
            if (isset($request['years']) && $request['years']!='' ) {$updateData['years'] = $request['years'];}

            DB::table('receive_order')
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
            $requestModule['class'] = 'Class_ReceiveOrder';
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