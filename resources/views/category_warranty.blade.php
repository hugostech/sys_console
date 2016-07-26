@extends('master')

@section('mainContent')

    <div class="col-md-6"></div>
    <div class="col-md-2"><button type="button" class="btn btn-success" data-toggle="modal" data-target="#myModal">Add new category</button></div>
    <div class="col-md-2"><button type="button" class="btn btn-success" data-toggle="modal" data-target="#myModal2">Add new Warranty</button></div>
    
    <div class="col-md-12" style="border-top: solid 1px gray;margin-top: 10px;padding: 10px;">
        @foreach($suppliers as $supplier)
            <button type="button" class="btn btn-success" data-toggle="modal" data-target="#myModal3" onclick="sysCategory({{$supplier->id}})">{{$supplier->name}}</button>
        @endforeach


    </div>
    <div class="col-md-12" style="border-top: solid 1px grey;padding: 10px;border-bottom: solid 1px grey">
        @foreach($category_warrantys as $warranty)
            <a type="button" class="btn btn-success" href="#">{{$warranty->supplier}}</a>
        @endforeach

    </div>


    <!-- Modal 1-->
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">New Category</h4>
                </div>
                {!! Form::open(['url'=>'addWarrantyGuide','method'=>'patch']) !!}
                <div class="modal-body">
                    <div class="form-group">
                        {!! form::text('name',null,['class'=>'form-control','placeholder'=>'Name']) !!}
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    {{--<button type="button" class="btn btn-primary">Save changes</button>--}}
                    {!! Form::submit('Save',['class'=>'btn btn-primary']) !!}
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>

    <!-- Modal 2-->
    <div class="modal fade" id="myModal2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Warranty Details</h4>
                </div>
                {!! Form::open(['url'=>'addWarrantyGuide','method'=>'put']) !!}
                <div class="modal-body">
                    <div class="form-group">
                        {!! form::text('supplier',null,['class'=>'form-control','placeholder'=>'Supplier Name']) !!}
                    </div>
                    <div class="form-group">
                        {!! form::text('tag',null,['class'=>'form-control','placeholder'=>'Tag...','required']) !!}
                    </div>
                    <div class="form-group">
                        <textarea name="detail" rows="5" class="form-control" placeholder="Details..." id="warranty_detail"></textarea>
                        <script>
                            $('#warranty_detail').wymeditor();
                        </script>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    {!! Form::submit('Save',['class'=>'btn btn-primary']) !!}
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>

    <!-- Modal 3-->
    <div class="modal fade" id="myModal3" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Link</h4>
                </div>
                {!! Form::open(['url'=>'addWarrantyGuide']) !!}

                <input type="hidden" name="category_id" id="category_id" value="">
                <div class="modal-body">
                    <div class="form-group">
                        <ul class="list-group">
                            @foreach($category_warrantys as $warranty)
                                <li class="list-group-item">
                                <span class="checkbox">
                                    <label>
                                        <input type="checkbox" value="{{$warranty->id}}" name="warranty_id{{$warranty->id}}"> {{$warranty->tag}}
                                    </label>
                                </span>
                                </li>
                            @endforeach


                        </ul>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    {!! Form::submit('Save',['class'=>'btn btn-primary']) !!}
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>

@endsection

@section('footer')
    <script>
        function sysCategory(id){
            $('#category_id').val(id);
        }
    </script>
@endsection