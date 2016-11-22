@extends('master')

@section('mainContent')
    <div class="col-md-12">

        <div class="panel panel-info">
            <div class="panel-heading">
                <h3>Flash sale product list</h3>
                <a class="btn btn-primary" href="{{url('publishFlash')}}">Publish</a>
                <a class="btn btn-danger" href="{{url('offlineFlash')}}">Offline</a>
            </div>
            <div class="panel-body">
                <table class="table table-bordered ">
                    <thead>
                    <tr>
                        <th class="col-sm-1">Code</th>
                        <th class="col-sm-6">Content</th>
                        <th class="col-sm-2">price</th>
                        <th class="col-sm-2">Quantity</th>
                        <th class="col-sm-1"></th>
                    </tr>

                    </thead>
                    <tbody>
                    @foreach($products as $product)
                    <tr>
                        <td>{{$product->code}}</td>
                        <td>{{$product->content}}</td>
                        <td>{!! Form::input('number','price[]',round($product->price*1.15,2),["class"=>"form-control","step"=>"0.01","onchange"=>"changeprice(this)"]) !!}</td>
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
            var price = $(item).val();
            var url = "{{url('flash_sale_price_edit')}}/"+code+"/"+price;

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