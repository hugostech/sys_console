<?php

namespace App\Jobs;

use App\Ex_product;
use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SetDiscountRatio extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;
    public $product;
    public $ratio;
    public $margin_rate;
    public $base_price_changable;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Ex_product $product, $ratio, $margin_rate, $base_price_changable)
    {
        $this->product = $product;
        $this->ratio = $ratio;
        $this->margin_rate = $margin_rate;
        $this->base_price_changable = $base_price_changable;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->product->setDiscountRatio(0.15, -0.05, true);
    }
}
