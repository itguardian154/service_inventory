<?php

namespace App\Http\Controllers\Model\Stock;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Log\LogError;
use App\Http\Controllers\Class_DB\Class_Stock;

class Stock extends Controller
{
    public function getStock($request)
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
            $classModel = new Class_Stock();
            $result = $classModel->show($requestModule);

            return $result;
        } catch (\Exception $ex) {
            # Insert Log Error
            $requestModule=[];
            $requestModule['reff'] = '-';
            $requestModule['service'] = 'Model';
            $requestModule['class'] = 'Stock';
            $requestModule['function'] = 'getStock';
            $requestModule['message'] = $ex->getMessage();
            $requestModule['note'] = '-';
            $classModel = new LogError();
            $result = $classModel->insertLogError($request);
            # End Log Error
            return $ex;
        }
    }

    public function insertStock($request)
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
            $classModel = new Class_Stock();
            $result = $classModel->insert($requestModule);

            return $result;
        } catch (\Exception $ex) {
            # Insert Log Error
            $requestModule=[];
            $requestModule['reff'] = '-';
            $requestModule['service'] = 'Model';
            $requestModule['class'] = 'Stock';
            $requestModule['function'] = 'insertStock';
            $requestModule['message'] = $ex->getMessage();
            $requestModule['note'] = '-';
            $classModel = new LogError();
            $result = $classModel->insertLogError($requestModule);
            # End Log Error
            return $ex;
        }
    }

    public function updateStock($request)
    {
        try
        {
            $requestModule=[];
            if (isset($request['id']) && $request['id']!='' ) {$requestModule['id'] = $request['id'];}
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
            $classModel = new Class_Stock();
            $result = $classModel->update($requestModule);

            return $result;
        } catch (\Exception $ex) {
            # Insert Log Error
            $requestModule=[];
            $requestModule['reff'] = '-';
            $requestModule['service'] = 'Model';
            $requestModule['class'] = 'Stock';
            $requestModule['function'] = 'updateStock';
            $requestModule['message'] = $ex->getMessage();
            $requestModule['note'] = '-';
            $classModel = new LogError();
            $result = $classModel->insertLogError($requestModule);
            # End Log Error
            return $ex;
        }
    }
}
