<?php

namespace App\Jobs;

use App\Models\Stock;
use App\Http\Controllers\API\API_Service;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class SendMinimalStockWhatsappJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $stock;

    public $tries = 3;

    public $timeout = 120;

    public function __construct(Stock $stock)
    {
        $this->stock = $stock;

        Log::info('[MINIMAL STOCK JOB] Job dispatched', [
            'stock_id'      => $stock->id,
            'code'          => $stock->code,
            'item'          => $stock->items,
            'final_stock'   => $stock->final_stock,
            'minimal_stock' => $stock->minimal_stock,
        ]);
    }

    public function handle(API_Service $notification)
    {
        Log::info('[MINIMAL STOCK JOB] Job started', [
            'job_id'        => optional($this->job)->getJobId(),
            'attempt'       => $this->attempts(),
            'stock_id'      => $this->stock->id,
            'code'          => $this->stock->code,
            'item'          => $this->stock->items,
            'final_stock'   => $this->stock->final_stock,
            'minimal_stock' => $this->stock->minimal_stock,
        ]);

        try {

            $message = "*PERINGATAN MINIMAL STOCK*\n\n";
            $message .= "Item : {$this->stock->items}\n";
            $message .= "Kode : {$this->stock->code}\n";
            $message .= "Stock Saat Ini : {$this->stock->final_stock}\n";
            $message .= "Minimal Stock : {$this->stock->minimal_stock}\n\n";
            $message .= "Mohon segera lakukan pembelian atau transfer stock.";

            $payload = [
                'recipient_phone' => '085941304991',
                'recipient_name'  => 'Warehouse',
                'title'           => 'Minimal Stock',
                'message'         => $message,
            ];

            Log::info('[MINIMAL STOCK JOB] Sending WhatsApp', [
                'job_id'          => optional($this->job)->getJobId(),
                'stock_id'        => $this->stock->id,
                'code'            => $this->stock->code,
                'recipient_phone' => $payload['recipient_phone'],
                'attempt'         => $this->attempts(),
            ]);

            $response = $notification->sentWhatsapp($payload);

            Log::info('[MINIMAL STOCK JOB] WhatsApp sent successfully', [
                'job_id'   => optional($this->job)->getJobId(),
                'stock_id' => $this->stock->id,
                'code'     => $this->stock->code,
                'response' => $response,
            ]);

        } catch (Throwable $e) {

            Log::error('[MINIMAL STOCK JOB] Error processing job', [
                'job_id'    => optional($this->job)->getJobId(),
                'stock_id'  => $this->stock->id ?? null,
                'code'      => $this->stock->code ?? null,
                'attempt'   => $this->attempts(),
                'message'   => $e->getMessage(),
                'file'      => $e->getFile(),
                'line'      => $e->getLine(),
            ]);

            throw $e;
        }
    }

    public function failed(Throwable $exception)
    {
        Log::critical('[MINIMAL STOCK JOB] Job permanently failed', [
            'job_id'        => optional($this->job)->getJobId(),
            'stock_id'      => $this->stock->id ?? null,
            'code'          => $this->stock->code ?? null,
            'final_stock'   => $this->stock->final_stock ?? null,
            'minimal_stock' => $this->stock->minimal_stock ?? null,
            'message'       => $exception->getMessage(),
            'file'          => $exception->getFile(),
            'line'          => $exception->getLine(),
        ]);
    }
}