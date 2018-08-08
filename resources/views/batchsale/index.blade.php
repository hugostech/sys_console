@extends('master')

@section('mainContent')
    <div class="panel panel-default">
        @if(\Illuminate\Support\Facades\Session::has('status'))
            <div class="alert alert-success alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <strong>Success!</strong> {{\Illuminate\Support\Facades\Session::get('status')}}
            </div>
        @endif
        <div class="panel-heading">
            <h3>Storewide Sale</h3>
        </div>
        <div class="panel-body">
            <div class="row">
                {!! Form::open(['route'=>'batchsale_report']) !!}
                <div class="form-group col-md-2">
                    <label>Target Percentage</label>
                    <div class="input-group">
                        {!! Form::number('target_percentage',old('target_percentage', null),['class'=>'form-control','required','min'=>0,'max'=>100]) !!}
                        <span class="input-group-addon">%</span>
                    </div>
                </div>
                <div class="form-group col-md-2">
                    <label>Pretty Price</label>
                    {!! Form::select('pretty_price',[0=>'No',1=>'Yes'],old('pretty_price', 1),['class'=>'form-control']) !!}
                </div>
                <div class="form-group col-md-2">
                    <label>Margin Rate</label>
                    <div class="input-group">
                        {!! Form::number('margin_rate',old('margin_rate', null),['class'=>'form-control','required','min'=>0,'max'=>100]) !!}
                        <span class="input-group-addon">%</span>
                    </div>
                </div>
                <div class="form-group col-md-2">
                    <label>Base Price Changeable</label>
                    {!! Form::select('base_changeable',[0=>'No',1=>'Yes'],old('base_changeable', 0),['class'=>'form-control']) !!}
                </div>
                <div class="form-group col-md-2">
                    <label>Products with Stock</label>
                    {!! Form::select('with_stock',[0=>'No',1=>'Yes'],old('with_stock', 1),['class'=>'form-control']) !!}
                </div>
                <div class="form-group col-md-2">
                    <label>Test</label>
                    {!! Form::select('test',[0=>'No',1=>'Yes'],old('test', 1),['class'=>'form-control']) !!}
                </div>
                <div class="form-group col-md-2">
                    <label> </label>
                    {!! Form::submit('Run',['class'=>'btn btn-primary btn-block','onclick'=>"return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </div>

        </div>
    </div>
@endsection