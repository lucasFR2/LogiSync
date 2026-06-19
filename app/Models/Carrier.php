<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Carrier extends Model
{
    protected $fillable = [
        'name',
        'cnpj',
        'state_registration',
        'contact',
        'email',
        'phone',
        'antt',
        'vehicle_plate',
        'vehicle_uf',
        'vehicle_type',
        'street',
        'number',
        'complement',
        'neighborhood',
        'city',
        'state',
        'zip_code',
    ];

    /**
     * Endereço formatado para exibição
     */
    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([
            $this->street,
            $this->number ? 'Nº ' . $this->number : null,
            $this->complement,
            $this->neighborhood,
            $this->city && $this->state ? "{$this->city}/{$this->state}" : ($this->city ?? $this->state),
            $this->zip_code ? 'CEP: ' . $this->zip_code : null,
        ]);
        return implode(', ', $parts);
    }
}
