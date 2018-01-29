@extends('master')

@section('mainContent')
<table class="table table-bordered" id="lockPrice_table">
    <thead>
    <tr>
        <th>#</th>
        <th>Code</th>
        <th>Name</th>
        <th>Quantity</th>
        <th>Price</th>
        <th>Special</th>
        <th></th>
    </tr>
    </thead>
    <tbody>
    @foreach($products as $key=>$product)
        <tr>
            <td>{{$key+1}}</td>
            <td>{{$product->model}}</td>
            <td>{{$product->description->name}}</td>
            <td>{{$product->quantity}}</td>
            <td>{{round($product->price*1.15,2)}}</td>
            <td>{{is_null($product->special)?0:round($product->special->price*1.15,2)}}</td>
            <td><a href="{{url('exproduct',[$product->product_id,'priceunlock'])}}?r=true" class="btn btn-danger btn-sm">Unlock</a></td>
        </tr>
    @endforeach
    </tbody>
</table>
@endsection

@section('footer')
    <script>
        $(document).ready( function () {
            $('#lockPrice_table').DataTable();
        });
    </script>

@endsection