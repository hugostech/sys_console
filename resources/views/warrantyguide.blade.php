@extends('master')
@section('mainContent')
    <div class="col-md-12" style="margin-top: 10px;border-bottom: solid 1px lightgray;padding-bottom: 30px;">
        @foreach($categorys as $category)
            <div class="col-md-3"><a class="btn btn-block btn-default" href="{{url('/warrantyGuide',[$category->id])}}">{{$category->name}}</a></div>
        @endforeach
    </div>
    <div class="col-md-12" style="padding-top: 20px;border-bottom: solid 1px lightgray;">
        <div class="jumbotron">

            <p>
                For all keyboard, mouse, speaker, cable, SD cards, USB, software, surge protection and all PC parts customer
                will have to apply the warranties through us a the middle man to contact with the supplier.
            </p>

        </div>

    </div>
    <div class="col-md-12">
        <iframe src="http://www.askvic.co.nz/login.php?prm1=tony@roctech.co.nz&prm2=dave" frameborder="0" width="100%" height="1000px" scrolling="no"></iframe>
    </div>
@endsection


