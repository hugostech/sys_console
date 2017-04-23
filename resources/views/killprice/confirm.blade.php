@extends('master')

@section('mainContent')
    <div class="col-md-12">
        {!! Form::open(['url'=>'killpriceConfirm']) !!}
            <table class="table table-bordered">
                <thead>
                <tr>
                    <td></td>
                    <td>Company</td>
                    <td>Price</td>
                </tr>
                </thead>
                <tbody>
                @foreach($priceList as $item)
                <tr>
                    <td>{!! Form::checkbox('companies',$item[0]) !!} </td>
                    <td>{{@item[0]}}</td>
                    <td>{{@item[1]}}</td>
                </tr>


                </tbody>
            </table>
        {!! Form::close() !!}
    </div>

@endsection