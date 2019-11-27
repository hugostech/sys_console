<?php

namespace App\Console\Commands;

use App\Ex_order;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendLuckyDrawEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:luckydraw {order? : The ID of the order}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Lucky Draw Email to Users who made order in Extremepc';

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

        $order = $this->argument('order');
        if (empty($order)){
            $orders = Ex_order::where('date_added','>=','2018-12-05')->where('order_status_id', 5)->where('lucky_draw',0)->get();
            foreach ($orders as $order){
                $this->order_reminder($order);
            }
        }else{
            if ($this->confirm('Do you wish to continue? [y|N]')) {
                $order = Ex_order::find($order);
                if (!is_null($order)){
                    $this->order_reminder($order);
                }
            }

        }

    }

    private function order_reminder($order){
        $this->send_email($order);
        $order->lucky_draw=1;
        $order->save();
    }

    private function send_email($order){
        Mail::send('email.lucky_draw_reminder', compact('order'), function ($m) use ($order) {
            $m->from('sales@extremepc.co.nz', 'ExtremePC');
            $m->replyTo('sales@extremepc.co.nz','ExtremePC Team');

            $m->bcc('sales@extremepc.co.nz', 'ExtremePC Team');
            $m->to($order->email, $order->firstname.' '.$order->lastname)->subject('ExtremePC Lucky Draw');
        });
    }
}
