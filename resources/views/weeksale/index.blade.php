@extends('master')

@section('mainContent')

    <div class="panel panel-default">
        @if(\Illuminate\Support\Facades\Session::has('alert-danger'))
            <div class="alert alert-danger alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <strong>Error!</strong> {{\Illuminate\Support\Facades\Session::get('alert-danger')}}
            </div>
        @endif
        <div class="panel-heading">
            <h3>Weekend Sales
                @if($editing_model)
                    <small class="text-muted">Editing Model</small>
                @endif
            </h3>
            <p class="text-muted text-capitalize text-danger"><span class="glyphicon glyphicon-alert"></span> All prices include GST</p>
        </div>
        <div class="panel-body">
            <div class="row">
                @foreach($weekendsale as $sale)
                <div class="col-sm-6 col-md-3">
                    <div class="thumbnail">
                        <div class="caption">
                            @if($editing_model)
                                @if($sale_id==$sale->id)
                                <h3 class="text-danger">Editing</h3>
                                @endif
                            @else
                                <p>
                                    <a href="{{route('weekendsale_up', ['id' => $sale->id])}}" onclick="return confirm('Are you sure?')" class="btn btn-primary btn-sm" role="button">Up</a>
                                    <a href="{{route('weekendsale_down', ['id' => $sale->id])}}" onclick="return confirm('Are you sure?')" class="btn btn-danger btn-sm" role="button">Down</a>
                                </p>
                            @endif

                            <p>Start Date: {{$sale->start_date}}</p>
                            <p>End Date: {{$sale->end_date}}</p>
                            <p>Last Update: {{$sale->updated_at}}</p>
                            <p>Status:
                            @if($sale->status == 1)
                                <label class="text-success">Running</label>
                            @else
                                <label class="text-danger">Stop</label>
                            @endif
                            </p>
                            @if(!$editing_model)
                            <a href="{{url('weekendsale',['sale',$sale->id])}}" class="btn btn-info btn-xs btn-block">Edit</a>
                            <a href="{{url('weekendsale',['del',$sale->id])}}" onclick="return confirm('Are you sure?')" class="btn btn-danger btn-xs btn-block">Del</a>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            <hr>


            <p class="text-right">
                @if(\Illuminate\Support\Facades\Input::has('a') || $editing_model)
                    <a href="{{url('weekendsale')}}" class="btn btn-warning btn-sm">Back</a>
                    <button type="button" class="btn btn-success btn-sm" onclick="submitForm()">Confirm</button>
                @else
                    <a href="{{url('weekendsale')}}?a=import" class="btn btn-primary btn-sm">Import</a>
                @endif
            </p>
        </div>
        @if(count($products)>0)
            @if($editing_model)
            {!! Form::open(['route'=>'weekendsale_update','id'=>'form_weekendsale']) !!}
            {!! Form::hidden('sale_id',$sale_id) !!}
            <div class="form-group col-sm-4">
                <label>End Date</label>
                {!! Form::input('date','end_date',$end_date,['class'=>'form-control']) !!}
            </div>
            @else
            {!! Form::open(['route'=>'weekendsale_create','id'=>'form_weekendsale']) !!}
            <div class="form-group col-sm-4">
                <label>End Date</label>
                {!! Form::input('date','end_date',null,['class'=>'form-control']) !!}
            </div>
            @endif
        <table class="table table-bordered" id="product_table">
            <thead>
            <tr>
                <th class="col-sm-1">Model</th>
                <th class="col-sm-1">Status</th>
                <th class="col-sm-3">Name</th>
                <th class="col-sm-1">Price(Cr)</th>
                <th class="col-sm-1">Special(Cr)</th>
                <th class="col-sm-1">Cost(Cr)</th>
                <th class="col-sm-2">Base Price</th>
                <th class="col-sm-2">Sale Price</th>
            </tr>
            </thead>
            <tbody>
            @foreach($products as $id=>$product)
            <tr>
                <td>{{$product['model']}}</td>
                <td class="text-center">
                @if($product['lock_status']==1)
                    <button type="button" class="btn btn-danger btn-xs">Locked</button>
                @else
                    <button type="button" class="btn btn-info btn-xs">Unlocked</button>
                @endif
                    <br>
                    <label>Stock: {{$product['stock']}}</label>
                </td>
                <td>{{$product['name']}}</td>
                <td>${{$product['price_current']}}</td>
                <td>${{$product['special_current']}}</td>
                <td>${{$product['cost']}}</td>
                <td>{!! Form::number('base['.$id.']',$product['sale_base'],['class'=>'form-control','step'=>0.01]) !!}</td>
                <td>{!! Form::number('special['.$id.']',$product['sale_special'],['class'=>'form-control','step'=>0.01]) !!}</td>
            </tr>
            @endforeach
            </tbody>
        </table>
        {!! Form::close() !!}
        @endif
    </div>

    <script>
        $(document).ready(function () {
            $('#product_table').dataTable({
                'paging': false
            });
        })
        function submitForm() {
            $('#form_weekendsale').submit();
        }
    </script>

@endsection