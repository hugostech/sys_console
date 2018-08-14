<table>
    <tr>
        <th>Model</th>
        <th>Base</th>
        <th>Price</th>
        <th>Cost</th>
    </tr>
    @foreach($list as $item)
        <tr>
            <td>{{$item['model']}}</td>
            <td>{{$item['base']}}</td>
            <td>{{$item['price']}}</td>
            <td>{{$item['cost']}}</td>
        </tr>
    @endforeach
</table>