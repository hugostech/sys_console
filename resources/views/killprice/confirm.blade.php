@extends('master')

@section('mainContent')

    <div class="col-md-12">
        {!! Form::open(['url'=>'killpriceConfirm']) !!}
            {!! Form::input('hidden','product_id',$product->product_id) !!}
            {!! Form::input('hidden','model',$product->model) !!}
            {!! Form::input('hidden','url',$url) !!}
            {!! Form::input('hidden','average_cost',$averageCost) !!}
            <div class="form-group">
                <h3>{{$product->description->name}}</h3>
                <label class="text-info">PriceSpy: {{$product_name}}</label>
            </div>
            <div class="form-group">
                {!! $product_detail !!}
            </div>
            <div class="form-group">
                <label>Bottom Price(inc GST)</label>
                {!! Form::input('number','bottomPrice',$averageCost,['class'=>'form-control','step'=>'0.01']) !!}
            </div>
            <div class="form-group">
                <label></label>
            </div>
            <table class="table table-bordered">
                <thead>
                <tr>
                    <td></td>
                    <td>Company</td>
                    <td>Price</td>
                </tr>
                </thead>
                <tbody>
                @foreach($priceList as $item)
                <tr>
                    <td>
                        @if(trim($item[0]) != 'ExtremePC' and trim($item[0])!='Ktech')
                            {!! Form::checkbox('companies[]',$item[0]) !!}
                        @endif
                    </td>
                    <td>{{$item[0]}}</td>
                    <td>{{$item[1]}}</td>
                </tr>
                @endforeach

                </tbody>
            </table>
            <div class="form-group">
                {!! Form::submit('Confirm') !!}
            </div>
        {!! Form::close() !!}
    </div>

@endsection