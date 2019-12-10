<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Self Snyc</title>
    <script src="{{url('/',['js','jquery-2.2.0.min.js'])}}"></script>
    <script src="{{url('',['js','bootstrap.min.js'])}}"></script>
</head>
<body onload="setRegular(18,00);">
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
<iframe src="{{url('royalpoint/run')}}"></iframe>

<script language="JavaScript">
    /*function myrefresh(){
        window.location.reload();
    }
    setTimeout('myrefresh()',86400000); //指定1秒刷新一次
    */
</script>


<script>
var syncTime = new Date();  
var syncHour = syncTime.getHours();
var syncMinutes = syncTime.getMinutes();
document.write("Stock Updated at "+syncTime +"<br>") ;//你自己的数据处理函数

 
function setRegular(targetHour, targetMinutes){
  var timeInterval,nowTime,nowSeconds,targetSeconds;

  nowTime = new Date();
  // 计算当前时间的秒数
  nowSeconds = nowTime.getHours() * 3600 + nowTime.getMinutes() * 60 + nowTime.getSeconds();
 
  // 计算目标时间对应的秒数
  targetSeconds =  targetHour * 3600 + targetMinutes * 60;
 
  //  判断是否已超过今日目标小时，若超过，时间间隔设置为距离明天目标小时的距离
  timeInterval = targetSeconds > nowSeconds ? targetSeconds - nowSeconds: targetSeconds + 24 * 3600 - nowSeconds;
 
  setTimeout(Sync, timeInterval * 1000);

    document.write (nowSeconds+"<br>");
    document.write (targetSeconds+"<br>");

}
 
function Sync(){
  window.location.reload();  
  setTimeout(Sync, 24*3600 * 1000);//之后每天调用一次
  //setTimeout(Sync, 60 * 1000);//之后每分钟调用一次
}


</script>

</body>
</html>
