<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateStockTransactionPrice extends Command
{
    /**
     * Nama dan signature command.
     *
     * @var string
     */
    protected $signature = 'stock:update-price 
                            {--code= : Update hanya untuk kode barang tertentu} 
                            {--start_date= : Filter tanggal mulai} 
                            {--end_date= : Filter tanggal akhir}';

    /**
     * Deskripsi command.
     *
     * @var string
     */
    protected $description = 'Update kolom price & total_price di stock_transaction. 
                              Jika no_transaction diawali "RCV" ambil dari receive_order_detail, 
                              jika "SRQ" ambil dari store_request_detail.';

    /**
     * Jalankan command.
     */
    public function handle()
    {
        $code      = $this->option('code');
        $startDate = $this->option('start_date');
        $endDate   = $this->option('end_date');

        $this->info("🔄 Proses update stock_transaction.price & total_price (RCV → receive_order_detail, SRQ → store_request_detail)...");

        // Ambil semua stock_transaction sesuai filter
        $query = DB::table('stock_transaction')
            ->select('id', 'code', 'no_transaction', 'date');

        if ($code) {
            $query->where('code', $code);
        }

        if ($startDate && $endDate) {
            $query->whereBetween('date', [$startDate, $endDate]);
        }

        $transactions = $query->get();

        $bar = $this->output->createProgressBar(count($transactions));
        $bar->start();

        foreach ($transactions as $st) {
            $price = null;
            $subTotal = null;

            if (str_starts_with($st->no_transaction, 'RCV')) {
                // Join ke receive_order_detail
                $detail = DB::table('receive_order_detail')
                    ->where('no_transaction', $st->no_transaction)
                    ->where('code_item', $st->code)
                    ->select('price', 'sub_total')
                    ->first();

                if ($detail) {
                    $price = $detail->price;
                    $subTotal = $detail->sub_total;
                }
            } elseif (str_starts_with($st->no_transaction, 'SRQ')) {
                // Join ke store_request_detail
                $detail = DB::table('store_request_detail')
                    ->where('no_transaction', $st->no_transaction)
                    ->where('code_item', $st->code)
                    ->select('price', 'sub_total')
                    ->first();

                if ($detail) {
                    $price = $detail->price;
                    $subTotal = $detail->sub_total;
                }
            }

            if (!is_null($price) && !is_null($subTotal)) {
                DB::table('stock_transaction')
                    ->where('id', $st->id)
                    ->update([
                        'price'       => $price,
                        'total_price' => $subTotal,
                    ]);
            }

            $bar->advance();
        }

        $bar->finish();

        $this->newLine(2);
        $this->info("✅ Update selesai. Total diperbarui: " . count($transactions));
    }
}
