<?php 
namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            ['id' => 1,  'name' => 'رز بسمتي 10 كيلو',   'price' => 18000],
            ['id' => 2,  'name' => 'زيت طبخ 1.8 لتر',    'price' => 4200],
            ['id' => 3,  'name' => 'سكر 5 كيلو',         'price' => 6500],
            ['id' => 4,  'name' => 'حليب نيدو 900 جرام', 'price' => 9800],
            ['id' => 5,  'name' => 'شاهي 450 جرام',      'price' => 3100],
            ['id' => 6,  'name' => 'معكرونة (6 حبات)',   'price' => 2700],
            ['id' => 7,  'name' => 'تونة (4 علب)',       'price' => 3600],
            ['id' => 8,  'name' => 'دقيق 10 كيلو',       'price' => 9500],
            ['id' => 9,  'name' => 'عسل سدر 500 جرام',   'price' => 22000],
            ['id' => 10, 'name' => 'قهوة يمنية 250 جرام','price' => 5200],
        ];

        foreach ($products as $product) {
            Product::updateOrCreate(
                ['id' => $product['id']],
                ['name' => $product['name'], 'price' => $product['price']]
            );
        }
    }
}
?>