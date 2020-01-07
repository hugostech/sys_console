<?php

namespace App\Console\Commands;

use App\Ex_product;
use Illuminate\Console\Command;

class CleanSpecialWithoutStock extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'special:clear-up';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear products special without stock';

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
        $query = Ex_product::where('status',1)->has('special')->where('quantity','<=',0);
        $count = $query->count();
        $this->info($count);
        foreach ($query->cursor() as $product){
            $this->info("Process $product->product_id");
        }
    }
}
