@extends('master')

@section('mainContent')
    <div class="col-md-12">
        <table class="table table-bordered ">
            <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Model</th>
                <th>Bottom Price</th>
                <th>Extremepc Price</th>

                <th>Note</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            @foreach($products as $key=>$product)
                <tr>
                    <td>{{$key + 1}}</td>
                    <td id="product_detail">
                        {{\App\Ex_product::find($product->product_id)->description->name}}
                    </td>
                    <td>
                        {{$product->model}}
                        <button type="button" class="btn btn-primary btn-xs" onclick="getPrice('{{ $product->model }}')">Show price</button>

                    </td>
                    <td>
                        {!! Form::open(['url'=>'editBottomPrice','class'=>'form-inline']) !!}
                            {!! Form::input('hidden','product_id',$product->id) !!}
                        <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-addon">$</span>
                                {!! Form::number('bottomPrice',$product->bottomPrice) !!}

                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::submit('edit',['class'=>'btn btn-xs']) !!}
                        </div>

                        {!! Form::close() !!}

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
                    <td><a href="{{url('killprice',[$product->id,'remove'])}}" type="button" class="btn btn-danger btn-sm"><span class="glyphicon glyphicon-trash"></span></a></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <script>
        function getPrice(code) {
            var url = '{{url('grabProductDetail')}}/'+code;
//            alert(url);
            $.ajax( url )
                .done(function(result) {
                    $('#product_detail').append(
                        '<hr>'+result
                    )
//                    alert( result );
                })

        }
    </script>
@endsection
