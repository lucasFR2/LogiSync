<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WarehouseLocation extends Model
{
    /** Dimensões padrão do cubo fixo (unidades arbitrárias) */
    public const CUBE_SIZE = 10.0;

    /** Volume máximo total de cada posição: 10 × 10 × 10 = 1.000 */
    public const MAX_VOLUME = 1000.0;

    protected $fillable = [
        'aisle',
        'column',
        'level',
        'full_code',
        'is_occupied',
        'allow_shared',
        'width',
        'height',
        'depth',
        'max_weight',
    ];

    protected $casts = [
        'is_occupied'  => 'boolean',
        'allow_shared' => 'boolean',
        'width'        => 'decimal:2',
        'height'       => 'decimal:2',
        'depth'        => 'decimal:2',
        'max_weight'   => 'decimal:2',
    ];

    // ────────────────────────────────────────────────────────────────────────
    // Relationships
    // ────────────────────────────────────────────────────────────────────────

    /**
     * Produtos vinculados a esta localização.
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'warehouse_location_id');
    }

    // ────────────────────────────────────────────────────────────────────────
    // Volumetrics
    // ────────────────────────────────────────────────────────────────────────

    /**
     * Volume total disponível nesta posição (largura × altura × profundidade).
     * Se as dimensões não estiverem definidas, usa o cubo padrão 10×10×10.
     */
    public function totalVolume(): float
    {
        $w = (float) ($this->width  ?: self::CUBE_SIZE);
        $h = (float) ($this->height ?: self::CUBE_SIZE);
        $d = (float) ($this->depth  ?: self::CUBE_SIZE);

        return $w * $h * $d;
    }

    /**
     * Volume já ocupado pelos produtos nesta posição.
     *
     * Calcula: Σ (width × height × depth × quantity) para cada produto vinculado.
     *
     * @param  int|null  $excludeProductId  Exclui este produto do cálculo (usado ao editar).
     */
    public function usedVolume(?int $excludeProductId = null): float
    {
        $query = $this->products()
            ->select('width', 'height', 'depth', 'quantity')
            ->when($excludeProductId, fn ($q) => $q->where('id', '!=', $excludeProductId));

        $total = 0.0;
        foreach ($query->get() as $p) {
            $w = (float) ($p->width  ?: 1.0);
            $h = (float) ($p->height ?: 1.0);
            $d = (float) ($p->depth  ?: 1.0);
            $total += $w * $h * $d * (int) $p->quantity;
        }

        return round($total, 4);
    }

    /**
     * Volume disponível restante nesta posição.
     */
    public function availableVolume(?int $excludeProductId = null): float
    {
        return max(0.0, $this->totalVolume() - $this->usedVolume($excludeProductId));
    }

    /**
     * Percentual de ocupação (0–100).
     */
    public function occupancyPercent(?int $excludeProductId = null): float
    {
        $total = $this->totalVolume();
        if ($total <= 0) return 100.0;

        return min(100.0, round(($this->usedVolume($excludeProductId) / $total) * 100, 1));
    }

    /**
     * Verifica se um produto com determinada quantidade cabe nesta posição.
     *
     * @param  Product   $product          Produto a ser inserido/movido.
     * @param  int|float $quantity         Quantidade a ser inserida.
     * @param  int|null  $excludeProductId Exclui este produto do cálculo de ocupação atual.
     *
     * @throws \Exception Se não couber.
     */
    public function canFitProduct(Product $product, $quantity, ?int $excludeProductId = null): void
    {
        if ($quantity <= 0) return;

        $requiredVolume = $product->unitVolume() * $quantity;
        $available      = $this->availableVolume($excludeProductId);

        if ($requiredVolume > $available) {
            $totalVol  = number_format($this->totalVolume(), 2);
            $usedVol   = number_format($this->usedVolume($excludeProductId), 2);
            $reqVol    = number_format($requiredVolume, 2);
            $avail     = number_format($available, 2);

            throw new \Exception(
                "Espaço insuficiente na posição {$this->full_code}. " .
                "Capacidade total: {$totalVol} u³ | Ocupado: {$usedVol} u³ | " .
                "Disponível: {$avail} u³ | Necessário para {$quantity} un: {$reqVol} u³."
            );
        }
    }

    // ────────────────────────────────────────────────────────────────────────
    // Legacy / Compat
    // ────────────────────────────────────────────────────────────────────────

    /**
     * Verifica se outro produto pode ser atribuído a esta localização.
     * Mantido para compatibilidade — agora delega para canFitProduct().
     *
     * @deprecated Use canFitProduct() para validação volumétrica completa.
     */
    public function canAssignTo(?int $excludeProductId = null): bool
    {
        if (!$this->is_occupied) return true;
        if ($this->allow_shared) return true;

        $occupants = $this->products()
            ->when($excludeProductId, fn ($q) => $q->where('id', '!=', $excludeProductId))
            ->count();

        return $occupants === 0;
    }
}
