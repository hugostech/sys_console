<?php

namespace App\Http\Controllers;

use App\Delivery;
use App\Note;
use App\SN_mapping;
use App\Status;
use App\Supplier;
use App\Warranty;
use Illuminate\Http\Request;
use DB;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class warrantyController extends Controller
{
    private $ip;
    public function __construct()
    {

        $safeIP = array(
            '103.250.119.7',
            '203.97.175.164',
            '122.59.131.230'
        );
        $ip = self::getIP();
        if(!in_array($ip,$safeIP)){
            echo 'Permission denied';
            exit;
        }
    }

    public function store(Request $request)
    {
        $warranty = Warranty::create($request->all());
        $content = 'Warranty Logged <br>';
        if ($request->has('note')) {
            $content .= 'Note: ';
            $content .= $request->note;
        }
        self::submitNote($warranty->id, $content);
        self::submitStatus(1, $warranty->id);
        if ($request->has('storage')) {
            $warranty->client_name = "roctech";
            $warranty->save();
            if ($request->has('sn')) {
                $sn = $request->input('sn');
            } else {
                $sn = "";
            }

            if ($request->has('model_code')) {

                self::connectSnport(1, $warranty->id, trim($request->input('model_code')), $sn, $request->input('quantity'));
            }

        }

        return redirect('list');

    }

    public function step2($id)
    {
        $warranty = Warranty::find($id);
        $content = 'Application Sent';
        self::submitNote($warranty->id, $content);
        self::submitStatus(2, $warranty->id);
        return redirect('/');

    }

    public function step3(Request $request)
    {
        Delivery::create($request->all());
        $content = 'Delivery to supplier';
        $content .= '<br>';
        $content .= 'Track No: ';
        $content .= $request->input('track_number');
        if (!empty($request->input('reference_no'))) {
            $content .= '<br>';
            $content .= 'Reference No: ';
            $content .= $request->input('reference_no');
        }


        if (!empty($request->input('note'))) {
            $content .= '<br>';
            $content .= 'Note: ';
            $content .= $request->input('note');
        }
        self::submitNote($request->input('model_id'), $content);
        self::submitStatus(3, $request->input('model_id'));
        return redirect('list');
    }

    public function step4(Request $request)
    {
        $content = "Contact to client";
        $content .= '<br>';
        $content .= 'Result: ';
        $content .= 'Client will pick up';
        if (!empty($request->input('note'))) {
            $content .= '<br>';
            $content .= 'Note: ';
            $content .= $request->input('note');
        }
        //save the replace sn
        if ($request->has('rep_sn_status')) {

            if ($request->has('id')) {

                $warranty = Warranty::find($request->input('id'));
                if (!empty($warranty->sn)) {
                    $sn_map = new SN_mapping();
                    $sn_map->warranty_id = $request->input('id');
                    $sn_map->original_sn = $warranty->sn;
                    $sn_map->sn = strtoupper($request->input('rep_sn'));
                    $sn_map->save();

                }

            }

        }
        if ($request->input('result') == 1) {
            self::submitNote($request->input('id'), $content);
            self::submitStatus(4, $request->input('id'));
        } else {
            $content = str_replace('Client will pick up', 'No answer', $content);
            self::submitNote($request->input('id'), $content);
        }
        return redirect('list');
    }

    public function finish($id)
    {

        $warranty = Warranty::find($id);
        $content = "Finish Warranty";
        self::submitNote($warranty->id, $content);
        self::submitStatus(5, $warranty->id);
        $warranty->disable = 'y';
        $warranty->save();
        if ($warranty->storage == 'y') {
            if (!empty($warranty->model_code)) {
                self::connectSnport(2, $warranty->id);
            }
        }
        return redirect('list');
    }


    /*
         * print the warranty form for client
         * variable: id => warranty id
         * */
    public function printWarranty($id)
    {
        $warranty = Warranty::find($id);
        $note = $warranty->note()->first()->note;
        $note = str_replace('Start warranty process <br>Note: ', '', $note);
        return view('print', compact('warranty', 'note'));
    }

    /*
         * update the warranty*/
    public function updateWarranty($id, Request $request)
    {
        $warranty = Warranty::find($id);
//        var_dump($request->input("quantity") != $warranty->quantity);
        if ($request->input("quantity") != $warranty->quantity) {
//        var_dump($request->input("quantity") != $warranty->quantity);
            self::connectSnport(3, $warranty->id, $warranty->model_code, "", $request->input("quantity"));
        }
        $warranty->update($request->all());

        return redirect("warranty/$id");
    }

    public function addNote($id, Request $request)
    {
        $content = 'Note: ' . $request->input('note');
        self:
        $this->submitNote($id, $content, 'manual');
        return redirect("warranty/$id");
    }

    public function show()
    {

        $suppliers = self::getSuppliers();
        return view('warranty', compact('suppliers'));
    }

    public function listAll()
    {
        $ip = self::getIP();
        $warrantys = Warranty::simplePaginate(30);
        $rates = self::rateData(Warranty::all());
        $suppliers = self::supplierData(Warranty::all());
        return view('list', compact('warrantys', 'rates', 'suppliers','ip'));
    }

    public function listOnGoing()
    {
        $ip = self::getIP();
        $warrantys = Warranty::where('disable', 'n')->orderBy('id', 'desc')->simplePaginate(30);
        $rates = self::rateData(Warranty::where('disable', 'n')->get());
        $suppliers = self::supplierData(Warranty::where('disable', 'n')->get());
        return view('list', compact('warrantys', 'rates', 'suppliers','ip'));
    }

    public function listFinish()
    {
        $ip = self::getIP();
        $warrantys = Warranty::where('disable', 'y')->orderBy('id', 'desc')->simplePaginate(30);
        $rates = self::rateData(Warranty::where('disable', 'y')->get());
        $suppliers = self::supplierData(Warranty::where('disable', 'y')->get());
        return view('list', compact('warrantys', 'rates', 'suppliers','ip'));
    }

    public function detail($id)
    {

        $item = self::sort($id);
        $suppliers = self::getSuppliers();
        return view('report', compact('item', 'suppliers'));
    }

    public function search(Request $request)
    {
        $ip = self::getIP();
        $condition = '%' . $request->input('condition') . '%';

        $warrantys = Warranty::where('model_name', 'like', $condition)
            ->orWhere('model_code', 'like', $condition)
            ->orWhere('client_name', 'like', $condition)
            ->orWhere('client_phone', 'like', $condition)
            ->orWhere('sn', 'like', $condition)
            ->orderBy('id', 'desc')
            ->simplePaginate(30);

        $rates = self::rateData(Warranty::where('model_name', 'like', $condition)
            ->orWhere('model_code', 'like', $condition)
            ->orWhere('client_name', 'like', $condition)
            ->orWhere('sn', 'like', $condition)
            ->orWhere('client_phone', 'like', $condition)->get());

        $suppliers = self::supplierData(Warranty::where('model_name', 'like', $condition)
            ->orWhere('model_code', 'like', $condition)
            ->orWhere('client_name', 'like', $condition)
            ->orWhere('sn', 'like', $condition)
            ->orWhere('client_phone', 'like', $condition)->get());
        return view('list', compact('warrantys', 'rates', 'suppliers','ip'));
    }

    public function checkShip($shipNo)
    {

        $url = 'http://api.gosweetspot.com/v2/shipmentstatus';
        $content = array(
            $shipNo
        );
        echo $this->send_post($url, $content);
    }

    private function getSuppliers()
    {
        $suppliers = Supplier::where('disable', 'n')->get();
        $tem = array();
        foreach ($suppliers as $supplier) {
            $tem[$supplier->id] = $supplier->name;
        }
        return $tem;
    }

    /*
     * variable $methodeNo: 1, add warranty product
     *                      2, remove warranty prodect
     *          $warrantyId: the warranty id
     *          $item_code: item code which same as the roctech system's product code
     *          $sn: optional serial number
     * */

    private function connectSnport($methodNo, $warrantyId, $item_code = "", $sn = '', $quantity = 1)
    {
        switch ($methodNo) {
            case 1:
                $url = env('SNPORT') . "?action=a&w=$warrantyId&code=$item_code&sn=$sn&q=$quantity";
                $url = htmlspecialchars_decode($url, ENT_NOQUOTES);
                self::getContent($url);
                break;
            case 2:
                $url = env('SNPORT') . "?action=f&w=$warrantyId";
                $url = htmlspecialchars_decode($url, ENT_NOQUOTES);
                self::getContent($url);

                break;
            case 3:
                echo $url = env('SNPORT') . "?action=u&w=$warrantyId&q=$quantity";
                $url = htmlspecialchars_decode($url, ENT_NOQUOTES);
                self::getContent($url);
                break;
            default:
                break;
        }
//        var_dump(self::getIP());
    }

    public function getSn($sn)
    {
        $sn = strtoupper($sn);

        $final_sn = self::findOriSn($sn);

        $final_sn = strtoupper($final_sn);

        $url = env("SNPORT") . "?action=s&sn=$final_sn";

        $output = self::getContent($url);

        $data = json_decode($output);


        if ($sn == $final_sn) {
            $sn_status = $sn;
        } else {
            $sn_status = $final_sn;
        }
        $data->original_sn = $sn_status;

        return json_encode($data);

    }


    //get product description by code
    public function getDesc($code)
    {
        $url = env("SNPORT") . "?action=c&code=$code";
        $output = self::getContent($url);
        $template = array(
            'desc' => $output
        );
        $output = json_encode($template);
        return $output;
    }

    private function findOriSn($sn)
    {

        $sn_map = SN_mapping::where('sn', $sn)->orderBy('id', 'desc')->get();

        if (count($sn_map) != 0) {

            $ori_sn = $sn_map[0]->original_sn;

            return self::findOriSn($ori_sn);

        } else {

            return $sn;
        }


    }

    private function getContent($url)
    {

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
            $ip = getenv("HTTP_CLIENT_IP");
        } elseif (getenv("HTTP_X_FORWARDED_FOR")) {
            $ip = getenv("HTTP_X_FORWARDED_FOR");
        } elseif (getenv("REMOTE_ADDR")) {
            $ip = getenv("REMOTE_ADDR");
        } else {
            $ip = "unknow";
        }
        return $ip;
    }

    private function submitStatus($statusCode, $warrantyId)
    {
        $status = new Status();
        $status->model_id = $warrantyId;
        $status->status = $statusCode;
        switch ($statusCode) {
            case 1:
                $status->status_content = 'Warranty Logged';
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

    // Function to get the client IP address
    private function get_client_ip() {
        $ipaddress = '';
        if (getenv('HTTP_CLIENT_IP'))
            $ipaddress = getenv('HTTP_CLIENT_IP');
        else if(getenv('HTTP_X_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        else if(getenv('HTTP_X_FORWARDED'))
            $ipaddress = getenv('HTTP_X_FORWARDED');
        else if(getenv('HTTP_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_FORWARDED_FOR');
        else if(getenv('HTTP_FORWARDED'))
            $ipaddress = getenv('HTTP_FORWARDED');
        else if(getenv('REMOTE_ADDR'))
            $ipaddress = getenv('REMOTE_ADDR');
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }

    private function submitNote($id, $content, $type = 'sys')
    {
        $note = new Note();
        $note->model_id = $id;
        $note->note = $content;
        $note->type = $type;
        $note->save();
    }


    private function supplierData($warrantys)
    {
        $suppliers = array();
        foreach ($warrantys as $warranty) {
            if (empty($warranty->supplier_id)) {
                $suppliers[$warranty->id] = "";
            } else {
                $supplier = Supplier::find($warranty->supplier_id);

                $suppliers[$warranty->id] = $supplier->doc;
            }

        }
        return $suppliers;
    }

    private function rateData($warranrys)
    {
        $rate = array();
        $status = array();
        foreach ($warranrys as $warranry) {
            $tem = Status::where('model_id', $warranry->id)->get();

            foreach ($tem as $value) {
                $status[$value->status] = "1";

            }

            for ($i = 1; $i < 6; $i++) {

                if (isset($status["$i"])) {
                    $rate[$warranry->id][$i] = 'active';
                } else {
                    $rate[$warranry->id][$i] = '';
                }
            }
            $status = array();
            $noteNumber = Note::where('model_id', $warranry->id)->where('type', 'manual')->get();
            $deliveryinfo = Delivery::where('model_id', $warranry->id)->first();

            $rate[$warranry->id]["note"] = count($noteNumber) == 0 ? "" : count($noteNumber);
            $rate[$warranry->id]["delivery"] = $deliveryinfo;
        }
//        $rate['ip']=self::getIP();

        return $rate;
    }


    private function sort($id)
    {
        $warranty = Warranty::find($id);
        $notes = $warranty->note()->get();
        $delivery = $warranty->delivery()->get();
        $status = array();
        $tem = Status::where('model_id', $id)->orderBy('status', 'asc')->get();
//        $rate= array();

        foreach ($tem as $value) {
            $status[$value->status] = $value;
        }


        $item = array(
            'warranty' => $warranty,
            'notes' => $notes,
            'delivery' => $delivery,
            'status' => $status,

        );
        return $item;

    }

}
