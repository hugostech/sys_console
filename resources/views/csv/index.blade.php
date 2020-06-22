@extends('master')

@section('mainContent')
    <div class="col-md-12">
        <div class="panel panel-default">
            @if(\Illuminate\Support\Facades\Session::has('success'))
            <div class="alert alert-success alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <strong>Success!</strong> {{\Illuminate\Support\Facades\Session::get('success')}}
            </div>
            @endif
            <div class="panel-body">
                <div class="page-header">
                    <h3>CSV Import History</h3>
                </div>
                {!! Form::open(['url'=>'csv/batchUpload','method'=>'post','files'=>true,'class'=>'form-inline']) !!}
                <div class="form-group">
                    {!! Form::file('csvs[]',['multiple']) !!}

                </div>
                <div class="form-group">
                    {!! Form::submit('Upload CSVs',['class'=>'btn btn-sm btn-primary']) !!}
                </div>
                {!! Form::close() !!}

            </div>
            <table class="table table-striped">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Supplier</th>
                    <th>Last Import Date</th>
                    <th>CSV Received?</th>
                </tr>
                </thead>
                <tbody>
                @foreach($csvRecords as $key=>$item)
                    @if(isset($supplier_list[$item['supplier_code']]))
                    <tr>
                        <td>{{$key+1}}</td>
                        <td>{{$supplier_list[$item['supplier_code']][0]}} <small>[ {{$supplier_list[$item['supplier_code']][1]}} ]</small></td>
                        <td><label class="text-danger">{{$item['updated_at']}}</label></td>
                        <td>
                            @if($supplier_list[$item['supplier_code']][2])
                                <button class="btn btn-xs btn-success">Yes</button>
                            @else
                                <button class="btn btn-xs btn-danger">No</button>
                            @endif
                        </td>
                    </tr>
                    @endif
                @endforeach

                </tbody>
            </table>
        </div>
    </div>
    <script>
        function run() {
            $('#progress_bar').removeClass('sr-only');
        }

        function doClear(type) {
            if (confirm('Are you sure to start clean the csv products?')){
                window.location = '{{url('csv/import/clear')}}?type='+type;
            }
        }
        function runDel() {
            if ($('#del_input').val()==='DELETE'){
                window.location = '{{url('csv/import/del')}}';
            }
        }
    </script>
@endsection