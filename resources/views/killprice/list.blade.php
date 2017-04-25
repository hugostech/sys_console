@extends('master')

@section('mainContent')
    <div class="col-md-12">
        <table class="table table-bordered ">
            <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Model</th>
                <th>Bottom Price</th>
                <th>Note</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            @foreach($products as $key=>$product)
                <tr>
                    <td>{{$key + 1}}</td>
                    <td>{{dd(\App\Ex_product::find($product->product_id))->description->name}}</td>
                    <td>{{$product->model}}</td>
                    <td>{{$product->bottomPrice}}</td>
                    <td>{{$product->note}}</td>
                    <td><button type="button" class="btn btn-danger btn-sm"><span class="glyphicon glyphicon-trash"></span></button></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection