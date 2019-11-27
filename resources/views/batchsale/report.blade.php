<label>{{count($list)}}</label>
<table border="1">
    <tr>
        <th>Model</th>
        <th>Name</th>
        <th>Base</th>
        <th>Price</th>
        <th>Cost</th>
    </tr>
    @foreach($list as $item)
        <tr>
            <td>{{$item['model']}}</td>
            <td>{{$item['name']}}</td>
            <td>{{$item['base']}}</td>
            <td>{{$item['price']}}</td>
            <td>{{$item['cost']}}</td>
        </tr>
    @endforeach
</table>