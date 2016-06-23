<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Urgent mail</title>
    <script src="{{url('/',['js','jquery-2.2.0.min.js'])}}"></script>
    <script src="{{url('',['js','bootstrap.min.js'])}}"></script>
</head>
<body>
<div class="container">
    @foreach($urgentlist as $order)
    <table class="table table-bordered">
        <thead>
        <tr>
            <th>Order ID</th>
            <th>Order Time</th>
            <th>Status</th>
        </tr>
        <tr>
            <td>{{$oder[0]->order_id}}</td>
            <td>{{$oder[0]->date_added}}</td>
            <td>{{$oder[2]}}</td>
        </tr>
        <tr>
            <th colspan="2">Product</th>
            <th>Model</th>
        </tr>
        @foreach($order[1] as $product)
        <tr>
            <td colspan="2">{{$product->name}}</td>
            <td>{{$product->model}}</td>
        </tr>
        </thead>
    </table>
    @endforeach
</div>

</body>
</html>