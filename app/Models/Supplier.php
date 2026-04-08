<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    public $timestamps = false;

    protected $fillable = ['name', 'contact_email', 'contact_phone'];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
