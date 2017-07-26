@extends('master')

@section('mainContent')
    <div class="col-md-12">
        <table class="table table-bordered ">
            <thead>
            <tr>
                <th>#</th>
                <th>Name | <a href="?filter=error">Show Error</a> | <a href="?filter=bottom">Show Bottom</a></th>
                <th>Model</th>
                <th>Bottom Price</th>
                <th>Extremepc Price</th>

                <th>Note</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            @foreach($products as $key=>$product)
                @if(!is_null(\App\Ex_product::find($product->product_id)))
                <tr>
                    <td>{{$key + 1}}</td>
                    <td id="product_detail_{{$product->model}}">
                        {{\App\Ex_product::find($product->product_id)->description->name}}
                        <hr>
                        {{$kill_list}}
                    </td>
                    <td>
                        {{$product->model}}
                        <button type="button" class="btn btn-primary btn-xs" onclick="getPrice('{{ $product->model }}')">Show price</button>
                        <a href="{{$product->url}}" target="_blank">Pricespy link</a>

                    </td>
                    <td>
                        {{--{!! Form::open(['url'=>'editBottomPrice','class'=>'form-inline']) !!}--}}
                            {{--{!! Form::input('hidden','product_id',$product->id) !!}--}}
                        <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-addon">$</span>
                                {!! Form::text('bottomPrice',$product->bottomPrice,['id'=>'producBottom_'.$product->id]) !!}

                            </div>
                        </div>
                        <div class="form-group">
                            {{--{!! Form::submit('Save',['class'=>'btn btn-xs']) !!}--}}
                            <button class="btn btn-sm" type="button" onclick="resetBottom('{{$product->id}}')">Save</button>
                        </div>

                        {{--{!! Form::close() !!}--}}

                    </td>
                    <td>
                        {{--{{!is_null(\App\Ex_product::find($product->product_id))?'t':'f'}}--}}
                        @if(is_null(\App\Ex_product::find($product->product_id)->special))
                            ${{ round(\App\Ex_product::find($product->product_id)->price * 1.15,2)}}
                        @else
                            ${{round(\App\Ex_product::find($product->product_id)->special->price * 1.15,2)}}
                            @if(round(\App\Ex_product::find($product->product_id)->special->price * 1.15,2)<$product->bottomPrice)
                                <sup class="text-info">Error</sup>
                            @elseif(round(\App\Ex_product::find($product->product_id)->special->price * 1.15,2)==$product->bottomPrice)
                                <sup class="text-primary">Touch Bottom</sup>
                            @else
                                <sup class="text-danger">On Sale</sup>
                            @endif
                        @endif



                    </td>
                    <td>{!! $product->note !!}</td>
                    <td id="td_item_{{$product->id}}">
                        <button class="btn btn-danger" type="button" onclick="removeItem({{$product->id}})">Del</button>
                        {{--<a href="{{url('killprice',[$product->id,'remove'])}}" type="button" class="btn btn-danger btn-sm"><span class="glyphicon glyphicon-trash"></span></a>--}}
                    </td>
                </tr>
                @else
                    <tr>
                        <td colspan="6">
                            {{$product->model}}

                        </td>
                        <td><a href="{{url('killprice',[$product->id,'remove'])}}" type="button" class="btn btn-danger btn-sm"><span class="glyphicon glyphicon-trash"></span></a></td>
                    </tr>
                @endif
            @endforeach
            </tbody>
        </table>
        <p id="error_debug"></p>
    </div>
    <script>
        function removeItem(id) {
            var url = '{{url('killprice')}}/'+id+'/remove';

            $.ajax({url: url, success: function(result){

                $("#td_item_"+id).html('<span class="glyphicon glyphicon-trash"></span>');
            }});
        }
        function getPrice(code) {
            var url = '{{url('grabProductDetail')}}/'+code;
//            alert(url);
            $.ajax( url )
                .done(function(result) {
                    $('#product_detail_'+code).append(
                        '<hr>'+result
                    )
//                    alert( result );
                })

        }

        function resetBottom(product_id){
//            alert(product_id)
            var url = '{{url('editBottomPrice')}}';
            var price = $('#producBottom_'+product_id).val();
            $.post( url, { product_id: product_id, bottomPrice: price})
                .done(function( data ) {
                    alert( data );
                });

        }
    </script>
@endsection
