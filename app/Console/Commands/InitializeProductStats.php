<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;

class InitializeProductStats extends Command
{
    protected $signature = 'app:initialize-product-stats';
    protected $description = 'Initialize sales_count and recalculate ratings for all products from existing data';

    public function handle()
    {
        $this->info('Initializing product statistics...');
        
        $products = Product::all();
        
        foreach ($products as $product) {
            $this->line("Processing: {$product->name}");
            
            $product->updateSalesCount();
            $product->updateRatingStats();
        }
        
        $this->info('✓ Product statistics initialized successfully!');
    }
}
