<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiImportBatch extends Model
{
    protected $guarded = [];

    protected $casts = [
        'previous_editor_json' => 'array',
        'created_product_ids' => 'array',
        'created_brand_ids' => 'array',
    ];
}
