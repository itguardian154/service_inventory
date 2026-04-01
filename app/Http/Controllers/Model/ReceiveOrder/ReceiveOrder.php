<?php

namespace App\Http\Controllers\Model\ReceiveOrder;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Log\LogError;
use App\Http\Controllers\Generate\GenerateID;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Whatsapp\SentMessage;
use App\Http\Controllers\API\API_Service;

use App\Http\Controllers\Model\Stock\Stock;
use App\Http\Controllers\Model\StoreRequest\StoreRequest;
use App\Http\Controllers\Model\Stock\StockTransaction;

use App\Http\Controllers\Class_DB\Class_Stock;
use App\Http\Controllers\Class_DB\Class_StockTransaction;
use App\Http\Controllers\Class_DB\Class_ReceiveOrder;
use App\Http\Controllers\Class_DB\Class_ReceiveOrderDetail;
use App\Http\Controllers\Class_DB\Class_ReceiveOrderHistoryApproval;
use App\Http\Controllers\Class_DB\Class_UsersAccessManagement;

use App\Http\Controllers\Model\RoleAccess\RoleAccessManagement;
use App\Models\receive_order_detail;
use Carbon\Carbon;
use DateTime;
use Exception;

class ReceiveOrder extends Controller
{
    public function getReceiveOrder($request)
    {
        try
        {
            $result=[];
            $classDB = new Class_ReceiveOrder();
            $resultClassDB = $classDB->show($request);
            $result = $resultClassDB['data'];

            if (isset($request['no_transaction']) && $request['no_transaction']!='') 
            {
                $result=[];
                $result['get_receive_order'] = $resultClassDB['data'];

                $classDB = new Class_ReceiveOrderDetail();
                $resultClassDB = $classDB->show($request);
                $result['get_receive_order_detail'] = $resultClassDB['data'];

                $classDB = new Class_ReceiveOrderHistoryApproval();
                $resultClassDB = $classDB->show($request);
                $result['get_receive_order_history_approval'] = $resultClassDB['data'];
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
            $requestModule['class'] = 'ReceiveOrder';
            $requestModule['function'] = 'getReceiveOrder';
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

    public function insertReceiveOrder($request)
    {
        try
        {
            DB::beginTransaction();
            // Validate the incoming request data
            $request->validate([
                'type_transaction'           => 'required|string',             // Required
            ]);
         
            $noTransaction=''; $idDepartemen='';$departemen=''; $idSubDepartemen=''; $subDepartemen=''; 
            $typeTransaction=''; $dateTransaction=Carbon::now()->format('Y-m-d'); $noPo=''; $noInvoice=''; $supplier=''; $typeParent=''; $isSr='0';
            $detailItem=''; $total=''; $status=''; $note=''; $years=Carbon::now()->format('Y'); $reff=''; $jsonPoItem='';

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
            if (isset($request['json_po_item']) && $request['json_po_item']!='' ) {$jsonPoItem = $request['json_po_item'];}
            if (isset($request['total']) && $request['total']!='' ) {$total = $request['total'];}
            if (isset($request['status']) && $request['status']!='' ) {$status = $request['status'];}
            if (isset($request['note']) && $request['note']!='' ) {$note = $request['note'];}
            if (isset($request['years']) && $request['years']!='' ) {$years = $request['years'];}
            if (isset($request['reff']) && $request['reff']!='' ) {$reff = $request['reff'];}
           
            $result=[];

            // generate ID Adjustment
            $requestClassID=[];
            $classID = new GenerateID();
            $noTransaction = $classID->getIDReceiveOrder($requestClassID);

            # insert receive order
            $requestModule = [];
            $requestModule['no_transaction'] = $noTransaction;
            $requestModule['id_departemen'] = $idDepartemen;
            $requestModule['departemen'] = $departemen;
            $requestModule['id_sub_departemen'] = $idSubDepartemen;
            $requestModule['sub_departemen'] = $subDepartemen;
            $requestModule['type_transaction'] = $typeTransaction;
            $requestModule['date_transaction'] = $dateTransaction;
            $requestModule['no_po'] = $noPo;
            $requestModule['no_invoice'] = $noInvoice;
            $requestModule['supplier'] = $supplier;
            $requestModule['type_parent'] = $typeParent;
            $requestModule['is_sr'] = $isSr;
            $requestModule['detail_item'] = $detailItem;
            $requestModule['json_po_item'] = $jsonPoItem;
            $requestModule['total'] = $total;
            $requestModule['status'] = $status;
            $requestModule['note'] = $note;
            $requestModule['years'] = $years;
            $requestModule['reff'] = $reff;
            $classDB = new Class_ReceiveOrder();
            $resultClassDB = $classDB->insert($requestModule);
       
            if(!$resultClassDB['success'])
            {
                DB::rollBack();
                return $resultClassDB;
            }
            $result['insert_receiveOrder'] = $resultClassDB['data'];
            $idReceiverOrderMaster = $result['insert_receiveOrder']->id;
            # end insert receive order

            if($detailItem!='')
            {
                # insert receive order detail
                $jsonDetailItem = json_decode($detailItem);
                $totalPrice=0;
                foreach($jsonDetailItem as $v)
                {
                    $requestModule = [];
                    $requestModule['no_transaction'] = $noTransaction;
                    if (isset($v->code_item) && $v->code_item!='' ) {$requestModule['code_item'] = $v->code_item;}
                    if (isset($v->items) && $v->items!='' ) {$requestModule['items'] = $v->items;}
                    if (isset($v->description) && $v->description!='' ) {$requestModule['description'] = $v->description;}
                    if (isset($v->unit) && $v->unit!='' ) {$requestModule['unit'] = $v->unit;}
                    if (isset($v->qty) && $v->qty!='' ) {$requestModule['qty'] = $v->qty;}
                    if (isset($v->price) && $v->price!='' ) {$requestModule['price'] = $v->price;}
                    if (isset($v->sub_total) && $v->sub_total!='' ) {$requestModule['sub_total'] = $v->sub_total;}
                    if (isset($v->expired_date) && $v->expired_date!='' ) {$requestModule['expired_date'] = $v->expired_date;}
                    if (isset($v->note) && $v->note!='' ) {$requestModule['note'] = $v->note;}
                    $requestModule['years'] = $years;

                    $qty=$v->qty;
                    $price=$v->price;
                    $totalPrice += $qty*$price;
                    $classDB = new Class_ReceiveOrderDetail();
                    $resultClassDB = $classDB->insert($requestModule);
                    if(!$resultClassDB['success'])
                    {
                        DB::rollBack();
                        return $resultClassDB;
                    }
                    $result['insert_receiveOrderDetail'] = $resultClassDB['data'];
                } 
                # end insert receive order detail

                # update total Receive Order
                if($total=='')
                {
                    $requestModule=[];
                    $requestModule['id'] = $idReceiverOrderMaster;
                    $requestModule['total'] = $totalPrice;
                    $classDB = new Class_ReceiveOrder();
                    $resultClassDB = $classDB->update($requestModule);

                    if(!$resultClassDB['success'])
                    {
                        DB::rollBack();
                        return $resultClassDB;
                    }
                    $result['update_receiveOrder'] = $resultClassDB['data'];
                }
                # end update total Receive Order
            }
            
          
            # insert history approval
            $requestRoleAccess =[];
            $requestRoleAccess['id_access_management'] = 'RO-001'; // approval_adjustment
            $classRoleAccess = new RoleAccessManagement();
            $resultRoleAccess = $classRoleAccess->getRoleAccess($requestRoleAccess);

            $_first=true;
            $i=0;
            foreach($resultRoleAccess['data']['get_roleAccessDetail'] as $v)
            {
                $requestModule=[];
                $requestModule['no_transaction']=$noTransaction;
                $requestModule['ord']=$v->ord;
                $requestModule['id_role_access']=$v->id_role_access;
                $requestModule['pic']=$v->pic;
                $requestModule['grade']=$v->grade;
                $requestModule['departemen']=$v->departemen;

                if($_first==true)
                {
                    // Memisahkan input ke variabel
                    $idKaryawan=''; $name =''; $grade=''; $departemen='';
                    $processedString = preg_replace('/\s*-\s*/', '-', $reff);
                    list($name, $departemen, $grade) = explode('-', $processedString);
                    # get role access
                    $requestClassDB =[];
                    $requestClassDB['id_role_access'] = $v->id_role_access;
                    $requestClassDB['name'] = $name;
                    $requestClassDB['departemen'] = $departemen;
                    $requestClassDB['grade'] = $grade;
                 
                    $classDB = new Class_UsersAccessManagement();
                    $resultClassDB = $classDB->show($requestClassDB);
                
                    if($resultClassDB['success']) 
                    { 
                        $idKaryawan = $resultClassDB['data'][0]->id_karyawan;
                        $name = $resultClassDB['data'][0]->name;
                        $departemen = $resultClassDB['data'][0]->departemen;
                        $grade = $resultClassDB['data'][0]->grade;
                    }
                    else
                    {
                        $requestClassAPI=[];
                        $requestClassAPI['name'] = $name;
                        $requestClassAPI['departemen'] = $departemen;
                        $requestClassAPI['grade'] = $grade;
                        $classApi = new API_Service();
                        $resultClassAPI = $classApi->getDataKaryawan($requestClassAPI);
                 
                        if($resultClassAPI['success'] && $resultClassAPI['data'][0]!=null) 
                        {
                            $idKaryawan=$resultClassAPI['data'][0]['id_absen'];
                        }
                    }

                    $requestModule['id_karyawan']=$idKaryawan;
                    $requestModule['name']=$name;
                    $requestModule['grade']=$grade;
                    $requestModule['departemen']=$departemen;
                    $requestModule['status']='1';
                    $requestModule['date']=Carbon::now()->format('Y-m-d H:i:s');
                    $requestModule['years']=Carbon::now()->format('Y');
                 
                    $_first=false;
                }
           
                $classDB = new Class_ReceiveOrderHistoryApproval();
                $resultClassDBApproval = $classDB->insert($requestModule);
                // if(!$resultClassDBApproval['success'])
                // {
                //     DB::rollBack();
                //     return $resultClassDBApproval;
                // }
                $result['insert_receiveOrderHistoryApproval'] = $resultClassDBApproval['data'];  
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
            $requestModule['class'] = 'ReceiveOrder';
            $requestModule['function'] = 'insertReceiveOrder';
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
            if (isset($request['json_po_item']) && $request['json_po_item']!='' ) {$jsonPoItem = $request['json_po_item'];}
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
                // delete from table receive_order_detail
                DB::table('receive_order_detail')
                ->where('no_transaction',$noTransaction)
                ->delete();
                $jsonDetailItem = json_decode($detailItem);
                $totalPrice=0;

                foreach($jsonDetailItem as $v)
                {

                    $totalPrice += $v->qty * $v->price;
    
                    $requestModule=[];
                    $requestModule['no_transaction'] = $noTransaction;
                    if (isset($v->code_item) && $v->code_item!='' ) {$requestModule['code_item'] = $v->code_item;}
                    if (isset($v->items) && $v->items!='' ) {$requestModule['items'] = $v->items;}
                    if (isset($v->description) && $v->description!='' ) {$requestModule['description'] = $v->description;}
                    if (isset($v->unit) && $v->unit!='' ) {$requestModule['unit'] = $v->unit;}
                    if (isset($v->qty) && $v->qty!='' ) {$requestModule['qty'] = $v->qty;}
                    if (isset($v->price) && $v->price!='' ) {$requestModule['price'] = $v->price;}
                    if (isset($v->sub_total) && $v->sub_total!='' ) {$requestModule['sub_total'] = $v->sub_total;}
                    if (isset($v->expired_date) && $v->expired_date!='' ) {$requestModule['expired_date'] = $v->expired_date;}
                    if (isset($v->note) && $v->note!='' ) {$requestModule['note'] = $v->note;}
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

                // Get Receive Order
                $requestModule=[];
                $requestModule['no_transaction'] = $noTransaction;
                $classDB = new Class_ReceiveOrderDetail();
                $resultClassDB = $classDB->show($requestModule);
            
                // update Receove Order
                if(!$resultClassDB['success'])
                {
                    DB::rollBack();
                    return $resultClassDB;
                }
                $listItemDetail  = $resultClassDB['data'];

                # update Receive Order
                $requestModule=[];
                $requestModule['id'] = $id;
                $requestModule['total'] = $totalPrice;
                $requestModule['detail_item'] = json_encode($listItemDetail);
                $classDB = new Class_ReceiveOrder();
                $resultClassDB = $classDB->update($requestModule);
        
                if(!$resultClassDB['success'])
                {
                    DB::rollBack();
                    return $resultClassDB;
                }
                $result['update_receiveOrder'] = $resultClassDB['data'];
                # end edit with Detail Items
            }

            DB::commit();

            # sent WA if Submit
            if($request['status']=='1' || $request['status']==1)
            {
                // get data master   
                $requestModule=[];
                $requestModule['no_transaction'] = $noTransaction;
                $classModel = new Class_ReceiveOrder();
                $resultClassDB = $classModel->show($requestModule);
                if(!$resultClassDB['success'])
                {
                    return $resultClassDB;
                }
            
                // cek sudah complete atau belum
                $requestClassDB = [];
                $requestClassDB['no_transaction'] = $noTransaction;
                $requestClassDB['status'] = '0'; // belum di approve
                $classDB = new Class_ReceiveOrderHistoryApproval();
                $resultClassDB = $classDB->show($requestClassDB);
                if(!$resultClassDB['success'])
                {
            
                } 
                else
                {
                    $idRoleAccess = $resultClassDB['data'][0]->id_role_access; 
                    $pic = $resultClassDB['data'][0]->pic;
                    $dateTransacion = $resultClassDB['data'][0]->created_at;
                    // sent to WA Message
                    $requestClassDB=[];
                    $requestClassDB['id_role_access'] = '006';
                    $requestClassDB['departemen'] = 'Finance & Accounting';
                    $requestClassDB['grade'] = 'SPV';
                
                    $classApi = new API_Service();
                    $resultClassDB = $classApi->getUsersAccessManagement($requestClassDB);
                
                    if($resultClassDB['success'])
                    {
                        $userAccessManagement = $resultClassDB['data']['get_UserAccessManagement'];
                        $count = 0;
                        foreach($userAccessManagement as $v)
                        {
                            if ($count >= 3) {
                                break; // stop setelah 5 kali loop
                            }
                            $requestClassAPI=[];
                            $requestClassAPI['id_karyawan'] = $v['id_karyawan'];
                            $classApi = new API_Service();
                            $resultClassAPI = $classApi->getDataKaryawan($requestClassAPI);
                            if($resultClassAPI['success'] && $resultClassAPI['data'][0]!=null) 
                            {
                                $statusKaryawan=$resultClassAPI['data'][0]['status'];
                                if($statusKaryawan=='1')
                                {
                                    // sent Message
                                    $name = $resultClassAPI['data'][0]['name'];
                                    $idKaryawan = $resultClassAPI['data'][0]['id_absen'];
                                    $telephone = $resultClassAPI['data'][0]['no_hp'];
                            
                                    $requestWA=[];
                                    $requestWA['type'] = 'receive_order';
                                    $requestWA['name'] = $name;
                                    $requestWA['no_transaction'] = $noTransaction;
                                    $requestWA['date_transaction'] = $dateTransacion;
                                    $requestWA['telephone'] = $telephone;
                                    
                                    $classWhatsapp = new SentMessage();
                                    $resultclassWA = $classWhatsapp->sentWhatsappRequest($requestWA);
                                    $result['status_sentWhatsapp'] = $resultclassWA;
                                }
                            }
                        }
                    }
                }
            }
            # end submit
            
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
                       $result['update_ReceiveOrder'] = 'successfuly update No Transaction : '.$noTransaction.' Status : '. $status;
                    }
                   else
                   {
                       $result['status_acount'] = 'Akun ('.$idKaryawan.') Not Have Access Approval '. $v->pic .' (status : waiting '.$v->pic.')';
                       // return $result;
                   }

                   if($status!='1') // draft =0; reject =9;
                   {
                        $requestClassDB =[];
                        $requestClassDB['id'] = $id;
                        $requestClassDB['status'] =  $status;
                        $classDB = new Class_ReceiveOrder();
                        $resultClassDB = $classDB->update($requestClassDB);
                        if(!$resultClassDB['success'])
                        {
                            DB::rollBack();
                            return $resultClassDB;
                        }
                        $result['update_ReceiveOrder'] = $resultClassDB['data'];

                        DB::commit(); // break for
                        return [
                            'success' => true,
                            'message' => 'Update successfuly',
                            'data'=> $result
                        ];
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
                // cek apakah langsung SR
                $requestSR=[];
                $requestSR['no_transaction'] = $noTransaction;
                $resultFunctionSR = $this->insertPOSR($requestSR);
                if(!$resultModel['success'])
                {
                    DB::rollBack();
                    return $resultModel;
                }
                $result['insert_SR'] = $resultFunctionSR;
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

    private function insertPOSR($request)
    {
        try
        {
            $noTransaction = $request['no_transaction'];
            // Get Receive Order
            $requestModule=[];
            $requestModule['no_transaction'] = $noTransaction;
            $classDB = new Class_ReceiveOrder();
            $resultClassDB = $classDB->show($requestModule);
        
            // update Receove Order
            if(!$resultClassDB['success'])
            {
                DB::rollBack();
                return $resultClassDB;
            }
            
            $isSR = $resultClassDB['data'][0]->is_sr;
            # with SR
            if($isSR=='1')
            {
                $idDepartemen = $resultClassDB['data'][0]->id_departemen;
                $departemen = $resultClassDB['data'][0]->departemen;
                $idSubDepartemen = $resultClassDB['data'][0]->id_sub_departemen;
                $subDepartemen = $resultClassDB['data'][0]->sub_departemen;
                $dateTransaction = $resultClassDB['data'][0]->date_transaction;
                $reff = $resultClassDB['data'][0]->reff;
                $detailItem = $resultClassDB['data'][0]->detail_item;
                $listItemDetail = json_decode($detailItem);
            
                $arrayData = json_decode(json_encode($listItemDetail), true);
                // convert array
                $transformedArray = array_map(function ($item) {
                    return [
                        "code_item" => $item["code_item"],
                        "items" => $item["items"],
                        "description" => $item["items"], // Menggunakan nilai `items`
                        "unit" => $item["unit"],
                        "qty" => $item["qty"],
                        "price" => $item["price"],
                        "sub_total" => $item["sub_total"],
                        "expired_date" => $item["expired_date"],
                        "note" => $item["note"] ?? "" // Ubah null jadi string kosong
                    ];
                }, $arrayData);
            
                $requestModel =[];
                $requestModel['id_departemen']  = $idDepartemen;
                $requestModel['departemen']  = $departemen;
                $requestModel['id_sub_departemen']  = $idSubDepartemen;
                $requestModel['sub_departemen']  = $subDepartemen;
                $requestModel['date_transaction']  = $dateTransaction;
                $requestModel['detail_items']  = json_encode($transformedArray);
                $requestModel['status']  = '11';
                $requestModel['note']  = $noTransaction;
                $requestModel['reff']  = $reff;
                
                $resultModel=[];
                $classModel = new StoreRequest();
                $resultModel = $classModel->insertStoreRequest($requestModel);
                if(!$resultModel['success'])
                {
                    return [
                        'success' => false,
                        'message' => 'Gagal Menambahkan Store Request',
                        'data' => $resultModel
                    ];
                }

                $noTransactionSR = $resultModel['data']['insert_storeRequest']['no_transaction'];
                foreach($listItemDetail as $v)
                {
                    $requestModel =[];
                    if (isset($v->item_group) && $v->item_group!='' ) {$requestModel['item_group'] = $v->item_group;}
                    if (isset($v->brand) && $v->brand!='' ) {$requestModel['brand'] = $v->brand;}
                    if (isset($v->code_item) && $v->code_item!='' ) {$requestModel['code'] = $v->code_item;}
                    if (isset($v->items) && $v->items!='' ) {$requestModel['items'] = $v->items;}
                    if (isset($v->description) && $v->description!='' ) {$requestModel['description'] = $v->description;}
                    if (isset($v->qty) && $v->qty!='' ) {$requestModel['qty'] = $v->qty;}
                    $requestModel['type_transaction'] = '2';
                    $requestModel['in'] = '0';
                    if (isset($v->out) && $v->out!='' ) {$requestModel['out'] = $v->out;}
                    $requestModel['no_transaction'] = $noTransactionSR;
                    if (isset($v->date) && $v->date!='' ) {$requestModel['date'] = $v->date;}
                    if (isset($v->price) && $v->price!='' ) {$requestModel['price'] = $v->price;}
                    if (isset($v->sub_total) && $v->sub_total!='' ) {$requestModel['total_price'] = $v->sub_total;}
                    $requestModel['origin_of_goods'] = 'Store Request';
                    if (isset($v->expired_date) && $v->expired_date!='' ) {$requestModel['exp_date'] = $v->expired_date;}
                    if (isset($v->remark) && $v->remark!='' ) {$requestModel['remark'] = $v->remark;}
                    if (isset($v->years) && $v->years!='' ) {$requestModel['years']= $v->years;}
                
                    $resultModel=[];
                    $classModel = new StockTransaction();
                    $resultModel = $classModel->insertStockTransaction($requestModel);
                    if(!$resultModel['success'])
                    {
                        return [
                            'success' => false,
                            'message' => 'Gagal Menambahkan Stock Transaction',
                            'data' => $resultModel
                        ];
                    }
                }
                return [
                    'success' => true,
                    'message' => 'Success Insert Stock Transaction'
                ];
            }
            else
            {
                return [
                    'success' => true,
                    'message' => 'Receive Order Not Set SR'
                ];
            }
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

    public function updateReceiveOrderItemDetail($request) // Custom
    {
        try
        {
            DB::beginTransaction();
            // Validate the incoming request data
            $request->validate([
                'no_transaction'    => 'required|string',             // Required
                'code_item'              => 'required|string',             // Required
            ]);

            $result=[];
            $requestModule=[];
            $noTransaction=''; $codeItem='';
            if (isset($request['no_transaction']) && $request['no_transaction']!='' ) {$noTransaction = $request['no_transaction'];}
            if (isset($request['code_item']) && $request['code_item']!='' ) {$codeItem = $request['code_item'];}
            if (isset($request['price']) && $request['price']!='' ) {$updateData['price'] = $request['price'];}
            if (isset($request['qty']) && $request['qty']!='' ) {$updateData['qty'] = $request['qty'];}
            if (isset($request['sub_total']) && $request['sub_total']!='' ) {$updateData['sub_total'] = $request['sub_total'];}
            if (isset($request['expired_date']) && $request['expired_date']!='' ) {$updateData['expired_date'] = $request['expired_date'];}
            if (isset($request['note']) && $request['note']!='' ) {$updateData['note'] = $request['note'];}
            if (isset($request['reff']) && $request['reff']!='' ) {$reff = $request['reff'];}

            // update Receive Order Detail
            DB::table('receive_order_detail')
            ->where('no_transaction',$noTransaction)
            ->where('code_item',$codeItem)
            ->update($updateData);

            // Get Receive Order
            $requestModule=[];
            $requestModule['no_transaction'] = $noTransaction;
            $classDB = new Class_ReceiveOrderDetail();
            $resultClassDB = $classDB->show($requestModule);
          
            // update Receove Order
            if(!$resultClassDB['success'])
            {
                DB::rollBack();
                return $resultClassDB;
            }
            $listItemDetail  = $resultClassDB['data'];
            
            $totalPrice=0;
            foreach($listItemDetail as $v)
            {
                $totalPrice+=$v->sub_total;
            }
      
            // Update Receive Order
            DB::table('receive_order')
            ->where('no_transaction',$noTransaction)
            ->update([
                'detail_item' => json_encode($listItemDetail),
                'total' => $totalPrice,
            ]);

            DB::commit();
            return [
                'success' => true,
                'message' => 'Update successfuly'
            ];
        } catch (\Exception $ex) {
        
            # Insert Log Error
            $requestModule=[];
            $requestModule['reff'] = '-';
            $requestModule['service'] = 'Model';
            $requestModule['class'] = 'ReceiveOrder';
            $requestModule['function'] = 'updateReceiveOrderItemDetail';
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