@extends('master')

@section('mainContent')
    {!! Form::open(['url'=>'sales_list']) !!}
    <table class="table table-striped">
        <thead>
        <tr>
            <th>Model</th>
            <th>Available Date</th>
            <th>Action</th>
        </tr>
        </thead>
        @foreach($result as $single)
            <tr>
                <td>{{$single['product']->model}}</td>
                <td></td>
                {{--<td>{{round($single['special']->price*1.15,2)}}</td>--}}
                <td><a href="{{url('/sales_remove',[$single['product']->product_id])}}" class="btn btn-danger">Del</a></td>
            </tr>
        @endforeach

            <tr>
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