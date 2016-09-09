@extends('master')

@section('mainContent')
    {!! Form::open(['url'=>'']) !!}
    <div class="col-md-6 col-md-offset-3">
        <table class="table table-strip">

            <tr>

                <td>
                    <div class="form-group has-feedback">
                        <label class="control-label" for="inputSuccess2">CPU Family</label>
                        <select class="form-control" aria-describedby="inputSuccess2Status" name="30">
                            <option value="">--select CPU model--</option>
                            <option value="Intel Atom">Intel Atom</option>
                            <option value="Intel Celeron">Intel Celeron</option>
                            <option value="Intel Core i3">Intel Core i3</option>
                            <option value="Intel Core i5">Intel Core i5</option>
                            <option value="Intel Core i7">Intel Core i7</option>
                            <option value="Intel Core M">Intel Core M</option>
                            <option value="Intel Pentium">Intel Pentium</option>
                            <option value="Intel Xeon E3">Intel Xeon E3</option>
                            <option value="AMD A4">AMD A4</option>
                            <option value="AMD A6">AMD A6</option>
                            <option value="AMD A8">AMD A8</option>
                            <option value="AMD A10">AMD A10</option>
                            <option value="AMD E1">AMD E1</option>

                        </select>

                        {{--<span class="glyphicon glyphicon-ok form-control-feedback" aria-hidden="true"></span>--}}
                        {{--<span id="inputSuccess2Status" class="sr-only">(success)</span>--}}
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                <div class="form-group has-feedback">
                    <label class="control-label" for="inputSuccess2">RAM size</label>
                    <select class="form-control" aria-describedby="inputSuccess2Status" name="31">
                        <option>1</option>
                        <option>2</option>
                        <option>3</option>
                        <option>4</option>
                        <option>5</option>
                    </select>

                    {{--<span class="glyphicon glyphicon-ok form-control-feedback" aria-hidden="true"></span>--}}
                    {{--<span id="inputSuccess2Status" class="sr-only">(success)</span>--}}
                </div>
                </td>
            </tr>
            <tr>
                <td>
                <div class="form-group has-feedback">
                    <label class="control-label" for="inputSuccess2">Screen size</label>
                    <select class="form-control" aria-describedby="inputSuccess2Status" name="32">
                        <option>1</option>
                        <option>2</option>
                        <option>3</option>
                        <option>4</option>
                        <option>5</option>
                    </select>

                    {{--<span class="glyphicon glyphicon-ok form-control-feedback" aria-hidden="true"></span>--}}
                    {{--<span id="inputSuccess2Status" class="sr-only">(success)</span>--}}
                </div>
                </td>
            </tr>
            <tr>
                <td>
                <div class="form-group has-feedback">
                    <label class="control-label" for="inputSuccess2">HDD size</label>
                    <select class="form-control" aria-describedby="inputSuccess2Status" name="33">
                        <option>1</option>
                        <option>2</option>
                        <option>3</option>
                        <option>4</option>
                        <option>5</option>
                    </select>

                    {{--<span class="glyphicon glyphicon-ok form-control-feedback" aria-hidden="true"></span>--}}
                    {{--<span id="inputSuccess2Status" class="sr-only">(success)</span>--}}
                </div>
                </td>
            </tr>
            <tr>
                <td>
                <div class="form-group has-feedback">
                    <label class="control-label" for="inputSuccess2">SSD size</label>
                    <input type="number" name="ssd" class="form-control">

                    {{--<span class="glyphicon glyphicon-ok form-control-feedback" aria-hidden="true"></span>--}}
                    {{--<span id="inputSuccess2Status" class="sr-only">(success)</span>--}}
                </div>
                </td>
            </tr>
            <tr>
                <td>
                <div class="form-group has-feedback">
                    <label class="control-label" for="inputSuccess2">Graphics card</label>
                    <select class="form-control" aria-describedby="inputSuccess2Status" name="35">
                        <option>GT720M</option>
                        <option>GTX860M</option>
                        <option>GTX920M</option>
                        <option>GTX930M</option>
                        <option>GTX940M</option>
                        <option>GTX950M</option>
                        <option>GTX960M</option>
                        <option>GTX965M</option>
                        <option>GTX980M</option>
                        <option>GTX1060M</option>
                        <option>GTX1070M</option>
                        <option>GTX1080M</option>
                        <option>Integrated</option>
                    </select>

                    {{--<span class="glyphicon glyphicon-ok form-control-feedback" aria-hidden="true"></span>--}}
                    {{--<span id="inputSuccess2Status" class="sr-only">(success)</span>--}}
                </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="form-group has-feedback">
                        <label class="control-label" for="inputSuccess2">Screen Resolution</label>
                        <select class="form-control" aria-describedby="inputSuccess2Status" name="36">
                            <option>1366 X 768</option>
                            <option>1440 X 900</option>
                            <option>1600 X 900</option>
                            <option>1920 X 1080</option>
                            <option>1920 X 1200</option>
                            <option>2304 X 1440</option>
                            <option>2560 X 1440</option>
                            <option>2560 X 1600</option>
                            <option>2880 X 1620</option>
                            <option>3200 X 1800</option>
                            <option>3840 X 2160</option>
                        </select>

                        {{--<span class="glyphicon glyphicon-ok form-control-feedback" aria-hidden="true"></span>--}}
                        {{--<span id="inputSuccess2Status" class="sr-only">(success)</span>--}}
                    </div>
                </td>
            </tr>

        </table>
    </div>

@endsection