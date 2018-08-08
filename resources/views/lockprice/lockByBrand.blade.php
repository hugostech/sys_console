@extends('master')

@section('mainContent')
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3>Lock by brand</h3>
        </div>
        <div class="panel-body">
            {!! Form::open(['route'=>'lockbybrand_update']) !!}
            <div class="form-group col-md-3">
                {!! Form::select('brand',$manufacturers,null,['class'=>'form-control','required','placeholder'=>'select the brand you want to lock']) !!}
            </div>
            <div class="form-group col-md-3">
                {!! Form::select('status',[0=>'Unlock',1=>'Lock'],1,['class'=>'form-control','required']) !!}
            </div>
            <div class="form-group col-md-6">
                {!! Form::submit('Run',['class'=>'btn btn-primary','onclick'=>'return confirm("Are you sure?")']) !!}
            </div>
            {!! Form::close() !!}
        </div>
    </div>
@endsection