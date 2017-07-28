@extends('master')

@section('mainContent')
    <div class="col-lg-12" ng-app="">
        <div class="page-header text-center">
            <h3>ExtremePC order detail</h3>
        </div>

        <div class="form-group">
            <div class="input-group">
                <input type="text" ng-model="code" class="form-control">

                <span class="input-group-btn">
                    <a class="btn btn-default" href="?code=@{{ code }}">Find</a>
                </span>
            </div>
        </div>
    </div>
    <div class="col-lg-12">
        @if(!is_null($order))
            <div class="row">
                <div class="col-lg-6">
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            Client Detail
                        </div>
                        <div class="panel-body">
                            <strong class="text-capitalize text-muted">{{$order->firstname.' '.$order->lastname}}</strong><br>
                            Email: {{$order->email}}<br>
                            Phone: {{$order->telephone}}
                        </div>
                    </div>
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            Order Detail
                        </div>
                        <div class="panel-body">
                            <strong class="text-capitalize text-muted">Shipping Method: {{$order->shipping_method}}</strong><br>
                            <strong class="text-capitalize text-muted">Total: ${{$order->total}}</strong><br>
                            <strong class="text-capitalize text-muted">Order Status: {{$order->status->name}}</strong><br>


                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            Shipping Address
                        </div>
                        <div class="panel-body">
                            <strong class="text-capitalize text-muted">{{$order->shipping_firstname.' '.$order->shipping_lastname}}</strong><br>
                            Company: {{$order->shipping_company}}<br>
                            {{$order->shipping_address_1}}<br>
                            {{$order->shipping_address_2}}<br>
                            {{$order->shipping_city.', '.$order->shipping_postcode}}<br>

                        </div>
                    </div>
                    <div class="panel panel-default">

                        <div class="panel-body">
                            @if($order->status->name!='Complete')
                                <a class="btn btn-success" href="{{url('completeOrder',[$order->order_id])}}">Complete Order</a>
                            @else
                                <strong class="text-capitalize text-muted">Order Status: {{$order->status->name}}</strong><br>
                            @endif

                        </div>
                    </div>
                </div>
            </div>


        @endif
    </div>

@endsection