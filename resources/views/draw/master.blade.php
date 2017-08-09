<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>ExtremePC Lucky Draw</title>
    <link rel="stylesheet" href="{{url('/',['css','bootstrap.min.css'])}}">
    <script src="{{url('/',['js','jquery-2.2.0.min.js'])}}"></script>
    <script src="{{url('',['js','bootstrap.min.js'])}}"></script>
    <script src="{{url('',['js','angular.min.js'])}}"></script>
    <style>
        body{
            background: url("{{url('image/luckybg.jpg')}}") no-repeat ;
            /*background: no-repeat;*/
            /*background-position: center;*/
        }
        .bg-ottt{
            background-color: rgba(256,180, 180, 0.6);
            position: absolute;
            bottom:30px;
        }
    </style>
</head>
<body>
<div class="container-fluid">
    @yield('content')
</div>


</body>
</html>