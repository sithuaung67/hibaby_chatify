<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Chatify\Traits\UUID;

class ChMessage extends Model
{
    use UUID;

    protected $fillable = [
        'type',
        'from_id',
        'to_id',
        'body',
        'sent_by',
        'product_id',
        'attachment',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
