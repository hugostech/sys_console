@extends('master')

@section('mainContent')

    <div>

        <!-- Nav tabs -->
        <ul class="nav nav-tabs" role="tablist">
            @foreach($categotys as $key=>$value)
                @if($key == 0)
            <li role="presentation" class="active"><a href="#{{$category_names[$value]}}" aria-controls="{{$category_names[$value]}}" role="tab" data-toggle="tab">{{$category_names[$value]}}</a></li>

                @else
            <li role="presentation"><a href="#{{$category_names[$value]}}" aria-controls="{{$category_names[$value]}}" role="tab" data-toggle="tab">{{$category_names[$value]}}</a></li>

                @endif
            @endforeach
        </ul>

        <!-- Tab panes -->
        <div class="tab-content">
            @foreach($categotys as $key=>$value)
                @if($key == 0)
                    <div role="tabpanel" class="tab-pane active" id="{{$category_names[$value]}}">
                        <div class="panel panel-info">
                            <div class="panel-heading">
                                <h3>{{$category_names[$value]}}</h3>

                            </div>
                            <div class="panel-body">
                                <div class="row">
                                <table class="table table-bordered ">
                                    <thead>
                                    <tr>
                                        <th class="col-sm-1">Code</th>
                                        <th class="col-sm-5">Content</th>
                                        <th class="col-sm-2">price</th>
                                        <th class="col-sm-2">RRP</th>
                                        <th class="col-sm-1">Quantity</th>
                                        <th class="col-sm-1"></th>
                                    </tr>

                                    </thead>
                                    <tbody>
                                    @foreach($category_products[$value] as $product)
                                        <tr>
                                            <td>{{$product->code}}</td>
                                            <td>{{$product->content}}</td>
                                            <td>{!! Form::input('number','price[]',round($product->price*1.15,2),["class"=>"form-control","step"=>"0.01","onchange"=>"changeprice(this)"]) !!}</td>
                                            <td>{!! Form::input('number','rrp[]',$product->rrp,["class"=>"form-control","step"=>"0.01","onchange"=>"changerrp(this)"]) !!}</td>
                                            <td>{!! Form::input('number','quantity',$product->qty,['class'=>'form-control','onchange'=>'changeqty(this)']) !!}</td>
                                            <td><button type="button" class="btn btn-danger" onclick="flash_del({{$product->id}})">Del</button></td>
                                        </tr>
                                    @endforeach
                                    <tr>
                                        {!! Form::open(['url'=>'add_flash_sale_product']) !!}
                                        <td colspan="3">
                                            {!! Form::text('code',null,['class'=>'form-control']) !!}
                                        </td>
                                        <td colspan="2">
                                            <button type="submit" class="btn btn-default">Add</button>
                                        </td>
                                        {!! Form::close() !!}
                                    </tr>
                                    </tbody>


                                </table>
                                </div>
                            </div>
                        </div>

                    </div>
                @else
                    <div role="tabpanel" class="tab-pane" id="{{$category_names[$value]}}">
                        <div class="panel panel-info">
                            <div class="panel-heading">
                                <h3>{{$category_names[$value]}}</h3>

                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    <table class="table table-bordered ">
                                        <thead>
                                        <tr>
                                            <th class="col-sm-1">Code</th>
                                            <th class="col-sm-5">Content</th>
                                            <th class="col-sm-2">price</th>
                                            <th class="col-sm-2">RRP</th>
                                            <th class="col-sm-1">Quantity</th>
                                            <th class="col-sm-1"></th>
                                        </tr>

                                        </thead>
                                        <tbody>
                                        @foreach($category_products[$value] as $product)
                                            <tr>
                                                <td>{{$product->code}}</td>
                                                <td>{{$product->content}}</td>
                                                <td>{!! Form::input('number','price[]',round($product->price*1.15,2),["class"=>"form-control","step"=>"0.01","onchange"=>"changeprice(this)"]) !!}</td>
                                                <td>{!! Form::input('number','rrp[]',$product->rrp,["class"=>"form-control","step"=>"0.01","onchange"=>"changerrp(this)"]) !!}</td>
                                                <td>{!! Form::input('number','quantity',$product->qty,['class'=>'form-control','onchange'=>'changeqty(this)']) !!}</td>
                                                <td><button type="button" class="btn btn-danger" onclick="flash_del({{$product->id}})">Del</button></td>
                                            </tr>
                                        @endforeach
                                        <tr>
                                            {!! Form::open(['url'=>'add_flash_sale_product']) !!}
                                            <td colspan="3">
                                                {!! Form::text('code',null,['class'=>'form-control']) !!}
                                            </td>
                                            <td colspan="2">
                                                <button type="submit" class="btn btn-default">Add</button>
                                            </td>
                                            {!! Form::close() !!}
                                        </tr>
                                        </tbody>


                                    </table>
                                </div>
                            </div>
                        </div>

                    </div>

                @endif
            @endforeach

        </div>

    </div>

    {!! Form::open(['url'=>'sales_list']) !!}

    {{--<table class="table table-striped">--}}
        {{--<thead>--}}
        {{--<tr>--}}
            {{--<th class="col-md-1"></th>--}}
            {{--<th class="col-md-2">Model</th>--}}
            {{--<th class="col-md-7">title</th>--}}
            {{--<th class="col-md-2">Action</th>--}}
        {{--</tr>--}}
        {{--</thead>--}}
        {{--@foreach($result as $key=>$single)--}}
            {{--<tr>--}}
                {{--<td>{{$key+1}}</td>--}}
                {{--<td>{{$single['product']->model}}</td>--}}
                {{--<td>{{$single['product_detail']->name}}</td>--}}
                {{--<td>{{round($single['special']->price*1.15,2)}}</td>--}}
                {{--<td><a href="{{url('/sales_remove',[$single['product']->product_id])}}" class="btn btn-danger">Del</a></td>--}}
            {{--</tr>--}}
        {{--@endforeach--}}

            {{--<tr>--}}
                {{--{!! Form::input('hidden','category_id',$category_id) !!}--}}
                {{--<td id="models"><input type="text" name="modelnum[]" class="form-control" placeholder="Model" required>--}}
                    {{--</td>--}}
                {{--<td></td>--}}
                {{--<td><input type="submit" value="submit" class="btn btn-primary"><button type="button" onclick="addone()" class="btn btn-default">more</button></td>--}}
            {{--</tr>--}}

    {{--</table>--}}
    {!! Form::close() !!}
    <script>
        function changeprice(item){
            var code = $(item).parents('tr').children(0).html();
            var price = $(item).val();
            var url = "{{url('flash_sale_price_edit')}}/"+code+"/"+price;

            $.ajax({
                url: url,
            }).done(function() {

                location.reload();

            });

        }
        function changeqty(item){
            var code = $(item).parents('tr').children(0).html();
            var qty = $(item).val();
            var url = "{{url('flash_sale_qty_edit')}}/"+code+"/"+qty;

            $.ajax({
                url: url,
            }).done(function() {

                location.reload();

            });

        }

        function changerrp(item){
            var code = $(item).parents('tr').children(0).html();
            var rrp = $(item).val();
            var url = "{{url('flash_sale_rrp_edit')}}/"+code+"/"+rrp;

            $.ajax({
                url: url,
            }).done(function() {

                location.reload();

            });

        }
        function flash_del(id){
            var url = "{{url('flash_sale_product_del')}}/"+id;
            $.ajax({
                url: url,
            }).done(function() {

                location.reload();

            });
        }
    </script>
@endsection