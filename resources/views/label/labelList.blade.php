@extends('master')

@section('mainContent')
    @if($print == true)

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
                    height: 105.66037px;
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
                    height: 86.7925px;
                }
                .tape-top-left{
                    padding-left: 5px;
                    width: 190px;

                    float: left;
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
                    padding-top: 8px;
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
                    background-size: 128.5714px 17.5px;
                    background-repeat: no-repeat;

                    border-top: solid 1px black;
                    height: 18.86792px;
                    text-align: right;
                    font-family: "Microsoft YaHei UI";
                    font-size: 14px;
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
                height: 105.66037px;
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
                height: 86.7925px;
            }
            .tape-top-left{
                padding-left: 5px;
                width: 150px;

                float: left;
                font-family: "Microsoft YaHei";
                line-height: 17px;
                font-size: 15px;
                font-weight: 600;
            }
            .tape-top-right{
                width: 140px;

                text-align: right;
                float: left;
                font-family: "Arial Black";
                padding-top: 8px;
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
                background-size: 128.5714px 18px;
                background-repeat: no-repeat;

                border-top: solid 1px black;
                height: 18.86792px;
                text-align: right;
                font-family: "Microsoft YaHei UI";
                font-size: 14px;
                padding-right: 3px;
            }
        </style>
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

    @else
    <div class="col-sm-12">
        <table class="table">
            <tr>
                <th>Code</th>
                <th>Desciption</th>
                <th>Price</th>
                <th></th>
            </tr>
            @foreach($labels as $label)
                <tr>
                    <td>{{$label->code}}</td>
                    <td>{!! $label->description !!}</td>
                    <td>{{$label->price}}</td>
                    <td><a href="{{url('editLabel',[$label->id])}}" class="btn btn-primary">Edit</a>
                        <a class="btn btn-danger" href="{{url('removeLabelFromPrintList',[$label->id])}}">Remove from print list</a>

                    </td>
                </tr>
            @endforeach
            <tr>
                <td colspan="4">
                    <a class="btn btn-warning" href="?print=true">Print</a>
                </td>
            </tr>
        </table>
        <div class="text-center">
            {{ $labels->links() }}
        </div>
    </div>
    @endif
@endsection