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
                                <a href="@{{ x.id }}" class="list-group-item @{{ x.status }}">@{{x.name}}</a>



                            </ul>
                        </div>
                    </div>

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