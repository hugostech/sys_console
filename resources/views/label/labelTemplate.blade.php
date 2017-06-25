@extends('master')

@section('mainContent')

    <div class="col-sm-12">
        {!! Form::open(['url'=>'findLabel']) !!}
        <div class="form-group">
            <div class="input-group">
                {!! Form::text('code',null,['class'=>'form-control']) !!}
                <span class="input-group-btn">
                    {!! Form::submit('Find',['class'=>'btn btn-default']) !!}
                </span>
            </div>
        </div>

        <div class="form-group">
            <a class="btn btn-info" href="{{url('labelList')}}">Print list</a>
        </div>
        {!! Form::close() !!}

    </div>
    <div class="col-sm-12">
        @if(isset($label))
            @if( !is_null($label))

                @if($label->type == 1)

                    <div class="col-sm-12">

                        {!! Form::open(['url'=>'editLabel']) !!}
                        {!! Form::input('hidden','label_id',$label->id) !!}

                        <div class="form-group">
                            <label>Price

                            </label>
                            {{Form::input('number','price',$label->price,['class'=>'form-control','step'=>0.01])}}
                        </div>
                        <div class="form-group">
                            Origin price:<label class="text-info"> {{round($product->price*1.15,2)}}</label><br>
                            @if(!is_null($special))
                                Special price:<label class="text-danger"> {{round($special->price*1.15,2)}}</label>
                            @endif
                        </div>
                        <div class="form-group">
                            {!! Form::select('type',['1'=>'Short Label','2'=>'Long Label'],$label->type,['class'=>'form-control']) !!}
                        </div>
                        <div class="form-group">
                            <label for='description'>Desciption</label>
                            @if($label->prepare2print == 1)
                                <small class="text-danger">Product in print list</small>
                            @endif
                            @if(is_null(json_decode($label->description,true)))
                                <textarea name="description" rows="5" class="form-control" placeholder="Details..."
                                          id="warranty_detail">{{$label->description}}</textarea>
                            @else
                                <textarea name="description" rows="5" class="form-control" placeholder="Details..."
                                          id="warranty_detail">@foreach(json_decode($label->description,true) as $key=>$item){{$item.'&#13;&#10;'}}@endforeach</textarea>
                            @endif
                            {{--<script>--}}

                            {{--CKEDITOR.replace( 'warranty_detail' );--}}
                            {{--</script>--}}
                        </div>
                        <div class="form-group">
                            {!! Form::submit('Edit',['class'=>'btn btn-primary']) !!}
                            @if($label->prepare2print == 0)
                                <a class="btn btn-success" href="{{url('addLabel2PrintList',[$label->id])}}">Add to
                                    print list</a>
                            @else
                                <a class="btn btn-danger" href="{{url('removeLabelFromPrintList',[$label->id])}}">Remove
                                    from print list</a>
                            @endif


                        </div>

                        {!! Form::close() !!}


                    </div>
                    <style>
                        table {
                            /*border: solid 1px beige;*/
                            margin: 0;
                            padding: 0;
                        }

                        table tr td {
                            border: solid 1px black;
                            height: 105.66037px;
                            width: 298px;
                            /*background: cornflowerblue;*/
                            text-align: left;
                            padding: 0;
                        }

                        table tr td div {
                            width: 298px;
                            /*float: left;*/
                        }

                        .tape-top {
                            height: 86.7925px;
                        }

                        .tape-top-left {
                            padding-left: 5px;
                            width: 150px;

                            float: left;
                            font-family: "Microsoft YaHei";
                            line-height: 17px;
                            font-size: 15px;
                            font-weight: 600;
                        }

                        .tape-top-right {
                            width: 140px;

                            text-align: right;
                            float: left;
                            font-family: "Arial Black";
                            padding-top: 8px;
                        }

                        .tape-top-right h3 {
                            font-family: "Arial";
                            /*font-family: "Cordia New";*/
                            font-size: 70px;
                            margin: 0;
                            padding: 0;
                            letter-spacing: -3px;

                            /*position: relative;*/

                        }

                        .tape-top-right h3 sup {
                            font-family: "Arial Unicode MS";
                            font-size: 40px;

                        }

                        .tape-bottom {
                            background: rgb(000, 204, 000);
                            background-image: url("{{url('image',['logo.png'])}}");
                            background-size: 128.5714px 18px;
                            background-repeat: no-repeat;

                            border-top: solid 1px black;
                            height: 18.86792px;
                            text-align: right;
                            font-family: "Microsoft YaHei UI";
                            font-size: 14px;
                            padding-right: 3px;
                        }

                    </style>
                    <div class="col-sm-12">
                        <table>
                            <tr>
                                <td>
                                    <div class="tape-top">
                <span class="tape-top-left">
                    @if(is_null(json_decode($label->description,true)))
                        {!! $label->description !!}
                    @else
                        @foreach(json_decode($label->description,true) as $item)
                            {!! $item !!}
                        @endforeach
                    @endif
                </span>
                                        <span class="tape-top-right">
                    <h3><sup>$</sup>{{number_format($label->price)}}</h3>
                </span>


                                    </div>
                                    <div class="tape-bottom">Code: {{$label->code}}</div>
                                </td>
                                <td></td>
                            </tr>
                        </table>

                    </div>
                @else
                    {{--Long label--}}
                    <div class=col-sm-12>
                        {!! Form::open(['url'=>'editLabel']) !!}
                        {!! Form::input('hidden','label_id',$label->id) !!}

                        <div class="form-group">
                            <label>Price

                            </label>
                            {{Form::input('number','price',$label->price,['class'=>'form-control','step'=>0.01])}}
                        </div>
                        <div class="form-group">
                            Origin price:<label class="text-info"> {{round($product->price*1.15,2)}}</label><br>
                            @if(!is_null($special))
                                Special price:<label class="text-danger"> {{round($special->price*1.15,2)}}</label>
                            @endif
                        </div>
                        <div class="form-group">
                            {!! Form::select('type',['1'=>'Short Label','2'=>'Long Label'],$label->type,['class'=>'form-control']) !!}
                        </div>
                        <div class="form-group">
                            <label for='description'>Desciption</label>
                            @if($label->prepare2print == 1)
                                <small class="text-danger">Product in print list</small>
                            @endif

                            <br>
                            @if(is_null(json_decode($label->description,true)))
                                @foreach(range(0,3) as $i)
                                    <label>Row {{$i+1}} :</label>
                                    @if($i == 0)
                                        <input type="text" class="form-control" name="description[]"
                                               value="{{$label->description}}">
                                    @else
                                        <input type="text" name="description[]" class="form-control">
                                    @endif
                                @endforeach
                            @else
                                @foreach(json_decode($label->description,true) as $key=>$item)
                                    <label>Row {{$key+1}} :</label>
                                    <input type="text" class="form-control" value="{{$item}}" name="description[]">
                                @endforeach
                            @endif
                        </div>
                        <div class="form-group">
                            {!! Form::submit('Edit',['class'=>'btn btn-primary']) !!}
                            @if($label->prepare2print == 0)
                                <a class="btn btn-success" href="{{url('addLabel2PrintList',[$label->id])}}">Add to
                                    print list</a>
                            @else
                                <a class="btn btn-danger" href="{{url('removeLabelFromPrintList',[$label->id])}}">Remove
                                    from print list</a>
                            @endif


                        </div>

                        {!! Form::close() !!}
                    </div>
                    <style>
                        table {
                            /*border: solid 1px beige;*/
                            margin: 0;
                            padding: 0;
                        }

                        table tr td {
                            border: solid 1px black;
                            height: 105.66037px;
                            width: 298px;
                            /*background: cornflowerblue;*/
                            text-align: left;
                            padding: 0;
                        }

                        table tr td div {
                            width: 298px;
                            /*float: left;*/
                        }

                        .tape-top {
                            height: 86.7925px;
                        }

                        .tape-top-left {
                            padding-left: 5px;
                            width: 150px;

                            float: left;
                            font-family: "Microsoft YaHei";
                            line-height: 17px;
                            font-size: 15px;
                            font-weight: 600;
                        }


                        .tape-top-right {
                            width: 140px;

                            text-align: right;
                            float: left;
                            font-family: "Arial Black";
                            padding-top: 8px;
                        }

                        .tape-top-right h3 {
                            font-family: "Arial";
                            /*font-family: "Cordia New";*/
                            font-size: 70px;
                            margin: 0;
                            padding: 0;
                            letter-spacing: -3px;

                            /*position: relative;*/

                        }

                        .tape-top-right h3 sup {
                            font-family: "Arial Unicode MS";
                            font-size: 40px;

                        }

                        .tape-bottom {
                            background: rgb(000, 204, 000);
                            background-image: url("{{url('image',['logo.png'])}}");
                            background-size: 128.5714px 18px;
                            background-repeat: no-repeat;

                            border-top: solid 1px black;
                            height: 18.86792px;
                            text-align: right;
                            font-family: "Microsoft YaHei UI";
                            font-size: 14px;
                            padding-right: 3px;
                        }
/*Long label css*/
                        .long-label{
                            width: 600px;
                        }

                        .long-tape-top-left {
                            padding-left: 5px;
                            width: 300px;

                            float: left;
                            font-family: "Microsoft YaHei";
                            line-height: 17px;
                            font-size: 15px;
                            font-weight: 600;
                        }

                        .long-tape-top-right {
                            width: 280px;

                            text-align: right;
                            float: left;
                            font-family: "Arial Black";
                            padding-top: 8px;
                        }

                        .long-tape-top-right h2 {
                            font-family: "Arial";
                            /*font-family: "Cordia New";*/
                            font-size: 70px;
                            margin: 0;
                            padding: 0;
                            letter-spacing: -3px;

                            /*position: relative;*/

                        }

                        .long-tape-bottom {
                            background: rgb(000, 204, 000);
                            background-image: url("{{url('image',['logo.png'])}}");
                            background-size: 128.5714px 18px;
                            background-repeat: no-repeat;

                            border-top: solid 1px black;
                            height: 18.86792px;
                            text-align: right;
                            font-family: "Microsoft YaHei UI";
                            font-size: 14px;
                            padding-right: 3px;
                            width: 600px;
                        }

                    </style>
                    <div class="col-sm-12">
                        <table>
                            <tr>
                                <td colspan="2">
                                    <div class="long-label tape-top">
                <span class="long-tape-top-left">
                    @if(is_null(json_decode($label->description,true)))
                        {!! $label->description !!}
                    @else
                        @foreach(json_decode($label->description,true) as $item)
                            {!! $item !!}
                        @endforeach
                    @endif
                </span>
                <span class="long-tape-top-right">
                    <h2><sup>$</sup>{{number_format($label->price)}}</h2>
                </span>


                                    </div>
                                    <div class="long-tape-bottom">Code: {{$label->code}}</div>
                                </td>

                            </tr>
                        </table>

                    </div>
                @endif
            @endif
        @endif
    </div>
@endsection