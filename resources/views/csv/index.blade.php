@extends('master')

@section('mainContent')
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3>CSV import</h3>
                <button type="button" class="btn btn-danger btn-xs" onclick="doClear()">Clean Products</button>
                <button type="button" class="btn btn-warning btn-xs" data-toggle="modal" data-target="#myModal">Delete All Disabled Products</button>
                <!-- Modal -->
                <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title text-danger" id="myModalLabel">Delete Confirm</h4>
                            </div>
                            <div class="modal-body">
                                <div class="form-group">
                                    <label>Please Type "DELETE"</label>
                                    <input type="text" class="form-control" id="del_input">
                                </div>


                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                <button type="button" class="btn btn-primary" onclick="runDel()">Confirm</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="panel-body">


                {!! Form::open(['url'=>'csv/import/run','method'=>'post','files'=>true]) !!}
                <div class="form-group col-md-4">
                    {{--<label>Supplier</label>--}}
                    {!! Form::select('supply_code', $supplier_list, null, ['placeholder' => 'Pick supplier','class'=>'form-control','required']) !!}
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
                                <th class="text-capitalize">mpn</th>
                                <th class="text-capitalize">stock</th>
                                <th class="text-capitalize">price</th>
                                <th class="text-capitalize">name</th>
                                <th class="text-capitalize">supplier code</th>
                            </tr>
                            <tr>
                                @foreach($firstsheet as $row)
                                    <td>{{$row}}</td>
                                @endforeach
                            </tr>
                        </table>
                        <a href="{{url('csv/import',[$supply_code,'start'])}}" class="btn btn-primary text-capitalize" onclick="run()">{{$supply_code}} Import!</a>
                        <br>
                        <div class="progress sr-only" id="progress_bar">
                            <div class="progress-bar progress-bar-striped active" role="progressbar"
                                 aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width:40%">
                                Importing...
                            </div>
                        </div>
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
                        <td>{{$item['supplier_code']}}</td>
                        <td><label class="text-danger">{{$item['updated_at']}}</label></td>
                    </tr>
                @endforeach

                </tbody>
            </table>
        </div>
    </div>
    <script>
        function run() {
            $('#progress_bar').removeClass('sr-only');
        }

        function doClear() {
            if (confirm('Are you sure to start clean the csv products?')){
                window.location = '{{url('csv/import/clear')}}';
            }
        }
        function runDel() {
            if ($('#del_input').val()==='DELETE'){
                window.location = '{{url('csv/import/del')}}';
            }
        }
    </script>
@endsection