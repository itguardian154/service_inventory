<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Log\LogError;
use App\Http\Controllers\Model\StoreRequest\StoreRequest;
use App\Models\store_request;

use Carbon\Carbon;
use DateTime;


class Service_StoreRequest extends Controller
{
    public function getStoreRequest(Request $request)
    {
        try
        {
            $module = new StoreRequest();
            $resultModel = $module->getStoreRequest($request); 
            
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
            $requestModule['service'] = 'Get-StoreRequest';
            $requestModule['class'] = 'Service_StoreRequest';
            $requestModule['function'] = 'getStoreRequest';
            $requestModule['message'] = $ex->getMessage();
            $requestModule['note'] = '-';
            $classModel = new LogError();
            $result = $classModel->insertLogError($requestModule);
            # End Log Error
            return $ex;
        }  
    }

    public function insertStoreRequest(Request $request)
    {
        try
        { 
            $result = [];
            $classModel = new StoreRequest();
            $resultModel = $classModel->insertStoreRequest($request); 
            
            if($resultModel['success'])
            {
                $result=response()->json([
                    'status' => 'success',
                    'message' => 'Created Store Requst Successfuly',
                    'data' => $resultModel['data']
                ]);
            }
            else
            {
                $result=response()->json([
                    'status' => 'failed',
                    'message' => 'Error Created Store Requst',
                    'data' => $resultModel['message']
                ]);
            }
            return $result;
        } catch (\Exception $ex) {
            # Insert Log Error
            $requestModule=[];
            $requestModule['reff'] = 'Service';
            $requestModule['service'] = 'Insert-StoreRequest';
            $requestModule['class'] = 'Service_StoreRequest';
            $requestModule['function'] = 'insertStoreRequest';
            $requestModule['message'] = $ex->getMessage();
            $requestModule['note'] = '-';
            $classModel = new LogError();
            $result = $classModel->insertLogError($requestModule);
            # End Log Error
            return $ex;
        }  
    }

    public function updateStoreRequest(Request $request)
    {
        try
        {
            if (isset($request['status_approve']) && $request['status_approve']!='' ) 
            {
                $classModel = new StoreRequest();
                $result = $classModel->updateApprovalStoreRequest($request); 
            }
            else
            {
                $classModel = new StoreRequest();
                $result = $classModel->updateStoreRequest($request); 
            }
           
            $result=response()->json([
                'status' => 'success',
                'message' => 'Update Store Request Successfuly',
                'data' => $result
            ]);

            return $result;
        } catch (\Exception $ex) {
            # Insert Log Error
            $requestModule=[];
            $requestModule['reff'] = 'Service';
            $requestModule['service'] = 'Update-StoreRequest';
            $requestModule['class'] = 'StoreRequest';
            $requestModule['function'] = 'updateStoreRequest';
            $requestModule['message'] = $ex->getMessage();
            $requestModule['note'] = '-';
            $classModel = new LogError();
            $result = $classModel->insertLogError($requestModule);
            # End Log Error
            return $ex;
        }  
    } 
    
    public function updateDistributionStatus(Request $request)
    {
        try {
            $noTransaction = $request->input('no_transaction', '');
            $newStatus     = $request->input('status', '');

            // === VALIDASI INPUT ===
            if (empty($noTransaction) && empty($newStatus)) {
                return response()->json([
                    'status'  => 'failed',
                    'message' => 'no_transaction and status cannot be empty',
                    'data'    => null
                ], 400); // Bad Request
            }

            DB::beginTransaction();

            // Update Store Request
            $updated = DB::table('store_request')
                ->where('no_transaction', $noTransaction)
                ->update([
                    'status_distribution' => $newStatus,
                    'updated_at'          => Carbon::now()
                ]);

            DB::commit();

            // === RESPONSE JIKA DATA TIDAK DITEMUKAN ===
            if (!$updated) {
                return response()->json([
                    'status'  => 'failed',
                    'message' => 'No data updated. Transaction not found.',
                    'data'    => null
                ], 404);
            }

            // === RESPONSE BERHASIL ===
            return response()->json([
                'status'  => 'success',
                'message' => 'Update Distribution Status Successfully',
                'data'    => [
                    'no_transaction' => $noTransaction,
                    'status'         => $newStatus
                ]
            ]);

        } catch (\Exception $ex) {
            DB::rollBack();

            // Insert Log Error
            $logData = [
                'reff'     => 'Service',
                'service'  => 'Update-DistributionStatus',
                'class'    => 'Service_StoreRequest',
                'function' => 'updateDistributionStatus',
                'message'  => $ex->getMessage(),
                'note'     => '-'
            ];

            (new LogError())->insertLogError($logData);

            return response()->json([
                'status'  => 'error',
                'message' => $ex->getMessage()
            ], 500);
        }
    }
}
