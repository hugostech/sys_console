@extends('master')

@section('mainContent')

    <div class="panel panel-default">
        <div class="panel-heading">
            <h3>Weekend Sales
                @if($editing_model)
                    <strong>Editing Model</strong>
                @endif
            </h3>
        </div>
        <div class="panel-body">
            <h3>Current Weekend Sales</h3>
            <p class="text-muted text-capitalize text-danger"><span class="glyphicon glyphicon-alert"></span> All prices include GST</p>
            <p class="text-right">
                @if(\Illuminate\Support\Facades\Input::has('a') || $editing_model)
                    <a href="{{url('weekendsale')}}" class="btn btn-warning btn-xs">Back</a>
                    <button type="button" class="btn btn-success btn-xs" onclick="submitForm()">Confirm</button>
                @else
                    <a href="{{url('weekendsale')}}?a=import" class="btn btn-primary btn-xs">Import</a>
                @endif
            </p>
            <hr>
            <div class="row">
                @foreach($weekendsale as $sale)
                <div class="col-sm-6 col-md-3">
                    <div class="thumbnail">
                        <div class="caption">
                            <p>
                                <a href="#" class="btn btn-primary btn-sm" role="button">Up</a>
                                <a href="#" class="btn btn-danger btn-sm" role="button">Down</a></p>
                            <p>Start Date: {{$sale->start_date}}</p>
                            <p>Last Update: {{$sale->updated_at}}</p>
                            <p>Status:
                            @if($sale->status == 1)
                                <label class="text-success">Running</label>
                            @else
                                <label class="text-danger">Stop</label>
                            @endif
                            </p>
                            <a href="{{url('weekendsale',['sale',$sale->id])}}" class="btn btn-info btn-xs btn-block">Edit</a>
                            <a href="{{url('weekendsale',['del',$sale->id])}}" onclick="return confirm('Are you sure?')" class="btn btn-danger btn-xs btn-block">Del</a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

        </div>
        @if(count($products)>0)
            @if($editing_model)
            {!! Form::open(['route'=>'weekendsale_update','id'=>'form_weekendsale']) !!}
            {!! Form::hidden('sale_id',$sale_id) !!}
            @else
            {!! Form::open(['route'=>'weekendsale_create','id'=>'form_weekendsale']) !!}
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
            console.log('table init');
            $('#product_table').dataTable({
                'paging': false
            });
        })
        function submitForm() {
            $('#form_weekendsale').submit();
        }
    </script>

@endsection