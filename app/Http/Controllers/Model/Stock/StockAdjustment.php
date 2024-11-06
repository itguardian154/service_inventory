<?php

namespace App\Http\Controllers\Model\Stock;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use App\Http\Controllers\Log\LogError;
use App\Http\Controllers\Generate\GenerateID;
use App\Http\Controllers\Class_DB\Class_StockAdjustment;
use App\Http\Controllers\Class_DB\Class_StockAdjustmentDetail;
use App\Http\Controllers\Class_DB\Class_StockAdjustmentHistoryApproval;
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

            // generate ID Adjustment
            $requestClassID=[];
            $classID = new GenerateID();
            $noAdjustment = $classID->getIDAdjustment($requestClassID);
        
            // declare variable 
            $date=carbon::now()->format('Y-m-d'); $totalItem=0; $totalQty=0; $totalPrice=0; $status='1'; $years=carbon::now()->format('Y'); $reff='-';
            $detailItem='';
            $result=[];
            # declare variable from request
            if (isset($request['date']) && $request['date']!='' ) {$date = $request['date'];}
            if (isset($request['total_item']) && $request['total_item']!='' ) {$totalItem = $request['total_item'];}
            if (isset($request['total_qty']) && $request['total_qty']!='' ) {$totalQty = $request['total_qty'];}
            if (isset($request['total_price']) && $request['total_price']!='' ) {$totalPrice = $request['total_price'];}
            if (isset($request['status']) && $request['status']!='' ) {$status = $request['status'];}
            if (isset($request['years']) && $request['years']!='' ) {$years = $request['years'];}
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
            $classModel = new Class_StockAdjustment();
            $result['insert_classAdjustment'] = $classModel->insert($requestModuleAdjustment);
           
            # declare variable request detail
           
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
                $classModel = new Class_StockAdjustmentDetail();
                $result['insert_classAdjustmentDetail'] = $classModel->insert($requestModuleAdjustmentDetail);    
            }

            // update master adjustment 
            $requestModuleAdjustment=[];
            $requestModuleAdjustment['no_adjustment'] = $noAdjustment;
            $requestModuleAdjustment['total_item'] = $totalItem;
            $requestModuleAdjustment['total_qty'] = $totalQty;
            $requestModuleAdjustment['total_price'] = $totalPrice;
            $classModel = new Class_StockAdjustment();
            $result['update_classAdjustment'] = $classModel->update($requestModuleAdjustment);

            // insert History
            $requestHistory = [];
            $requestHistory['id_item'] = $noAdjustment;
            $requestHistory['reff'] = $reff;
            $requestHistory['activity'] = 'Insert StockAdjustment';
            $requestHistory['detail_act'] = $detailItem;
            $history = new StockLog();
            $result['insert_history'] = $history->insertHistoryStock($requestHistory);

            # get role access
            $requestRoleAccess =[];
            $requestRoleAccess['id_access_management'] = 'AA-001'; // approval_adjustment
            $classRoleAccess = new RoleAccessManagement();
            $resultRoleAccess = $classRoleAccess->getRoleAccess($requestRoleAccess);
            foreach($resultRoleAccess['data']['get_roleAccessDetail'] as $v)
            {
                $requestModuleAdjustmentHistoryApproval=[];
                $requestModuleAdjustmentHistoryApproval['no_adjustment']=$noAdjustment;
                $requestModuleAdjustmentHistoryApproval['ord']=$v->ord;
                $requestModuleAdjustmentHistoryApproval['pic']=$v->pic;
                $requestModuleAdjustmentHistoryApproval['name']=$v->name;
                $requestModuleAdjustmentHistoryApproval['grade']=$v->grade;
                $requestModuleAdjustmentHistoryApproval['departemen']=$v->departemen;
                $requestModuleAdjustmentHistoryApproval['signature']='';
                $requestModuleAdjustmentHistoryApproval['status']='';
                $requestModuleAdjustmentHistoryApproval['years']=$years;
                # end declare variable history approval
                
                $classModel = new Class_StockAdjustmentHistoryApproval();
                $result['insert_classAdjustmentHistoryApproval'] = $classModel->insert($requestModuleAdjustmentHistoryApproval);
                
            }
            # declare variable history approval

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
            $id=''; $date=''; $reff='';$detailItem='';
            $result=[];
            # declare variable from request
            if (isset($request['id']) && $request['id']!='' ) {$id = $request['id'];}
            if (isset($request['date']) && $request['date']!='' ) {$date = $request['date'];}
            if (isset($request['status']) && $request['status']!='' ) {$status = $request['status'];}  
            if (isset($request['reff']) && $request['reff']!='' ) {$reff = $request['reff'];}     
            if (isset($request['detail_item']) && $request['detail_item']!='' ) {$detailItem = $request['detail_item'];}
            # end declare variable request

            # declare variable request adjustment
            $requestModuleAdjustment=[];
            $requestModuleAdjustment['id'] = $id;
            $requestModuleAdjustment['date'] = $date;
            $requestModuleAdjustment['status'] = $status;
       
            # end declare variable request adjustment
            $classModel = new Class_StockAdjustment();
            $result['update_classAdjustment'] = $classModel->update($requestModuleAdjustment);

            # declare variable request detail
            if($detailItem!='')
            {
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

}
