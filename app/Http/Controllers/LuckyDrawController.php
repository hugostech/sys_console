<?php

namespace App\Http\Controllers;

use App\Lucky_draw_client_list;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Facades\Excel;

class LuckyDrawController extends Controller
{


    public function index(){
        return view('draw.index');
    }

    public function register(Request $request){
        $this->validate($request,[
            'name'=>'required',
            'email'=>'required | email'
        ]);
        $email = Lucky_draw_client_list::where('email',$request->input('email'))->get();
        if (count($email)<1){
            Lucky_draw_client_list::create($request->all());
        }
        $request->session()->flash('info','Thanks for your Sign Up, you are in the pool now. Good Luck!');
//        Session::flash('info','');
        return redirect('luckydraw');
    }

    public function clinetList(){
        $list = Lucky_draw_client_list::all();
        return view('draw.result',compact('list'));
    }

    public function dryPool(){
        Lucky_draw_client_list::query()->truncate();
        return redirect()->back();
    }

    public function exportCsv(){
        Excel::create('luckylist',function($excel){
            $excel->sheet('list',function ($sheet){
                foreach (Lucky_draw_client_list::all() as $item){
                    $sheet->appendRow([$item->name,$item->email,$item->phone]);
                }
            });
        })->download('csv');
    }
}
