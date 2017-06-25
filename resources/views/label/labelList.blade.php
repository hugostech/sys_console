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
                    <td>
                        @if($label->type == 1)
                        {!! $label->description !!}
                        @else

                            @foreach(json_decode($label->description,true) as $item)
                                {!! $item !!}
                            @endforeach

                        @endif
                    </td>
                    <td>{{$label->price}}</td>
                    <td><a href="{{url('editLabel',[$label->id])}}" class="btn btn-primary">Edit</a>
                        <a class="btn btn-danger" href="{{url('removeLabelFromPrintList',[$label->id])}}?list=true">Remove from print list</a>

                    </td>
                </tr>
            @endforeach
            <tr>
                <td colspan="4">
                    <a class="btn btn-warning" href="?print=true">Print Short Label</a>
                    <a class="btn btn-warning" href="?print=true&long=true">Print Long Label</a>

                </td>
            </tr>
        </table>
        <div class="text-center">
            {{ $labels->links() }}
        </div>
    </div>

@endsection