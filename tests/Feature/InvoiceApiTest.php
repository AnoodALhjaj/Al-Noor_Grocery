<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceApiTest extends TestCase
{
    use RefreshDatabase;

   

    public function test_it_rejects_invalid_invoice_data(): void
    {
        $invalidPayload = [
            'customer' => '',
            'items' => [
                ['name' => 'حليب', 'qty' => -2, 'price' => 0],
            ],
        ];

        $response = $this->postJson('/api/invoices', $invalidPayload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'customer',
                'items.0.qty',
                'items.0.price',
            ]);
    }
    public function test_it_prevents_double_submission_automatically_within_15_seconds_and_allows_after(): void
{
    $payload = [
        'customer' => 'عبدالله الشامي',
        'items'    => [['name' => 'تونة (4 علب)', 'qty' => 1, 'price' => 3600]],
    ];

    // الإرسال الأول
    $this->postJson('/api/invoices', $payload)->assertStatus(201);

    // النقر المزدوج خلال نفس اللحظة (بدون Idempotency-Key) -> يُعيد الفاتورة السابقة 200
    $this->postJson('/api/invoices', $payload)->assertStatus(200);
    $this->assertDatabaseCount('invoices', 1);

    // تقديم الزمن 60 ثانية باستخدام أدوات Laravel للزمن -> يسمح بإنشاء فاتورة جديدة 201
   $this->travel(60)->seconds();
    $this->postJson('/api/invoices', $payload)->assertStatus(201);
    $this->assertDatabaseCount('invoices', 2);
}

private function getFixtures(): array
    {
        //test file
        $path = base_path('tests/grocery.json');
        $content = file_get_contents($path);

        return json_decode($content, true, 512, JSON_THROW_ON_ERROR);
    }

    public function test_all_valid_requests_from_json_fixture(): void
    {
        $fixtures = $this->getFixtures();

        foreach ($fixtures['valid_requests'] as $case) {
            $payload = [
                'customer' => $case['customer'],
                'items'    => $case['items'],
            ];

            $response = $this->postJson('/api/invoices', $payload);

            $response->assertStatus(201)
                ->assertJsonPath('data.customer', $case['customer'])
                ->assertJsonPath('data.subtotal', $case['expected']['subtotal'])
                ->assertJsonPath('data.tax', $case['expected']['tax'])
                ->assertJsonPath('data.total', $case['expected']['total'])
                ->assertJsonCount(count($case['items']), 'data.items');
        }

        $this->assertDatabaseCount('invoices', count($fixtures['valid_requests']));
    }

    public function test_all_invalid_requests_from_json_fixture(): void
    {
        $fixtures = $this->getFixtures();

        foreach ($fixtures['invalid_requests'] as $case) {
            $response = $this->postJson('/api/invoices', $case['body']);

            // Notice the 4th case in grocery.json has "total": 1 with valid items:
            // The server must ignore "total": 1 and still create the invoice with 201!
            if (isset($case['body']['total'])) {
                $response->assertStatus(201)
                    ->assertJsonPath('data.subtotal', 9800)
                    ->assertJsonPath('data.tax', 1470)
                    ->assertJsonPath('data.total', 11270);
            } else {
                // All malformed cases (negative qty, missing price, empty items) must fail with 422
                $response->assertStatus(422);
            }
        }
    }
}
?>