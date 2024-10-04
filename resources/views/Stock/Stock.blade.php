
<table>
    <thead>
        <tr class="text-center">
            <th >NO</th>
            <th >ID ITEM</th>
            <th >ITEM GROUP</th>
            <th >BRAND</th>
            <th >CODE</th>
            <th >ITEMS</th>
            <th >DESCRIPTION</th>
            <th >UNIT</th>
            <th >HAVE EXP</th>
            <th >INITIAL STOCK</th>
            <th >STOCK IN</th>
            <th >STOCK OUT</th>
            <th >FINAL STOCK</th>
            <th >LAST PRICE</th>
            <th >AVERAGE PRICE</th>
            <th >TOTAL PRICE</th>
        </tr>
        </thead>
        <tbody>
        @php($i=0)

        @foreach($stock as $d)
        <tr>
                <td>{{ $i }}</td>
                <td>{{ $d->id_item }}</td>
                <td>{{ $d->item_group }}</td>
                <td>{{ $d->brand }}</td>
                <td>{{ $d->code }}</td>
                <td>{{ $d->items }}</td>
                <td>{{ $d->description }}</td>
                <td>{{ $d->unit }}</td>
                <td>{{ $d->have_exp }}</td>    
                <td>{{ $d->initial_stock }}</td>
                <td>{{ $d->stock_in }}</td>
                <td>{{ $d->stock_out }}</td>
                <td>{{ $d->final_stock }}</td>
                <td>{{ $d->last_price }}</td>
                <td>{{ $d->average_price }}</td>
                <td>{{ $d->total_price }}</td>
            </tr>
        @php($i++)  
        @endforeach
    </tbody>
</table>