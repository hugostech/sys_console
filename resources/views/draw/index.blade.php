@extends('draw.master')

@section('content')
<div class="col-sm-5 col-sm-offset-4 bg-ottt" >

    <div class="page-header">
        {{--{{dd(\Illuminate\Support\Facades\Session::get('info'))}}--}}
        @if(!is_null(Session('info')))

            <p class="alert alert-success">{{ Session('info') }} <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
        @endif
        <h3>Lucky Draw</h3>
    </div>
    {!! Form::open(['url'=>'/luckyydraw/register','method'=>'post']) !!}

    <div class="form-group">
        <label>Name</label>
        {!! Form::text('name',null,['class'=>'form-control']) !!}
    </div>
    <div class="form-group">
        <label>Email</label>
        {!! Form::input('email','email',null,['class'=>'form-control']) !!}
    </div>
    <div class="form-group">
        <label>Phone <small>(optional)</small></label>
        {!! Form::text('phone',null,['class'=>'form-control']) !!}
    </div>
    <div class="form-group">
        {!! Form::submit('Sign up',['class'=>'btn btn-primary']) !!}
    </div>
    {!! Form::close() !!}
    @if(isset($errors))

        @if (count($errors) > 0)
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    @endif
</div>
@endsection