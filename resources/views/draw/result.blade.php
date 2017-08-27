@extends('draw.master')

@section('content')
<div class="col-sm-12" >

    <div class="panel panel-info">
        <div class="panel-heading">
            <h4>Sign up list</h4>
        </div>
        <table class="table table-bordered table-stripped">
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Email</th>
            </tr>
            @foreach($list as $key=>$item)
                <tr>
                    <td>{{$key}}</td>
                    <td>{{$item->name}}</td>
                    <td>{{$item->email}}</td>
                </tr>
            @endforeach
        </table>
    </div>
</div>
@endsection