<?php

namespace App\Models;



use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    protected $fillable = [
        'invoice_number',
        'customer_name',
        'subtotal',
        'tax_rate',
        'tax_amount',
        'total',
        'currency',
        'idempotency_key',
    ];

    protected $casts = [
        'subtotal'   => 'float',
        'tax_rate'   => 'float',
        'tax_amount' => 'float',
        'total'      => 'float',
    ];
//حتى يتم ربط العلاقة بين المنتجات بالفاتورة
    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    // ربط الفاتورة برقم فريد او برقم بضيعة INV حتى يتمكن من استدعائها
    public function resolveRouteBinding($value, $field = null)
    {
        return $this->where('invoice_number', $value)
            ->orWhere('id', $value)
            ->firstOrFail();
    }
}
