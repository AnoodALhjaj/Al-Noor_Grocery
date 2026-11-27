<?php

namespace App\Models;



use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceItem extends Model
{
    protected $fillable = [
        'invoice_id',
        'item_name',
        'qty',
        'unit_price',
        'line_total',
    ];

    protected $casts = [
        'qty'        => 'integer',
        'unit_price' => 'float',
        'line_total' => 'float',
    ];
//المنتجات التي تنتمي لفاتورة تم تعريف علقاتها ليتم الاستدعاء
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}