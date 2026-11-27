<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
{
//convert json to database to store and show data base as json
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'invoice_number' => $this->invoice_number,
            'customer'       => $this->customer_name,
            'subtotal'       => (float) $this->subtotal,
            'tax'            => (float) $this->tax_amount,
            'total'          => (float) $this->total,
            'currency'       => $this->currency ?? 'YER',
            'items'          => $this->whenLoaded('items', function () {
                return $this->items->map(fn ($item) => [
                  //  'product_id' => $item->product_id,
                    'name'       => $item->item_name,
                    'qty'        => (int) $item->qty,
                    'price'      => (float) $item->unit_price,
                    'line_total' => (float) $item->line_total,
                ]);
            }),
            'created_at'     => $this->created_at?->toDayDateTimeString(),
        ];
    }
}
?>
