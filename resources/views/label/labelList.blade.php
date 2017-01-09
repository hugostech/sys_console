@extends('master')

@section('mainContent')



    <div class="col-sm-12">
        <table class="table">
            <tr>
                <th>Code</th>
                <th>Desciption</th>
                <th>Price</th>
                <th></th>
            </tr>
            @foreach($labels as $label)
                <tr>
                    <td>{{$label->code}}</td>
                    <td>{!! $label->description !!}</td>
                    <td>{{$label->price}}</td>
                    <td><a href="{{url('editLabel',[$label->id])}}" class="btn btn-primary">Edit</a>
                        <a class="btn btn-danger" href="{{url('removeLabelFromPrintList',[$label->id])}}">Remove from print list</a>

                    </td>
                </tr>
            @endforeach
            <tr>
                <td colspan="4">
                    <a class="btn btn-warning" href="?print=true">Print</a>
                </td>
            </tr>
        </table>
        <div class="text-center">
            {{ $labels->links() }}
        </div>
    </div>

@endsection