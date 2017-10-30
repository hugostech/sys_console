@extends('master')

@section('mainContent')
    
    <div class="col-md-12 text-left">
        <h3>{{$category->name}}</h3>
    </div>
    <div class="col-md-12">
        @foreach($category->suppliers as $supplier)

            <div class="col-md-3"><a class="btn btn-block btn-primary" href="{{url('/warrantydetail',[$supplier->id])}}">{{$supplier->supplier}}</a></div>
        @endforeach

    </div>
    
@endsection