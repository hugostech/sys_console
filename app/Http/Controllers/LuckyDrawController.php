<?php

namespace App\Http\Controllers;

use App\Lucky_draw_client_list;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Session;

class LuckyDrawController extends Controller
{


    public function index(){
        return view('draw.index');
    }

    public function register(Request $request){
        $this->validate($request,[
            'name'=>'required',
            'email'=>'required|unique:lucky_draw_list,email'
        ]);
        Lucky_draw_client_list::create($request->all());
        $request->session()->flash('info','Thanks for your Sign Up, you are in the pool now. Good Luck!');
//        Session::flash('info','');
        return redirect('luckydraw');
    }

    public function clinetList(){
        $list = Lucky_draw_client_list::all();
        return view('draw.result',compact('list'));
    }
}
