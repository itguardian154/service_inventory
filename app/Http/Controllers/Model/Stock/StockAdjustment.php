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
use App\Http\Controllers\Class_DB\Class_StockAdjustmentHistoryApproval;;
use App\Http\Controllers\Model\RoleAccess\RoleAccessManagement;
use App\Http\Controllers\Model\Stock\StockLog;

class StockAdjustment extends Controller
{
    public function getStockAdjustment($request)
    {
        try
        {
            $requestModule=[];
            if (isset($request['id_item']) && $request['id_item']!='' ) {$requestModule['id_item'] = $request['id_item'];}
            if (isset($request['item_group']) && $request['item_group']!='' ) {$requestModule['item_group'] = $request['item_group'];}
            if (isset($request['brand']) && $request['brand']!='' ) {$requestModule['brand'] = $request['brand'];}
            if (isset($request['code']) && $request['code']!='' ) {$requestModule['code'] = $request['code'];}
            if (isset($request['items']) && $request['items']!='' ) {$requestModule['items'] = $request['items'];}
            if (isset($request['description']) && $request['description']!='' ) {$requestModule['description'] = $request['description'];}
            if (isset($request['unit']) && $request['unit']!='' ) {$requestModule['unit'] = $request['unit'];}
            if (isset($request['initial_stock']) && $request['initial_stock']!='' ) {$requestModule['initial_stock'] = $request['initial_stock'];}
            if (isset($request['have_exp']) && $request['have_exp']!='' ) {$requestModule['have_exp'] = $request['have_exp'];}
            if (isset($request['stock_in']) && $request['stock_in']!='' ) {$requestModule['stock_in'] = $request['stock_in'];}
            if (isset($request['stock_out']) && $request['stock_out']!='' ) {$requestModule['stock_out'] = $request['stock_out'];}
            if (isset($request['final_stock']) && $request['final_stock']!='' ) {$requestModule['final_stock'] = $request['final_stock'];}
            if (isset($request['last_price']) && $request['last_price']!='' ) {$requestModule['last_price'] = $request['last_price'];}
            if (isset($request['average_price']) && $request['average_price']!='' ) {$requestModule['average_price'] = $request['average_price'];}
            if (isset($request['total_price']) && $request['total_price']!='' ) {$requestModule['total_price'] = $request['total_price'];}

            $result=[];
            $classModel = new Class_StockAdjustment();
            $result['get_adjustment'] = $classModel->show($requestModule);

            if (isset($request['code']) && $request['code']!='' )
            {
                $classModel = new Class_StockAdjustmentDetail();
                $result['get_detail'] = $classModel->show($requestModule);
    
                $classModel = new Class_StockAdjustmentHistoryApproval();
                $result['get_history'] = $classModel->show($requestModule);
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
                $jsonDecodeDetailItem = json_decode($detailItem);
             
                $price=0; $expDate=''; $remark='-';
                foreach($jsonDecodeDetailItem as $v)
                {
                    $itemGroup='-'; $brand='-'; $code='-'; $items='-'; $description='-'; $typeTransaction=''; $qty=0; $unit='-';
                    $totalItem++;
                    $totalQty = $totalQty + $v->qty;
                    $totalPrice = $totalPrice + $v->price;

                    $itemGroup = $v->item_group;
                    $brand = $v->brand;
                    $code = $v->code;
                    $items = $v->items;
                    $description = $v->description;
                    $typeTransaction =  $v->type_transaction;
                    $qty = $v->qty;
                    $unit = $v->unit;
                    $price = $v->price;
                    $expDate = $v->exp_date;
                    $remark = $v->remark;

                    $requestModuleAdjustmentDetail=[];
                    $requestModuleAdjustmentDetail['no_adjustment'] = $noAdjustment;
                    $requestModuleAdjustmentDetail['item_group'] = $itemGroup;
                    $requestModuleAdjustmentDetail['brand'] =  $brand;
                    $requestModuleAdjustmentDetail['code'] = $code;
                    $requestModuleAdjustmentDetail['items'] =  $items;
                    $requestModuleAdjustmentDetail['description'] = $description;
                    $requestModuleAdjustmentDetail['type_transction'] = $typeTransaction;
                    $requestModuleAdjustmentDetail['qty'] = $qty;
                    $requestModuleAdjustmentDetail['unit'] = $unit;
                    $requestModuleAdjustmentDetail['price'] = $price;
                    $requestModuleAdjustmentDetail['exp_date'] = $expDate;
                    $requestModuleAdjustmentDetail['remark'] = $remark;
                    $requestModuleAdjustmentDetail['years'] = $years;
                    $requestModuleAdjustmentDetail['reff'] = $reff;
                    # end declare variable request detail
                    $resultClassDB = new Class_StockAdjustmentDetail();
                    $resultClassDB = $resultClassDB->insert($requestModuleAdjustmentDetail);    
                    if(!$resultClassDB['success'])
                    {
                        DB::rollBack();
                        return $resultClassDB;
                    }
                    $result['insert_stockAdjustmentDetail'] = $resultClassDB['data'];
                }

                // update master adjustment 
                $requestModuleAdjustment=[];
                $requestModuleAdjustment['id'] = $idClassAdjustment;
                $requestModuleAdjustment['total_item'] = $totalItem;
                $requestModuleAdjustment['total_qty'] = $totalQty;
                $requestModuleAdjustment['total_price'] = $totalPrice;
                $classDB = new Class_StockAdjustment();
                $resultClassDB = $classDB->update($requestModuleAdjustment);
                if(!$resultClassDB['success'])
                {
                    DB::rollBack();
                    return $resultClassDB;
                }
                $result['update_classAdjustment'] = $resultClassDB['data'];
            
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
                // $requestClassDB['id_karyawan']='';
                // $requestClassDB['name']='';
                // $requestClassDB['signature']='';
                // $requestClassDB['status']='';
                // $requestClassDB['date']='';
                // $requestClassDB['note']='';
                // $requestClassDB['years']='';

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
            if($resultClassDB==null)
            {
                return 'data Sudah Full Acc';
            }
            else
            {
                foreach($resultClassDB as $v) // jika user mempunyai double role maka akan terupdate semua
                {
                    $idRoleAccess = $v->id_role_access;
                    $pic = $v->pic;
            
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
                            'date' => carbon::now()->format('Y-m-d H:i:s')
                        ]);

                    }
                    else
                    {
                        return 'Anda Tidak Mempunyai Akses Approval '. $v->pic;
                    }
                }
            }

            // cek sudah complete atau belum
            $requestClassDB = [];
            $requestClassDB['no_adjustment'] = $noAdjustment;
            $requestClassDB['status'] = '0'; // belum di approve
            $classDB = new Class_StockAdjustmentHistoryApproval();
            $resultClassDB = $classDB->show($requestClassDB);
            if($resultClassDB==null) // complete
            {
                $requestClassDB = [];
                $requestClassDB['no_adjustment'] = $noAdjustment;
                $classDB = new Class_StockAdjustment();
                $resultClassDB = $classDB->show($requestClassDB);
                $idClassAdjustment = $resultClassDB[0]->id;

                $requestClassDB =[];
                $requestClassDB['id'] = $idClassAdjustment;
                $requestClassDB['status'] =  '1'; // complete full acc
                $classDB = new Class_StockAdjustment();
                $result['update_StockAdjustment'] = $classDB->update($requestClassDB);

                // update stock by adjustment
                $requestClassDB=[];
                $requestClassDB['no_adjustment'] = $noAdjustment;
                $classDB = new Class_StockAdjustmentDetail();
                $resultClassDB = $classDB->show($requestClassDB);
                foreach($resultClassDB as $v)
                {
                    // find id item
                    $requestClassDB =[];
                    $requestClassDB['code'] = $v->code;
                    $classDB = new Class_Stock();
                    $resultClassDB = $classDB->show($requestClassDB);
      
                    if($resultClassDB==null)
                    {
                        return 'Code Item '. $v->code.' Tidak Ditemukan';
                    }
                    else
                    {   
                        $idItem = $resultClassDB[0]->id;
                        $initialStock=0;
                        $initialStock = $resultClassDB[0]->initial_stock;
                  
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
                        
                    }
                }
             
            }
            else
            {

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

    // private function
    private function updateAdjustmentHistoryApprove($request)
    {
        try
        {

        } catch (\Exception $ex) {
            DB::rollBack();
            # Insert Log Error
            $requestModule=[];
            $requestModule['reff'] = '-';
            $requestModule['service'] = 'Model';
            $requestModule['class'] = 'StockAdjustment';
            $requestModule['function'] = 'updateAdjustmentHistoryApprove';
            $requestModule['message'] = $ex->getMessage();
            $requestModule['note'] = '-';
            $classModel = new LogError();
            $result = $classModel->insertLogError($requestModule);
            # End Log Error
            return $ex;
        }
    }

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
            foreach($jsonDecodeDetailItem as $v)
            {
          
                $totalItem++;
                $totalQty = $totalQty + $v->qty;
                $totalPrice = $totalPrice + $v->price;
                
                // delete from table stock_adjustment_detail
                DB::table('stock_adjustment_detail')
                ->where('no_adjustment',$noAdjustment)
                ->delete();
        
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
                $classModel = new Class_StockAdjustmentDetail();
                $result['insert_classAdjustmentDetail'] = $classModel->insert($requestModuleAdjustmentDetail);    
            }
    
            // update master adjustment 
            $requestModuleAdjustment=[];
            $requestModuleAdjustment['id'] = $id;
            $requestModuleAdjustment['total_item'] = $totalItem;
            $requestModuleAdjustment['total_qty'] = $totalQty;
            $requestModuleAdjustment['total_price'] = $totalPrice;
            $classModel = new Class_StockAdjustment();
            $result['update_classAdjustment'] = $classModel->update($requestModuleAdjustment);
            return $result;
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
