@extends('master')

@section('mainContent')
    <div class="col-md-2"></div>
    <div class="col-md-8">
        <h2>Warranty form</h2>
        <hr>

        {!! Form::open(['url'=>'/warranty']) !!}

            <div class="panel panel-info ">
                <div class="panel-heading">
                    <h4 class="panel-title">Model detail</h4>
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        {!! Form::label('model_name','Model Name',['class'=>'sr-only']) !!}
                        {!! Form::text('model_name',Null,['class'=>'form-control','placeholder'=>'Model Name']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('model_code','Item No',['class'=>'sr-only']) !!}
                        {!! Form::text('model_code',Null,['class'=>'form-control','placeholder'=>'Item Code']) !!}
                    </div>
		    <div class="form-group">
                        {!! Form::label('sn','Sn',['class'=>'sr-only']) !!}
                        {!! Form::text('sn',Null,['class'=>'form-control','placeholder'=>'SN']) !!}
                    </div>
                    <div class="form-group">
                        <label for="supplier">Supplier</label>
                        <select class="form-control" name="supplier_id" id="supplier" required>
                            @foreach($suppliers as $supplier)
                                <option value="{{$supplier->id}}">{{$supplier->name}}</option>
                            @endforeach
                            {{--<option value="Ingram Micro">Ingram Micro</option>--}}
                            {{--<option value="Synnex">Synnex</option>--}}
                            {{--<option value="PB Tech">PB Tech</option>--}}
                            {{--<option value="Anyware">Anyware</option>--}}
                            {{--<option value="Ray Tech">Ray Tech</option>--}}
                            {{--<option value="Dove">Dove</option>--}}
                            {{--<option value="Acer">Acer</option>--}}
                            {{--<option value="XP computer">XP computer</option>--}}
                            {{--<option value="TSA">TSA</option>--}}
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="staff">Staff</label>
                        <select class="form-control" name="staff" id="staff">
                            <option value="kelvin">Kelvin</option>
                            <option value="jimmy">Jimmy</option>
                            <option value="piero">Piero</option>
                            <option value="steven">Steven</option>
                            <option value="klaus">Klaus</option>
                            <option selected value="wei">Wei</option>
                            <option value="hugo">Hugo</option>
                        </select>
                    </div>
                    {{--<input type="checkbox" onselect="selfStorage()" value="Roc">--}}
                    <!--<input type="text" placeholder="Reference No." class="form-control"><br>-->

                    <hr>
                    <div class="form-group">
                        {!! Form::label('roc','Roctech: ') !!}
                        <input type="checkbox" onselect="selfStorage()" value="y" name="storage" id="roc">
                    </div>
                    <div class="form-group">
                        {!! Form::label('client_name','Client Name',['class'=>'sr-only']) !!}
                        {!! Form::text('client_name',Null,['class'=>'form-control','placeholder'=>'Client name','id'=>'client_name']) !!}

                    </div>
                    <div class="form-group">
                        {!! Form::label('client_phone','Client Phone',['class'=>'sr-only']) !!}
                        {!! Form::text('client_phone',Null,['class'=>'form-control','placeholder'=>'Client phone','id'=>'client_phone']) !!}

                    </div>

                    <hr>
                    <textarea rows="8" class="form-control" placeholder="Description" name="note"></textarea>

                </div>
            </div>
            {!! Form::submit('Submit',['class'=>'btn btn-block btn-primary']) !!}


        {!! Form::close() !!}
    </div>
    <div class="col-md-2"></div>
@stop
