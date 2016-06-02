@extends('master')

@section('mainContent')
    <h3>Normal</h3>
    <hr>
    @foreach($usingSuppliers as $supplier)
        <a href="{{url('/',['supplier',$supplier->id])}}"><button type="button" class="btn btn-success">{{$supplier->name}}</button></a>
    @endforeach
    <hr>
    <h3>Disable</h3>
    @foreach($disableSuppliers as $supplier)
        <a href="{{url('/',['supplier',$supplier->id])}}"><button type="button" class="btn btn-danger">{{$supplier->name}}</button></a>
    @endforeach
    <hr>

@stop
