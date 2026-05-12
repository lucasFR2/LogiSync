<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    protected $fillable = [
        'name',
        'cnpj',
        'state_registration',
        'email',
        'phone',
        'contact',
        'street',
        'number',
        'neighborhood',
        'city',
        'state',
        'zip_code',
        'address',
    ];

    /**
     * Relationship: Um fornecedor tem muitos produtos
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
