<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use Carbon\Carbon;

class Export_InventoryValuationReport implements FromView, WithColumnWidths, WithColumnFormatting
{
   protected $param = [];

    function __construct($param) 
    {  
      
        $this->param = $param;
        ini_set('memory_limit', '512M'); // Meningkatkan limit memori
        set_time_limit(300); // Meningkatkan batas eksekusi skrip
    }

    public function view(): View
    {
        $date_start = $request['date_start'] ?? Carbon::now()->startOfMonth()->toDateString();
        $date_end = $request['date_end'] ?? Carbon::now()->endOfMonth()->toDateString();
        $code = $request['code'] ?? '';
        $export_type = $request['export_type'] ?? 'all_stock'; // Default: all_stock
        $item_group = $request['item_group'] ?? null;
        $have_exp = $request['have_exp'] ?? null;

        // Normalisasi export_type
        if ($export_type == 'all_stock') $export_type = 'all_stock';
        if ($export_type == '0') $export_type = 'not_have_stock';
        if ($export_type == '1') $export_type = 'have_stock';

        // 1. Ambil previous stock (before date_start)
        $prevStock = DB::table('stock_transaction')
            ->select('code', DB::raw('SUM(`in`) - SUM(`out`) AS initial_stock'))
            ->where('date', '<', $date_start)
            ->groupBy('code')
            ->get()
            ->keyBy('code');

        // 1.1 Ambil nilai stok awal (harga)
        $prevStockPrice = DB::table('stock_transaction')
            ->select('code',
                DB::raw('SUM(CASE WHEN `in` > 0 THEN `in` * price ELSE 0 END) - SUM(CASE WHEN `out` > 0 THEN `out` * price ELSE 0 END) AS price_initial')
            )
            ->where('date', '<', $date_start)
            ->groupBy('code')
            ->get()
            ->keyBy('code');

        // 2. Ambil transaksi stok dalam periode
        $transactions = DB::table('stock_transaction')
            ->select('code',
                DB::raw('SUM(`in`) AS stock_in'),
                DB::raw('SUM(`out`) AS stock_out')
            )
            ->whereBetween('date', [$date_start, $date_end])
            ->groupBy('code')
            ->get()
            ->keyBy('code');

        // 2.1 Ambil nilai transaksi stok dalam periode (harga)
        $priceTransactions = DB::table('stock_transaction')
            ->select('code',
                DB::raw('SUM(CASE WHEN `in` > 0 THEN `in` * price ELSE 0 END) AS price_in'),
                DB::raw('SUM(CASE WHEN `out` > 0 THEN `out` * price ELSE 0 END) AS price_out')
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
        $stockQuery->chunk(500, function ($stocks) use (&$data, $prevStock, $prevStockPrice, $transactions, $priceTransactions, $export_type) {
            foreach ($stocks as $item) {
                $initial = $prevStock[$item->code]->initial_stock ?? 0;
                $stock_in = $transactions[$item->code]->stock_in ?? 0;
                $stock_out = $transactions[$item->code]->stock_out ?? 0;
                $final_stock = $initial + $stock_in - $stock_out;

                $price_initial = $prevStockPrice[$item->code]->price_initial ?? 0;
                $price_in = $priceTransactions[$item->code]->price_in ?? 0;
                $price_out = $priceTransactions[$item->code]->price_out ?? 0;
                $price_final = $price_initial + $price_in - $price_out;

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

                    // Stok
                    'initial_stock' => $initial,
                    'stock_in' => $stock_in,
                    'stock_out' => $stock_out,
                    'final_stock' => $final_stock,

                    // Harga total
                    'initial_amount' => $price_initial,
                    'in_amount' => $price_in,
                    'out_amount' => $price_out,
                    'final_amount' => $price_final,
                ]);
            }
        });
        return view('Stock.InventoryValuationReport', $data);
    }
    
    // public function view(): View
    // {
    //     $date_start = $this->param['date_start'] ?? Carbon::now()->startOfMonth();
    //     $date_end = $this->param['date_end'] ?? Carbon::now()->endOfMonth();
    //     $code = $this->param['code'] ?? '';
    //     $export_type = $this->param['export_type'] ?? 'have_stock'; // Default: all_stock
    //     $item_group = $this->param['item_group'] ?? null;
    //     $have_exp = $this->param['have_exp'] ?? null;
       
    //     if($export_type=='0') //tidak memiliki stock
    //     {
    //         $export_type='all_stock';
    //     }
    //     if($export_type=='1') //memiliki stock
    //     {
    //         $export_type='have_stock';
    //     }

    //     $query = DB::table('stock')
    //     // Join transaksi dalam rentang tanggal
    //     ->leftJoin('stock_transaction as st', function ($join) use ($date_start, $date_end) {
    //         $join->on('stock.code', '=', 'st.code')
    //             ->whereBetween('st.date', [$date_start, $date_end]);
    //     })

    //     // Subquery untuk initial_stock dan initial_amount
    //     ->leftJoin(DB::raw("
    //         (SELECT 
    //             code, 
    //             SUM(`in`) - SUM(`out`) AS initial_stock,
    //             SUM(`in` * price) - SUM(`out` * price) AS initial_amount
    //         FROM stock_transaction
    //         WHERE date < '$date_start'
    //         GROUP BY code
    //         ) AS prev_stock
    //     "), 'stock.code', '=', 'prev_stock.code')

    //     // SELECT semua data yang dibutuhkan
    //     ->select([
    //         'stock.id', 'stock.item_group', 'stock.brand', 'stock.code', 'stock.items',
    //         'stock.description', 'stock.unit', 'stock.have_exp',

    //         // Stok awal dan nilai awal
    //         DB::raw('COALESCE(prev_stock.initial_stock, 0) AS initial_stock'),
    //         DB::raw('COALESCE(prev_stock.initial_amount, 0) AS initial_amount'),

    //         // Transaksi selama periode
    //         DB::raw('COALESCE(SUM(CASE WHEN st.`in` > 0 THEN st.`in` ELSE 0 END), 0) AS stock_in'),
    //         DB::raw('COALESCE(SUM(CASE WHEN st.`out` > 0 THEN st.`out` ELSE 0 END), 0) AS stock_out'),

    //         // Nilai transaksi selama periode
    //         DB::raw('COALESCE(SUM(CASE WHEN st.`in` > 0 THEN st.`in` * st.price ELSE 0 END), 0) AS in_amount'),
    //         DB::raw('COALESCE(SUM(CASE WHEN st.`out` > 0 THEN st.`out` * st.price ELSE 0 END), 0) AS out_amount'),

    //         // Final stock dan final amount
    //         DB::raw('
    //             (COALESCE(prev_stock.initial_stock, 0) 
    //             + COALESCE(SUM(CASE WHEN st.`in` > 0 THEN st.`in` ELSE 0 END), 0) 
    //             - COALESCE(SUM(CASE WHEN st.`out` > 0 THEN st.`out` ELSE 0 END), 0)) AS final_stock
    //         '),
    //         DB::raw('
    //             (COALESCE(prev_stock.initial_amount, 0) 
    //             + COALESCE(SUM(CASE WHEN st.`in` > 0 THEN st.`in` * st.price ELSE 0 END), 0) 
    //             - COALESCE(SUM(CASE WHEN st.`out` > 0 THEN st.`out` * st.price ELSE 0 END), 0)) AS final_amount
    //         '),
    //     ])

    //     // Filter berdasarkan kode
    //     ->when(!empty($code), function ($query) use ($code) {
    //         return $query->where('stock.code', $code);
    //     })

    //     // Group by semua field non-agregat
    //     ->groupBy([
    //         'stock.id', 'stock.item_group', 'stock.brand', 'stock.code', 'stock.items',
    //         'stock.description', 'stock.unit', 'stock.have_exp',
    //         'prev_stock.initial_stock', 'prev_stock.initial_amount'
    //     ]);

    //     // Filter tambahan berdasarkan export_type / group / expired
    //     if ($export_type === 'have_stock') {
    //         $query->havingRaw('
    //             COALESCE(prev_stock.initial_stock, 0) > 0 
    //             OR COALESCE(SUM(CASE WHEN st.`in` > 0 THEN st.`in` ELSE 0 END), 0) > 0 
    //             OR COALESCE(SUM(CASE WHEN st.`out` > 0 THEN st.`out` ELSE 0 END), 0) > 0 
    //             OR (
    //                 COALESCE(prev_stock.initial_stock, 0) 
    //                 + COALESCE(SUM(CASE WHEN st.`in` > 0 THEN st.`in` ELSE 0 END), 0) 
    //                 - COALESCE(SUM(CASE WHEN st.`out` > 0 THEN st.`out` ELSE 0 END), 0)
    //             ) > 0
    //         ');
    //     } elseif (isset($item_group)) {
    //         $query->where('stock.item_group', $item_group);
    //     } elseif (isset($have_exp)) {
    //         $query->where('stock.have_exp', $have_exp);
    //     }

    //     // Urutkan
    //     $query->orderBy('stock.items', 'asc');

    //     // Eksekusi dengan chunk
    //     $data['stock'] = collect();
    //     $query->chunk(500, function ($stocks) use (&$data) {
    //         $data['stock'] = $data['stock']->merge($stocks);
    //     });
        
    //     return view('Stock.InventoryValuationReport', $data);
    // }

    public function columnWidths(): array
    {
        return [
            'A' => 4,  'B' => 15, 'C' => 25, 'D' => 20, 'E' => 25, 'F' => 20, 'G' => 15, 'H' => 15,
            'I' => 10, 'J' => 15, 'K' => 40, 'L' => 40, 'M' => 20, 'N' => 20, 'O' => 20, 'P' => 20,
            'Q' => 20, 'R' => 20, 'S' => 20, 'T' => 20, 'U' => 20
        ];
    }

    public function columnFormats(): array
    {
        return array_fill_keys(range('A', 'U'), NumberFormat::FORMAT_TEXT);
    }
}
