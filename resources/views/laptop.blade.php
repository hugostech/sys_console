@extends('master')

@section('mainContent')
    {!! Form::open(['url'=>'laptop_attribute']) !!}
    <input type="hidden" name="product_id" value="{{$id}}">
    <input type="hidden" name="product_sku" value="{{$product->sku}}">
    <div class="col-md-6 col-md-offset-3">
        <table class="table table-strip">
            <tr>
                <td>
                    <p>{{$product->description->name}}</p>
                </td>
            </tr>

            <tr>

                <td>
                    <div class="form-group has-feedback">
                        <label class="control-label" for="inputSuccess2">CPU Family</label>

                        {!! Form::select('3',$cpus,$data[3],['class'=>'form-control','placeholder' => 'Pick a CPU...']) !!}

                        {{--<span class="glyphicon glyphicon-ok form-control-feedback" aria-hidden="true"></span>--}}
                        {{--<span id="inputSuccess2Status" class="sr-only">(success)</span>--}}
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                <div class="form-group has-feedback">
                    <label class="control-label" for="inputSuccess2">RAM size</label>
                    {!! Form::input('number','4',$data[4],['class'=>'form-control']) !!}


                    {{--<span class="glyphicon glyphicon-ok form-control-feedback" aria-hidden="true"></span>--}}
                    {{--<span id="inputSuccess2Status" class="sr-only">(success)</span>--}}
                </div>
                </td>
            </tr>
            <tr>
                <td>
                <div class="form-group has-feedback">
                    <label class="control-label" for="inputSuccess2">Screen size</label>

                    {!! Form::input('number','6',$data[6],['class'=>'form-control','placeholder' => 'Entry Screen size','step'=>'0.1']) !!}


                </div>
                </td>
            </tr>
            <tr>
                <td>
                <div class="form-group has-feedback">
                    <label class="control-label" for="inputSuccess2">HDD size</label>
                    {!! Form::input('number','9',$data[9],['class'=>'form-control']) !!}


                </div>
                </td>
            </tr>
            <tr>
                <td>
                <div class="form-group has-feedback">
                    <label class="control-label" for="inputSuccess2">SSD size</label>
                    <input type="number" name="5" class="form-control" value="{{$data[5]}}">


                </div>
                </td>
            </tr>
            <tr>
                <td>
                <div class="form-group has-feedback">
                    <label class="control-label" for="inputSuccess2">Graphics card</label>

                    {!! Form::select('8',$graphics_card,$data[8],['class'=>'form-control','placeholder' => 'Pick a graphic_card...']) !!}


                </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="form-group has-feedback">
                        <label class="control-label" for="inputSuccess2">Screen Resolution</label>

                        {!! Form::select('11',$resolution,$data[11],['class'=>'form-control','placeholder' => 'Pick a Resolution...']) !!}


                    </div>
                </td>
            </tr>

            <tr>
                <td>
                    <div class="form-group has-feedback">
                        <label class="control-label" for="inputSuccess2">Touch Screen</label>

                        {!! Form::select('10',['No'=>'No','Yes'=>'Yes'],$data[10],['class'=>'form-control','placeholder' => 'Pick a Touch Screen...']) !!}


                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="form-group has-feedback">
                        <label class="control-label" for="inputSuccess2">Operating system</label>

                        {!! Form::select('7',$os,$data[7],['class'=>'form-control','placeholder' => 'Pick a OS...']) !!}


                    </div>
                </td>
            </tr>

            <tr>
                <td>
                    {!! Form::submit('Save', ['class'=>'btn btn-save']) !!}
                </td>
            </tr>

        </table>
    </div>
{!! Form::close() !!}
@endsection