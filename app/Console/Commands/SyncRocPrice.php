<?php

namespace App\Console\Commands;

use App\Ex_product;
use Illuminate\Console\Command;

class SyncRocPrice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'price:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'update price from extremepc to roctech';

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
        foreach (Ex_product::whereNotNull('sku')->where('status', 1)->has('description')->cursor() as $product){
            $product->pushPriceToRoc();
        }
    }
}
