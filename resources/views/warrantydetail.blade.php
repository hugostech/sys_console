@extends('master')

@section('mainContent')
    <div class="col-md-12">
        <h2>{{$supplier->supplier}}</h2>
        <hr>
    </div>
    <div class="col-md-12">
       	{!! $supplier->detail !!}
    </div>
@endsection
