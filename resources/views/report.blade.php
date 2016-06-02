@extends('master')

@section('mainContent')
    {{--<button type="button" class="btn btn-default" onclick="goBack()">Back</button>--}}
    <div class="col-md-2"></div>
    <div class="col-md-8">
        <div class="panel panel-info">

            <div class="panel-heading">
                <h3>#{{$item['warranty']->id}} Warranty Detail</h3>

            </div>

            <div class="panel-body">

                {!! Form::model($item['warranty'],['url'=>'/warranty/'.$item['warranty']->id,'method'=>'put']) !!}
                    <div class="form-group">
                        {!! Form::label('model_name','Model Name') !!}
                        {!! Form::text('model_name',null,['class'=>'form-control']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('model_code','Model Code') !!}
                        {!! Form::text('model_code',null,['class'=>'form-control']) !!}

                    </div>
		            <div class="form-group">
                        {!! Form::label('sn','Sn',['class'=>'sr-only']) !!}
                        {!! Form::text('sn',Null,['class'=>'form-control','placeholder'=>'SN']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('quantity','Quantity',['class'=>'sr-only']) !!}
                        {!! Form::text('quantity',Null,['class'=>'form-control','placeholder'=>'quantity']) !!}
                    </div>
                    <div class="form-group">

                        {!! Form::label('staff','Operator') !!}
                        {!! Form::text('staff',null,['class'=>'form-control']) !!}
                    </div>
                    <div class="form-group">
                        <label for="supplier" class="sr-only">Supplier</label>

                        {!! Form::select('supplier_id', $suppliers, null, ['placeholder' => 'Pick a Suppiler...','class'=>'form-control']) !!}
                    </div>
                        <label>Status:&nbsp;&nbsp;&nbsp; <strong style="color:red;font-size: 18px">{{end($item['status'])->status_content}}</strong></label>
                <hr>
                    <div class="form-group">
                        {!! Form::label('client_name','Client Name') !!}
                        {!! Form::text('client_name',null,['class'=>'form-control']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('client_phone','Client Phone') !!}
                        {!! Form::text('client_phone',null,['class'=>'form-control']) !!}
                    </div>
                <hr>

                @foreach($item['notes'] as $note)
                    @if($note->type == 'sys')
                        <div class="alert alert-warning alert-dismissible" role="alert">
                            {{--<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>--}}
                            <strong>{{$note->created_at}} </strong> {!! $note->note !!}
                        </div>
                    @else
                        <div class="alert alert-info alert-dismissible" role="alert">
                            {{--<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>--}}
                            <strong>{{$note->created_at}} </strong> {!! $note->note !!}
                        </div>
                    @endif

                @endforeach

                <button type="button" class="btn btn-default btn-sm btn-block btn-info" data-toggle="modal" data-target="#myModal">Add a note</button>

<br>
		<a target="_blank" type="button" class="btn btn-default btn-sm btn-block btn-success" href="{{url('/',['print',$item['warranty']->id])}}">Print the form</a>
<br>
                <button type="submit" class="btn btn-default btn-sm btn-block btn-primary">Update</button>


                {!! Form::close() !!}
            </div>

        </div>
    </div>
    <!-- Note Panel-->
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">New Note</h4>
                </div>
                {!! Form::open(['url'=>'warranty/'.$item['warranty']->id]) !!}
                <div class="modal-body">
                    <div class="form-group">
                        <textarea name="note" rows="8" placeholder="Typing..." class="form-control"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
                {!! Form::close() !!}

            </div>
        </div>
    </div>
    <div class="col-md-2"></div>
@stop
