<?php

namespace App\Console\Commands;

use App\Ex_order;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class MarketPlaceReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminder:marketplace 
                            {order}
                            {--emailtype= : email type, available: pending-order, review-order}
                            {--limit=-1 : limit email number }
                            {--test : output email on screen}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminders for marketplace order';

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
        if ($this->argument('order')){
            $order = Ex_order::find($this->argument('order'));
            $this->trigger($order);
        }else{
            switch ($this->option('emailtype')){
                case 'pending-order':

                    break;
                case 'review-order':
                    $query = Ex_order::whereHas('historys', function ($query){
                        //order completed 3 days ago
                        $offday = 3;
                        $from = Carbon::today()->subDays($offday)->endOfDay();
                        $to = $from->copy()->endOfDay();
                        $query->completeBetween($from, $to);
                    });
                    break;
                default:
                    $query = null;
                    break;
            }


            $bar = $this->output->createProgressBar($query->count());
            foreach ($query->get() as $order){
                $this->info("Order ID $order->order_id");
                $bar->advance();
            }
            $bar->finish();
        }
    }

    private function trigger(Ex_order $order){
        switch ($this->option('emailtype')){
            case 'pending-order':
                $to = [$order->email, "$order->firstname $order->lastname", 'ExtremePC Pending Order '.$order->id];
                $template = 'email.pending_order';
                $this->send($to, $template, $order);
                break;
            case 'review-order':
                $to = [$order->email, "$order->firstname $order->lastname", 'How did we do?'];
                $template = 'email.review_us';
                $this->send($to, $template, $order);
                break;
            default:
                $this->error('Need email type');
                break;
        }


    }

    /**
     * @param $to array
     * format: ['info@email.com', 'user name', 'subject']
     * @param $template string
     * @param $order Ex_order
     */
    private function send($to, $template, Ex_order $order){
        if ($this->option('test')){
            $this->info("send email to $to[1]($to[0]) - $to[2]" );
            $this->output->block(view($template, compact('order'))->render());
        }else{
            Mail::send($template, compact('order'), function ($m) use ($to){
                $m->from('akl.sales@extremepc.co.nz', 'ExtremePC');
                $m->bcc('tony@extremepc.co.nz', 'Tony Situ');
                $m->to($to[0],$to[1])->subject($to[2]);
            });
        }
    }
}
