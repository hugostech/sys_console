@extends('draw.master')

@section('content')
<div class="col-sm-5 col-sm-offset-4 bg-ottt" >

    <div class="page-header">
        {{--{{dd(\Illuminate\Support\Facades\Session::get('info'))}}--}}
        @if(!is_null(Session('info')))

            <p class="alert alert-success">{{ Session('info') }} <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
        @endif
        <h2><a href="http://extremepc.co.nz"><img src="{{url('image',['exlogo.png'])}}"></a> Lucky Draw</h2>
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
    <div class="form-group">
        <address>
            <strong>ExtremePC</strong><br>
            Shop A2, St Lukes Mega Centre<br>
            1 Wagener Place, St Lukes, Auckland<br>
            <abbr title="Web">U:</abbr> <a href="http://www.extremepc.co.nz">www.extremepc.co.nz</a><br>
            <abbr title="Phone">P:</abbr> <a href="tel:+64-9-849-4888">(09) 849-4888</a><br>
            <abbr title="Email">E:</abbr> <a href="mailto:sales@roctrech.co.nz">sales@roctrech.co.nz</a>

        </address>
    </div>
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