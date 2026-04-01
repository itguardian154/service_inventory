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
use App\Http\Controllers\Class_DB\Class_StockGoodsReturn;
use App\Http\Controllers\Class_DB\Class_StockGoodsReturnDetail;
use App\Http\Controllers\Class_DB\Class_StockGoodsReturnHistoryApproval;;
use App\Http\Controllers\Model\RoleAccess\RoleAccessManagement;
use App\Http\Controllers\Model\Stock\StockLog;
use App\Http\Controllers\Model\Stock\Stock;

class StockGoodsReturn extends Controller
{
    public function getGoodsReturn($request)
    {
        try
        {
            $requestModule=[];
            if (isset($request['id']) && $request['id']!='' ) {$requestModule['id'] = $request['id'];}
            if (isset($request['no_receive']) && $request['no_receive']!='' ) {$requestModule['no_receive'] = $request['no_receive'];}
            if (isset($request['date']) && $request['date']!='' ) {$requestModule['date'] = $request['date'];}
            if (isset($request['type']) && $request['type']!='' ) {$requestModule['type'] = $request['type'];}
            if (isset($request['supplier']) && $request['supplier']!='' ) {$requestModule['supplier'] = $request['supplier'];}
            if (isset($request['detail_goods_return']) && $request['detail_goods_return']!='' ) {$requestModule['detail_goods_return'] = $request['detail_goods_return'];}
            if (isset($request['status']) && $request['status']!='' ) {$requestModule['status'] = $request['status'];}
            if (isset($request['years']) && $request['years']!='' ) {$requestModule['years'] = $request['years'];}
            if (isset($request['reff']) && $request['reff']!='' ) {$requestModule['reff'] = $request['reff'];}

            $result=[];
            $classModel = new Class_StockGoodsReturn();
            $resultClassDB = $classModel->show($requestModule);
            if(!$resultClassDB['success'])
            {
                return $resultClassDB;
            }
            
            $result = $resultClassDB['data'];
            if (isset($request['no_receive']) && $request['no_receive']!='' )
            {
                $result=[];
                $result['get_goodsReturn'] = $resultClassDB['data'];

                $classModel = new Class_StockGoodsReturnDetail();
                $resultClassDB = $classModel->show($requestModule);
                $result['get_detail'] = $resultClassDB['data'];
    
                $classModel = new Class_StockGoodsReturnHistoryApproval();
                $resultClassDB = $classModel->show($requestModule);
                $result['get_history'] = $resultClassDB['data'];
            }
            return [
                'success' => true,
                'message' => 'Insert successfuly',
                'data'=> $result
            ];
        } catch (\Exception $ex) {
            # Insert Log Error
            $requestModule=[];
            $requestModule['reff'] = '-';
            $requestModule['service'] = 'Model';
            $requestModule['class'] = 'StockGoodsReturn';
            $requestModule['function'] = 'getGoodsReturn';
            $requestModule['message'] = $ex->getMessage();
            $requestModule['note'] = '-';
            $classModel = new LogError();
            $result = $classModel->insertLogError($requestModule);
            # End Log Error
            return $ex;
        }
    }

    public function insertGoodsReturn($request)
    {
        try
        {
            DB::beginTransaction();
            // Validate the incoming request data
            $request->validate([
                'type'            => 'required|string',             // Required
                'supplier'        => 'sometimes|nullable|string',   // Optional, can be null
                'reff'            => 'required|string',             // Required
            ]);

            // generate ID
            $requestClassID=[];
            $classID = new GenerateID();
            $noReceive = $classID->getIDGoodsReturn($requestClassID);
          
            // declare variable 
            $date=carbon::now()->format('Y-m-d'); 
            $totalItem=0; $totalQty=0; $totalPrice=0; $status='0'; $years=carbon::now()->format('Y'); $type=''; $reff='-'; 
            $detailGoodsReturn='';
            
            $result=[];
            # declare variable from request
            if (isset($request['date']) && $request['date']!='' ) {$date = $request['date'];}
            if (isset($request['type']) && $request['type']!='' ) {$type = $request['type'];}
            if (isset($request['reff']) && $request['reff']!='' ) {$reff = $request['reff'];}
            if (isset($request['detail_goods_return']) && $request['detail_goods_return']!='' ) {$detailGoodsReturn = $request['detail_goods_return'];}
            # end declare variable request

            # declare variable request
            $requestModule=[];
            $requestModule['no_receive'] = $noReceive;
            $requestModule['date'] = $date;
            $requestModule['type'] = $type;
            $requestModule['total_item'] = $totalItem;
            $requestModule['total_qty'] = $totalQty;
            $requestModule['total_price'] = $totalPrice;
            $requestModule['detail_goods_return'] = $detailGoodsReturn;
            $requestModule['status'] = $status;
            $requestModule['years'] = $years;
            $requestModule['reff'] = $reff;
            # end declare variable request adjustment
        
            $classDB = new Class_StockGoodsReturn();
            $resultClassDB = $classDB->insert($requestModule);
            if(!$resultClassDB['success'])
            {
                DB::rollBack();
                return $resultClassDB;
            }
      
            $result['insert_stockGoodsReturn'] = $resultClassDB['data'];
            # declare variable request detail
            if($detailGoodsReturn !='')
            {
                $idClassGoodsReturn = $resultClassDB['data']->id;
                $requestPrivateFunction = [];
                $requestPrivateFunction['id'] = $idClassGoodsReturn;
                $requestPrivateFunction['no_receive'] = $noReceive;
                $requestPrivateFunction['reff'] = $reff;
                $requestPrivateFunction['detail_item'] = $detailGoodsReturn;
                $resultPrivateFunction = $this->updateGoodsReturnDetail($requestPrivateFunction);
           
                if(!$resultPrivateFunction['success'])
                {
                    DB::rollBack();
                    return $resultPrivateFunction;
                }
                $result['insert_stockAdjustmentDetail'] = $resultPrivateFunction['data'];
            }

            # get role access
            $requestRoleAccess =[];
            $requestRoleAccess['id_access_management'] = 'AA-002'; // approval_goods_return
            $classRoleAccess = new RoleAccessManagement();
            $resultRoleAccess = $classRoleAccess->getRoleAccess($requestRoleAccess);
       
            foreach($resultRoleAccess['data']['get_roleAccessDetail'] as $v)
            {
                $requestClassDB=[];
                $requestClassDB['no_receive']=$noReceive;
                $requestClassDB['ord']=$v->ord;
                $requestClassDB['id_role_access']=$v->id_role_access;
                $requestClassDB['pic']=$v->pic;
                $requestClassDB['grade']=$v->grade;
                $requestClassDB['departemen']=$v->departemen;

                $classDB = new Class_StockGoodsReturnHistoryApproval();
                $resultClassDB = $classDB->insert($requestClassDB); 
             
                if(!$resultClassDB['success'])
                {
                    DB::rollBack();
                    return $resultClassDB;
                }
                $result['insert_classStockGoodsReturnHistoryApproval'] = $resultClassDB['data'];
            }
            # declare variable history approval

            // insert History
            $requestHistory = [];
            $requestHistory['id_item'] = $noReceive;
            $requestHistory['reff'] = $reff;
            $requestHistory['activity'] = 'Insert StockAdjustment';
            $requestHistory['detail_act'] = $detailGoodsReturn;
            $history = new StockLog();
            $result['insert_history'] = $history->insertHistoryStock($requestHistory);

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

    public function updateGoodsReturn($request)
    {
        try
        {
            DB::beginTransaction();
            // declare variable 
            $noReceive=''; $date=''; $type=''; $supplier=''; $idKaryawan=''; $reff=''; $note=''; $detailGoodsReturn='';
            $result=[]; $status='';

            # declare variable from request
            if (isset($request['no_receive']) && $request['no_receive']!='' ) {$noReceive = $request['no_receive'];}
            if (isset($request['date']) && $request['date']!='' ) {$date = $request['date'];}
            if (isset($request['type']) && $request['type']!='' ) {$type = $request['type'];}
            if (isset($request['supplier']) && $request['supplier']!='' ) {$supplier = $request['supplier'];}
            if (isset($request['status']) && $request['status']!='' ) {$status = $request['status'];}  
            if (isset($request['id_karyawan']) && $request['id_karyawan']!='' ) {$idKaryawan = $request['id_karyawan'];}    
            if (isset($request['reff']) && $request['reff']!='' ) {$reff = $request['reff'];}    
            if (isset($request['note']) && $request['note']!='' ) {$note = $request['note'];}    
            if (isset($request['detail_goods_return']) && $request['detail_goods_return']!='' ) {$detailGoodsReturn = $request['detail_goods_return'];}
            # end declare variable request

            // get data Stock Goods Return
            $requestClassDB = [];
            $requestClassDB['no_receive'] = $noReceive;
            $classDB = new Class_StockGoodsReturn();
            $resultClassDB = $classDB->show($requestClassDB);
            if(!$resultClassDB['success'])
            {
                DB::rollBack();
                return $resultClassDB;
            }
            $idClassGoodsReturn = $resultClassDB['data'][0]->id;
         
            // edit detail
            if($detailGoodsReturn!='')
            {
                $requestPrivateFunction=[];
                $requestPrivateFunction['id'] = $idClassGoodsReturn;
                $requestPrivateFunction['no_receive'] = $noReceive;
                $requestPrivateFunction['detail_item'] = $detailGoodsReturn;
                $requestPrivateFunction['reff'] = $reff;
                $result['update_StockGoodsReturnDetail'] = $this->updateGoodsReturnDetail($requestPrivateFunction);
        
            }

            if($status=='')
            {
                DB::commit();
                return $result;
            }
         
            // cek adjustment history approve
            $requestClassDB = [];
            $requestClassDB['no_receive'] = $noReceive;
            $requestClassDB['status'] = '0'; // belum di approve
            $classDB = new Class_StockGoodsReturnHistoryApproval();
            $resultClassDB = $classDB->show($requestClassDB);
            if(!$resultClassDB['success'])
            {
                // DB::rollBack();
                return $resultClassDB;
            }
            else
            {
                $statusApprovalGoodsReturn = 0;
                foreach($resultClassDB['data'] as $v) // jika user mempunyai double role maka akan terupdate semua
                {
                    $idRoleAccess = $v->id_role_access;
                    $pic = $v->pic;
                    $statusApprovalGoodsReturn = $v->ord;
                    if($status=='9') // reject
                    {
                        $statusApprovalGoodsReturn='9';
                    }
                    if($status=='0')
                    {
                        DB::table('stock_goodss_return_history_approval')
                        ->where('no_adjustment',$noAdjustment)
                        ->update([
                            'status' => $status,
                        ]);
                        $statusApprovalGoodsReturn='0'; // back to draft
                    }
             
                    // cek user access management 
                    $roleUser_ = DB::table('users_access_management')
                    ->where('id_karyawan',$idKaryawan)
                    ->where('id_role_access', $idRoleAccess);
                    if($roleUser_->exists())
                    {
                        $roleUser = $roleUser_->first();
                    
                        DB::table('stock_goods_return_history_approval')
                        ->where('no_receive',$noReceive)
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
                        $result['update_approveHistory'] = 'successfuly update No Receive : '.$noReceive.' ID Role : '. $idRoleAccess;
                     
                        $requestClassDB =[];
                        $requestClassDB['id'] = $idClassGoodsReturn;
                        $requestClassDB['status'] =  $statusApprovalGoodsReturn; // 11= complete full acc;
                        $classDB = new Class_StockGoodsReturn();
                        $resultClassDB = $classDB->update($requestClassDB);
            
                        if(!$resultClassDB['success'])
                        {
                            DB::rollBack();
                            return $resultClassDB;
                        }
                        $result['update_stockGoodsReturn'] = 'successfuly update No Receive : '.$noReceive.' Status : '. $statusApprovalGoodsReturn;
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
            $requestClassDB['no_receive'] = $noReceive;
            $requestClassDB['status'] = '0'; // belum di approve
            $classDB = new Class_StockGoodsReturnHistoryApproval();
            $resultClassDB = $classDB->show($requestClassDB);
            if(!$resultClassDB['success']) // complete
            {
                $requestClassDB =[];
                $requestClassDB['id'] = $idClassGoodsReturn;
                $requestClassDB['status'] =  '11'; // complete full acc
                $classDB = new Class_StockGoodsReturn();
                $resultClassDB = $classDB->update($requestClassDB);
                if(!$resultClassDB['success'])
                {
                    DB::rollBack();
                    return $resultClassDB;
                }
                $result['update_stockGoodsReturn'] = $resultClassDB['data'];

                // update stock by adjustment
                $requestClassDB=[];
                $requestClassDB['no_receive'] = $noReceive;
                $classDB = new Class_StockGoodsReturnDetail();
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
                        $requestClassDB['no_transaction'] = $v->no_receive;
                        $requestClassDB['qty'] = $v->qty;
                        $requestClassDB['origin_of_goods'] = 'Goods Return';
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
            $requestModule['class'] = 'StockGoodsReturn';
            $requestModule['function'] = 'updateGoodsReturn';
            $requestModule['message'] = $ex->getMessage();
            $requestModule['note'] = '-';
            $classModel = new LogError();
            $result = $classModel->insertLogError($requestModule);
            # End Log Error
            return $ex;
        }
    }

    // private function ------------------------------------
    private function updateGoodsReturnDetail($request)
    {
        try
        {
            $id = $request['id'];
            $noReceive = $request['no_receive'];
            $reff = $request['reff'];
            $detailItem = $request['detail_item'];
            $jsonDecodeDetailItem = json_decode($detailItem);
         
            $price=0; $expDate=''; $remark='-';
            $totalQty =0; $totalPrice=0; $totalItem=0;
            
            // delete from table stock_adjustment_detail
            DB::table('stock_goods_return_detail')
            ->where('no_receive',$noReceive)
            ->delete();
         
            foreach($jsonDecodeDetailItem as $v)
            {
                
                $totalItem++;
                $totalQty += $v->qty;
                $totalPrice += ($v->price * $v->qty);
                
                $requestModule=[];
                $requestModule['no_receive'] = $noReceive;
                $requestModule['item_group'] = $v->item_group;
                $requestModule['brand'] =  $v->brand;
                $requestModule['code'] = $v->code;
                $requestModule['items'] =  $v->items;
                $requestModule['description'] = $v->description;
                $requestModule['type_transaction'] = '2'; // 1=in; 2=out;
                $requestModule['qty'] = $v->qty;
                $requestModule['unit'] = $v->unit;
                $requestModule['price'] = $v->price;
                $requestModule['total_price'] = ($v->price * $v->qty); 
                $requestModule['exp_date'] = $v->exp_date;
                $requestModule['remark'] = $v->remark;
                $requestModule['years'] = Carbon::now()->format('Y');
                $requestModule['reff'] = $reff;
            
                # end declare variable request detail
                $classDB = new Class_StockGoodsReturnDetail();
                $resultClassDB = $classDB->insert($requestModule);    
              
                if(!$resultClassDB['success'])
                {
                    return $resultClassDB;
                }
                $result['insert_classGoodsReturnDetail'] = $resultClassDB['data'];
            }
       
            // update master 
            $requestModule=[];
            $requestModule['id'] = $id;
            $requestModule['total_item'] = $totalItem;
            // $requestModule['total_qty'] = $totalQty;
            $requestModule['total_price'] = $totalPrice;
            $requestModule['detail_goods_return'] = $detailItem;
            $classModel = new Class_StockGoodsReturn();
            $result['update_classStockGoodsReturn'] = $classModel->update($requestModule);

            return [
                'success' => true,
                'message' => 'Update Detail Stock Goods Return successful',
                'data' => $result
            ];
        } catch (\Exception $ex) {         
            # Insert Log Error
            $requestModule=[];
            $requestModule['reff'] = '-';
            $requestModule['service'] = 'Model';
            $requestModule['class'] = 'StockGoodsReturn';
            $requestModule['function'] = 'updateGoodsReturnDetail';
            $requestModule['message'] = $ex->getMessage();
            $requestModule['note'] = '-';
            $classModel = new LogError();
            $result = $classModel->insertLogError($requestModule);
            # End Log Error
            return $ex;
        }
    }
}
