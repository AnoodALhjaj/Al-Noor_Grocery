<?php
namespace App\Http\Services;

use App\Models\Invoice;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class InvoiceService
{
    // make the mathmatics and doublicate of invoice checked
    private const TAX_RATE = 15.00;

    public function createInvoice(array $validatedData, ?string $idempotencyKey = null): Invoice
    {
       // check if the invoice is exsist for 60 second no matter how the request is been sent to the backend
     $effectiveKey = $idempotencyKey ?: $this->generateTimeWindowHash($validatedData);

       if ($effectiveKey) {
            $existingInvoice = Invoice::with('items')
                ->where('idempotency_key', $effectiveKey)
                ->first();

            if ($existingInvoice) {
                return $existingInvoice;
            }
        }
        //transaction to make sure the invoice won't create if anything wrong goes and only create if everything is ok
        try {
        return DB::transaction(function () use ($validatedData, $effectiveKey) {
                 $subtotal=0;
                $preparedItems = [];

            foreach ($validatedData['items'] as $item) {
                $quantity  = (int) $item['qty'];
                $unitPrice = round((float) $item['price'], 2);
                $lineTotal = round($quantity * $unitPrice, 2);

                $subtotal += $lineTotal;

                $preparedItems[] = [
                    'item_name'  => $item['name'],
                    'qty'   => $quantity,
                    'unit_price' => $unitPrice,
                    'line_total' => $lineTotal,
                ];
            }

            $subtotal  = round($subtotal, 2);
            $taxAmount = round($subtotal * (self::TAX_RATE / 100), 2);
            $total     = round($subtotal + $taxAmount, 2);

            $invoice = Invoice::create([
                'invoice_number' => 'INV-' . date('Ymd') . '-' . strtoupper(Str::random(5)),
                'customer_name'  => trim($validatedData['customer']),
                'subtotal'       => $subtotal,
                'tax_rate'       => self::TAX_RATE,
                'tax_amount'     => $taxAmount,
                'total'          => $total,
                'idempotency_key' => $effectiveKey,
            ]);

            $invoice->items()->createMany($preparedItems);

            return $invoice->load('items');
        });
        } catch (UniqueConstraintViolationException $e) {
            //if two request of the same invoice reach at the same time the backend
          
                return Invoice::with('items')
                    ->where('idempotency_key', $effectiveKey)
                    ->firstOrFail();
            
        }
        
    }
    private function generateTimeWindowHash(array $data): string
{
    // Dividing time into 60-second segments
    $timeBucket = floor(now()->timestamp / 60);

    $normalizedPayload = [
        'customer' => trim($data['customer']),
        'items'    => $data['items'],
        'bucket'   => $timeBucket,
    ];

    return 'auto_' . hash('sha256', json_encode($normalizedPayload, JSON_UNESCAPED_UNICODE));
}
}
?>