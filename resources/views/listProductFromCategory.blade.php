@extends('master')

@section('mainContent')
    <div class="col-md-12">

        <div class="panel panel-info">
            <div class="panel-heading">
                <h3>Product List</h3>
                {{--<a class="btn btn-primary" href="{{url('publishFlash')}}">Publish</a>--}}
                {{--<a class="btn btn-danger" href="{{url('offlineFlash')}}">Offline</a>--}}
            </div>
            <div class="panel-body">
                <div class="row" ng-app="myApp" ng-controller="autoComplete">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <input type="text" name="category" class="form-control" ng-model="categoryFilter">
                            <ul class="list-group" ng-if="categoryFilter" ng-repeat="x in categorys | filter : categoryFilter">
                                <a href="?id=@{{ x.id }}" class="list-group-item @{{ x.status }}">@{{x.name}}</a>



                            </ul>
                        </div>
                    </div>
                    @if(!is_null($result))
                    <div class="col-sm-12">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th class="col-md-1"></th>
                                <th class="col-md-2">Model</th>
                                <th class="col-md-7">title</th>
                                <th class="col-md-2">Action</th>
                            </tr>
                            </thead>
                            @foreach($result as $key=>$single)
                                <tr>
                                    <td>{{$key+1}}</td>
                                    <td>{{$single['product']->model}}</td>
                                    <td>{{$single['product_detail']->name}}</td>
                                    {{--<td>{{round($single['special']->price*1.15,2)}}</td>--}}
                                    <td><a href="{{url('/deleteProductFromCategory',[Input::get('id'),$single['product']->product_id])}}" class="btn btn-danger">Del</a></td>
                                </tr>
                            @endforeach

                            <tr>
                                <td id="models"><input type="text" name="modelnum[]" class="form-control" placeholder="Model" required>
                                </td>
                                <td></td>
                                <td><input type="submit" value="submit" class="btn btn-primary"><button type="button" onclick="addone()" class="btn btn-default">more</button></td>
                            </tr>

                        </table>
                    </div>
                        @endif

                </div>

            </div>
        </div>
    </div>
    <script>
        var myapp = angular.module('myApp', []);
        myapp.controller('autoComplete',function($scope){
           $scope.categorys = {!! $categorys !!};
        });
    </script>

@endsection