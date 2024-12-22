<?php

namespace App\Http\Controllers\Model\Stock;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use App\Http\Controllers\Log\LogError;
use App\Http\Controllers\Generate\GenerateID;

use App\Http\Controllers\Class_DB\Class_Stock;
use App\Http\Controllers\Class_DB\Class_StockTransaction;
use App\Http\Controllers\Class_DB\Class_StockAdjustment;
use App\Http\Controllers\Class_DB\Class_StockAdjustmentDetail;
use App\Http\Controllers\Class_DB\Class_StockExpired;
use App\Http\Controllers\Class_DB\Class_StockAdjustmentHistoryApproval;;
use App\Http\Controllers\Model\RoleAccess\RoleAccessManagement;
use App\Http\Controllers\Model\Stock\StockLog;
use App\Http\Controllers\Model\Stock\Stock;

class StockAdjustment extends Controller
{
    public function getStockAdjustment($request)
    {
        try
        {
            $requestModule=[];
            if (isset($request['id']) && $request['id']!='' ) {$requestModule['id'] = $request['id'];}
            if (isset($request['no_adjustment']) && $request['no_adjustment']!='' ) {$requestModule['no_adjustment'] = $request['no_adjustment'];}
            if (isset($request['code']) && $request['code']!='' ) {$requestModule['code'] = $request['code'];}
            if (isset($request['item_group']) && $request['item_group']!='' ) {$requestModule['item_group'] = $request['item_group'];}
            if (isset($request['brand']) && $request['brand']!='' ) {$requestModule['brand'] = $request['brand'];}
            if (isset($request['code']) && $request['code']!='' ) {$requestModule['code'] = $request['code'];}
            if (isset($request['items']) && $request['items']!='' ) {$requestModule['items'] = $request['items'];}
            if (isset($request['description']) && $request['description']!='' ) {$requestModule['description'] = $request['description'];}
            if (isset($request['unit']) && $request['unit']!='' ) {$requestModule['unit'] = $request['unit'];}
            if (isset($request['have_exp']) && $request['have_exp']!='' ) {$requestModule['have_exp'] = $request['have_exp'];}

            $result=[];
            $classModel = new Class_StockAdjustment();
            $resultClassDB = $classModel->show($requestModule);
            $result = $resultClassDB['data'];

            if (isset($request['no_adjustment']) && $request['no_adjustment']!='' )
            {
                $result=[];
                $result['get_adjustment'] = $resultClassDB['data'];

                $classModel = new Class_StockAdjustmentDetail();
                $resultClassDB = $classModel->show($requestModule);
                $result['get_detail'] = $resultClassDB['data'];
    
                $classModel = new Class_StockAdjustmentHistoryApproval();
                $resultClassDB = $classModel->show($requestModule);
                $result['get_history'] = $resultClassDB['data'];
            }

            return $result;
        } catch (\Exception $ex) {
            # Insert Log Error
            $requestModule=[];
            $requestModule['reff'] = '-';
            $requestModule['service'] = 'Model';
            $requestModule['class'] = 'StockAdjustment';
            $requestModule['function'] = 'getStockAdjustment';
            $requestModule['message'] = $ex->getMessage();
            $requestModule['note'] = '-';
            $classModel = new LogError();
            $result = $classModel->insertLogError($requestModule);
            # End Log Error
            return $ex;
        }
    }

    public function insertAdjustment($request)
    {
        try
        {
            DB::beginTransaction();
            // Validate the incoming request data
            $request->validate([
                'reff'            => 'required|string',      // Required
            ]);

            // generate ID Adjustment
            $requestClassID=[];
            $classID = new GenerateID();
            $noAdjustment = $classID->getIDAdjustment($requestClassID);
        
            // declare variable 
            $date=carbon::now()->format('Y-m-d'); 
            $totalItem=0; $totalQty=0; $totalPrice=0; $status='0'; $years=carbon::now()->format('Y'); $reff='-'; $detailItem='';
            $result=[];
            # declare variable from request
            if (isset($request['date']) && $request['date']!='' ) {$date = $request['date'];}
            if (isset($request['reff']) && $request['reff']!='' ) {$reff = $request['reff'];}
            if (isset($request['detail_item']) && $request['detail_item']!='' ) {$detailItem = $request['detail_item'];}
            # end declare variable request

            # declare variable request adjustment
            $requestModuleAdjustment=[];
            $requestModuleAdjustment['no_adjustment'] = $noAdjustment;
            $requestModuleAdjustment['date'] = $date;
            $requestModuleAdjustment['total_item'] = $totalItem;
            $requestModuleAdjustment['total_qty'] = $totalQty;
            $requestModuleAdjustment['total_price'] = $totalPrice;
            $requestModuleAdjustment['status'] = $status;
            $requestModuleAdjustment['years'] = $years;
            $requestModuleAdjustment['reff'] = $reff;
            # end declare variable request adjustment

            $classDB = new Class_StockAdjustment();
            $resultClassDB = $classDB->insert($requestModuleAdjustment);
            if(!$resultClassDB['success'])
            {
                DB::rollBack();
                return $resultClassDB;
            }
            $result['insert_stockAdjustment'] = $resultClassDB['data'];

            # declare variable request detail
            if($detailItem !='')
            {
                $idClassAdjustment = $resultClassDB['data']->id;

                $requestPrivateFunction = [];
                $requestPrivateFunction['id'] = $idClassAdjustment;
                $requestPrivateFunction['no_adjustment'] = $noAdjustment;
                $requestPrivateFunction['reff'] = $reff;
                $requestPrivateFunction['detail_item'] = $detailItem;
                $resultPrivateFunction = $this->updateAdjustmentDetail($requestPrivateFunction);
   
                if(!$resultPrivateFunction['success'])
                {
                    DB::rollBack();
                    return $resultPrivateFunction;
                }
                $result['insert_stockAdjustmentDetail'] = $resultPrivateFunction['data'];
            }

            # get role access
            $requestRoleAccess =[];
            $requestRoleAccess['id_access_management'] = 'AA-001'; // approval_adjustment
            $classRoleAccess = new RoleAccessManagement();
            $resultRoleAccess = $classRoleAccess->getRoleAccess($requestRoleAccess);
          
            foreach($resultRoleAccess['data']['get_roleAccessDetail'] as $v)
            {
                $requestClassDB=[];
                $requestClassDB['no_adjustment']=$noAdjustment;
                $requestClassDB['ord']=$v->ord;
                $requestClassDB['id_role_access']=$v->id_role_access;
                $requestClassDB['pic']=$v->pic;
                $requestClassDB['grade']=$v->grade;
                $requestClassDB['departemen']=$v->departemen;

                $classDB = new Class_StockAdjustmentHistoryApproval();
                $result['insert_classAdjustmentHistoryApproval'] = $classDB->insert($requestClassDB);    
            }
            # declare variable history approval

            // insert History
            $requestHistory = [];
            $requestHistory['id_item'] = $noAdjustment;
            $requestHistory['reff'] = $reff;
            $requestHistory['activity'] = 'Insert StockAdjustment';
            $requestHistory['detail_act'] = $detailItem;
            $history = new StockLog();
            $result['insert_history'] = $history->insertHistoryStock($requestHistory);

            DB::commit();
            return $result;
        } catch (\Exception $ex) {
            DB::rollBack();
            # Insert Log Error
            $requestModule=[];
            $requestModule['reff'] = '-';
            $requestModule['service'] = 'Model';
            $requestModule['class'] = 'StockAdjustment';
            $requestModule['function'] = 'insertAdjustment';
            $requestModule['message'] = $ex->getMessage();
            $requestModule['note'] = '-';
            $classModel = new LogError();
            $result = $classModel->insertLogError($requestModule);
            # End Log Error
            return $ex;
        }
    }

    public function updateAdjustment($request)
    {
        try
        {
            DB::beginTransaction();
            // declare variable 
            $noAdjustment=''; $date=''; $status=''; $idKaryawan=''; $reff=''; $note=''; $detailItem='';
            $result=[];

            # declare variable from request
            if (isset($request['no_adjustment']) && $request['no_adjustment']!='' ) {$noAdjustment = $request['no_adjustment'];}
            if (isset($request['date']) && $request['date']!='' ) {$date = $request['date'];}
            if (isset($request['status']) && $request['status']!='' ) {$status = $request['status'];}  
            if (isset($request['id_karyawan']) && $request['id_karyawan']!='' ) {$idKaryawan = $request['id_karyawan'];}    
            if (isset($request['reff']) && $request['reff']!='' ) {$reff = $request['reff'];}    
            if (isset($request['note']) && $request['note']!='' ) {$note = $request['note'];}    
            if (isset($request['detail_item']) && $request['detail_item']!='' ) {$detailItem = $request['detail_item'];}
            # end declare variable request

            // get data Adjustment
            $requestClassDB = [];
            $requestClassDB['no_adjustment'] = $noAdjustment;
            $classDB = new Class_StockAdjustment();
            $resultClassDB = $classDB->show($requestClassDB);
            if(!$resultClassDB['success'])
            {
                DB::rollBack();
                return $resultClassDB;
            }
            $idClassAdjustment = $resultClassDB['data'][0]->id;

            // edit detail
            if($detailItem!='')
            {
                $request['id'] = $idClassAdjustment;
                $result['update_StockAdjustmentDetail'] = $this->updateAdjustmentDetail($request);
            }

            if($status=='')
            {
                DB::commit();
                return $result;
            }
        
            // cek adjustment history approve
            $requestClassDB = [];
            $requestClassDB['no_adjustment'] = $noAdjustment;
            $requestClassDB['status'] = '0'; // belum di approve
            $classDB = new Class_StockAdjustmentHistoryApproval();
            $resultClassDB = $classDB->show($requestClassDB);
            if(!$resultClassDB['success'])
            {
                // DB::rollBack();
                return $resultClassDB;
            }
            else
            {
                $statusApprovalAdjustment = 0;
                foreach($resultClassDB['data'] as $v) // jika user mempunyai double role maka akan terupdate semua
                {
                    $idRoleAccess = $v->id_role_access;
                    $pic = $v->pic;
                    $statusApprovalAdjustment = $v->ord;
                    if($status=='9') // reject
                    {
                        $statusApprovalAdjustment='9';
                    }
                    if($status=='0')
                    {
                        DB::table('stock_adjustment_history_approval')
                        ->where('no_adjustment',$noAdjustment)
                        ->update([
                            'status' => $status,
                        ]);
                        $statusApprovalAdjustment='0'; // back to draft
                    }
               
                    // cek user access management 
                    $roleUser_ = DB::table('users_access_management')
                    ->where('id_karyawan',$idKaryawan)
                    ->where('id_role_access', $idRoleAccess);
                    if($roleUser_->exists())
                    {
                        $roleUser = $roleUser_->first();
                    
                        DB::table('stock_adjustment_history_approval')
                        ->where('no_adjustment',$noAdjustment)
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
                        $result['update_approveHistory'] = 'successfuly update No Adjustment : '.$noAdjustment.' ID Role : '. $idRoleAccess;

                        $requestClassDB =[];
                        $requestClassDB['id'] = $idClassAdjustment;
                        $requestClassDB['status'] =  $statusApprovalAdjustment; // 11= complete full acc;
                        $classDB = new Class_StockAdjustment();
                        $resultClassDB = $classDB->update($requestClassDB);
                        if(!$resultClassDB['success'])
                        {
                            DB::rollBack();
                            return $resultClassDB;
                        }
                        $result['update_stockAdjustment'] = 'successfuly update No Adjustment : '.$noAdjustment.' Status : '. $statusApprovalAdjustment;

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
            $requestClassDB['no_adjustment'] = $noAdjustment;
            $requestClassDB['status'] = '0'; // belum di approve
            $classDB = new Class_StockAdjustmentHistoryApproval();
            $resultClassDB = $classDB->show($requestClassDB);
            if(!$resultClassDB['success']) // complete
            {
                $requestClassDB =[];
                $requestClassDB['id'] = $idClassAdjustment;
                $requestClassDB['status'] =  '11'; // complete full acc
                $classDB = new Class_StockAdjustment();
                $resultClassDB = $classDB->update($requestClassDB);
                if(!$resultClassDB['success'])
                {
                    DB::rollBack();
                    return $resultClassDB;
                }
                $result['update_stockAdjustment'] = $resultClassDB['data'];

                // update stock by adjustment
                $requestClassDB=[];
                $requestClassDB['no_adjustment'] = $noAdjustment;
                $classDB = new Class_StockAdjustmentDetail();
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
                    $requestClassDB['code'] = $v->code;
                    $classDB = new Class_Stock();
                    $resultClassDB = $classDB->show($requestClassDB);
                    if(!$resultClassDB['success'])
                    {
                        DB::rollBack();
                        return 'Code Item '. $v->code.' Tidak Ditemukan';
                    }
                    else
                    {   
                        $idItem = $resultClassDB['data'][0]->id;
                        $initialStock=0;
                        $initialStock = $resultClassDB['data'][0]->initial_stock;
                  
                        // insert stock transaction
                        $requestClassDB = [];
                        $requestClassDB['id_item'] = $idItem;
                        $requestClassDB['item_group'] = $v->item_group;
                        $requestClassDB['brand'] =$v->brand;
                        $requestClassDB['code'] = $v->code;
                        $requestClassDB['items'] = $v->items;
                        $requestClassDB['description'] = $v->description;
                        $requestClassDB['type_transaction'] = $v->type_transaction;
                        if($v->type_transaction=='1') // in
                        {
                            $requestClassDB['in'] = $v->qty;
                        }
                        if($v->type_transaction=='2') // out
                        {
                            $requestClassDB['out'] = $v->qty;
                        }
                        $requestClassDB['no_transaction'] = $v->no_adjustment;
                        $requestClassDB['qty'] = $v->qty;
                        $requestClassDB['origin_of_goods'] = 'Adjustment';
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
                        $requestClassModel['code'] = $v->code;
                        $classModel = new Stock();
                        $resultModel = $classModel->updateStockFromTransaction($requestClassModel);
                      
                        if(!$resultModel['success'])
                        {
                            DB::rollBack();
                            return $resultModel;
                        }
                        $result['update_stock'] = $resultModel['data'];

                        # insert expired
                        $expDate='';
                        if ($v->exp_date!='' || $v->exp_date!=null) {$expDate = $v->exp_date;}
                        if($expDate!='')
                        {
                            $requestClassDB=[];
                            $requestClassDB['no_transaction'] = $v->no_adjustment;
                            $requestClassDB['item_group'] = $v->item_group;
                            $requestClassDB['code'] = $v->code;
                            $requestClassDB['item'] = $v->items;
                            $requestClassDB['unit'] = $v->unit;
                            $requestClassDB['stock'] = '0';
                            $requestClassDB['qty'] = $v->qty;
                            $requestClassDB['date_expired'] = $expDate;
                            $requestClassDB['status'] = '1';
                            $requestClassDB['years'] = Carbon::now()->format('Y');

                            $classDB = new Class_StockExpired();
                            $resultClassDB = $classDB->insert($requestClassDB);
                            if(!$resultClassDB['success'])
                            {
                                DB::rollBack();
                                return $resultClassDB;
                            }
                            $result['insert_classStockExpired'] = $resultClassDB['data'];
                        }
                    }
                }
             
            }
            else
            {
                $result['status_approval'] = 'Approval Adjustment not Completed';
            }
        
            DB::commit();
            return $result;
        } catch (\Exception $ex) {
            DB::rollBack();
            # Insert Log Error
            $requestModule=[];
            $requestModule['reff'] = '-';
            $requestModule['service'] = 'Model';
            $requestModule['class'] = 'StockAdjustment';
            $requestModule['function'] = 'updateAdjustment';
            $requestModule['message'] = $ex->getMessage();
            $requestModule['note'] = '-';
            $classModel = new LogError();
            $result = $classModel->insertLogError($requestModule);
            # End Log Error
            return $ex;
        }
    }

    // private function ------------------------------------
    private function updateAdjustmentDetail($request)
    {
        try
        {
            $id = $request['id'];
            $noAdjustment = $request['no_adjustment'];
            $reff = $request['reff'];
            $detailItem = $request['detail_item'];
            $jsonDecodeDetailItem = json_decode($detailItem);
         
            $price=0; $expDate=''; $remark='-';
            $totalQty =0; $totalPrice=0; $totalItem=0;
            
            // delete from table stock_adjustment_detail
            DB::table('stock_adjustment_detail')
            ->where('no_adjustment',$noAdjustment)
            ->delete();
           
            foreach($jsonDecodeDetailItem as $v)
            {
                
                $totalItem++;
                $totalQty = $totalQty + $v->qty;
                $totalPrice = $totalPrice + $v->price;
                
                $requestModuleAdjustmentDetail=[];
                $requestModuleAdjustmentDetail['no_adjustment'] = $noAdjustment;
                $requestModuleAdjustmentDetail['item_group'] = $v->item_group;
                $requestModuleAdjustmentDetail['brand'] =  $v->brand;
                $requestModuleAdjustmentDetail['code'] = $v->code;
                $requestModuleAdjustmentDetail['items'] =  $v->items;
                $requestModuleAdjustmentDetail['description'] = $v->description;
                $requestModuleAdjustmentDetail['type_transaction'] = $v->type_transaction;
                $requestModuleAdjustmentDetail['qty'] = $v->qty;
                $requestModuleAdjustmentDetail['unit'] = $v->unit;
                $requestModuleAdjustmentDetail['price'] = $v->price;
                $requestModuleAdjustmentDetail['exp_date'] = $v->exp_date;
                $requestModuleAdjustmentDetail['remark'] = $v->remark;
                $requestModuleAdjustmentDetail['years'] = Carbon::now()->format('Y');
                $requestModuleAdjustmentDetail['reff'] = $reff;
            
                # end declare variable request detail
                $classDB = new Class_StockAdjustmentDetail();
                $resultClassDB = $classDB->insert($requestModuleAdjustmentDetail);    
             
                if(!$resultClassDB['success'])
                {
                    DB::rollBack();
                    return $resultClassDB;
                }
                $result['insert_classAdjustmentDetail'] = $resultClassDB['data'];
            }
    
            // update master adjustment 
            $requestModuleAdjustment=[];
            $requestModuleAdjustment['id'] = $id;
            $requestModuleAdjustment['total_item'] = $totalItem;
            $requestModuleAdjustment['total_qty'] = $totalQty;
            $requestModuleAdjustment['total_price'] = $totalPrice;
            $requestModuleAdjustment['detail_adjustment'] = $detailItem;
            $classModel = new Class_StockAdjustment();
            $result['update_classAdjustment'] = $classModel->update($requestModuleAdjustment);

            return [
                'success' => true,
                'message' => 'Update Detail Stock Adjustment successful',
                'data' => $result
            ];
        } catch (\Exception $ex) {
         
            # Insert Log Error
            $requestModule=[];
            $requestModule['reff'] = '-';
            $requestModule['service'] = 'Model';
            $requestModule['class'] = 'StockAdjustment';
            $requestModule['function'] = 'updateAdjustmentDetail';
            $requestModule['message'] = $ex->getMessage();
            $requestModule['note'] = '-';
            $classModel = new LogError();
            $result = $classModel->insertLogError($requestModule);
            # End Log Error
            return $ex;
        }
    }
}
