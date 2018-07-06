@extends('master')

@section('mainContent')
    <div class="col-md-12">
        @if(!empty($content))
            @foreach($content as $key=>$cat)
                {{str_replace("%20", " ", $key)}}:<label style="color: red">{{$cat}}</label><br>
            @endforeach
        @endif
    </div>

    <div class="col-md-12" ng-app="myApp">
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
            <div class="form-group"  ng-controller="autoComplete">
                <input type="text" name="category" class="form-control" ng-model="categoryFilter" placeholder="Category name">
                <ul class="list-group" ng-if="categoryFilter" >
                    <a ng-repeat="x in categorys | filter : categoryFilter" href="{{url('signProduct2Category',[$data['product_id']])}}?id=@{{ x.id }}" class="list-group-item @{{ x.status }}">@{{x.name}}</a>



                </ul>
            </div>
            {{--<table class="table table-bordered" ng-app="myApp" ng-controller="customersCtrl">--}}
            <table class="table table-bordered"
                   ng-init="normal_price={{$data['extremepcprice']}};special_price={{$data['special']}}">
                <tr>
                    {{--<td class="col-md-1">Code</td>--}}
                    <td class="col-md-3">Name</td>
                    <td class="col-md-5" colspan="2">Extemepc Price(inc GST)
                        @if($data['product']->price_lock==1)
                            <label class="text-danger">Locked</label>
                        @endif
                    </td>
                    <td class="col-md-4">Detail:</td>
                    {{--<td class="col-md-8"></td>--}}
                </tr>
                <tr>
                    {{--<td>{{$data['code']}}</td>--}}
                    <td>{{str_limit($data['des'],2000)}}
                    <br><img src="http://www.extremepc.co.nz/image/{{$data['img']}}" width="150px">
                    </td>
                    <td>
                        {!! Form::open(['url'=>'/killprice','method'=>'put']) !!}
                        <div class="form-group">
                            <input type="hidden" name='code' value="{{$data['code'] }}">
                            <div class="input-group">
                                <input type='text' class="form-control" name='price' value='{{$data['extremepcprice']}}'
                                       ng-model="normal_price" onchange="remind(this)">
                        <span class="input-group-btn">
                            <button type="submit" class="btn btn-default">Edit Price</button>
                        </span>
                            </div>
                            <p>Ex GST @{{normal_price/1.15 | number:2}}</p>
                        </div>
                        <div class="form-group">
                            <div class="input-group">
                                <input type="text" class="form-control" name='special' value="{{$data['special']}}"
                                       ng-model="special_price" onchange="remind(this)">
                                <span class="input-group-btn">
                                    <button type="submit" class="btn btn-default">Edit Sepcial</button>
                                </span>
                            </div>
                            <p ng-show(special_price)>Ex GST @{{special_price/1.15 | number:2}}</p>
                        </div>
                        <div class="form-group">
                            <label for="starttime">Start Time:</label>
                            <div class="input-group">
                                {!! Form::date('starttime', $data['special_start'],['class'=>'form-control']) !!}

                                <span class="input-group-btn">
                                    <button type="submit" name='setStartDate' class="btn btn-default">Set</button>
                                </span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="endtime">End Time:</label>
                            <div class="input-group">
                                {!! Form::date('endtime', $data['special_end'],['class'=>'form-control']) !!}

                                <span class="input-group-btn">
                                    <button type="submit" name='setEndDate' class="btn btn-default">Set</button>
                                </span>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="form-group text-right">

                            <a href="{{url('laptop_attribute',[$data['product_id']])}}" class="btn btn-warning">Add Laptop Attributes</a>

                            @if($data['status']==1)
                                <input type="submit" class="btn btn-success" name="product_status" value="Enable">
                            @else
                                <input type="submit" class="btn btn-danger" name="product_status" value="Disable">
                            @endif
                            @if(is_null($data['kill_price_status']))
                                <a href="{{url('startKillPrice').'?id='.$data['product_id']}}" class="btn btn-primary">Add to kill price list</a>
                            @else
                                <label class="text-success">Product in killprice list</label>
                            @endif
                            @if(strlen($data['des'])<500)
                                @if($data['product']->price_lock==0)
                                    <button class="btn btn-danger" onclick="lock('{{$data['product']->product_id}}')">Price Lock</button>
                                @else
                                    <button class="btn btn-success" onclick="unlock('{{$data['product']->product_id}}')">Price UnLock</button>
                                @endif
                            @else
                                @if($data['product']->price_lock==0)
                                    <a class="btn btn-danger" href="{{url('exproduct',[$data['product']->product_id,'pricelock'])}}?r=g">Price Lock</a>
                                @else
                                    <a class="btn btn-success" href="{{url('exproduct',[$data['product']->product_id,'priceunlock'])}}?r=g">Price UnLock</a>
                                @endif
                            @endif



                        </div>


                        {!! Form::close() !!}
                        <br>
                    </td>

                    {{--<td><a href="http://www.extremepc.co.nz/william/categories.php?search={{$data['code']}}" target="_blank">{{$data['extremepcprice']}}</a></td>--}}
                    <td>
                        {!! str_limit($data['price'],2000) !!}<br>
                        Viewed:<label>{{$data['view']}}</label><br>
                        @if($data['product']->csvs()->count()>0)
                            <ul class="list-group">

                            </ul>
                            @foreach($data['product']->csvs as $item)
                                <li class="list-group-item"><span class="badge">{{$item->stock}}</span>{{$item->supply_code}}:${{$item->price}} Code: {{$item->supplier_code}}</li>
                            @endforeach
                        @endif
                    </td>
                </tr>







                <tr>
                    <td colspan="4">
                        <iframe id="if_pricespy" src="http://pricespy.co.nz/#search={{urlencode(substr($data['des'],0,25)) }}" width="100%"
                                height="600px;" frameborder="0"></iframe>
                    </td>
                </tr>
            </table>
        @endif
    </div>
    <script>
        function killpriceStart(){
            $('#price_url').val(parent.document.getElementById("if_pricespy").contentWindow.location.href);
            $('#killprice_form').submit();
        }

        function unlock(id) {
            var url = '{{url('exproduct')}}/'+id+'/priceunlock';
            console.log(url);
            axjx(url);
        }
        function lock(id) {
            var url = '{{url('exproduct')}}/'+id+'/pricelock';
            console.log(url);
            axjx(url);
        }

        function axjx(url) {
            $.ajax(
                {
                    type: 'get',
                    url: url,
                    dataType: 'json',
                    success: function (result) {
//                        alert(result);
                        location.reload();
                    }
                }
            );
        }


        var myapp = angular.module('myApp', []);
        myapp.controller('autoComplete',function($scope){
            $scope.categorys = {!! $categorys !!};
        });
        @if(!empty($data))
        function remind(price){
            var bottomPrice = {{$data['bottom_cost']}};
//            bottomPrice = parseFloat(bottomPrice);
            var oprice = $(price).val();
            if(oprice<bottomPrice){
                alert('This price is under average cost');
            }
        }
        @endif
    </script>


@endsection
