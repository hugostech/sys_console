<?php

namespace App\Http\Controllers;

use App\Ex_category;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

Define('NOCATEGORY',381);
class CsvController extends Controller
{
    private $category;
    public function __construct()
    {
        $this->category = Ex_category::find(NOCATEGORY);
    }

    public function import(){

//        Excel::load('storage/app/pb.csv',function ($render){
//            $render->each(function ($sheet){
//
//            });
//        });
        Excel::filter('chunk')->load('storage/app/pb.csv')->chunk(10, function($results)
        {
            foreach($results as $row)
            {

            }
            dd('test');
        });
    }

    public function importProduct($data){
        //to do check product exisit
        //generate product
        //add product_tag
    }
}
