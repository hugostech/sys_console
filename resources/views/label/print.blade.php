<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
    <style>
        @media print {
            body{
                background: none;
                -webkit-print-color-adjust: exact;
            }
            table{
                /*border: solid 1px beige;*/
                margin: 0;
                padding: 0;
            }
            table tr td{
                border: solid 1px black;
                height: 100px;
                width: 298px;
                /*background: cornflowerblue;*/
                text-align: left;
                padding: 0;
            }
            table tr td div{
                width: 298px;
                /*float: left;*/
            }
            .tape-top{
                height: 82px;
            }
            .tape-top-left{
                padding-left: 5px;
                width: 190px;

                float: left;
                font-family: "Microsoft YaHei";
                line-height: 16px;
                font-size: 15px;
                font-weight: 600;
            }
            .tape-top-left p{
                font-family: "Microsoft YaHei";
                line-height: 17px;
                font-size: 15px;
                font-weight: 600;
            }
            .tape-top-right{
                width: 100px;

                text-align: right;
                float: left;
                font-family: "Arial Black";
                padding-top: 2px;
            }
            .tape-top-right h3{
                font-family: "Arial";
                /*font-family: "Cordia New";*/
                font-size: 70px;
                margin: 0;
                padding: 0;

                /*position: relative;*/

            }
            .tape-top-right h3 sup{
                font-family: "Arial Unicode MS";
                font-size: 40px;


            }


            .tape-bottom{
                background: rgb(000,204,000);
                background-image: url("{{url('image',['logo.png'])}}") ;
                background-size: 126px 17px;
                background-repeat: no-repeat;

                border-top: solid 1px black;
                height: 18px;
                text-align: right;
                font-family: "Microsoft YaHei UI";
                font-size: 15px;
                padding-right: 3px;
            }
        }
        table{
            /*border: solid 1px beige;*/
            margin: 0;
            padding: 0;
        }
        table tr td{
            border: solid 1px black;
            height: 100px;
            width:298px;
            /*background: cornflowerblue;*/
            text-align: left;
            padding: 0;
        }
        table tr td div{
            width: 298px;
            /*float: left;*/
        }
        .tape-top{
            height: 82px;
        }
        .tape-top-left{
            padding-left: 5px;
            width: 150px;

            float: left;
            font-family: "Microsoft YaHei";
            line-height: 16px;
            font-size: 15px;
            font-weight: 600;
        }
        .tape-top-right{
            width: 140px;

            text-align: right;
            float: left;
            font-family: "Arial Black";
            padding-top: 2px;
        }
        .tape-top-right h3{
            font-family: "Arial";
            /*font-family: "Cordia New";*/
            font-size: 70px;
            margin: 0;
            padding: 0;
            letter-spacing: -3px;

            /*position: relative;*/

        }
        .tape-top-right h3 sup{
            font-family: "Arial Unicode MS";
            font-size: 40px;


        }


        .tape-bottom{
            background: rgb(000,204,000);
            background-image: url("{{url('image',['logo.png'])}}") ;
            background-size: 126px 17px;
            background-repeat: no-repeat;

            border-top: solid 1px black;
            height: 18px;
            text-align: right;
            font-family: "Microsoft YaHei UI";
            font-size: 15px;
            padding-right: 3px;
        }
    </style>
</head>
<body>
<table>
    @foreach($labels as $key=>$label)
        @if($key%2==0)
            <tr>
                <td>
                    <div class="tape-top">
                <span class="tape-top-left">
                    {!! $label->description !!}
                </span>
                        <span class="tape-top-right">
                    <h3><sup>$</sup>{{number_format($label->price)}}</h3>
                </span>


                    </div>
                    <div class="tape-bottom">Code: {{$label->code}}</div>
                </td>
                @else
                    <td>
                        <div class="tape-top">
                <span class="tape-top-left">
                    {!! $label->description !!}
                </span>
                            <span class="tape-top-right">
                    <h3><sup>$</sup>{{number_format($label->price)}}</h3>
                </span>


                        </div>
                        <div class="tape-bottom">Code: {{$label->code}}</div>
                    </td>
            </tr>
        @endif
    @endforeach

</table>
<br>
{!! Form::open(['url'=>'cleanLabelList']) !!}

    {!! Form::input('hidden','labels',json_encode($labels->pluck('id')->all())) !!}
{!! Form::submit('Clean',['class'=>'btn btn-danger']) !!}
{!! Form::close() !!}
</body>
</html>