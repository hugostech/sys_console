@extends('master')

@section('mainContent')
    <table class="table table-striped">
        <thead>
        <tr>
            <th>Model</th>
            <th>Available Date</th>
            <th>Action</th>
        </tr>
        </thead>
        @foreach($etas as $eta)
            <tr>
                <td>{{$eta->model}}</td>
                <td>{{$eta->available_time}}</td>
                <td><a href="{{url('/eta_remove',[$eta->id])}}" class="btn btn-danger">Del</a></td>
            </tr>
        @endforeach
        {!! Form::open(['url'=>'eta_list']) !!}
            <tr>
                <td id="models"><input type="string" name="model[]" class="form-control" required placeholder="Model"></td>
                <td><input type="date" name="available_time" class="form-control" required></td>
                <td><input type="submit" value="submit" class="btn btn-primary"><button type="button" onclick="addone()" class="btn btn-default">more</button></td>
            </tr>
        {!! Form::close() !!}
    </table>
    <script>
        function addone(){
            var content = '<input type="string" name="model[]" class="form-control" placeholder="Model">';
            $(content).appendTo('#models');
        }
    </script>
@endsection