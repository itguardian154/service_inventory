<?php

namespace App\Http\Controllers\Model\Stock;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Log\LogError;

use App\Http\Controllers\Class_DB\Class_Stock;
use App\Http\Controllers\Class_DB\Class_StockTransaction;
use App\Http\Controllers\Class_DB\Class_StockLog;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use DateTime;
use Exception;

class Stock extends Controller
{
    public function getStock($request)
    {
        try
        {
            $result=[];
            if (isset($request['code']) && $request['code']!='') 
            {
                $result=[];
                $result['get_stock'] = $this->getStockByDate($request);;

                $classDB = new Class_StockTransaction();
                $resultClassDB = $classDB->show($request);
                $result['get_stock_transaction'] = $resultClassDB['data'];

                return [
                'success' => true,
                'message' => 'Get successful',
                'data'=> $result
                ];
            }
            else
            {
                $result = $this->getStockByDate($request);

                return [
                'success' => true,
                'message' => 'Get successful',
                'data'=> $result
                ];
            }
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
            $result = $classModel->insertLogError($requestModule);
            # End Log Error
            return [
                'success' => false,
                'message' => $ex->getMessage()
            ];
        }
    }

    public function getStockByDate($request)
    {
        try {
            $date_start = $request['date_start'] ?? Carbon::now()->startOfMonth()->toDateString();
            $date_end = $request['date_end'] ?? Carbon::now()->endOfMonth()->toDateString();
            $code = $request['code'] ?? '';
            $export_type = $request['export_type'] ?? 'all_stock'; // Default: have_stock
            $item_group = $request['item_group'] ?? null;
            $have_exp = $request['have_exp'] ?? null;

            // Normalisasi export_type
            if ($export_type == '0') $export_type = 'all_stock';
            if ($export_type == '1') $export_type = 'have_stock';

            // 1. Ambil previous stock (before date_start)
            $prevStock = DB::table('stock_transaction')
                ->select('code', DB::raw('SUM(`in`) - SUM(`out`) AS initial_stock'))
                ->where('date', '<', $date_start)
                ->groupBy('code')
                ->get()
                ->keyBy('code');

            // 2. Ambil transaksi pada periode (date_start - date_end)
            $transactions = DB::table('stock_transaction')
                ->select('code',
                    DB::raw('SUM(`in`) AS stock_in'),
                    DB::raw('SUM(`out`) AS stock_out')
                )
                ->whereBetween('date', [$date_start, $date_end])
                ->groupBy('code')
                ->get()
                ->keyBy('code');

            // 3. Ambil data stok utama
            $stockQuery = DB::table('stock')
                ->when(!empty($code), fn($q) => $q->where('code', $code))
                ->when($item_group, fn($q) => $q->where('item_group', $item_group))
                ->when($have_exp !== null, fn($q) => $q->where('have_exp', $have_exp))
                ->orderBy('items');

            // Gunakan chunk untuk menghindari overload
            $data['stock'] = collect();
            $stockQuery->chunk(500, function ($stocks) use (&$data, $prevStock, $transactions, $export_type) {
                foreach ($stocks as $item) {
                    $initial = $prevStock[$item->code]->initial_stock ?? 0;
                    $stock_in = $transactions[$item->code]->stock_in ?? 0;
                    $stock_out = $transactions[$item->code]->stock_out ?? 0;
                    $final_stock = $initial + $stock_in - $stock_out;

                    // Filter jika hanya ingin yang ada stok
                    if ($export_type === 'have_stock' && $final_stock <= 0) {
                        continue;
                    }

                    $data['stock']->push((object)[
                        'id' => $item->id,
                        'item_group' => $item->item_group,
                        'brand' => $item->brand,
                        'code' => $item->code,
                        'items' => $item->items,
                        'description' => $item->description,
                        'unit' => $item->unit,
                        'have_exp' => $item->have_exp,
                        'initial_stock' => $initial,
                        'stock_in' => $stock_in,
                        'stock_out' => $stock_out,
                        'final_stock' => $final_stock,
                    ]);
                }
            });

            return $data['stock'];
        } catch (\Exception $ex) {
            // Logging error
            $requestModule = [
                'reff' => '-',
                'service' => 'Model',
                'class' => 'Stock',
                'function' => 'getStockByDate',
                'message' => $ex->getMessage(),
                'note' => '-',
            ];
            (new LogError())->insertLogError($requestModule);

            return [
                'success' => false,
                'message' => $ex->getMessage()
            ];
        }
    }


    // public function getStockByDate($request)
    // {
    //     try
    //     {
    //         $date_start = $request['date_start'] ?? Carbon::now()->startOfMonth();
    //         $date_end = $request['date_end'] ?? Carbon::now()->endOfMonth();
    //         $code = $request['code'] ?? '';
    //         $export_type = $request['export_type'] ?? 'have_stock'; // Default: all_stock
    //         $item_group = $request['item_group'] ?? null;
    //         $have_exp = $request['have_exp'] ?? null;
        
    //         if($export_type=='0') //tidak memiliki stock
    //         {
    //             $export_type='all_stock';
    //         }
    //         if($export_type=='1') //memiliki stock
    //         {
    //             $export_type='have_stock';
    //         }

    //         $query = DB::table('stock')
    //             ->leftJoin('stock_transaction as st', function ($join) use ($date_start, $date_end) {
    //                 $join->on('stock.code', '=', 'st.code')
    //                     ->whereBetween('st.date', [$date_start, $date_end]);
    //             })
    //             ->leftJoin(DB::raw("
    //                 (SELECT 
    //                     code, 
    //                     SUM(`in`) - SUM(`out`) AS initial_stock
    //                 FROM stock_transaction
    //                 WHERE date < '$date_start'
    //                 GROUP BY code
    //                 ) AS prev_stock
    //             "), 'stock.code', '=', 'prev_stock.code')
    //             ->select([
    //                 'stock.id', 'stock.item_group', 'stock.brand', 'stock.code', 'stock.items',
    //                 'stock.description', 'stock.unit', 'stock.have_exp',
    //                 DB::raw('COALESCE(prev_stock.initial_stock, 0) AS initial_stock'),
    //                 DB::raw('COALESCE(SUM(CASE WHEN st.`in` > 0 THEN st.`in` ELSE 0 END), 0) AS stock_in'),
    //                 DB::raw('COALESCE(SUM(CASE WHEN st.`out` > 0 THEN st.`out` ELSE 0 END), 0) AS stock_out'),
    //                 DB::raw('COALESCE(prev_stock.initial_stock, 0) 
    //                         + COALESCE(SUM(CASE WHEN st.`in` > 0 THEN st.`in` ELSE 0 END), 0) 
    //                         - COALESCE(SUM(CASE WHEN st.`out` > 0 THEN st.`out` ELSE 0 END), 0) 
    //                         AS final_stock')
    //             ])
    //             ->when(!empty($code), function ($query) use ($code) {
    //                 return $query->where('stock.code', $code);
    //             })
    //             ->groupBy([
    //                 'stock.id', 'stock.item_group', 'stock.brand', 'stock.code', 'stock.items',
    //                 'stock.description', 'stock.unit', 'stock.have_exp',
    //                 'prev_stock.initial_stock'
    //             ]);

    //         // **🔹 Filter Berdasarkan export_type**
    //         if ($export_type === 'have_stock') {
    //             $query->havingRaw('
    //                 COALESCE(prev_stock.initial_stock, 0) > 0 
    //                 OR COALESCE(SUM(CASE WHEN st.`in` > 0 THEN st.`in` ELSE 0 END), 0) > 0 
    //                 OR COALESCE(SUM(CASE WHEN st.`out` > 0 THEN st.`out` ELSE 0 END), 0) > 0 
    //                 OR (COALESCE(prev_stock.initial_stock, 0) 
    //                     + COALESCE(SUM(CASE WHEN st.`in` > 0 THEN st.`in` ELSE 0 END), 0) 
    //                     - COALESCE(SUM(CASE WHEN st.`out` > 0 THEN st.`out` ELSE 0 END), 0)) > 0
    //             ');
    //         } elseif (isset($item_group)) {
    //             $query->where('stock.item_group', $item_group);
    //         } elseif (isset($have_exp)) {
    //             $query->where('stock.have_exp', $have_exp);
    //         }

    //         $query->orderBy('stock.items', 'asc');

    //         // **🔹 Gunakan chunk untuk menghindari error memory limit**
    //         $data['stock'] = collect();
    //         $query->chunk(500, function ($stocks) use (&$data) {
    //             $data['stock'] = $data['stock']->merge($stocks);
    //         });
            
    //         return $data['stock'];
    //     } catch (\Exception $ex) {
    //         # Insert Log Error
    //         $requestModule=[];
    //         $requestModule['reff'] = '-';
    //         $requestModule['service'] = 'Model';
    //         $requestModule['class'] = 'Stock';
    //         $requestModule['function'] = 'getStock';
    //         $requestModule['message'] = $ex->getMessage();
    //         $requestModule['note'] = '-';
    //         $classModel = new LogError();
    //         $result = $classModel->insertLogError($requestModule);
    //         # End Log Error
    //         return [
    //             'success' => false,
    //             'message' => $ex->getMessage()
    //         ];
    //     }
    // }

    public function insertStock($request)
    {
        try
        {
            DB::beginTransaction();
            // Validate the incoming request data
            $request->validate([
                'item_group'      => 'sometimes|nullable|string',   // Optional, can be null
                'brand'           => 'sometimes|nullable|string',   // Optional, can be null
                'code'            => 'required|string',             // Required
                'items'           => 'required|string',             // Required
                'description'     => 'sometimes|nullable|string',   // Optional, can be null
                'unit'            => 'required|string',             // Required
                'have_exp'        => 'sometimes|nullable|string',   // Optional, can be null
                'initial_stock'   => 'sometimes|nullable|int',      // Optional, can be null, integer
                'reff'            => 'required|string',             // Required
            ]);
         
            $requestModule=[];
            if (isset($request['item_group']) && $request['item_group']!='' ) {$requestModule['item_group'] = $request['item_group'];}
            if (isset($request['brand']) && $request['brand']!='' ) {$requestModule['brand'] = $request['brand'];}
            if (isset($request['code']) && $request['code']!='' ) {$requestModule['code'] = $request['code'];}
            if (isset($request['items']) && $request['items']!='' ) {$requestModule['items'] = $request['items'];}
            if (isset($request['description']) && $request['description']!='' ) {$requestModule['description'] = $request['description'];}
            if (isset($request['unit']) && $request['unit']!='' ) {$requestModule['unit'] = $request['unit'];}
            if (isset($request['have_exp']) && $request['have_exp']!='' ) {$requestModule['have_exp'] = $request['have_exp'];}
            if (isset($request['initial_stock']) && $request['initial_stock']!='' ) {$requestModule['initial_stock'] = $request['initial_stock'];}
           
            $result=[];
            // insert class stock
            $classDB = new Class_Stock();
            $resultClassDB = $classDB->insert($requestModule);
            if(!$resultClassDB['success'])
            {
                DB::rollBack();
                return $resultClassDB;
            }
            $result['insert_stock'] = $resultClassDB['data'];
      
            // insert class transaction if have initial stock
            if (isset($request['initial_stock']) && $request['initial_stock']!='' && $request['initial_stock']!='0' )
            {
                $requestClassDB=[];
                if (isset($request['item_group']) && $request['item_group']!='' ) {$requestClassDB['item_group'] = $request['item_group'];}
                if (isset($request['brand']) && $request['brand']!='' ) {$requestClassDB['brand'] = $request['brand'];}
                if (isset($request['code']) && $request['code']!='' ) {$requestClassDB['code'] = $request['code'];}
                if (isset($request['items']) && $request['items']!='' ) {$requestClassDB['items'] = $request['items'];}
                if (isset($request['description']) && $request['description']!='' ) {$requestClassDB['description'] = $request['description'];}
                $requestClassDB['type_transaction'] = '1';
                $requestClassDB['in'] = $request['initial_stock'];
                $requestClassDB['out'] =0;
                $requestClassDB['no_transaction'] = '-';
                $requestClassDB['qty'] = $request['initial_stock'];
                $requestClassDB['origin_of_goods'] = 'Initial Stock';
                $requestClassDB['date'] = Carbon::now()->format('Y-m-d');
                $requestClassDB['years'] = Carbon::now()->format('Y');
             
                $classDB = new Class_StockTransaction();
                $resultClassDBStockTransaction = $classDB->insert($requestClassDB);
                if(!$resultClassDBStockTransaction['success'])
                {
                    DB::rollBack();
                    return $resultClassDBStockTransaction;
                }
                $result['insert_stockTransaction'] = $resultClassDBStockTransaction['data'];
            }
            
            $requestClassDB = [];
            $requestClassDB['id_item'] = $resultClassDB['data']->id;
            $requestClassDB['reff'] = $request['reff'];
            $requestClassDB['activity'] = 'Insert Stock';
            $requestClassDB['detail_act'] = json_encode($requestModule);
            $requestClassDB['years'] = Carbon::now()->format('Y');
            // insert class stock log
            $classDB = new Class_StockLog();
            $resultClassDB = $classDB->insert($requestClassDB);
            $result['insert_stockLog'] = $resultClassDB['data'];

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
            $requestModule['class'] = 'Stock';
            $requestModule['function'] = 'insertStock';
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

    public function updateStock($request)
    {
        try
        {
            DB::beginTransaction();
            // Validate the incoming request data
            $request->validate([
                'id'              => 'required|string',             // Required
                'item_group'      => 'sometimes|nullable|string',   // Optional, can be null
                'brand'           => 'sometimes|nullable|string',   // Optional, can be null
                'code'            => 'required|string',             // Required
                'brand'           => 'sometimes|nullable|string',   // Optional, can be null
                'description'     => 'sometimes|nullable|string',   // Optional, can be null
                'unit'            => 'sometimes|nullable|string',   // Optional, can be null
                'have_exp'        => 'sometimes|nullable|string',   // Optional, can be null
                'initial_stock'   => 'sometimes|nullable|string',   // Optional, can be null
                'reff'            => 'required|string',             // Required
            ]);

            $requestModule=[];
            if (isset($request['id']) && $request['id']!='' ) {$requestModule['id'] = $request['id'];}
            if (isset($request['item_group']) && $request['item_group']!='' ) {$requestModule['item_group'] = $request['item_group'];}
            if (isset($request['brand']) && $request['brand']!='' ) {$requestModule['brand'] = $request['brand'];}
            if (isset($request['code']) && $request['code']!='' ) {$requestModule['code'] = $request['code'];}
            if (isset($request['items']) && $request['items']!='' ) {$requestModule['items'] = $request['items'];}
            if (isset($request['description']) && $request['description']!='' ) {$requestModule['description'] = $request['description'];}
            if (isset($request['unit']) && $request['unit']!='' ) {$requestModule['unit'] = $request['unit'];}
            if (isset($request['initial_stock']) && $request['initial_stock']!='' ) {$requestModule['initial_stock'] = $request['initial_stock'];}
            if (isset($request['have_exp']) && $request['have_exp']!='' ) {$requestModule['have_exp'] = $request['have_exp'];}

            $result=[];
            $classModel = new Class_Stock();
            $resultClassDB = $classModel->update($requestModule);

            $result['update_stock'] = $resultClassDB['data'];
            if(!$resultClassDB['success'])
            {
                DB::rollBack();
                return $resultClassDB;
            }
 
            $requestClassDB = [];
            $requestClassDB['id_item'] = $request['id'];
            $requestClassDB['reff'] = $request['reff'];
            $requestClassDB['activity'] = 'Update Stock';
            $requestClassDB['detail_act'] = json_encode($requestModule);
            $requestClassDB['years'] = Carbon::now()->format('Y');
            $classDB = new Class_StockLog();
            $resultClassDB = $classDB->insert($requestClassDB);
            $result['insert_stockLog'] = $resultClassDB['data'];

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
            $requestModule['class'] = 'Stock';
            $requestModule['function'] = 'updateStock';
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

    public function updateStockFromTransaction($request)
    {
        try
        {
            $idItem = ''; $code ='';
            if (isset($request['id_item']) && $request['id_item']!='' ) {$idItem = $request['id_item'];}
            if (isset($request['code']) && $request['code']!='' ) {$code= $request['code'];}

            // cek stock transaction in
            $stockIn = DB::table('stock_transaction')
            ->select(DB::raw('sum(`in`) as total'))
            ->where('code', $code)
            ->groupBy('code')
            ->first();

            // cek stock transaction Out
            $stockOut = DB::table('stock_transaction')
            ->select(DB::raw('sum(`out`) as total'))
            ->where('code', $code)
            ->groupBy('code')
            ->first();
            // final_stock = initial_stock + stock_in - stock_out
            $finalStock = 0;
            $finalStock = $stockIn->total - $stockOut->total;
            // update stock
            $requestClassDB=[];
            $requestClassDB['id'] = $idItem;
            $requestClassDB['stock_in'] = $stockIn->total;
            $requestClassDB['stock_out'] = $stockOut->total;
            $requestClassDB['final_stock'] = $finalStock;
            $classDB = new Class_Stock();
            $requestClassDB = $classDB->update($requestClassDB);

            return [
                'success' => true,
                'message' => 'Successfuly Update Stock',
                'data' => $requestClassDB
            ];
        } catch (\Exception $ex) {
            # Insert Log Error
            $requestModule=[];
            $requestModule['reff'] = '-';
            $requestModule['service'] = 'Model';
            $requestModule['class'] = 'Stock';
            $requestModule['function'] = 'updateStockFromTransaction';
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
