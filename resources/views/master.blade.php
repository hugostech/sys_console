<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('sync_percentage') Warranty Management System</title>
    <link rel="stylesheet" href="{{url('/',['css','bootstrap.min.css'])}}">
    <script src="{{url('/',['js','jquery-2.2.0.min.js'])}}"></script>
    <script src="{{url('',['js','bootstrap.min.js'])}}"></script>
    <script src="{{url('',['js','special.js'])}}"></script>
    <script src="{{url('',['js','angular.min.js'])}}"></script>
    <script charset="utf-8" src="{{url('',['js','ckeditor','ckeditor.js'])}}"></script>
    <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.16/css/jquery.dataTables.css">
    <script type="text/javascript" charset="utf8" src="//cdn.datatables.net/1.10.16/js/jquery.dataTables.js"></script>
    @yield('script')
    {{--<script charset="utf-8" src="{{url('',['js','kindeditor','kindeditor-all-min.js'])}}"></script>--}}
    {{--<script charset="utf-8" src="{{url('',['js','kindeditor','lang','zh-CN.js'])}}"></script>--}}
    {{--<script>--}}

        {{--KindEditor.ready(function(K) {--}}
            {{--window.editor = K.create('#warranty_detail');--}}

        {{--});--}}
    {{--</script>--}}
    {{--<script src="{{url('',['js','wymeditor','wymeditor','jquery.wymeditor.min.js'])}}"></script>--}}

    <style>

        /* Paste this css to your style sheet file or under head tag */
        /* This only works with JavaScript,
        if it's not present, don't show loader */
        {{--.no-js #loader { display: none;  }--}}
        {{--.js #loader { display: block; position: absolute; left: 100px; top: 0; }--}}
        {{--.se-pre-con {--}}
            {{--position: fixed;--}}
            {{--left: 0px;--}}
            {{--top: 0px;--}}
            {{--width: 100%;--}}
            {{--height: 100%;--}}
            {{--z-index: 9999;--}}
            {{--background: url("{{url('/',['image','Preloader.gif'])}}") center no-repeat #fff;--}}
        {{--}--}}
        #footer {
            background-color: #f5f5f5;
        }
    </style>
    <script>
    //paste this code under the head tag or in a separate js file.
    // Wait for window load
//    $(window).load(function() {
//    // Animate loader off screen
//    $(".se-pre-con").fadeOut("slow");;
//    });
    </script>

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
                            <li><a href="{{url('/',['warrantyGuide'])}}">Warranty Guide</a></li>

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
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Tools <span class="caret"></span></a>
                                <ul class="dropdown-menu">
                                    <li><a href="{{url('/',['listAll'])}}">All</a></li>
                                    <li><a href="{{url('/',['list'])}}">On going</a></li>
                                    <li><a href="{{url('/',['listFinish'])}}">Finish</a></li>
                                    {{--<li><a href="#">Something else here</a></li>--}}
                                    <li role="separator" class="divider"></li>
                                    <li><a href="{{url('/',['killprice'])}}">Kill price</a></li>
                                    <li><a href="{{url('/',['lockProducts'])}}">Lock List</a></li>

                                    <li><a href="{{url('/',['eta_list'])}}">ETA</a></li>
                                    <li><a href="{{url('/',['sales_list'])}}">Sales</a></li>
                                    <li><a href="{{url('/',['weekendsale'])}}">Weekend Sale</a></li>
                                    <li><a href="{{url('/',['flash_sale'])}}">Flash Sale</a></li>
                                    <li><a href="{{url('/',['categoryArrange'])}}">categoryArrange</a></li>
                                    <li><a href="{{url('/category',['shade'])}}">categorySale</a></li>
                                    <li><a href="{{url('/',['listAllKillProduct'])}}">listAllKillingProducts</a></li>
                                    {{--<li><a href="{{url('/',['sync'])}}">Sync</a></li>--}}
                                    <li><a href="{{url('/',['putProducts2Base'])}}">putProducts2Base</a></li>
                                    <li><a href="{{url('/',['listProductFromCategory'])}}">listProductFromCategory</a></li>
                                    <li><a href="{{url('/',['findMissProduct'])}}">findMissProduct</a></li>
                                    <li><a href="{{url('/',['ex_order_confirm'])}}">ex_order_finder</a></li>
                                    <li><a href="{{url('/csv',['import'])}}">Csv Import</a></li>
                                    <li><a href="{{url('/csv',['selfcheck'])}}">Csv Check Error</a></li>
                                    <li role="separator" class="divider"></li>
                                    <li><a href="{{url('/',['createLabel'])}}">Create Label</a></li>
                                    <li><a href="{{url('/',['luckydrawlist'])}}">lucky draw list</a></li>
                                    <li><a href="http://italker.info/lunch">Lunch</a></li>
                                    <li role="separator" class="divider"></li>

                                    <li><a href="{{url('runKillPrice')}}">SyncAutoKill</a></li>
                                    <li><a href="{{url('self_sync')}}">SyncRoc</a></li>
                                    {{--<li><a href="{{url('/',['syncproall'])}}">SyncAllProduct</a></li>--}}
                                    {{--<li><a href="{{url('/',['syncpro'])}}">SyncSingleProduct</a></li>--}}
                                    {{--<li><a href="{{url('/',['self_check'])}}">self_check</a></li>--}}

                                    <li><a href="{{url('/',['supplier'])}}">new Supplier</a></li>

                                    <li><a href="{{url('/',['suppliers'])}}">edit Supplier</a></li>
                                    <li><a href="{{url('/',['addWarrantyGuide'])}}">edit warranty guide</a></li>


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
            @yield('footer')
            {{--@include('errors.error')--}}
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
    <footer class="container-fluid" style="background-color: #f5f5f5;margin-top: 20px;">

        <div class="container">
            <div class="col-md-10 text-right" style="padding-top:8px ">
            <label>Powered by <a href="https://nz.linkedin.com/in/hankunwang
">HugoW</a></label>
            </div>
        </div>

    </footer>
</body>
<div class="se-pre-con"></div>
</html>
