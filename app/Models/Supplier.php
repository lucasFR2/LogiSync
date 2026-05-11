<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    protected $fillable = [
        'name',
<<<<<<< Updated upstream
        'contact',
        'phone',
        'address',
        'email',
=======
        'cnpj',
        'state_registration',
        'email',
        'phone',
        'street',
        'number',
        'neighborhood',
        'zip_code',
>>>>>>> Stashed changes
        'city',
        'state',
        'cnpj',
    ];

    /**
     * Relationship: Um fornecedor tem muitos produtos
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
