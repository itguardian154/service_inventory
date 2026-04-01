
<table>
    <thead>
        <tr class="text-center">
            <th >NO</th>
            <th >CODE</th>
            <th >ITEMS</th>
            <th >ITEM GROUP</th>
            <th >UNIT</th>
            <th >BEGINNING QTY</th>
            <th >BEGINNING AMOUNT</th>
            <th >IN QTY</th>
            <th >IN AMOUNT</th>
            <th >OUT QTY</th>
            <th >OUT AMOUNT</th>
            <th >END QTY</th>
            <th >END AMOUNT</th>
        </tr>
        </thead>
        <tbody>
        @php($i=1)

        @foreach($stock as $d)
        <tr>
                <td>{{ $i }}</td>
                <td>{{ $d->code }}</td>
                <td>{{ $d->items }}</td>
                <td>{{ $d->item_group }}</td>
                <td>{{ $d->unit }}</td>  
                <td>{{ $d->initial_stock }}</td>
                <td>{{ $d->initial_amount }}</td>
                <td>{{ $d->stock_in }}</td>
                <td>{{ $d->in_amount }}</td>
                <td>{{ $d->stock_out }}</td>
                <td>{{ $d->out_amount }}</td>
                <td>{{ $d->final_stock }}</td>
                <td>{{ $d->final_amount }}</td>
            </tr>
        @php($i++)  
        @endforeach
    </tbody>
</table>