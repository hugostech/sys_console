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
                <td><input type="string" name="model" class="form-control" required placeholder="Model"></td>
                <td><input type="date" name="available_time" class="form-control" required></td>
                <td><input type="submit" value="Add" class="btn btn-primary"></td>
            </tr>
        {!! Form::close() !!}
    </table>

@endsection