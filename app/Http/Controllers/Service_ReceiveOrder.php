<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Log\LogError;
use App\Http\Controllers\Model\ReceiveOrder\ReceiveOrder;
use App\Models\receive_order;

use Carbon\Carbon;
use DateTime;

class Service_ReceiveOrder extends Controller
{
    public function getReceiveOrder(Request $request)
    {
        try
        {
            $module = new ReceiveOrder();
            $resultModel = $module->getReceiveOrder($request); 
            
            if($resultModel['success'])
            {
                $result=response()->json([
                    'status' => 'success',
                    'message' => 'Get Data Successfuly',
                    'data' => $resultModel['data']
                ]);
            }
            else
            {
                $result=response()->json([
                    'status' => 'failed',
                    'message' => 'Error Get Data',
                    'data' => $resultModel
                ]);
            }

            return $result;
        } catch (\Exception $ex) {
            # Insert Log Error
            $requestModule=[];
            $requestModule['reff'] = 'Service';
            $requestModule['service'] = 'Get-ReceiveOrder';
            $requestModule['class'] = 'Service_ReceiveOrder';
            $requestModule['function'] = 'getReceiveOrder';
            $requestModule['message'] = $ex->getMessage();
            $requestModule['note'] = '-';
            $classModel = new LogError();
            $result = $classModel->insertLogError($requestModule);
            # End Log Error
            return $ex;
        }  
    }

    public function insertReceiveOrder(Request $request)
    {
        try
        { 
            $result = [];
            $classModel = new ReceiveOrder();
            $resultModel = $classModel->insertReceiveOrder($request); 
            
            if($resultModel['success'])
            {
                $result=response()->json([
                    'status' => 'success',
                    'message' => 'Created Receive Order Successfuly',
                    'data' => $resultModel['data']
                ]);
            }
            else
            {
                $result=response()->json([
                    'status' => 'failed',
                    'message' => 'Error Created Receive Order',
                    'data' => $resultModel['message']
                ]);
            }
            return $result;
        } catch (\Exception $ex) {
            # Insert Log Error
            $requestModule=[];
            $requestModule['reff'] = 'Service';
            $requestModule['service'] = 'Insert-ReceiveOrder';
            $requestModule['class'] = 'Service_ReceiveOrder';
            $requestModule['function'] = 'InsertStock';
            $requestModule['message'] = $ex->getMessage();
            $requestModule['note'] = '-';
            $classModel = new LogError();
            $result = $classModel->insertLogError($requestModule);
            # End Log Error
            return $ex;
        }  
    }

    public function updateReceiveOrder(Request $request)
    {
        try
        {
            if (isset($request['status_approve']) && $request['status_approve']!='' ) 
            {
                $classModel = new ReceiveOrder();
                $result = $classModel->updateApprovalReceiveOrder($request); 
            }
            else
            {
                $classModel = new ReceiveOrder();
                $result = $classModel->updateReceiveOrder($request); 
            }
           

            $result=response()->json([
                'status' => 'success',
                'message' => 'Update Receive Order Successfuly',
                'data' => $result
            ]);

            return $result;
        } catch (\Exception $ex) {
            # Insert Log Error
            $requestModule=[];
            $requestModule['reff'] = 'Service';
            $requestModule['service'] = 'Update-ReceiveOrder';
            $requestModule['class'] = 'ReceiveOrder';
            $requestModule['function'] = 'updateReceiveOrder';
            $requestModule['message'] = $ex->getMessage();
            $requestModule['note'] = '-';
            $classModel = new LogError();
            $result = $classModel->insertLogError($requestModule);
            # End Log Error
            return $ex;
        }  
    } 
}
