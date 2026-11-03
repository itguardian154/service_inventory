<?php

namespace App\Jobs;

use App\Http\Controllers\API\API_Service;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;
use App\Models\stock;

class SendMinimalStockWhatsappJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $stockId;

    public $tries = 3;

    public $timeout = 120;

    public function __construct($stockId)
    {
        $this->stockId = $stockId;
    }

    public function handle(API_Service $notification)
    {
        Log::info('[MINIMAL STOCK JOB] Job started', [
            'job_id'   => optional($this->job)->getJobId(),
            'stock_id' => $this->stockId,
            'attempt'  => $this->attempts(),
        ]);

        try {

            $stock = Stock::find($this->stockId);

            if (!$stock) {
                Log::warning('[MINIMAL STOCK JOB] Stock not found', [
                    'stock_id' => $this->stockId,
                ]);

                return;
            }

            // $message = "*PERINGATAN MINIMAL STOCK*\n\n";
            // $message .= "Item : {$stock->items}\n";
            // $message .= "Kode : {$stock->code}\n";
            // $message .= "Stock Saat Ini : {$stock->final_stock}\n";
            // $message .= "Minimal Stock : {$stock->minimal_stock}\n\n";
            // $message .= "Mohon segera lakukan pembelian atau transfer stock.";

            $message = "*MINIMAL STOCK*\n\n";
            $message .= "Dear Bapak/Ibu PIC Warehouse,\n\n";
            $message .= "Stock item telah mencapai atau berada di bawah batas minimal dengan data sebagai berikut:\n\n";
            $message .= "Item : {$stock->items}\n";
            $message .= "Kode Item : {$stock->code}\n";
            $message .= "Stock Saat Ini : {$stock->final_stock} {$stock->unit}\n";
            $message .= "Minimal Stock : {$stock->minimal_stock} {$stock->unit}\n\n";
            $message .= "Mohon untuk dapat melakukan pengecekan dan segera melakukan pembelian stock apabila diperlukan.\n\n";
            $message .= "Matur Nuwun.\n\n";
            $message .= "[sent by Bot Loka]";

            $payload = [
                'recipient_phone' => '081230907899',
                'recipient_name'  => 'Warehouse',
                'title'           => 'Minimal Stock',
                'message'         => $message,
            ];

            Log::info('[MINIMAL STOCK JOB] Sending WhatsApp', [
                'stock_id'    => $stock->id,
                'code'        => $stock->code,
                'final_stock' => $stock->final_stock,
                'attempt'     => $this->attempts(),
            ]);

            $response = $notification->sentWhatsapp($payload);

            Log::info('[MINIMAL STOCK JOB] WhatsApp sent successfully', [
                'stock_id' => $stock->id,
                'code'     => $stock->code,
                'response' => $response,
            ]);

        } catch (Throwable $e) {

            Log::error('[MINIMAL STOCK JOB] Error processing job', [
                'stock_id' => $this->stockId,
                'attempt'  => $this->attempts(),
                'message'  => $e->getMessage(),
                'file'     => $e->getFile(),
                'line'     => $e->getLine(),
            ]);

            throw $e;
        }
    }

    public function failed(Throwable $exception)
    {
        Log::critical('[MINIMAL STOCK JOB] Job permanently failed', [
            'stock_id' => $this->stockId,
            'message'  => $exception->getMessage(),
        ]);
    }
}