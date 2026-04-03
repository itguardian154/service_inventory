<?php

namespace App\Http\Controllers\Model\StoreRequest;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Log\LogError;
use App\Http\Controllers\Generate\GenerateID;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Whatsapp\SentMessage;
use App\Http\Controllers\API\API_Service;

use App\Http\Controllers\Model\Stock\Stock;
use App\Http\Controllers\Model\Stock\StockTransaction;

use App\Http\Controllers\Class_DB\Class_Stock;
use App\Http\Controllers\Class_DB\Class_StockTransaction;
use App\Http\Controllers\Class_DB\Class_UsersAccessManagement;

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

                $classDB = new Class_StoreRequestDetail();
                $resultClassDB = $classDB->show($request);
                $result['get_StoreRequestDetail'] = $resultClassDB['data'];

                $classDB = new Class_StoreRequestHistoryApproval();
                $resultClassDB = $classDB->show($request);
                $result['get_StoreRequetHistoryApproval'] = $resultClassDB['data'];
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
         
            $noTransaction=''; $idDepartemen='-'; $departemen='-'; $idSubDepartemen='-'; $subDepartemen='-';
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
        
            # generate ID Store Request
            if($noTransaction=='')
            {
                $requestClassID=[];
                $classID = new GenerateID();
                $noTransaction = $classID->getIDStoreRequest($requestClassID);  
            }
            # end
        
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
            if($detailItems!='')
            {
                $jsonDetailItem = json_decode($detailItems);
                $totalItems=0;
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

                // insert Stock Transaction 
                $requestStockTransaction=[];
                $requestStockTransaction['no_transaction'] = $noTransaction;
                $result['insert_stockTransaction'] =  $this->insertStockTransaction($requestStockTransaction);
            }
          
            # insert history approval
            $requestRoleAccess =[];
            $requestRoleAccess['id_access_management'] = 'SR-001'; // approval_adjustment
            $classRoleAccess = new RoleAccessManagement();
            $resultRoleAccess = $classRoleAccess->getRoleAccess($requestRoleAccess);
          
            $_first=true;
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
                    list($name, $departemen, $grade) = explode('-', $reff);
                
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
                    $requestModule['id_karyawan']=$idKaryawan;
                    $requestModule['name']=$name;
                    $requestModule['grade']=$grade;
                    $requestModule['departemen']=$departemen;
                    $requestModule['status']='1';
                    $requestModule['date']=Carbon::now()->format('Y-m-d H:i:s');
                    $requestModule['years']=Carbon::now()->format('Y');
                  
                    $_first=false;
                }
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

    public function updateStoreRequest($request)
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
            if (isset($request['detail_items']) && $request['detail_items']!='' ) {$detailItem = $request['detail_items'];}
        
            $result=[];
            $requestModule=[];
            if (isset($request['id']) && $request['id']!='' ) {$requestModule['id'] = $request['id'];}
            if (isset($request['id_departemen']) && $request['id_departemen']!='' ) {$requestModule['id_departemen'] = $request['id_departemen'];}
            if (isset($request['departemen']) && $request['departemen']!='' ) {$requestModule['departemen'] = $request['departemen'];}
            if (isset($request['id_sub_departemen']) && $request['id_sub_departemen']!='' ) {$requestModule['id_sub_departemen'] = $request['id_sub_departemen'];}
            if (isset($request['sub_departemen']) && $request['sub_departemen']!='' ) {$requestModule['sub_departemen'] = $request['sub_departemen'];}
            if (isset($request['date_transaction']) && $request['date_transaction']!='' ) {$requestModule['date_transaction'] = $request['date_transaction'];}
            if (isset($request['detail_items']) && $request['detail_items']!='' ) {$requestModule['detail_items'] = $request['detail_items'];}
            if (isset($request['total_items']) && $request['total_items']!='' ) {$requestModule['total_items'] = $request['total_items'];}
            if (isset($request['status']) && $request['status']!='' ) {$requestModule['status'] = $request['status'];}
            if (isset($request['note']) && $request['note']!='' ) {$requestModule['note'] = $request['note'];}
            if (isset($request['years']) && $request['years']!='' ) {$requestModule['years'] = $request['years'];}
            if (isset($request['reff']) && $request['reff']!='' ) {$requestModule['reff'] = $request['reff'];}

            $classModel = new Class_StoreRequest();
            $resultClassDB = $classModel->update($requestModule);
            if(!$resultClassDB['success'])
            {
                DB::rollBack();
                return $resultClassDB;
            }
            $result['update_storeRequest'] = $resultClassDB['data'];
          
            # edit with Detail Items
        
            if($detailItem!='')
            {
                // delete Stock Transaction 
                $requestStockTransaction=[];
                $requestStockTransaction['no_transaction'] = $noTransaction;
                $result['insert_stockTransaction'] =  $this->deleteStockTransaction($requestStockTransaction);

                // delete from table store_request_detail
                DB::table('store_request_detail')
                ->where('no_transaction',$noTransaction)
                ->delete();
                $jsonDetailItem = json_decode($detailItem);
                $totalItems=0;

                foreach($jsonDetailItem as $v)
                {
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
                    $classDB = new Class_StoreRequestDetail();
                    $resultClassDB = $classDB->insert($requestModule);    
              
                    if(!$resultClassDB['success'])
                    {
                        DB::rollBack();
                        return $resultClassDB;
                    }
                    $result['insert_StoreRequestDetail'] = $resultClassDB['data'];
              
                    $totalItems++;
                }
                # update Store Request
                $requestModule=[];
                $requestModule['id'] = $id;
                $requestModule['total_items'] = $totalItems;
            
                $classDB = new Class_StoreRequest();
                $resultClassDB = $classDB->update($requestModule);
              
                if(!$resultClassDB['success'])
                {
                    DB::rollBack();
                    return $resultClassDB;
                }
                $result['update_storeRequest'] = $resultClassDB['data'];
                # end update Store Request

                // insert Stock Transaction 
                $requestStockTransaction=[];
                $requestStockTransaction['no_transaction'] = $noTransaction;
                $result['insert_stockTransaction'] =  $this->insertStockTransaction($requestStockTransaction);
            }  
            # end edit with Detail Items
         
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
            $requestModule['class'] = 'StoreRequest';
            $requestModule['function'] = 'updateStoreRequest';
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

    public function updateApprovalStoreRequest($request)
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
           $classDB = new Class_StoreRequestHistoryApproval();
           $resultClassDB = $classDB->show($requestClassDB);
    
           if(!$resultClassDB['success'])
           {
                $requestClassDB = [];
                $requestClassDB['no_transanction'] = $noTransaction;
                $requestClassDB['status'] = '1'; // sudah di approve
                $classDB = new Class_StoreRequestHistoryApproval();
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
                      
                       DB::table('store_request_history_approval')
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
                       $classDB = new Class_StoreRequest();
                       $resultClassDB = $classDB->update($requestClassDB);
                       if(!$resultClassDB['success'])
                       {
                           DB::rollBack();
                           return $resultClassDB;
                       }
                       $result['update_StoreRequest'] = 'successfuly update No Transaction : '.$noTransaction.' Status : '. $statusApproval;

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
                        $classDB = new Class_StoreRequest();
                        $resultClassDB = $classDB->update($requestClassDB);
                        if(!$resultClassDB['success'])
                        {
                            DB::rollBack();
                            return $resultClassDB;
                        }
                        $result['update_StoreRequest'] = $resultClassDB['data'];

                        DB::table('store_request_history_approval')
                        ->where('no_transaction','=',$noTransaction)
                        ->where('ord','<>','1')
                        ->update([
                            'id_karyawan' => '',
                            'name' => '',
                            'departemen' => '',
                            'grade' => '',
                            'status' => '0',
                        ]);

                         DB::commit(); // break for
                         return [
                             'success' => true,
                             'message' => 'Update successfuly',
                             'data'=> $result
                         ];
                    }
                    if($status=='9')
                    {
                        // delete transaction
                        $requestModel = [];
                        $requestModel['no_transaction'] = $noTransaction;
                        $result['delete_StockTransaction'] = $this->deleteStockTransaction($requestModel);

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
           $classDB = new Class_StoreRequestHistoryApproval();
           $resultClassDB = $classDB->show($requestClassDB);
          
           if(!$resultClassDB['success']) // complete
           {
               $requestClassDB =[];
               $requestClassDB['id'] = $id;
               $requestClassDB['status'] =  '11'; // complete full acc
               $classDB = new Class_StoreRequest();
               $resultClassDB = $classDB->update($requestClassDB);
             
               if(!$resultClassDB['success'])
               {
                   DB::rollBack();
                   return $resultClassDB;
               }
               $result['update_StoreRequest'] = $resultClassDB['data'];
           }
           else
           {
               $result['status_approval'] = 'Approval Receive Order not Completed (waiting '. $pic.')';
           
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
                                $telephone = '085941304991'; // hardcode untuk testing  
                                $requestWA=[];
                                $requestWA['type'] = 'store_request';
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
            $requestModule['class'] = 'StoreRequest';
            $requestModule['function'] = 'updateApprovalStoreRequest';
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

    private function insertStockTransaction($request) // custom
    {
        try
        {
            $noTransaction = $request['no_transaction'];
            // get detail sr
            $requestClassModel =[];
            $requestClassModel['no_transaction'] = $noTransaction;
            $classDB = new Class_StoreRequestDetail();
            $resultClassDB = $classDB->show($requestClassModel);
            if(!$resultClassDB['success'])
            {
                return $resultClassDB;
            }
            $listItem = $resultClassDB['data'];

            $requestStockTransaction=[];
            $requestStockTransaction['no_transaction'] = $noTransaction;
            $result['delete_stockTransaction'] = $this->deleteStockTransaction($requestStockTransaction);
            
            foreach($listItem as $v)
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
                if (isset($v->no_transaction) && $v->no_transaction!='' ) {$requestModel['no_transaction'] = $v->no_transaction;}
                if (isset($v->date) && $v->date!='' ) {$requestModel['date'] = $v->date;}
                if (isset($v->price) && $v->price!='' ) {$requestModel['price'] = $v->price;}
                if (isset($v->sub_total) && $v->sub_total!='' ) {$requestModel['total_price'] = $v->sub_total;}
                $requestModel['origin_of_goods'] = 'Store Request';
                if (isset($v->expired_date) && $v->expired_date!='' ) {$requestModel['exp_date'] = $v->expired_date;}
                if (isset($v->remark) && $v->remark!='' ) {$requestModel['remark']= $v->remark;}
                if (isset($v->years) && $v->years!='' ) {$requestModel['years']= $v->years;}
              
                $classModel = new StockTransaction();
                $result['insert_StockTransaction'] = $classModel->insertStockTransaction($requestModel);
            }

            return [
                'success' => true,
                'message' => 'insert successfuly',
                'data'=> $result
            ];

        } catch (\Exception $ex) {
            # Insert Log Error
            $requestModule=[];
            $requestModule['reff'] = '-';
            $requestModule['service'] = 'Model';
            $requestModule['class'] = 'StoreRequest';
            $requestModule['function'] = 'insertStockTransaction';
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

    private function deleteStockTransaction($request)
    {
        $noTransaction = $request['no_transaction'];
        try
        {
            $requestClassDB = [];
            $requestClassDB['no_transaction'] = $noTransaction;
            $classDB = new Class_StockTransaction();
            $resultClassDB = $classDB->show($requestClassDB);
            if(!$resultClassDB['success'])
            {
                return $resultClassDB;
            }
            
            $listItem = $resultClassDB['data'];
            DB::table('stock_transaction')
            ->where('no_transaction',$noTransaction)
            ->where('origin_of_goods','Store Request')
            ->delete();

            foreach($listItem as $v)
            {
                $code = $v->code;
                // --GET ID Class Stock
                $requestModule=[];
                $requestModule['code'] = $code; 
                $classDB = new Class_Stock();
                $resultClassDB = $classDB->show($requestModule);
                if(!$resultClassDB['success'])
                {
                    return $resultClassDB;
                }
                $idStockItem = $resultClassDB['data'][0]->id;
            
                $requestModule=[];
                $requestModule['id_item'] = $idStockItem;
                $requestModule['code'] = $code; 
                $classDB = new Stock();
                $resultClassDB = $classDB->updateStockFromTransaction($requestModule);
                if(!$resultClassDB['success'])
                {
                    return $resultClassDB;
                }
                $result['update_stock'] = $resultClassDB['data'];
            }
            
            return [
                'success' => true,
                'message' => 'delete successfuly',
            ];
       } catch (\Exception $ex) {
        # Insert Log Error
        $requestModule=[];
        $requestModule['reff'] = '-';
        $requestModule['service'] = 'Model';
        $requestModule['class'] = 'StoreRequest';
        $requestModule['function'] = 'delteStockTransaction';
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
