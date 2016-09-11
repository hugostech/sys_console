@extends('master')

@section('mainContent')
    <div class="col-md-12">
        @if(!empty($content))
            @foreach($content as $key=>$cat)
                {{str_replace("%20", " ", $key)}}:<label style="color: red">{{$cat}}</label><br>
            @endforeach
        @endif
    </div>

    <div class="col-sm-12">
        {!! Form::open(['url'=>'/killprice']) !!}
        <div class="form-group">
            <div class="input-group">
                {!! Form::text('code',empty($data['code'])?null:$data['code'],['class'=>'form-control','placeholder'=>'code']) !!}
                <span class="input-group-btn">
                        {!! Form::submit('check',['class'=>'btn btn-default']) !!}
                    </span>
            </div>

        </div>
        {!! Form::close() !!}

        @if(!empty($data))
            {{--<table class="table table-bordered" ng-app="myApp" ng-controller="customersCtrl">--}}
            <table class="table table-bordered" ng-app=""
                   ng-init="normal_price={{$data['extremepcprice']}};special_price={{$data['special']}}">
                <tr>
                    {{--<td class="col-md-1">Code</td>--}}
                    <td class="col-md-4">Name</td>
                    <td class="col-md-4">Extemepc Price(inc GST)</td>
                    <td class="col-md-4">Detail:</td>
                    {{--<td class="col-md-8"></td>--}}
                </tr>
                <tr>
                    {{--<td>{{$data['code']}}</td>--}}
                    <td>{{$data['des']}}</td>
                    <td>
                        {!! Form::open(['url'=>'/killprice','method'=>'put']) !!}
                        <div class="form-group">
                            <input type="hidden" name='code' value="{{$data['code'] }}">
                            <div class="input-group">
                                <input type='text' class="form-control" name='price' value='{{$data['extremepcprice']}}'
                                       ng-model="normal_price">
					<span class="input-group-btn">
						<button type="submit" class="btn btn-default">Edit Price</button>
					</span>
                            </div>
                            <p>Ex GST @{{normal_price/1.15 | number:2}}</p>
                        </div>
                        <div class="form-group">
                            <div class="input-group">
                                <input type="text" class="form-control" name='special' value="{{$data['special']}}"
                                       ng-model="special_price">
                                <span class="input-group-btn">
                                    <button type="submit" class="btn btn-default">Edit Sepcial</button>
                                </span>
                            </div>
                            <p ng-show(special_price)>Ex GST @{{special_price/1.15 | number:2}}</p>
                        </div>
                        <div class="form-group text-right">
                            Viewed:<label>{{$data['view']}}</label>
                            <a href="{{url('laptop_attribute',[$data['product_id']])}}" class="btn btn-warning">Add Laptop Attributes</a>
                            @if($data['status']==1)
                                <input type="submit" class="btn btn-success" name="product_status" value="Enable">
                            @else
                                <input type="submit" class="btn btn-danger" name="product_status" value="Disable">
                            @endif

                        </div>


                        {!! Form::close() !!}
                        <br></td>

                    {{--<td><a href="http://www.extremepc.co.nz/william/categories.php?search={{$data['code']}}" target="_blank">{{$data['extremepcprice']}}</a></td>--}}
                    <td>

                        {!! $data['price'] !!}</td>
                </tr>
                <tr>
                    <td colspan="3">
                        <iframe src="http://pricespy.co.nz/#rparams=ss={{substr($data['des'],0,25) }}" width="100%"
                                height="600px;" frameborder="0"></iframe>
                    </td>
                </tr>
            </table>
        @endif
    </div>


@endsection
