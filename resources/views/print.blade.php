<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Warranty form</title>
    <link rel="stylesheet" href="{{url('/',['css','bootstrap.min.css'])}}">
    <style>
        .emBorder{
            border-style: solid;
            border-color: #ddd;
            border-width: 1px;
            /*border-radius: 4px 4px 0 0;*/
        }
    </style>
</head>
<body onload="window.print()">
    <div class="container">

        <div class="col-sm-12">
            <div class="col-xs-12 text-center">
                <h1>Extreme PC Warranty Form</h1>
                <br>
            </div>
            <div class="col-xs-5">
                <address>
                    <strong>Extreme PC</strong><br>
                    Shop A2, St Lukes Mega Centre<br>
                    1 Wagener Road,<br>
                    St Lukes, Auckland<br>
                    <abbr title="Phone">P:</abbr> (09) 2814198 <br>
                    <abbr title="Fax">F:</abbr> (09) 8151469
                </address>
            </div>
            <div class="col-xs-7 text-right">
                <h5>Technician: <ins>&nbsp;&nbsp;&nbsp;{{$warranty->staff}}&nbsp;&nbsp;&nbsp;</ins> Warranty No: <ins>&nbsp;&nbsp;&nbsp;{{$warranty->id}}&nbsp;&nbsp;&nbsp;</ins></h5>
                <h5>Submit date: <ins> {{$warranty->created_at}} </ins></h5>
                <br>
                <p>Web: <ins>www.roctech.co.nz</ins><br>Email: <ins>sales@roctech.co.nz</ins></p>
            </div>
            <div class="col-xs-12">
                <strong>Customer Name:</strong><ins>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$warranty->client_name}}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</ins>&nbsp;&nbsp;&nbsp;
                <strong>Customer Phone:</strong><ins>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$warranty->client_phone}}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</ins>
            </div>
            <div class="col-xs-12 text-center">
                <h2>Product Details</h2>
            </div>
            <div class="col-xs-12">
                <table class="table table-bordered">
                    <tr>
                        <td class="col-xs-6"><strong>Model Name:</strong>&nbsp;{{$warranty->model_name}}</td>
                        <td class="col-xs-6"><strong>Item Code:</strong>&nbsp;{{$warranty->model_code}}</td>
                    </tr>
                    <tr>
                        <td colspan="2"><strong>Note:</strong><br>
                            {!! $note !!}
                        </td>
                    </tr>

                </table>
            </div>
            <div class="col-xs-12">
                <h5>TERMS & CONDITIONS</h5>
                <p>
                    <ol>
                        <li> An invoice or proof must be presented for warranty before any work will be undertaken. A minimum of $80 labour will be charged if goods are found not faulty</li>
                    <li> Warranty will be void if goods are found tampered with</li>
                    <li> Warranty will be void if the warranty labels are removed or peeled</li>
                    <li> Warranty does not cover the damage to other equipment used in conjunction with these goods</li>
                    <li> The vendor excludes consequential loss or damage arising from the use of good supplied on the invoice</li>
                    <li> Software problems are not covered by ROC TECH warranty. In the instance of fault deemed a software or set-up problem, labour will be charged at a minimum of $40 per hour</li>
                    <li> All courier charges and insurance costs will be borne by the customer</li>
                    <li> It is the customer’s responsibility to backup all data before presenting the device for repair. Laptop and desktop repair takes no responsibility for data stored within the device.</li>
                    <li> The customer’s information will be saved in our system for the service records.</li>
                    </ol>
                </p>
            </div>

        </div>

    </div>
</body>
</html>
