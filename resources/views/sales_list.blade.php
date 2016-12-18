@extends('master')

@section('mainContent')
    {!! Form::open(['url'=>'sales_list']) !!}
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
                <td><a href="{{url('/sales_remove',[$single['product']->product_id])}}" class="btn btn-danger">Del</a></td>
            </tr>
        @endforeach

            <tr>
                {{--{!! Form::input('hidden','category_id',$category_id) !!}--}}
                <td id="models"><input type="text" name="modelnum[]" class="form-control" placeholder="Model" required>
                    </td>
                <td></td>
                <td><input type="submit" value="submit" class="btn btn-primary"><button type="button" onclick="addone()" class="btn btn-default">more</button></td>
            </tr>

    </table>
    {!! Form::close() !!}
    <script>
        function addone(){
            var content = "<input type='text' name='modelnum[]' class='form-control' placeholder='Model'>";
            $(content).appendTo('#models');
        }
    </script>
@endsection