<?php

namespace App\Http\Controllers;

use App\Delivery;
use App\Note;
use App\Status;
use App\Supplier;
use App\Warranty;
use Illuminate\Http\Request;
use DB;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class warrantyController extends Controller
{
    public function store(Request $request){
        $warranty = Warranty::create($request->all());
        $content = 'Start warranty process <br>';
        if($request->has('note')){
            $content .= 'Note: ';
            $content .= $request->note;
        }
        self::submitNote($warranty->id,$content);
        self::submitStatus(1,$warranty->id);
        if($request->has('storage')){
            if($request->has('sn')){
                $sn = $request->input('sn');
            }else{
                $sn = "";
            }

		if($request->has('model_code')){
	
        	    	self::connectSnport(1,$warranty->id,trim($request->input('model_code')),$sn);
		}

        }

        return redirect('list');

    }

    public function step3(Request $request){
        Delivery::create($request->all());
        $content = 'Delivery to supplier';
        $content .= '<br>';
        $content .= 'Track No: ';
        $content .= $request->input('track_number');
        if(!empty($request->input('reference_no'))){
            $content .= '<br>';
            $content .= 'Reference No: ';
            $content .= $request->input('reference_no');
        }


        if(!empty($request->input('note'))){
            $content .= '<br>';
            $content .= 'Note: ';
            $content .= $request->input('note');
        }
        self::submitNote($request->input('model_id'),$content);
        self::submitStatus(3,$request->input('model_id'));
        return redirect('list');
    }

    public function step4(Request $request){
        $content = "Contact to client";
        $content .= '<br>';
        $content .= 'Result: ';
        $content .= 'Client will pick up';
        if(!empty($request->input('note'))){
            $content .= '<br>';
            $content .= 'Note: ';
            $content .= $request->input('note');
        }
        if($request->input('result')==1){
            self::submitNote($request->input('id'),$content);
            self::submitStatus(4,$request->input('id'));
        }else{
            $content = str_replace('Client will pick up','No answer',$content);
            self::submitNote($request->input('id'),$content);
        }
        return redirect('list');
    }

    public function finish($id){

        $warranty = Warranty::find($id);
        $content = "Finish Warranty";
        self::submitNote($warranty->id,$content);
        self::submitStatus(5,$warranty->id);
        $warranty->disable = 'y';
        $warranty->save();
	if($warranty->storage=='y'){
           if(!empty($warranty->model_code)){
 		self::connectSnport(2,$warranty->id);
	}
	}
        return redirect('list');
    }
/*
     * print the warranty form for client
     * variable: id => warranty id
     * */
    public function printWarranty($id){
        $warranty = Warranty::find($id);
        $note = $warranty->note()->first()->note;
        $note = str_replace('Start warranty process <br>Note: ','',$note);
        return view('print',compact('warranty','note'));
    }

/*
     * update the warranty*/
    public function updateWarranty($id,Request $request){
        $warranty = Warranty::find($id);
        $warranty->update($request->all());
        return redirect("warranty/$id");
    }

    public function addNote($id,Request $request){
        $content = 'Note: '.$request->input('note');
        self:$this->submitNote($id,$content,'manual');
        return redirect("warranty/$id");
    }
    public function show(){
        $suppliers = Supplier::where('disable','n')->get();
        return view('warranty',compact('suppliers'));
    }
    public function listAll(){
        $warrantys = Warranty::simplePaginate(30);
        $rates = self::rateData(Warranty::all());
        $suppliers = self::supplierData(Warranty::all());
        return view('list',compact('warrantys','rates','suppliers'));
    }

    public function listOnGoing(){
        $warrantys = Warranty::where('disable','n')->orderBy('id','desc')->simplePaginate(30);
        $rates = self::rateData(Warranty::where('disable','n')->get());
        $suppliers = self::supplierData(Warranty::where('disable','n')->get());
//        var_dump($suppliers);
        return view('list',compact('warrantys','rates','suppliers'));
    }

    public function listFinish(){
        $warrantys = Warranty::where('disable','y')->orderBy('id','desc')->simplePaginate(30);
        $rates = self::rateData(Warranty::where('disable','y')->get());
        $suppliers = self::supplierData(Warranty::where('disable','y')->get());
        return view('list',compact('warrantys','rates','suppliers'));
    }

    public function detail($id){
        $item = self::sort($id);
        return view('report',compact('item'));
    }

    public function search(Request $request){

        $condition = '%'.$request->input('condition').'%';

        $warrantys = Warranty::where('model_name', 'like', $condition)
            ->orWhere('model_code','like',$condition)
            ->orWhere('client_name','like',$condition)
            ->orWhere('client_phone','like',$condition)
            ->orWhere('sn','like',$condition)
            ->orderBy('id','desc')
            ->simplePaginate(30);

        $rates = self::rateData(Warranty::where('model_name', 'like', $condition)
            ->orWhere('model_code','like',$condition)
            ->orWhere('client_name','like',$condition)
            ->orWhere('sn','like',$condition)
            ->orWhere('client_phone','like',$condition)->get());
        $suppliers = self::supplierData(Warranty::where('model_name', 'like', $condition)
            ->orWhere('model_code','like',$condition)
            ->orWhere('client_name','like',$condition)
            ->orWhere('sn','like',$condition)
            ->orWhere('client_phone','like',$condition)->get());
        return view('list',compact('warrantys','rates','suppliers'));
    }
    public function checkShip($shipNo){

        $url = 'http://api.gosweetspot.com/v2/shipmentstatus';
        $content = array(
            $shipNo
        );
        echo $this->send_post($url,$content);
    }

    /*
     * variable $methodeNo: 1, add warranty product
     *                      2, remove warranty prodect
     *          $warrantyId: the warranty id
     *          $item_code: item code which same as the roctech system's product code
     *          $sn: optional serial number
     * */

    private function connectSnport($methodNo,$warrantyId,$item_code="",$sn=''){
        switch($methodNo){
            case 1:
                $url = env('SNPORT')."?action=a&w=$warrantyId&code=$item_code&sn=$sn";
                $url =  htmlspecialchars_decode($url,ENT_NOQUOTES);
                self::getContent($url);
                break;
            case 2:
                $url = env('SNPORT')."?action=f&w=$warrantyId";
                $url =  htmlspecialchars_decode($url,ENT_NOQUOTES);
                self::getContent($url);

                break;
            default:
                break;
        }
//        var_dump(self::getIP());
    }
    private function getContent($url){

        // create curl resource
        $ch = curl_init();

        // set url
        curl_setopt($ch, CURLOPT_URL, $url);

        //return the transfer as a string
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        // $output contains the output string
        $output = curl_exec($ch);

        // close curl resource to free up system resources
        curl_close($ch);

        return $output;
    }
    private function getIP()
    {
        if (getenv("HTTP_CLIENT_IP")) {
            $ip=getenv("HTTP_CLIENT_IP");
        }elseif (getenv("HTTP_X_FORWARDED_FOR")) {
            $ip=getenv("HTTP_X_FORWARDED_FOR");
        }elseif (getenv("REMOTE_ADDR")) {
            $ip=getenv("REMOTE_ADDR");
        }else{
            $ip="unknow";
        }
        return $ip;
    }

    private function submitStatus($statusCode,$warrantyId){
        $status = new Status();
        $status->model_id = $warrantyId;
        $status->status = $statusCode;
        switch($statusCode){
            case 1:
                $status->status_content = 'Start warranty';
                break;
            case 2:
                $status->status_content = 'Waiting for supplier\'s response';
                break;
            case 3:
                $status->status_content = 'Delivery';
                break;
            case 4:
                $status->status_content = 'Waiting for client to pick up';
                break;
            case 5:
                $status->status_content = 'Finish';
                break;
        }

        $status->save();
    }


    private function submitNote($id,$content,$type='sys'){
        $note = new Note();
        $note->model_id = $id;
        $note->note = $content;
        $note->type = $type;
        $note->save();
    }


    private function supplierData($warrantys){
        $suppliers = array();
        foreach ($warrantys as $warranty) {
            $supplier = Supplier::find($warranty->supplier_id);

            $suppliers[$warranty->id]=$supplier->doc;
        }
        return $suppliers;
    }

    private function rateData($warranrys)
    {
        $rate = array();
        $status = array();
        foreach($warranrys as $warranry){
            $tem = Status::where('model_id',$warranry->id)->get();

            foreach($tem as $value){
                $status[$value->status] = "1";

            }

            for($i = 1;$i < 6;$i++){

                if(isset($status["$i"])){
                    $rate[$warranry->id][$i] = 'active';
                }else{
                    $rate[$warranry->id][$i] = '';
                }
            }
            $status = array();
            $noteNumber = Note::where('model_id',$warranry->id)->where('type','manual')->get();
            $rate[$warranry->id]["note"] = count($noteNumber)==0?"":count($noteNumber);
        }

        return $rate;
    }



    private function sort($id){
        $warranty = Warranty::find($id);
        $notes = $warranty->note()->get();
        $delivery = $warranty->delivery()->get();
        $status = array();
        $tem = Status::where('model_id',$id)->get();
//        $rate= array();

        foreach($tem as $value){
            $status[$value->status] = $value;
        }

        $supplier = Supplier::find($warranty->supplier_id);

        $item = array(
            'warranty'=>$warranty,
            'notes'=>$notes,
            'delivery'=>$delivery,
            'status'=>$status,
            'supplier'=>$supplier->name
        );
        return $item;

    }

}
