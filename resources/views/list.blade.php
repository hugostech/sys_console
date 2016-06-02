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
            <th class="col-sm-1">Client</th>
            <th class="col-sm-1">Date</th>
            <th class="col-sm-1">Ref</th>
            <th class="col-sm-4">Model detail</th>
            <th class="col-sm-2">Status</th>
        </tr>
        @foreach($warrantys as $warranty)

            <tr>
                <td>{{$warranty->id}}</td>
                <td class="text-left">

                    @if(empty($warranty->model_code))
                        <img src="{{url('/broken-image.gif')}}" width="80px" alt="{{$warranty->model_code}}">
                    @else
                        <img src="{{env('IMG').$warranty->model_code.".jpg"}}" width="80px" alt="{{$warranty->model_code}}">
                    @endif
                </td>
                <td>{{$warranty->model_code }}</td>
                <td>{{$warranty->client_name }}</td>

                <td>{{$warranty->created_at }}</td>
                <td>{{empty($rates[$warranty->id]['delivery']->reference_no)?'N/A':$rates[$warranty->id]['delivery']->reference_no}}</td>
                <td><a href="{{url('/warranty',[$warranty->id])}}">{{$warranty->model_name}}
                            <span class="badge">{{$rates[$warranty->id]['note']}}</span></a></td>
                <td>
                        <ul class="pagination" style="margin: 0;">

                            <li class="{{$rates[$warranty->id][1]}}"><a href="#">1</a></li>
                            <li class="{{$rates[$warranty->id][2]}} "><a href="{{$suppliers[$warranty->id]}}" onclick="step2({{$warranty->id}})">2</a></li>
                            <li class="{{$rates[$warranty->id][3]}} step3" step3="{{$warranty->id}}"><a href="#" data-toggle="modal" data-target="#myModal1" onclick="step3({{$warranty->id}})">3</a></li>
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
                                <div class="input-group">
                                      <span class="input-group-addon">
                                          {!! Form::input('checkbox','rep_sn_status','replace') !!}
                                        {{--<input type="checkbox">--}}
                                      </span>
                                    {!! Form::text('rep_sn',null,['class'=>'form-control','placeholder'=>'Replacement SN']) !!}

                                </div><!-- /input-group -->
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

@section('footer')
    <div style=";visibility: hidden;position: absolute;z-index: 1000;" id="disPanel" class="alert alert-success" role="alert"></div>
    <script>
        var x=0;
        var y=0;
        var url;
        var content;

        $(document).ready(function(){
            $(".step3").mouseover(function(e){

                content = $(this).attr("step3");
                url = "getTrackInfo/"+content;

                $("#disPanel").load(url);
                $("#disPanel").css("left", e.pageX);
                $("#disPanel").css("top", e.pageY);
                $("#disPanel").css("visibility", "visible");

            });
            $(".step3").mouseout(function(){
                $("#disPanel").css("visibility", "hidden");
                $("#disPanel").innerHTML("");
            });
        });
    </script>
@stop
