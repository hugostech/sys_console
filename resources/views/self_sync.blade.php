<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Self Snyc</title>
</head>
<body>
    {{$content}}
<script language="JavaScript">

    function myrefresh(){
        window.location.reload();
    }
    setTimeout('myrefresh()',86400000); //指定1秒刷新一次
</script>
</body>
</html>
