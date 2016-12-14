@extends('master')

@section('mainContent')
    <div class="col-md-12">

        <div class="panel panel-info">
            <div class="panel-heading">
                <h3>Product List </h3>
                @if(!is_null($category_name))
                    <label>{{$category_name}}</label>
                @endif
                {{--<a class="btn btn-primary" href="{{url('publishFlash')}}">Publish</a>--}}
                {{--<a class="btn btn-danger" href="{{url('offlineFlash')}}">Offline</a>--}}
            </div>
            <div class="panel-body">

                <div class="row" ng-app="myApp" ng-controller="autoComplete">

                    <div class="col-sm-6">
                        <div class="form-group">
                            <input type="text" name="category" class="form-control" ng-model="categoryFilter">
                            <ul class="list-group" ng-if="categoryFilter" ng-repeat="x in categorys | filter : categoryFilter">
                                <a href="{{url('listProductFromCategory')}}?id=@{{ x.id }}" class="list-group-item @{{ x.status }}">@{{x.name}}</a>



                            </ul>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <a href="{{url('/batchEditPrice',[$category_id])}}" class="btn btn-default">批量改价</a>
                    </div>
                    <div class="col-sm-8">
                        <div class="form-group">
                            <label for="base_rate">Base Rate</label>
                            {!! Form::number('base_rate',null,['class'=>'form-control','ng-model'=>'base_rate','step'=>'0.01']) !!}
                        </div>
                        <div class="form-group">
                            <label for="special_rate">Special Rate</label>
                            {!! Form::number('special_rate',null,['class'=>'form-control','ng-model'=>'special_rate','step'=>'0.01']) !!}
                        </div>

                    </div>

                    @if(!is_null($result))
                    <div class="col-sm-12">
                        {!! Form::open(['url'=>'batchPriceEdit']) !!}
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                {{--<th class="col-md-1">Model</th>--}}
                                <th class="col-md-1">Model</th>
                                <th class="col-md-5">Title</th>
                                <th class="col-md-1">Quantity</th>
                                <th class="col-md-1">AverageCost</th>
                                <th class="col-md-1">(Cr)Price</th>
                                <th class="col-md-1">(Cr)Special</th>
                                <th class="col-md-1">Base</th>
                                <th class="col-md-1">New Special</th>

                            </tr>
                            </thead>
                            <tbody ng-repeat="y in result">
                                <input type="hidden" name="product_id[]" value="@{{ y.product_id }}">
                                <td>@{{y.code}}</td>
                                <td>@{{y.name}}</td>
                                <td>@{{y.quantity}}</td>
                                <td>@{{y.average_cost * 1.15 | number:2}}</td>
                                <td>@{{y.price * 1.15 | number:2}}</td>
                                <td>@{{y.special | number:2}}</td>
                                <td>
                                    <div class="input-group">
                                        <input type="number" name="base_price[]" step="0.01" value="@{{y.average_cost * base_rate * 1.15 | c9}}" class="form-control">
                                        <span class="input-group-btn">
                                            <button type="button" class="btn btn-default" onclick="clearVar(this)"><span class="glyphicon glyphicon-trash text-danger"></span></button>
                                        </span>
                                    </div>

                                </td>
                                <td>
                                    <div class="input-group">
                                        <input type="number" name="special_price[]" step="0.01" value="@{{y.average_cost * special_rate * 1.15 | c9}}" class="form-control">
                                        <span class="input-group-btn">
                                            <button type="button" class="btn btn-default" onclick="clearVar(this)"><span class="glyphicon glyphicon-trash text-danger"></span></button>
                                        </span>
                                    </div>
                                    </td>

                            </tbody>
                            {{--@foreach($result as $key=>$single)--}}
                                {{--<tr>--}}
                                    {{--<td>{{$key+1}}</td>--}}
                                    {{--<td>{{$single['product']->model}}</td>--}}
                                    {{--<td>{{$single['product_detail']->name}}</td>--}}
                                    {{--<td>{{$single['quantity']}}</td>--}}
                                    {{--<td>{{$single['average_cost']*1.15}}</td>--}}
                                    {{--<td>{{round($single['product']->price*1.15,2)}}</td>--}}
                                    {{--<td>{{round($single['special'],2)}}</td>--}}
                                    {{--<td>{!! Form::number('base_price','{{'. $single['average_cost']*1.15.' * base_rate }}',['class'=>'form-control','step'=>'0.01']) !!}</td>--}}
                                    {{--<td>{!! Form::number('special','{{'. $single['average_cost']*1.15.' * special_rate }}',['class'=>'form-control','step'=>'0.01']) !!}</td>--}}
                                    {{--<td><input type="number" step="0.01" name="base_price" value="@{{  }}"  "}}"></td>--}}
                                    {{--<td><a href="{{url('/deleteProductFromCategory',[$category_id,$single['product']->product_id])}}" class="btn btn-danger">Del</a></td>--}}
                                {{--</tr>--}}
                            {{--@endforeach--}}
                            {{--{!! Form::open(['url'=>'saveProduct2Category']) !!}--}}
                            <tr>
                                {{Form::input('hidden','category_id',$category_id)}}
                                {{--<td id="models"><input type="text" name="modelnum" class="form-control" placeholder="Model" required>--}}
                                {{--</td>--}}
                                {{--<td></td>--}}
                                <td colspan="8"><input type="submit" value="submit" class="btn btn-primary btn-block"></td>
                            </tr>
                            {!! Form::close() !!}

                        </table>

                    </div>
                @endif

                </div>

            </div>
        </div>

    </div>
    <script>



        var myapp = angular.module('myApp', []);
        myapp.filter('c9', function() {
            return function(input) {
                input = Math.floor(input / 10)*10+9;
                return input;
            };
        });
        myapp.controller('autoComplete',function($scope){
           $scope.categorys = {!! $categorys !!};
            $scope.base_rate = 1.5;
            $scope.special_rate = 1.1;
            $scope.result = {!! $result !!};
        });
        function clearVar(btn_self){
            $(btn_self).prev().val('');
        }

    </script>

@endsection