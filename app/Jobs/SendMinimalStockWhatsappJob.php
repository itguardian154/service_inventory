<?php

namespace App\Jobs;

use App\Models\Stock;
use App\Http\Controllers\API\API_Service;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendMinimalStockWhatsappJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $stock;

    public function __construct(Stock $stock)
    {
        $this->stock = $stock;
    }

    public function handle(API_Service $notification)
    {
        // Log::info('JOB MINIMAL STOCK BERJALAN');

        $message = "*PERINGATAN MINIMAL STOCK*\n\n";
        $message .= "Item : {$this->stock->items}\n";
        $message .= "Kode : {$this->stock->code}\n";
        $message .= "Stock Saat Ini : {$this->stock->final_stock}\n";
        $message .= "Minimal Stock : {$this->stock->minimal_stock}\n\n";
        $message .= "Mohon segera lakukan pembelian atau transfer stock.";

        $notification->sentWhatsapp([
            'recipient_phone' => '085941304991', // nomor PIC
            'recipient_name'  => 'Warehouse',
            'title'           => 'Minimal Stock',
            'message'         => $message,
        ]);
    }
}