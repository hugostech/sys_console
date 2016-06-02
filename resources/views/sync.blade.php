@extends('master')
@section('sync_percentage')
    {{--{{$percentage}}--}}
@endsection

@section('mainContent')
    {{--{{$content}}--}}
    <div class="col-md-12">
        <h3>Sync system</h3>
        {!! Form::open(['url'=>'/sync','id'=>'sync_form']) !!}
        <div class="form-group">
            {!! Form::checkbox('quantity','sync_quantity') !!} Sync product quantity between Roctech and Extremepc &nbsp;&nbsp;&nbsp;<br>
            {!! Form::checkbox('disable','disable_unfound_code') !!} Disable the products which unfound code &nbsp;&nbsp;&nbsp;<br>
            {!! Form::checkbox('match','disable_unfound_code') !!} Match pricespy price &nbsp;&nbsp;&nbsp;<br>
            {{--{!! Form::checkbox('self','self_check') !!} Self_check--}}
        </div>

        <div class="form-group">
            {!! Form::label('status','Product Type:') !!}<br>
            {!! Form::radio('status','1',true) !!} All  &nbsp;&nbsp;
            {!! Form::radio('status','2') !!} Enable
        </div>
        <div class="form-group">
            {{--{!! Form::submit('Sync',['class'=>'btn btn-success','onclick'=>'showLoading()']) !!}--}}
            @if(!empty($result))
                <button type="button" class="btn btn-success" onclick="showLoading()">Finish</button>
            @else
                <button type="button" class="btn btn-primary" onclick="showLoading()">Sync</button>
            @endif

            <span id="sync_loading" style="visibility: hidden"><img src="{{url('/image',['loading.gif'])}}" alt="Loading..." width="20px;"></span>

        </div>

        {!! Form::close() !!}
    </div>
    <div class="col-md-12">
        @if(!empty($result))
            <div class="col-sm-12">
                <label>Total scan products: </label>&nbsp;{{$result['total']}}

            </div>
            <div class="col-sm-12">
                <label>Code missing products: </label>&nbsp;<font color="red">{{$result['int']}}</font>
            </div>
            <div class="col-md-12">
                @foreach($result['unfound'] as $product)
                    <div class="col-md-1" style="border: solid silver 1px">{{$product}}</div>
                @endforeach
            </div>
        @endif
    </div>
@endsection