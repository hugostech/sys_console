@extends('master')

@section('mainContent')
    <div class="col-md-2"></div>
    <div class="col-md-8" ng-app="SNCheck" ng-controller="snRange">
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
                        {!! Form::text('model_name',Null,['class'=>'form-control','placeholder'=>'Model Name','ng-model'=>'desc']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('model_code','Item No',['class'=>'sr-only']) !!}

                        <div class="input-group ">
                                {!! Form::text('model_code',Null,['class'=>'form-control','placeholder'=>'Item Code','ng-model'=>'code']) !!}

                            <span class="input-group-btn">
                                <button class="btn btn-default" type="button" ng-click="fill()">Fill</button>

                            </span>
                        </div>
                    </div>
		            <div class="form-group">
                        <div class="input-group">

                            {!! Form::text('sn',Null,['class'=>'form-control','placeholder'=>'SN','ng-model'=>'sn','autofocus','my-enter'=>'checkSn()']) !!}
                            <span class="input-group-btn">
                            <button class="btn btn-default" type="button" ng-click="checkSn()">Check</button>
                        </span>
                        </div>

                    </div>
                    <hr ng-show="pruchase_inv">
                    <div class="form-group">
                        <p ng-show="sn_status">Original SN: <label style="color: red">@{{ sn_status }}</label></p>

                        <h4 ng-show="pruchase_inv">Purchase Details:</h4>
                        <p ng-show="pruchase_inv">Purchase Invoice No: <label>@{{ pruchase_inv }}</label></p>
                        <p ng-show="pruchase_inv">Purchase Date: <label>@{{ purchase_date }}</label></p>

                        <h4 ng-show="sale_inv">Sale Details:</h4>
                        <p ng-show="sale_inv">Sale Invoice No: <label>@{{ sale_inv }}</label></p>
                        <p ng-show="sale_inv">Sale Date: <label>@{{ sale_date }}</label></p>


                        <label ng-show="firstName">Name: @{{ firstName }}</label>
                    </div>
                    <hr>
                    <div class="form-group">
                        <label for="supplier" class="sr-only">Supplier</label>
                        {{--<select class="form-control" name="supplier_id" id="supplier" required>--}}
                            {{--@foreach($suppliers as $supplier)--}}
                                {{--<option value="{{$supplier->id}}">{{$supplier->name}}</option>--}}
                            {{--@endforeach--}}

                        {{--</select>--}}
                        {!! Form::select('supplier_id', $suppliers, null, ['placeholder' => 'Pick a Suppiler...','class'=>'form-control']) !!}
                    </div>


                    <div class="form-group">
                        {!! Form::label('quantity','Quantity') !!}
                        {!! Form::input('number','quantity',1,['class'=>'form-control']) !!}
                    </div>

                    <div class="form-group">
                        <label for="staff">Staff</label>
                        <select class="form-control" name="staff" id="staff">
                            <option value="kelvin">Kelvin</option>
                            <option value="jimmy">Jimmy</option>
                            <option value="piero">Piero</option>
                            <option value="tony">Tony</option>
                            <option value="tony">Elfa</option>
                            <option selected value="Danny">Danny</option>
                            <option value="matt">Matt</option>
                            <option value="John">John</option>
                            <option value="hugo">Hugo</option>
                        </select>
                    </div>
                    {{--<input type="checkbox" onselect="selfStorage()" value="Roc">--}}
                    <!--<input type="text" placeholder="Reference No." class="form-control"><br>-->

                    <hr>
                    <div class="form-group">
                        {!! Form::label('roc','Roctech: ') !!}
                        <input type="checkbox" value="y" name="storage" id="roc">
                    </div>
                    <div class="form-group">
                        {!! Form::label('client_name','Client Name',['class'=>'sr-only']) !!}
                        {!! Form::text('client_name',Null,['class'=>'form-control','placeholder'=>'Client name','id'=>'client_name']) !!}

                    </div>
                    <div class="form-group">
                        {!! Form::label('client_phone','Client Phone',['class'=>'sr-only']) !!}
                        {!! Form::text('client_phone',Null,['class'=>'form-control','placeholder'=>'Client phone','id'=>'client_phone']) !!}

                    </div>
                    <div class="form-group">

                    <hr>
                    <textarea rows="8" class="form-control" placeholder="Description" name="note"></textarea>
                    </div>
                    <div class="form-group">
                        {!! Form::submit('Submit',['class'=>'btn btn-block btn-primary form-control']) !!}
                    </div>
                </div>


            </div>


        {!! Form::close() !!}
    </div>
    <div class="col-md-2"></div>
@stop

@section("footer")
    <script>
        $(document).ready(function() {
            $(window).keydown(function(event){
                if(event.keyCode == 13) {
                    event.preventDefault();
                    return false;
                }
            });

        });

        var app = angular.module("SNCheck", []);

        app.controller("snRange", function($scope,$http) {

            $scope.checkSn = function(){
                if($scope.sn != ""){

                    var url = "{{url('/getsn')}}/" + $scope.sn;
//                    $scope.pruchase_inv = url;


                    $http.get(url).success(function(response) {

                        $scope.pruchase_inv = response.purchase_inv;
                        $scope.purchase_date = response.purchase_date;
                        $scope.sale_inv = response.sale_inv;
                        $scope.sale_date = response.sale_date;
                        $scope.desc = response.desc;
                        $scope.code = response.code;
                        $scope.sn_status = response.original_sn;
                    });
                }
            }
            $scope.fill = function(){
                if($scope.code != ""){

                    var url = "{{url('/getDesc')}}/" + $scope.code;



                    $http.get(url).success(function(response) {


                        $scope.desc = response.desc;

                    });
                }
            }
        });

    </script>
@stop
