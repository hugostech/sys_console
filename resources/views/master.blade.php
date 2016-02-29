<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Warranty Management System</title>
    <link rel="stylesheet" href="{{url('/',['css','bootstrap.min.css'])}}">
    <script src="{{url('/',['js','jquery-2.2.0.min.js'])}}"></script>
    <script src="{{url('',['js','bootstrap.min.js'])}}"></script>
    <script src="{{url('',['js','special.js'])}}"></script>

</head>
<body>
    <div class="container">
        <div class="header" id="header">
            <nav class="navbar navbar-default">
                <div class="container-fluid">
                    <!-- Brand and toggle get grouped for better mobile display -->
                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                            <span class="sr-only">Toggle navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                        <a class="navbar-brand" href="{{url('list')}}">ExtremePC</a>
                    </div>

                    <!-- Collect the nav links, forms, and other content for toggling -->
                    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                        <ul class="nav navbar-nav">
                            <li><a href="{{url('/',['list'])}}">Home <span class="sr-only">(current)</span></a></li>
                            <li><a href="{{url('/',['warranty'])}}"><strong>New Warranty</strong></a></li>
                            <li><a href="#" onclick="goBack()">Go Back</a></li>

                        </ul>

                            {!! Form::open(['url'=>'/list','method'=>'put','class'=>'navbar-form navbar-right','role'=>'search']) !!}
                            <div class="input-group">
                                <input type="text" class="form-control" name="condition" placeholder="Search for...">
                                  <span class="input-group-btn">
                                    <button class="btn btn-default" type="submit">Go!</button>
                                  </span>
                            </div><!-- /input-group -->
                            {!! Form::close() !!}

                        <ul class="nav navbar-nav navbar-right">
                            {{--<li><a href="#">Link</a></li>--}}
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Order by <span class="caret"></span></a>
                                <ul class="dropdown-menu">
                                    <li><a href="{{url('/',['listAll'])}}">All</a></li>
                                    <li><a href="{{url('/',['list'])}}">On going</a></li>
                                    <li><a href="{{url('/',['listFinish'])}}">Finish</a></li>
                                    {{--<li><a href="#">Something else here</a></li>--}}
                                    <li role="separator" class="divider"></li>
                                    <li><a href="{{url('/',['supplier'])}}">new Supplier</a></li>

                                    <li><a href="{{url('/',['suppliers'])}}">edit Supplier</a></li>
                                </ul>
                            </li>
                        </ul>
                    </div><!-- /.navbar-collapse -->
                </div><!-- /.container-fluid -->
            </nav>
        </div>
        <div class="mainContent" id="mainContent">
            @yield('mainContent')
        </div>
        <div class="footer" id="footer">
            {{--@include('errors.error')--}}
        </div>
    </div>

</body>
</html>
