<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Class_DB\Class_ReceiveOrder;
use App\Http\Controllers\Class_DB\Class_ReceiveOrderDetail;
use App\Http\Controllers\Class_DB\Class_StoreRequest;
use App\Http\Controllers\Class_DB\Class_StoreRequestDetail;

use Illuminate\Support\Facades\DB;

class SyncStockTransaction extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:syncStockTransaction';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Sync Stock Transaction Price and Total Price'; 

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try
        {
            $StockTrn = DB::table('stock_transaction')
            ->select('no_transaction','code')
            ->where('price',null)
            ->orderBy('id','desc')
            ->get();
          
            foreach ($StockTrn as $key => $value) {
                $noTransaction=$value->no_transaction;
                $code=$value->code;
          
                $typeGroup = substr($noTransaction,0,7);
         
                if($typeGroup=='RCV-PIP')
                {
                  
                    // table receive order detail
                    $getOrderDetail = DB::table('receive_order_detail')
                    ->select('price','sub_total')
                    ->where('no_transaction',$noTransaction)
                    ->where('code_item',$code)
                    ->get();

                    if($getOrderDetail->isEmpty())
                    {
                        
                    }
                    else
                    {
                        foreach ($getOrderDetail as $key => $value) {
                            $price = $value->price;
                            $subTotal = $value->sub_total;
                    
                            // update table stock_transaction
                            DB::table('stock_transaction')
                            ->where('no_transaction',$noTransaction)
                            ->where('code',$code)
                            ->update([
                                'price' => $price,
                                'total_price' => $subTotal,
                    
                            ]);
                        }
                    }
                }
                if($typeGroup=='SRQ-PIP')
                {
                    // table store request detail
                    $getOrderDetail = DB::table('store_request_detail')
                    ->select('price','sub_total')
                    ->where('no_transaction',$noTransaction)
                    ->where('code_item',$code)
                    ->get();
                    if($getOrderDetail->isEmpty())
                    {
                        
                    }
                    else
                    {
                        foreach ($getOrderDetail as $key => $value) {
                            $price = $value->price;
                            $subTotal = $value->sub_total;
                        
                            // update table stock_transaction
                            DB::table('stock_transaction')
                            ->where('no_transaction',$noTransaction)
                            ->where('code',$code)
                            ->update([
                                'price' => $price,
                                'total_price' => $subTotal,
                            ]);
                        }
                    }
                }
            }
            return 'success';
        }
        catch (\Exception $e)
        {
            dd($e);
            return $e->getMessage();
        }
    }
}
