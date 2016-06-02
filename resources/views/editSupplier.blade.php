@extends('master')

@section('mainContent')
    <div class="col-md-2"></div>
    <div class="col-md-8">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">New Supplier</h3>
            </div>
            <div class="panel-body">
                {!! Form::model($supplier,['url'=>'supplier/'.$supplier->id,'files'=>'true','method'=>'put']) !!}
                <div class="form-group">
                    {!! Form::label('name','Name:',['class'=>'sr-only']) !!}
                    {!! Form::text('name',null,['class'=>'form-control']) !!}
                </div>
                <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                    <div class="panel panel-default">
                        <div class="panel-heading" role="tab" id="headingOne">
                            <h4 class="panel-title">
                                <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true"
                                   aria-controls="collapseOne" onclick="detemineType('doc')">
                                    Local Document
                                </a>
                            </h4>
                        </div>
                        <div id="collapseOne" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
                            <div class="panel-body">
                                {!! Form::file('doc') !!}
                            </div>
                        </div>
                    </div>
                    <div class="panel panel-default">
                        <div class="panel-heading" role="tab" id="headingTwo">
                            <h4 class="panel-title">
                                <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="false"
                                   aria-controls="collapseTwo" onclick="detemineType('url')">
                                    URL
                                </a>
                            </h4>
                        </div>
                        <div id="collapseTwo" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">
                            <div class="panel-body">
                                {!! Form::text('url',null,['class'=>'form-control','placeholder'=>'Url...']) !!}
                            </div>
                        </div>
                    </div>
                    {{Form::input('hidden','type','doc',['id'=>'type'])}}
                    <hr>
			<div class="form-group">
                        {!! Form::label('disable','disable: ') !!}
                        <input type="checkbox" value="y" name="disable" id="disable">
                    </div>
                    <div class="form-group">

                        {!! Form::label('update','save',['class'=>'sr-only']) !!}
                        {{Form::submit('update',['class'=>'btn btn-block btn-primary'])}}
                    </div>


                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
    <div class="col-md-2"></div>



@stop

