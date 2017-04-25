@extends('master')

@section('mainContent')
    <div class="col-md-12">
        {!! Form::open(['url'=>'startKillPrice']) !!}
        @if(is_null($product))
        <div class="form-group">
            <label>Code</label>
            {!! Form::text('code',null,['class'=>'form-control']) !!}
        </div>
        @else
        <div class="form-group">
            {!! Form::input('hidden','product_id',$product->product_id) !!}
            {{$product->description->name}}
        </div>
        @endif
        <div class="form-group">
            <label>Url</label>
            {!! Form::text('pricespy_url',null,['class'=>'form-control']) !!}
        </div>

        <div class="form-group">
            {!! Form::submit('Next',['class'=>'btn btn-primary']) !!}
        </div>
        {!! Form::close() !!}
    </div>

@endsection