@extends('master')

@section('mainContent')



            {{--<a href="/warranty"><button type="button" class="btn btn-default">New Warranty</button> </a>--}}

        {{--<div class="col-md-4 col-md-offset-8">--}}
            {{--{!! Form::open(['url'=>'/list','method'=>'put']) !!}--}}
                {{--<div class="input-group">--}}
                    {{--<input type="text" class="form-control" name="condition" placeholder="Search for...">--}}
                  {{--<span class="input-group-btn">--}}
                    {{--<button class="btn btn-default" type="submit">Go!</button>--}}
                  {{--</span>--}}
                {{--</div><!-- /input-group -->--}}
            {{--{!! Form::close() !!}--}}
            {{--<br>--}}
        {{--</div>--}}


    <table class="table table-striped">
        <tr>
            <th class="col-sm-1">#</th>
            <th class="col-sm-1"></th>
            <th class="col-sm-1">Code</th>
            <th class="col-sm-2">Date</th>
            <th class="col-sm-4">Model detail</th>
            <th class="col-sm-3">Status</th>
        </tr>
        @foreach($warrantys as $warranty)

            <tr>
                <td>{{$warranty->id}}</td>
                <td><img src="{{env('IMG').$warranty->model_code.".jpg"}}" width="80px" alt="{{$warranty->model_code}}"></td>
                <td>{{$warranty->model_code }}</td>
                <td>{{$warranty->created_at }}</td>
                <td><a href="{{url('/warranty',[$warranty->id])}}">{{$warranty->model_name}}
                            <span class="badge">{{$rates[$warranty->id]['note']}}</span></a></td>
                <td class="text-center">
                        <ul class="pagination" style="margin: 0;">

                            <li class="{{$rates[$warranty->id][1]}}"><a href="#">1</a></li>
                            <li class="{{$rates[$warranty->id][2]}}"><a href="{{$suppliers[$warranty->id]}}">2</a></li>
                            <li class="{{$rates[$warranty->id][3]}}"><a href="#" data-toggle="modal" data-target="#myModal1" onclick="step3({{$warranty->id}})">3</a></li>
                            <li class="{{$rates[$warranty->id][4]}}"><a href="#" data-toggle="modal" data-target="#myModalstep4{{$warranty->id}}">4</a></li>
                            <li class="{{$rates[$warranty->id][5]}}"><a href="#" onclick="finishWarranty('{{url('/',['finishWarranty',$warranty->id])}}')">5</a></li>

                        </ul>
                </td>
            </tr>
            <!-- Modal Step 4 -->
            <div class="modal fade" id="myModalstep4{{$warranty->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                <div class="modal-dialog" role="document">
                    {!! Form::model($warranty,['url'=>'step4']) !!}
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="myModalLabel">Content details</h4>
                        </div>

                        <div class="modal-body">
                            <div class="form-group">
                                {!! Form::label('client_name','Client Name') !!}
                                {!! Form::text('client_name',null,['class'=>'form-control']) !!}
                            </div>
                            <div class="form-group">
                                {!! Form::label('client_phone','Client Phone') !!}
                                {!! Form::text('client_phone',null,['class'=>'form-control']) !!}
                            </div>

                            <div class="form-group">
                                <label for="result">Result</label>
                                <select class="form-control" name="result" id="result">
                                    <option value="1">Client will pick up</option>
                                    <option value="2" selected>Will call later</option>
                                </select>
                            </div>
                            <div class="form-group">
                                {!! Form::label('note','Description') !!}
                                <textarea name="note" id="note" rows="8" class="form-control"></textarea>
                            </div>
                            {!! Form::input('hidden','id',null,['id'=>'model_id_step4']) !!}
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
            <!-- Modal Step 4 End Here-->

        @endforeach
    </table>

        <!-- Modal Step 3 -->
        <div class="modal fade" id="myModal1" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document">
                {!! Form::open(['url'=>'step3']) !!}
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">Delivery details</h4>
                    </div>

                    <div class="modal-body">
                        <div class="form-group">
                            {!! Form::label('track_number','Track number') !!}
                            {!! Form::text('track_number',null,['class'=>'form-control']) !!}
                        </div>
                        <div class="form-group">
                            {!! Form::label('reference_no','Reference No.') !!}
                            {!! Form::text('reference_no',null,['class'=>'form-control']) !!}
                        </div>
                        <div class="form-group">
                            {!! Form::label('note','Description') !!}
                            <textarea name="note" id="note" rows="8" class="form-control"></textarea>
                        </div>
                            {!! Form::input('hidden','model_id',null,['id'=>'model_id_step3']) !!}
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
        <!-- Modal Step 3 End Here-->


        <!-- Paginate -->
        {!! $warrantys->render() !!}



@stop
