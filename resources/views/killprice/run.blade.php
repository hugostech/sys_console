<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>kill price</title>
    <script src="{{url('/',['js','jquery-2.2.0.min.js'])}}"></script>
    <script src="{{url('',['js','bootstrap.min.js'])}}"></script>
</head>
<body>
<div class="container">


    <h2>Kill price running! last Update {{\Carbon\Carbon::now()}}</h2>
    <label><a href="{{url('listAllKillProduct')}}">Report</a></label>

</div>
<script language="JavaScript">

    function myrefresh(){
        window.location.reload();
    }
    setTimeout('myrefresh()',86400000); //指定1秒刷新一次
</script>
</body>
</html>
