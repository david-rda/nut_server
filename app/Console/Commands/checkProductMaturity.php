<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
// use App\Models\Product;
// use Carbon\Carbon;

class checkProductMaturity extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check-product-maturity';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Motsemuli brdzaneba amowmebs pestitsidis vadas.";

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // $this->info(Product::first());
    }
}
