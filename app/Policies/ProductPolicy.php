<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ProductPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('produtos.visualizar');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Product $product): bool
    {
        return $user->hasPermission('produtos.visualizar');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('produtos.cadastrar');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Product $product): bool
    {
        return $user->hasPermission('produtos.editar');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Product $product): bool
    {
        return $user->hasPermission('produtos.excluir');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Product $product): bool
    {
        return $user->hasPermission('produtos.editar');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Product $product): bool
    {
        return $user->hasPermission('produtos.excluir');
    }

    /**
     * Determine whether the user can view the location of the product.
     */
    public function viewLocation(User $user, Product $product): bool
    {
        return $user->hasPermission('localizacao.visualizar');
    }

    /**
     * Determine whether the user can edit the location of the product.
     */
    public function editLocation(User $user, Product $product): bool
    {
        return $user->hasPermission('localizacao.editar');
    }
}
