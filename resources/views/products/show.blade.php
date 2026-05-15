@extends('layouts.app')

@section('title', 'Detalhes: ' . $product->name)
@section('page-title', $product->name)
@section('page-subtitle', 'Detalhes do produto')

@section('content')
<div style="display:flex;flex-direction:column;gap:1.5rem;">

    <!-- Breadcrumb -->
    <div style="display:flex;align-items:center;gap:.5rem;font-size:.8rem;color:var(--text-muted);">
        <a href="{{ route('products.index') }}" style="color:var(--accent);text-decoration:none;"><i class="fa-solid fa-boxes-stacked"></i> Produtos</a>
        <i class="fa-solid fa-chevron-right" style="font-size:.65rem;"></i>
        <span>{{ $product->name }}</span>
    </div>

    @if ($message = session('success'))
        <div class="alert alert-success">
            <i class="fa-solid fa-check-circle"></i>
            {{ $message }}
        </div>
    @endif

    <div style="display:grid;grid-template-columns:1fr 300px;gap:1.5rem;align-items:start;">
        <!-- Produto Info -->
        <div class="card anim-fade-up">
            <div class="card-header">
                <span class="card-title" style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                    <i class="fa-solid fa-box"></i> {{ $product->name }}
                </span>
                <div style="display:flex;gap:.5rem;">
                    @can('produtos.editar')
                    <a href="{{ route('products.edit', $product) }}" class="btn btn-secondary btn-sm">
                        <i class="fa-solid fa-pencil"></i> Editar
                    </a>
                    @endcan
                    @can('produtos.excluir')
                    <form method="POST" action="{{ route('products.destroy', $product) }}" style="display:inline;" onsubmit="return confirm('Tem certeza que deseja deletar este produto?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-primary btn-sm" style="background-color:var(--red);border-color:var(--red);">
                            <i class="fa-solid fa-trash"></i> Deletar
                        </button>
                    </form>
                    @endcan
                </div>
            </div>

            <div class="card-body" style="display:flex;flex-direction:column;gap:1.5rem;">
                
                {{-- Informações Básicas --}}
                <div>
                    <div style="font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--text-muted);margin-bottom:.875rem;">
                        <i class="fa-solid fa-info-circle"></i> Informações Básicas
                    </div>
                    <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(140px, 1fr));gap:1rem;">
                        <div>
                            <div style="font-size:.75rem;color:var(--text-muted);margin-bottom:.2rem;font-weight:600;">Código de Barras</div>
                            <div style="font-family:monospace;font-size:.875rem;">{{ $product->barcode ?? '—' }}</div>
                        </div>
                        <div>
                            <div style="font-size:.75rem;color:var(--text-muted);margin-bottom:.2rem;font-weight:600;">Categoria</div>
                            <div style="font-size:.875rem;">{{ ucfirst($product->category ?? 'Não informada') }}</div>
                        </div>
                        <div>
                            <div style="font-size:.75rem;color:var(--text-muted);margin-bottom:.2rem;font-weight:600;">Unidade de Medida</div>
                            <div style="font-size:.875rem;">{{ strtoupper($product->unit ?? 'un') }}</div>
                        </div>
                        <div>
                            <div style="font-size:.75rem;color:var(--text-muted);margin-bottom:.2rem;font-weight:600;">Fornecedor</div>
                            <div style="font-size:.875rem;">{{ $product->supplier?->name ?? '—' }}</div>
                        </div>
                        <div>
                            <div style="font-size:.75rem;color:var(--text-muted);margin-bottom:.2rem;font-weight:600;">Status</div>
                            <div style="font-size:.875rem;">
                                @if ($product->status == 'ativo')
                                    <span class="badge badge-green">Ativo</span>
                                @elseif ($product->status == 'inativo')
                                    <span class="badge badge-red">Inativo</span>
                                @else
                                    <span class="badge" style="background:var(--bg-hover);color:var(--text-primary);">Descontinuado</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div style="margin-top:1rem;">
                        <div style="font-size:.75rem;color:var(--text-muted);margin-bottom:.2rem;font-weight:600;">Descrição</div>
                        <div style="font-size:.875rem;color:var(--text-secondary);">{{ $product->description ?? '—' }}</div>
                    </div>
                </div>

                <div style="border-top:1px solid var(--border);"></div>

                {{-- Preços e Estoque --}}
                <div>
                    <div style="font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--text-muted);margin-bottom:.875rem;">
                        <i class="fa-solid fa-tag"></i> Preços e Estoque
                    </div>
                    <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(140px, 1fr));gap:1rem;">
                        <div>
                            <div style="font-size:.75rem;color:var(--text-muted);margin-bottom:.2rem;font-weight:600;">Preço de Venda</div>
                            <div style="font-size:1.1rem;font-weight:700;">R$ {{ number_format($product->unit_price, 2, ',', '.') }}</div>
                        </div>
                        <div>
                            <div style="font-size:.75rem;color:var(--text-muted);margin-bottom:.2rem;font-weight:600;">Custo Unitário</div>
                            <div style="font-size:1rem;font-weight:600;color:var(--text-secondary);">R$ {{ number_format($product->cost_price ?? 0, 2, ',', '.') }}</div>
                        </div>
                        <div>
                            <div style="font-size:.75rem;color:var(--text-muted);margin-bottom:.2rem;font-weight:600;">Quantidade</div>
                            <div style="font-size:1.1rem;font-weight:700;">{{ $product->quantity }}</div>
                        </div>
                        <div>
                            <div style="font-size:.75rem;color:var(--text-muted);margin-bottom:.2rem;font-weight:600;">Valor Total</div>
                            <div style="font-size:1rem;font-weight:600;color:var(--text-secondary);">R$ {{ number_format($product->quantity * $product->unit_price, 2, ',', '.') }}</div>
                        </div>
                        <div>
                            <div style="font-size:.75rem;color:var(--text-muted);margin-bottom:.2rem;font-weight:600;">Nível de Ressuprimento</div>
                            <div style="font-size:.875rem;font-weight:600;color:var(--text-secondary);">{{ $product->reorder_level }}</div>
                        </div>
                        <div>
                            <div style="font-size:.75rem;color:var(--text-muted);margin-bottom:.2rem;font-weight:600;">Estoque Máximo</div>
                            <div style="font-size:.875rem;font-weight:600;color:var(--text-secondary);">{{ $product->max_stock ?? '—' }}</div>
                        </div>
                        <div>
                            <div style="font-size:.75rem;color:var(--text-muted);margin-bottom:.2rem;font-weight:600;">Qtd. por Embalagem</div>
                            <div style="font-size:.875rem;font-weight:600;color:var(--text-secondary);">{{ $product->package_quantity ?? 1 }}</div>
                        </div>
                        <div>
                            <div style="font-size:.75rem;color:var(--text-muted);margin-bottom:.2rem;font-weight:600;">Status de Estoque</div>
                            <div style="font-size:.875rem;">
                                @if ($product->quantity <= $product->reorder_level)
                                    <span class="badge badge-red">Abaixo do limite</span>
                                @elseif ($product->quantity <= ($product->reorder_level * 1.5))
                                    <span class="badge badge-orange">Atenção</span>
                                @else
                                    <span class="badge badge-green">Em estoque</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                @if ($product->weight || $product->height || $product->width || $product->depth)
                <div style="border-top:1px solid var(--border);"></div>

                {{-- Dimensões e Peso --}}
                <div>
                    <div style="font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--text-muted);margin-bottom:.875rem;">
                        <i class="fa-solid fa-ruler-combined"></i> Dimensões e Peso
                    </div>
                    <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(120px, 1fr));gap:1rem;">
                        <div>
                            <div style="font-size:.75rem;color:var(--text-muted);margin-bottom:.2rem;font-weight:600;">Peso</div>
                            <div style="font-size:.875rem;font-weight:600;color:var(--text-secondary);">{{ $product->weight ?? '—' }} kg</div>
                        </div>
                        <div>
                            <div style="font-size:.75rem;color:var(--text-muted);margin-bottom:.2rem;font-weight:600;">Altura</div>
                            <div style="font-size:.875rem;font-weight:600;color:var(--text-secondary);">{{ $product->height ?? '—' }} cm</div>
                        </div>
                        <div>
                            <div style="font-size:.75rem;color:var(--text-muted);margin-bottom:.2rem;font-weight:600;">Largura</div>
                            <div style="font-size:.875rem;font-weight:600;color:var(--text-secondary);">{{ $product->width ?? '—' }} cm</div>
                        </div>
                        <div>
                            <div style="font-size:.75rem;color:var(--text-muted);margin-bottom:.2rem;font-weight:600;">Profundidade</div>
                            <div style="font-size:.875rem;font-weight:600;color:var(--text-secondary);">{{ $product->depth ?? '—' }} cm</div>
                        </div>
                    </div>
                </div>
                @endif

                @if ($product->warehouse_location)
                <div style="border-top:1px solid var(--border);"></div>

                {{-- Localização --}}
                <div>
                    <div style="font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--text-muted);margin-bottom:.875rem;">
                        <i class="fa-solid fa-warehouse"></i> Localização no Armazém
                    </div>
                    <div style="background:var(--bg-hover);padding:.75rem 1rem;border-radius:var(--r-md);font-weight:600;color:var(--text-primary);display:inline-block;">
                        {{ $product->location?->full_code ?? $product->warehouse_location }}
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Sidebar Right: Histórico -->
        <div style="display:flex;flex-direction:column;gap:1.5rem;">
            <div class="card anim-fade-up" style="animation-delay:0.1s;">
                <div class="card-header">
                    <span class="card-title" style="font-size:.9rem;"><i class="fa-solid fa-history"></i> Histórico</span>
                </div>
                <div class="card-body" style="padding:1rem;">
                    @php
                        $auditLogs = method_exists($product, 'auditLogs') ? $product->auditLogs()->orderBy('changed_at', 'desc')->limit(10)->get() : collect();
                    @endphp

                    @if ($auditLogs->count() > 0)
                        <div style="display:flex;flex-direction:column;gap:1rem;">
                            @foreach ($auditLogs as $log)
                                <div style="display:flex;gap:.75rem;">
                                    <div style="margin-top:.15rem;color:var(--accent);font-size:.8rem;">
                                        <i class="fa-solid fa-circle-dot"></i>
                                    </div>
                                    <div style="flex:1;">
                                        <div style="font-size:.8125rem;font-weight:600;">{{ $log->field_name }}</div>
                                        <div style="font-size:.75rem;color:var(--text-muted);margin-bottom:.25rem;">{{ $log->changed_at->format('d/m/Y H:i') }}</div>
                                        @if ($log->old_value !== null && $log->new_value !== null)
                                            <div style="font-size:.75rem;background:var(--bg-body);padding:.25rem .5rem;border-radius:var(--r-sm);border:1px solid var(--border);">
                                                <span style="color:var(--red);text-decoration:line-through;">{{ Str::limit($log->old_value, 20) }}</span>
                                                <i class="fa-solid fa-arrow-right" style="margin:0 .25rem;color:var(--text-muted);font-size:.6rem;"></i>
                                                <span style="color:var(--green);">{{ Str::limit($log->new_value, 20) }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div style="text-align:center;padding:1rem 0;">
                            <div style="color:var(--text-muted);font-size:1.5rem;margin-bottom:.5rem;"><i class="fa-solid fa-clock"></i></div>
                            <div style="font-size:.8rem;color:var(--text-secondary);">Sem alterações registradas</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Histórico de Entradas -->
    <div class="card anim-fade-up" style="animation-delay:0.2s;">
        <div class="card-header">
            <span class="card-title">
                <i class="fa-solid fa-truck-ramp-box"></i> Histórico de Entradas
            </span>
        </div>
        <div class="table-wrap">
            @if ($inventories && $inventories->count() > 0)
                <table>
                    <thead>
                        <tr>
                            <th>Data e Hora</th>
                            <th style="text-align:center;">Quantidade</th>
                            <th>Observações</th>
                            <th>Usuário</th>
                            <th>Lote</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($inventories as $inventory)
                            <tr>
                                <td>
                                    <div style="font-weight:600;font-size:.85rem;">{{ $inventory->created_at->format('d/m/Y H:i') }}</div>
                                </td>
                                <td style="text-align:center;">
                                    <span class="badge badge-green">+{{ $inventory->quantity }}</span>
                                </td>
                                <td>
                                    <div style="font-size:.85rem;color:var(--text-secondary);">{{ Str::limit($inventory->notes, 40) ?? '—' }}</div>
                                </td>
                                <td>
                                    <div style="font-size:.85rem;color:var(--text-secondary);">{{ $inventory->user->name ?? '—' }}</div>
                                </td>
                                <td>
                                    <div style="font-size:.85rem;color:var(--text-secondary);font-family:monospace;">{{ $inventory->lot_number ?? '—' }}</div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                @if(method_exists($inventories, 'links'))
                <div style="padding:1rem 1.25rem;border-top:1px solid var(--border);">
                    <div class="pagination-wrap" style="margin-top:0;">
                        {{ $inventories->links() }}
                    </div>
                </div>
                @endif
            @else
                <div class="empty-state">
                    <div class="empty-state-icon"><i class="fa-solid fa-inbox"></i></div>
                    <h4>Nenhuma entrada registrada</h4>
                    <p>As movimentações de entrada para este produto aparecerão aqui.</p>
                </div>
            @endif
        </div>
    </div>

</div>
@endsection
