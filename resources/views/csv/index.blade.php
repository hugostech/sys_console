@extends('master')

@section('mainContent')
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3>CSV import</h3>
            </div>
            <div class="panel-body">
                {!! Form::open(['url'=>'csv/import/run','method'=>'post','files'=>true]) !!}
                <div class="form-group col-md-4">
                    {{--<label>Supplier</label>--}}
                    {!! Form::select('supply_code', ['pb' => 'PB', 'aw' => 'Anywhere'], null, ['placeholder' => 'Pick supplier','class'=>'form-control','required']) !!}
                </div>
                <div class="form-group col-md-4">
                    {!! Form::input('file','csv') !!}
                </div>
                <div class="form-group col-md-4">
                    {!! Form::submit('Upload',['class'=>'btn btn-block btn-primary']) !!}
                </div>
                {!! Form::close() !!}
                @if(isset($firstsheet))

                @if(is_array($firstsheet))
                    <div class="form-group">
                        <label>First Sheet</label>
                        <table class="table table-bordered">
                            <tr>
                                @foreach($firstsheet as $row)
                                    <td>{{$row}}</td>
                                @endforeach
                            </tr>
                        </table>
                        <a href="{{url('csv/import',[$supply_code,'start'])}}" class="btn btn-primary text-capitalize">{{$supply_code}} Import!</a>
                    </div>


                @else
                    <div class="alert alert-danger">
                        <strong>Error!</strong> {{$firstsheet}}
                    </div>
                @endif
                @endif
            </div>
            <table class="table table-striped">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Supplier</th>
                    <th>Last Import Date</th>
                </tr>
                </thead>
                <tbody>
                @foreach($csvRecords as $key=>$item)
                    <tr>
                        <td>{{$key+1}}</td>
                        <td>{{$item->supplier_code}}</td>
                        <td class="text-muted text-danger">{{$key->updated_at}}</td>
                    </tr>
                @endforeach

                </tbody>
            </table>
        </div>
    </div>
@endsection