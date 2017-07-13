<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Self Snyc</title>
    <script src="{{url('/',['js','jquery-2.2.0.min.js'])}}"></script>
    <script src="{{url('',['js','bootstrap.min.js'])}}"></script>
</head>
<body>
<div class="container">


    <h2>Report</h2>
    <hr>
    <label>{{$content}}</label>
    <table class="table table-bordered">
        <thead>
        <tr>
            <th class="col-md-3">
                Enable Product(Total)
            </th>
            <th class="col-md-3">
                Disable Product(Total)
            </th>
            <th class="col-md-3">
                Skip Product(This time)
            </th>
            <th class="col-md-3">
                Disable Product(This time)
            </th>
        </tr>
        <tr>
            <td>{{$total_enable}}</td>
            <td>{{$total_disable}}</td>
            <td>{{count($unsync)}}</td>
            <td>{{count($disable)}}</td>
        </tr>
        <tr>
            <th colspan="2">
                @foreach($unsync as $item)
                    {{$item}} &nbsp;&nbsp;-&nbsp;&nbsp;
                @endforeach
            </th>
            <th colspan="2">
                @foreach($disable as $item)
                    {{$item}} &nbsp;&nbsp;-&nbsp;&nbsp;
                @endforeach
            </th>
        </tr>
        </thead>
    </table>
</div>
<iframe src="{{url().'royalpoint/run'}}"></iframe>
<script language="JavaScript">

    function myrefresh(){
        window.location.reload();
    }
    setTimeout('myrefresh()',86400000); //指定1秒刷新一次
</script>
</body>
</html>
