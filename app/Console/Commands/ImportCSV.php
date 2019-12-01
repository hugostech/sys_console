<?php

namespace App\Console\Commands;

use backend\CSVReader;
use Illuminate\Console\Command;

class ImportCSV extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'csv:read';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'read csv from folder';

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
        ini_set('memory_limit', -1);
//        $filePath = storage_path('csv/141970.CSV');
        foreach (glob(storage_path('csv').'/*.*') as $file){
            $this->info($file);
        }
        $csv = CSVReader::loadCSVByFile('141970.CSV');
        dd($csv->process());

    }
}
