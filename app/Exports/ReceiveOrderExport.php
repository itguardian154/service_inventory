<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ReceiveOrderExport implements FromCollection, WithHeadings
{
    protected $dateFrom;
    protected $dateTo;

    public function __construct($dateFrom, $dateTo)
    {
        $this->dateFrom = $dateFrom;
        $this->dateTo   = $dateTo;
    }

    public function collection()
    {
        return DB::table('receive_order as ro')
            ->leftJoin('receive_order_detail as rod', 'ro.no_transaction', '=', 'rod.no_transaction')
            ->select(
                'ro.no_transaction',
                'ro.date_transaction',
                'ro.departemen',
                'ro.sub_departemen',
                'ro.type_transaction',
                'ro.supplier',
                'ro.no_po',
                'ro.no_invoice',
                'ro.total',
                'ro.status',

                'rod.item_group',
                'rod.brand',
                'rod.code_item',
                'rod.items',
                'rod.description',
                'rod.unit',
                'rod.qty',
                'rod.price',
                'rod.sub_total',
                'rod.expired_date'
            )
            ->whereBetween('ro.date_transaction', [
                $this->dateFrom,
                $this->dateTo
            ])
            ->orderBy('ro.date_transaction')
            ->get();
    }

    public function headings(): array
    {
        return [
            'No Transaction',
            'Date Transaction',
            'Departemen',
            'Sub Departemen',
            'Type Transaction',
            'Supplier',
            'No PO',
            'No Invoice',
            'Total',
            'Status',

            'Item Group',
            'Brand',
            'Code Item',
            'Item',
            'Description',
            'Unit',
            'Qty',
            'Price',
            'Sub Total',
            'Expired Date',
        ];
    }
}