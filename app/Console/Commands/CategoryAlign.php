<?php

namespace App\Console\Commands;

use App\Ex_category;
use Illuminate\Console\Command;

class CategoryAlign extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'category:align';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'align products under category';

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
        foreach (Ex_category::available()->all() as $category){
            if ($category->hasParent()){
                $products = $category->products()->pluck('oc_product.product_id')->toArray();
                $parent = $category->parentCategory();
                while($parent){
                    $parent->products()->syncWithoutDetaching($products);
                    $parent = $parent->parentCategory();
                }
            }
        }
    }
}
