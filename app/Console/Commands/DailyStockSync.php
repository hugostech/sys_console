<?php

namespace App\Console\Commands;

use App\Ex_product;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class DailyStockSync extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stock:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'daily stock sync between roc and extremepc';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Mail::raw('Extremepc Is Sync with Roctech. Status: Running '.Carbon::now(), function ($message) {
            $message->from('sales@extremepc.co.nz');
            $message->to('tony@extremepc.co.nz', 'Tony Situ');
            $message->subject('Extremepc Sync Job start running '.Carbon::now());
        });

        $this->syncQuantity();

        Mail::raw(Carbon::now().' Sync Job Complete! You\'re safe, thanks Hugo!', function ($message) {
            $message->from('sales@extremepc.co.nz');
            $message->to('tony@extremepc.co.nz', 'Tony Situ');
            $message->subject('Extremepc Sync Job Succeeded '.Carbon::now());
        });
    }

    private function syncQuantity(){
        $products = Ex_product::rocLinked()->get();
        $roctech_array = $this->syncqty();
        $unsync = array();
        $disable = array();

        foreach ($products as $product) {
            if (isset($roctech_array[$product->sku])) {
                if ($roctech_array[$product->sku][0] == 'True') {
                    $product->update([
                        'status'=>0
                    ]);
                    $disable[] = $product->sku;

                } else {
                    $product->update([
                        'quantity' => $roctech_array[$product->sku][1],
                        'status' => 1,
                        'ean' => $roctech_array[$product->sku][2],
                        'jan' => $roctech_array[$product->sku][3],

                    ]);
                }

            } else {
                $unsync[] = $product->sku;
            }
        }
    }

    public function syncqty()
    {
        $url = config('app.roctech_endpoint') . "?action=allqty";
        $client = new Client();
        $response = $client->get($url);
        $content = $response->getBody();
        $content = str_replace(',}', '}', $content);
        $content = \GuzzleHttp\json_decode($content, true);
        return $content;
    }
}
