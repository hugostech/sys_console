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
    <style>
        table, th, td {
            border: 1px solid black;
        }
    </style>
    @foreach($clients as $client)
    <table class="table table-bordered">
        <thead>
        <tr>
            <th>Client Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Add time</th>
        </tr>
        <tr>
            <td>{{$client->firstname.' '.$client->lastname}}</td>
            <td>{{$client->email}}</td>
            <td>{{$client->telephone}}</td>
            <td>{{$client->date_added}}</td>

        </tr>

        </thead>
    </table>
    @endforeach
</div>

</body>
</html>