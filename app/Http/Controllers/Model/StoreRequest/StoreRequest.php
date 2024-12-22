<?php

namespace App\Http\Controllers\Model\StoreRequest;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Log\LogError;
use App\Http\Controllers\Generate\GenerateID;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Model\Stock\Stock;
use App\Http\Controllers\Class_DB\Class_Stock;
use App\Http\Controllers\Class_DB\Class_StockTransaction;

use App\Http\Controllers\Class_DB\Class_StoreRequest;
use App\Http\Controllers\Class_DB\Class_StoreRequestDetail;
use App\Http\Controllers\Class_DB\Class_StoreRequestHistoryApproval;

use App\Http\Controllers\Model\RoleAccess\RoleAccessManagement;
use App\Models\store_request_detail;
use Carbon\Carbon;
use DateTime;
use Exception;

class StoreRequest
{
    public function getStoreRequest($request)
    {
        try
        {
            $result=[];
            $classDB = new Class_StoreRequest();
            $resultClassDB = $classDB->show($request);
            $result = $resultClassDB['data'];

            if (isset($request['no_transaction']) && $request['no_transaction']!='') 
            {
                $result=[];
                $result['get_StoreRequest'] = $resultClassDB['data'];
            }

            return [
                'success' => true,
                'message' => 'Get successful',
                'data'=> $result
            ];
        } catch (\Exception $ex) {
            # Insert Log Error
            $requestModule=[];
            $requestModule['reff'] = '-';
            $requestModule['service'] = 'Model';
            $requestModule['class'] = 'StoreRequest';
            $requestModule['function'] = 'getStoreRequest';
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

    public function insertStoreRequest($request)
    {
        try
        {
            DB::beginTransaction();
            // Validate the incoming request data
            $request->validate([
                'detail_items'           => 'required|string',             // Required
            ]);
         
            $noTransaction=''; $idDepartemen=''; $departemen=''; $idSubDepartemen=''; $subDepartemen='';
            $dateTransaction=Carbon::now()->format('Y-m-d');
            $detailItems=''; $totalItems=''; $status=''; $note=''; $years=Carbon::now()->format('Y'); $reff='';

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

            $result=[];
            // generate ID Store Request
            $requestClassID=[];
            $classID = new GenerateID();
            $noTransaction = $classID->getIDStoreRequest($requestClassID);
        
            # insert Store Request
            $requestModule = [];
            $requestModule['no_transaction'] = $noTransaction;
            $requestModule['id_departemen'] = $idDepartemen;
            $requestModule['departemen'] = $departemen;
            $requestModule['id_sub_departemen'] = $idSubDepartemen;
            $requestModule['sub_departemen'] = $subDepartemen;
            $requestModule['date_transaction'] = $dateTransaction;
            $requestModule['detail_items'] = $detailItems;
            $requestModule['total_items'] = $totalItems;
            $requestModule['status'] = $status;
            $requestModule['note'] = $note;
            $requestModule['reff'] = $reff;
            $requestModule['years'] = $years;

            $classDB = new Class_StoreRequest();
            $resultClassDB = $classDB->insert($requestModule);
       
            if(!$resultClassDB['success'])
            {
                DB::rollBack();
                return $resultClassDB;
            }
            $result['insert_storeRequest'] = $resultClassDB['data'];
            $idSRMaster = $result['insert_storeRequest']->id;
            # end Insert Store Request

            # insert Store Request Detail
            $jsonDetailItem = json_decode($detailItems);
            $totalItems=0;
            foreach($jsonDetailItem as $v)
            {
                $requestModule = [];
                $requestModule['no_transaction'] = $noTransaction;
                if (isset($v->code_item) && $v->code_item!='' ) {$requestModule['code_item'] = $v->code_item;}
                if (isset($v->description) && $v->description!='' ) {$requestModule['description'] = $v->description;}
                if (isset($v->unit) && $v->unit!='' ) {$requestModule['unit'] = $v->unit;}
                if (isset($v->qty) && $v->qty!='' ) {$requestModule['qty'] = $v->qty;}
                if (isset($v->price) && $v->price!='' ) {$requestModule['price'] = $v->price;}
                if (isset($v->sub_total) && $v->sub_total!='' ) {$requestModule['sub_total'] = $v->sub_total;}
                if (isset($v->expired_date) && $v->expired_date!='' ) {$requestModule['expired_date'] = $v->expired_date;}
                if (isset($v->note) && $v->note!='' ) {$requestModule['note'] = $v->note;} 
                $requestModule['years'] = $years; 
                
                $classDB = new Class_StoreRequestDetail(); 
                $resultClassDB = $classDB->insert($requestModule); 
                if(!$resultClassDB['success']) 
                { 
                    DB::rollBack();
                    return $resultClassDB;
                }
                $result['insert_storeRequestDetail'] = $resultClassDB['data'];
                $totalItems++;
            } 
            # end insert receive Store Request Detail

            # update total Store Request
            $requestModule=[];
            $requestModule['id'] = $idSRMaster;
            $requestModule['total_items'] = $totalItems;
            $classDB = new Class_StoreRequest();
            $resultClassDB = $classDB->update($requestModule);
           
            if(!$resultClassDB['success'])
            {
                DB::rollBack();
                return $resultClassDB;
            }
            $result['update_storeRequest'] = $resultClassDB['data'];
            # end update total Store Request
          
            # insert history approval
            $requestRoleAccess =[];
            $requestRoleAccess['id_access_management'] = 'SR-001'; // approval_adjustment
            $classRoleAccess = new RoleAccessManagement();
            $resultRoleAccess = $classRoleAccess->getRoleAccess($requestRoleAccess);

            foreach($resultRoleAccess['data']['get_roleAccessDetail'] as $v)
            {
                $requestModule=[];
                $requestModule['no_transaction']=$noTransaction;
                $requestModule['ord']=$v->ord;
                $requestModule['id_role_access']=$v->id_role_access;
                $requestModule['pic']=$v->pic;
                $requestModule['grade']=$v->grade;
                $requestModule['departemen']=$v->departemen;

                $classDB = new Class_StoreRequestHistoryApproval();
                $resultClassDB = $classDB->insert($requestModule);
                if(!$resultClassDB['success'])
                {
                    DB::rollBack();
                    return $resultClassDB;
                }
                $result['insert_storeRequestHistoryApproval'] = $resultClassDB['data'];  
            }
            # end insert history approval

            DB::commit();
            return [
                'success' => true,
                'message' => 'Insert successfuly',
                'data'=> $result
            ];
        } catch (\Exception $ex) {
            DB::rollBack();
            # Insert Log Error
            $requestModule=[];
            $requestModule['reff'] = '-';
            $requestModule['service'] = 'Model';
            $requestModule['class'] = 'StoreRequest';
            $requestModule['function'] = 'insertStoreRequest';
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

    public function updateReceiveOrder($request)
    {
        try
        {
            DB::beginTransaction();
            // Validate the incoming request data
            $request->validate([
                'id'                        => 'required|string',             // Required
                'no_transaction'            => 'required|string',             // Required
                'reff'                      => 'required|string',             // Required
            ]);

            $id=''; $noTransaction=''; $detailItem='';
            if (isset($request['id']) && $request['id']!='' ) {$id = $request['id'];}
            if (isset($request['no_transaction']) && $request['no_transaction']!='' ) {$noTransaction = $request['no_transaction'];}
            if (isset($request['detail_item']) && $request['detail_item']!='' ) {$detailItem = $request['detail_item'];}

            $result=[];
            $requestModule=[];
            if (isset($request['id']) && $request['id']!='' ) {$requestModule['id'] = $request['id'];}
            if (isset($request['type_transaction']) && $request['type_transaction']!='' ) {$requestModule['type_transaction'] = $request['type_transaction'];}
            if (isset($request['date_transaction']) && $request['date_transaction']!='' ) {$requestModule['date_transaction'] = $request['date_transaction'];}
            if (isset($request['no_po']) && $request['no_po']!='' ) {$requestModule['no_po'] = $request['no_po'];}
            if (isset($request['no_invoice']) && $request['no_invoice']!='' ) {$requestModule['no_invoice'] = $request['no_invoice'];}
            if (isset($request['supplier']) && $request['supplier']!='' ) {$requestModule['supplier'] = $request['supplier'];}
            if (isset($request['type_parent']) && $request['type_parent']!='' ) {$requestModule['type_parent'] = $request['type_parent'];}
            if (isset($request['is_sr']) && $request['is_sr']!='' ) {$requestModule['is_sr'] = $request['is_sr'];}
            if (isset($request['detail_item']) && $request['detail_item']!='' ) {$requestModule['detail_item'] = $request['detail_item'];}
            if (isset($request['total']) && $request['total']!='' ) {$requestModule['total'] = $request['total'];}
            if (isset($request['status']) && $request['status']!='' ) {$requestModule['status'] = $request['status'];}
            if (isset($request['note']) && $request['note']!='' ) {$requestModule['note'] = $request['note'];}
            if (isset($request['years']) && $request['years']!='' ) {$requestModule['years'] = $request['years'];}
            if (isset($request['reff']) && $request['reff']!='' ) {$requestModule['reff'] = $request['reff'];}

            $classModel = new Class_ReceiveOrder();
            $resultClassDB = $classModel->update($requestModule);
            if(!$resultClassDB['success'])
            {
                DB::rollBack();
                return $resultClassDB;
            }
            $result['update_receiveOrder'] = $resultClassDB['data'];

            # edit with Detail Items
            if($detailItem!='')
            {
                // delete from table store_request_detail
                DB::table('store_request_detail')
                ->where('no_transaction',$noTransaction)
                ->delete();
                $jsonDetailItem = json_decode($detailItem);
                $totalPrice=0;

                foreach($jsonDetailItem as $v)
                {

                    $totalPrice += $v->qty * $v->price;
    
                    $requestModule=[];
                    $requestModule['no_transaction'] = $noTransaction;
                    $requestModule['code_item'] = $v->code_item;
                    $requestModule['description'] =  $v->description;
                    $requestModule['unit'] = $v->unit;
                    $requestModule['qty'] =  $v->qty;
                    $requestModule['price'] = $v->price;
                    $requestModule['sub_total'] = $v->sub_total;
                    $requestModule['expired_date'] = $v->expired_date;
                    $requestModule['note'] = $v->note;
                    $requestModule['years'] = Carbon::now()->format('Y');
                
                    # end declare variable request detail
                    $classDB = new Class_ReceiveOrderDetail();
                    $resultClassDB = $classDB->insert($requestModule);    
                 
                    if(!$resultClassDB['success'])
                    {
                        DB::rollBack();
                        return $resultClassDB;
                    }
                    $result['insert_classReceiveOrder'] = $resultClassDB['data'];
                }
            }
            # end edit with Detail Items
         
            # update Receive Order
            $requestModule=[];
            $requestModule['id'] = $id;
            $requestModule['total'] = $totalPrice;
            $classDB = new Class_ReceiveOrder();
            $resultClassDB = $classDB->update($requestModule);
       
            if(!$resultClassDB['success'])
            {
                DB::rollBack();
                return $resultClassDB;
            }
            $result['update_receiveOrder'] = $resultClassDB['data'];
            #

            DB::commit();
            return [
                'success' => true,
                'message' => 'Update successfuly',
                'data'=> $result
            ];
        } catch (\Exception $ex) {
            # Insert Log Error
            $requestModule=[];
            $requestModule['reff'] = '-';
            $requestModule['service'] = 'Model';
            $requestModule['class'] = 'ReceiveOrder';
            $requestModule['function'] = 'updateReceiveOrder';
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

    public function updateApprovalReceiveOrder($request)
    {
        try
        {
            DB::beginTransaction();
            // Validate the incoming request data
            $request->validate([
                'id'                             => 'required|string',             // Required
                'no_transaction'                 => 'required|string',             // Required
                'status_approve'                 => 'required|string',             // Required
                'id_karyawan_approve'            => 'required|string',             // Required
                'reff'                           => 'required|string',             // Required
            ]);

            $id=''; $noTransaction=''; $status=''; $idKaryawan='';
            if (isset($request['id']) && $request['id']!='' ) {$id=$request['id'];}
            if (isset($request['no_transaction']) && $request['no_transaction']!='' ) {$noTransaction=$request['no_transaction'];}
            if (isset($request['status_approve']) && $request['status_approve']!='' ) {$status=$request['status_approve'];}
            if (isset($request['id_karyawan_approve']) && $request['id_karyawan_approve']!='' ) {$idKaryawan=$request['id_karyawan_approve'];}

           // cek adjustment history approve
           $requestClassDB = [];
           $requestClassDB['no_transanction'] = $noTransaction;
           $requestClassDB['status'] = '0'; // belum di approve
           $classDB = new Class_ReceiveOrderHistoryApproval();
           $resultClassDB = $classDB->show($requestClassDB);
           if(!$resultClassDB['success'])
           {
                $requestClassDB = [];
                $requestClassDB['no_transanction'] = $noTransaction;
                $requestClassDB['status'] = '1'; // sudah di approve
                $classDB = new Class_ReceiveOrderHistoryApproval();
                $resultClassDB = $classDB->show($requestClassDB);
                if($resultClassDB['success'])
                {
                  
                }
                else
                {
                    DB::rollBack();
                    return $resultClassDB;
                }
           }
           else
           {
               $statusApproval = 0;
               foreach($resultClassDB['data'] as $v) // jika user mempunyai double role maka akan terupdate semua
               {
                   $idRoleAccess = $v->id_role_access;
                   $pic = $v->pic;
                   $statusApproval = $v->ord;
                   if($status=='9') // reject
                   {
                       $statusApproval='9';
                   }
                   if($status=='0')
                   {
                       DB::table('receive_order_history_approval')
                       ->where('no_transaction',$noTransaction)
                       ->update([
                           'status' => $status,
                       ]);
                       $statusApproval='0'; // back to draft
                   }
              
                   // cek user access management 
                   $roleUser_ = DB::table('users_access_management')
                   ->where('id_karyawan',$idKaryawan)
                   ->where('id_role_access', $idRoleAccess);
                   if($roleUser_->exists())
                   {
                       $roleUser = $roleUser_->first();
                   
                       DB::table('receive_order_history_approval')
                       ->where('no_transaction',$noTransaction)
                       ->where('id_role_access',$idRoleAccess)
                       ->update([
                           'id_karyawan' => $roleUser->id_karyawan,
                           'name' => $roleUser->name,
                           'departemen' => $roleUser->departemen,
                           'grade' => $roleUser->grade,
                           'status' => $status,
                           'years' => Carbon::now()->format('Y'),
                           'date' => carbon::now()->format('Y-m-d H:i:s')
                       ]);
                       $result['update_approveHistory'] = 'successfuly update No Transaction : '.$noTransaction.' ID Role : '. $idRoleAccess;

                       $requestClassDB =[];
                       $requestClassDB['id'] = $id;
                       $requestClassDB['status'] =  $statusApproval; // 11= complete full acc;
                       $classDB = new Class_ReceiveOrder();
                       $resultClassDB = $classDB->update($requestClassDB);
                       if(!$resultClassDB['success'])
                       {
                           DB::rollBack();
                           return $resultClassDB;
                       }
                       $result['update_ReceiveOrder'] = 'successfuly update No Transaction : '.$noTransaction.' Status : '. $statusApproval;

                   }
                   else
                   {
                       $result['status_acount'] = 'Akun ('.$idKaryawan.') Not Have Access Approval '. $v->pic .' (status : waiting '.$v->pic.')';
                       // return $result;
                   }
               }
           }
    
           // cek sudah complete atau belum
           $requestClassDB = [];
           $requestClassDB['no_transaction'] = $noTransaction;
           $requestClassDB['status'] = '0'; // belum di approve
           $classDB = new Class_ReceiveOrderHistoryApproval();
           $resultClassDB = $classDB->show($requestClassDB);
           if(!$resultClassDB['success']) // complete
           {
               $requestClassDB =[];
               $requestClassDB['id'] = $id;
               $requestClassDB['status'] =  '11'; // complete full acc
               $classDB = new Class_ReceiveOrder();
               $resultClassDB = $classDB->update($requestClassDB);
               if(!$resultClassDB['success'])
               {
                   DB::rollBack();
                   return $resultClassDB;
               }
               $result['update_ReceiveOrder'] = $resultClassDB['data'];

               // update stock by adjustment
               $requestClassDB=[];
               $requestClassDB['no_transaction'] = $noTransaction;
               $classDB = new Class_ReceiveOrderDetail();
               $resultClassDB = $classDB->show($requestClassDB);
               if(!$resultClassDB['success'])
               {
                   DB::rollBack();
                   return $resultClassDB;
               }
               foreach($resultClassDB['data'] as $v)
               {
                   // find id item
                   $requestClassDB =[];
                   $requestClassDB['code'] = $v->code_item;
                   $classDB = new Class_Stock();
                   $resultClassDB = $classDB->show($requestClassDB);
                   
                   if(!$resultClassDB['success'])
                   {
                       DB::rollBack();
                       return 'Code Item '. $v->code_item.' Tidak Ditemukan';
                   }
                   else
                   {   
                       $idItem = $resultClassDB['data'][0]->id;
                    
                       // insert stock transaction
                       $requestClassDB = [];
                       $requestClassDB['id_item'] = $idItem;
                       $requestClassDB['item_group'] = $v->item_group;
                       $requestClassDB['brand'] =$v->brand;
                       $requestClassDB['code'] = $v->code_item;
                       $requestClassDB['items'] = $v->items;
                       $requestClassDB['description'] = $v->description;
                       $requestClassDB['type_transaction'] = '1';
                       $requestClassDB['in'] = $v->qty;
                       $requestClassDB['out'] = 0;
                       $requestClassDB['no_transaction'] = $v->no_transaction;
                       $requestClassDB['qty'] = $v->qty;
                       $requestClassDB['price'] = $v->price;
                       $requestClassDB['total_price'] = $v->price * $v->qty;
                       $requestClassDB['exp_date'] = $v->expired_date;
                       $requestClassDB['origin_of_goods'] = 'Good Receive';
                       $requestClassDB['date'] = Carbon::now()->format('Y-m-d');
                       $requestClassDB['years'] = Carbon::now()->format('Y');
                       $classDB = new Class_StockTransaction();
                       $resultClassDB = $classDB->insert($requestClassDB);
                       if(!$resultClassDB['success'])
                       {
                           DB::rollBack();
                           return $resultClassDB;
                       }
                       $result['insert_stockTransaction'] = $resultClassDB['data'];

                       $requestClassModel = [];
                       $requestClassModel['id_item'] = $idItem;
                       $requestClassModel['code'] = $v->code_item;
                       $classModel = new Stock();
                       $resultModel = $classModel->updateStockFromTransaction($requestClassModel);
                     
                       if(!$resultModel['success'])
                       {
                           DB::rollBack();
                           return $resultModel;
                       }
                       $result['update_stock'] = $resultModel['data'];
                   }
               }
           }
           else
           {
               $result['status_approval'] = 'Approval Receive Order not Completed';
           }
           
           DB::commit();
           return [
               'success' => true,
               'message' => 'Update successfuly',
               'data'=> $result
           ];
        } catch (\Exception $ex) {
            # Insert Log Error
            $requestModule=[];
            $requestModule['reff'] = '-';
            $requestModule['service'] = 'Model';
            $requestModule['class'] = 'ReceiveOrder';
            $requestModule['function'] = 'updateApprovalReceiveOrder';
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
