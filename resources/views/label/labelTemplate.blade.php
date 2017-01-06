@extends('master')

@section('mainContent')
<div class="col-sm-12">
    {!! Form::open(['url'=>'findLabel']) !!}
        <div class="form-group">
            <div class="input-group">
                {!! Form::text('code',null,['class'=>'form-control']) !!}
                <span class="input-group-btn">
                    {!! Form::submit('Find',['class'=>'btn btn-default']) !!}
                </span>
            </div>
        </div>
    {!! Form::close() !!}

</div>
<div class="col-sm-12">
    @if(isset($label))
        @if( !is_null($label))
    <div class="col-sm-12">

    {!! Form::open(['url'=>'editLabel']) !!}
        <div class="form-group">
            <label>Price</label>
            {{Form::input('number','price',$label->price,['class'=>'form-control'])}}
        </div>
        <div class="form-group">
            <label for='description'>Desciption</label>
            <textarea name="description" rows="5" class="form-control" placeholder="Details..." id="warranty_detail">
                {!! $label->description !!}
            </textarea>
            <script>

                CKEDITOR.replace( 'warranty_detail' );
            </script>
        </div>
        <div class="form-group">
            {!! Form::submit('Edit',['class'=>'btn btn-primary']) !!}
        </div>

    {!! Form::close() !!}


    </div>
<style>
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
    <div class="col-sm-12">
        <table>
            <tr>
                <td>
                    <div class="tape-top">
                <span class="tape-top-left">
                    {{ $label->description }}
                </span>
                        <span class="tape-top-right">
                    <h3><sup>$</sup>{{$label->price}}</h3>
                </span>


                    </div>
                    <div class="tape-bottom">Code: {{$label->code}}</div>
                </td>
                <td></td>
            </tr>
        </table>

    </div>
    @endif
    @endif
</div>
@endsection