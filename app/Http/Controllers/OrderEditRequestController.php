<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderEditRequest;
use App\Models\OrderLog;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderEditRequestController extends Controller
{
    public function request(Request $request, Order $order)
    {
        try {
            $validated = $request->validate([
            'reason' => 'required|string|max:1000',
                'changes' => 'nullable|array'
        ]);

        // Verificar se já existe uma solicitação pendente
        if ($order->pendingEditRequest) {
            return response()->json([
                'success' => false,
                'message' => 'Já existe uma solicitação de edição pendente para este pedido.'
            ], 400);
        }

        // Verificar se o pedido pode ser editado
        if ($order->is_cancelled) {
            return response()->json([
                'success' => false,
                'message' => 'Pedidos cancelados não podem ser editados.'
            ], 400);
        }

        DB::beginTransaction();
            
            // Criar solicitação de edição
            $editRequest = OrderEditRequest::create([
                'order_id' => $order->id,
                'user_id' => Auth::id(),
                'reason' => $validated['reason'],
                'changes' => $validated['changes'] ?? [],
                'status' => 'pending'
            ]);

            // Atualizar pedido (se existir o campo)
            if (isset($order->has_pending_edit)) {
            $order->update([
                'has_pending_edit' => true
            ]);
            }

            // Criar log
            OrderLog::create([
                'order_id' => $order->id,
                'user_id' => Auth::id(),
                'user_name' => Auth::user()->name,
                'action' => 'edit_requested',
                'description' => 'Solicitação de edição enviada',
                'details' => json_encode([
                    'reason' => $validated['reason'],
                    'changes' => $validated['changes'] ?? []
                ])
            ]);

            // Notificar todos os admins e usuários de produção
            $admins = User::where('role', 'admin')->get();
            $producaoUsers = User::where('role', 'producao')->get();
            $usersToNotify = $admins->merge($producaoUsers);
            
            foreach ($usersToNotify as $userToNotify) {
                Notification::createEditRequest(
                    $userToNotify->id,
                    $order->id,
                    str_pad($order->id, 6, '0', STR_PAD_LEFT),
                    Auth::user()->name
                );
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Solicitação de edição enviada com sucesso.',
                'edit_request_id' => $editRequest->id
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro de validação.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erro ao processar solicitação de edição: ' . $e->getMessage(), [
                'order_id' => $order->id,
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao processar solicitação de edição: ' . $e->getMessage()
            ], 500);
        }
    }

    public function approve(Request $request, OrderEditRequest $editRequest)
    {
        // Verificar se o usuário é administrador ou de produção
        if (!Auth::user()->isAdmin() && !Auth::user()->isProducao()) {
            return redirect()->route('admin.edit-requests.index')
                ->with('error', 'Acesso negado. Apenas administradores e usuários de produção podem aprovar edições.');
        }

        $request->validate([
            'admin_notes' => 'nullable|string|max:1000'
        ]);

        if ($editRequest->status !== 'pending') {
            return redirect()->route('admin.edit-requests.index')
                ->with('error', 'Esta solicitação já foi processada.');
        }

        DB::beginTransaction();
        try {
            // Capturar snapshot do pedido ANTES da edição
            $order = $editRequest->order->load([
                'client', 
                'items.sublimations.size', 
                'items.sublimations.location',
                'items.sublimations.files',
                'items.files',
                'payments'
            ]);
            
            $snapshotBefore = [
                'order' => [
                    'id' => $order->id,
                    'seller' => $order->seller,
                    'contract_type' => $order->contract_type,
                    'nt' => $order->nt,
                    'order_date' => $order->order_date,
                    'delivery_date' => $order->delivery_date,
                    'entry_date' => $order->entry_date,
                    'subtotal' => $order->subtotal,
                    'discount' => $order->discount,
                    'delivery_fee' => $order->delivery_fee,
                    'total' => $order->total,
                    'notes' => $order->notes,
                    'cover_image' => $order->cover_image,
                    'is_event' => $order->is_event,
                ],
                'client' => [
                    'id' => $order->client->id,
                    'name' => $order->client->name,
                    'phone_primary' => $order->client->phone_primary,
                    'phone_secondary' => $order->client->phone_secondary,
                    'email' => $order->client->email,
                    'cpf_cnpj' => $order->client->cpf_cnpj,
                    'address' => $order->client->address,
                    'city' => $order->client->city,
                    'state' => $order->client->state,
                    'zip_code' => $order->client->zip_code,
                ],
                'items' => $order->items->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'item_number' => $item->item_number,
                        'print_type' => $item->print_type,
                        'art_name' => $item->art_name,
                        'art_notes' => $item->art_notes,
                        'quantity' => $item->quantity,
                        'unit_price' => $item->unit_price,
                        'fabric' => $item->fabric,
                        'color' => $item->color,
                        'collar' => $item->collar,
                        'model' => $item->model,
                        'detail' => $item->detail,
                        'sizes' => $item->sizes,
                        'cover_image' => $item->cover_image,
                        'sublimations' => $item->sublimations->map(function ($sub) {
                            return [
                                'id' => $sub->id,
                                'application_type' => $sub->application_type,
                                'art_name' => $sub->art_name,
                                'size_name' => $sub->size ? $sub->size->name : $sub->size_name,
                                'size_dimensions' => $sub->size ? $sub->size->dimensions : null,
                                'location_name' => $sub->location ? $sub->location->name : $sub->location_name,
                                'quantity' => $sub->quantity,
                                'color_count' => $sub->color_count,
                                'color_details' => $sub->color_details,
                                'has_neon' => $sub->has_neon,
                                'unit_price' => $sub->unit_price,
                                'total_price' => $sub->total_price,
                                'seller_notes' => $sub->seller_notes,
                                'application_image' => $sub->application_image,
                                'files' => $sub->files->map(function ($file) {
                                    return [
                                        'id' => $file->id,
                                        'file_name' => $file->file_name,
                                        'file_path' => $file->file_path,
                                        'file_type' => $file->file_type,
                                    ];
                                })->toArray(),
                            ];
                        })->toArray(),
                        'files' => $item->files->map(function ($file) {
                            return [
                                'id' => $file->id,
                                'file_name' => $file->file_name,
                                'file_path' => $file->file_path,
                                'file_type' => $file->file_type,
                            ];
                        })->toArray(),
                    ];
                })->toArray(),
                'payments' => $order->payments->map(function ($payment) {
                    return [
                        'id' => $payment->id,
                        'method' => $payment->method,
                        'payment_method' => $payment->payment_method,
                        'payment_methods' => $payment->payment_methods,
                        'amount' => $payment->amount,
                        'entry_amount' => $payment->entry_amount,
                        'remaining_amount' => $payment->remaining_amount,
                        'entry_date' => $payment->entry_date,
                        'payment_date' => $payment->payment_date,
                        'status' => $payment->status,
                    ];
                })->toArray(),
                'totals' => [
                    'total_items' => $order->items->sum('quantity'),
                    'total_sublimations' => $order->items->sum(function ($item) {
                        return $item->sublimations->count();
                    }),
                    'total_paid' => $order->payments->sum('entry_amount'),
                    'total_remaining' => $order->total - $order->payments->sum('entry_amount'),
                ],
                'timestamp' => now()->toDateTimeString(),
                'captured_by' => Auth::user()->name,
            ];

            // Aprovar edição e salvar snapshot
            $editRequest->update([
                'status' => 'approved',
                'admin_notes' => $request->admin_notes,
                'approved_by' => Auth::id(),
                'approved_at' => now(),
                'order_snapshot_before' => $snapshotBefore
            ]);

            // Atualizar pedido
            $editRequest->order->update([
                'has_pending_edit' => false,
                'last_updated_at' => now()
            ]);

            // Criar log
            OrderLog::create([
                'order_id' => $editRequest->order_id,
                'user_id' => Auth::id(),
                'user_name' => Auth::user()->name,
                'action' => 'edit_approved',
                'description' => 'Edição aprovada pelo admin - Aguardando implementação pelo usuário',
                'old_value' => ['status' => 'pending'],
                'new_value' => [
                    'status' => 'approved',
                    'reason' => $editRequest->reason,
                    'admin_notes' => $request->admin_notes,
                    'changes' => $editRequest->changes,
                    'snapshot_captured' => true
                ]
            ]);

            // Notificar o usuário que solicitou a edição
            Notification::createEditApproved(
                $editRequest->user_id,
                $editRequest->order_id,
                str_pad($editRequest->order_id, 6, '0', STR_PAD_LEFT),
                Auth::user()->name
            );

            DB::commit();

            return redirect()->route('admin.edit-requests.index')
                ->with('success', 'Edição aprovada com sucesso! O usuário pode agora implementar as alterações no pedido #' . str_pad($editRequest->order_id, 6, '0', STR_PAD_LEFT) . '. O estado atual do pedido foi salvo para comparação.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erro ao aprovar edição', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('admin.edit-requests.index')
                ->with('error', 'Erro ao aprovar edição: ' . $e->getMessage());
        }
    }

    public function reject(Request $request, OrderEditRequest $editRequest)
    {
        // Verificar se o usuário é administrador ou de produção
        if (!Auth::user()->isAdmin() && !Auth::user()->isProducao()) {
            return redirect()->route('admin.edit-requests.index')
                ->with('error', 'Acesso negado. Apenas administradores e usuários de produção podem rejeitar edições.');
        }

        $request->validate([
            'admin_notes' => 'required|string|max:1000'
        ]);

        if ($editRequest->status !== 'pending') {
            return redirect()->route('admin.edit-requests.index')
                ->with('error', 'Esta solicitação já foi processada.');
        }

        DB::beginTransaction();
        try {
            // Rejeitar edição
            $editRequest->update([
                'status' => 'rejected',
                'admin_notes' => $request->admin_notes,
                'approved_by' => Auth::id(),
                'approved_at' => now()
            ]);

            // Atualizar pedido
            $editRequest->order->update([
                'has_pending_edit' => false
            ]);

            // Criar log
            OrderLog::create([
                'order_id' => $editRequest->order_id,
                'user_id' => Auth::id(),
                'user_name' => Auth::user()->name,
                'action' => 'edit_rejected',
                'description' => 'Edição rejeitada',
                'old_value' => ['status' => 'pending'],
                'new_value' => [
                    'status' => 'rejected',
                    'admin_notes' => $request->admin_notes
                ]
            ]);

            // Notificar o usuário que solicitou a edição
            Notification::createEditRejected(
                $editRequest->user_id,
                $editRequest->order_id,
                str_pad($editRequest->order_id, 6, '0', STR_PAD_LEFT),
                Auth::user()->name,
                $request->admin_notes
            );

            DB::commit();

            return redirect()->route('admin.edit-requests.index')
                ->with('success', 'Edição rejeitada. O pedido #' . str_pad($editRequest->order_id, 6, '0', STR_PAD_LEFT) . ' permanece inalterado.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.edit-requests.index')
                ->with('error', 'Erro ao rejeitar edição: ' . $e->getMessage());
        }
    }

    public function complete(Request $request, OrderEditRequest $editRequest)
    {
        if ($editRequest->status !== 'approved') {
            return response()->json([
                'success' => false,
                'message' => 'Esta edição não foi aprovada ainda.'
            ], 400);
        }

        DB::beginTransaction();
        try {
            // Capturar snapshot do pedido DEPOIS da edição
            $order = $editRequest->order->load([
                'client', 
                'items.sublimations.size', 
                'items.sublimations.location',
                'items.sublimations.files',
                'items.files',
                'payments'
            ]);
            
            $snapshotAfter = [
                'order' => [
                    'id' => $order->id,
                    'seller' => $order->seller,
                    'contract_type' => $order->contract_type,
                    'nt' => $order->nt,
                    'order_date' => $order->order_date,
                    'delivery_date' => $order->delivery_date,
                    'entry_date' => $order->entry_date,
                    'subtotal' => $order->subtotal,
                    'discount' => $order->discount,
                    'delivery_fee' => $order->delivery_fee,
                    'total' => $order->total,
                    'notes' => $order->notes,
                    'cover_image' => $order->cover_image,
                    'is_event' => $order->is_event,
                ],
                'client' => [
                    'id' => $order->client->id,
                    'name' => $order->client->name,
                    'phone_primary' => $order->client->phone_primary,
                    'phone_secondary' => $order->client->phone_secondary,
                    'email' => $order->client->email,
                    'cpf_cnpj' => $order->client->cpf_cnpj,
                    'address' => $order->client->address,
                    'city' => $order->client->city,
                    'state' => $order->client->state,
                    'zip_code' => $order->client->zip_code,
                ],
                'items' => $order->items->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'item_number' => $item->item_number,
                        'print_type' => $item->print_type,
                        'art_name' => $item->art_name,
                        'art_notes' => $item->art_notes,
                        'quantity' => $item->quantity,
                        'unit_price' => $item->unit_price,
                        'fabric' => $item->fabric,
                        'color' => $item->color,
                        'collar' => $item->collar,
                        'model' => $item->model,
                        'detail' => $item->detail,
                        'sizes' => $item->sizes,
                        'cover_image' => $item->cover_image,
                        'sublimations' => $item->sublimations->map(function ($sub) {
                            return [
                                'id' => $sub->id,
                                'application_type' => $sub->application_type,
                                'art_name' => $sub->art_name,
                                'size_name' => $sub->size ? $sub->size->name : $sub->size_name,
                                'size_dimensions' => $sub->size ? $sub->size->dimensions : null,
                                'location_name' => $sub->location ? $sub->location->name : $sub->location_name,
                                'quantity' => $sub->quantity,
                                'color_count' => $sub->color_count,
                                'color_details' => $sub->color_details,
                                'has_neon' => $sub->has_neon,
                                'unit_price' => $sub->unit_price,
                                'total_price' => $sub->total_price,
                                'seller_notes' => $sub->seller_notes,
                                'application_image' => $sub->application_image,
                                'files' => $sub->files->map(function ($file) {
                                    return [
                                        'id' => $file->id,
                                        'file_name' => $file->file_name,
                                        'file_path' => $file->file_path,
                                        'file_type' => $file->file_type,
                                    ];
                                })->toArray(),
                            ];
                        })->toArray(),
                        'files' => $item->files->map(function ($file) {
                            return [
                                'id' => $file->id,
                                'file_name' => $file->file_name,
                                'file_path' => $file->file_path,
                                'file_type' => $file->file_type,
                            ];
                        })->toArray(),
                    ];
                })->toArray(),
                'payments' => $order->payments->map(function ($payment) {
                    return [
                        'id' => $payment->id,
                        'method' => $payment->method,
                        'payment_method' => $payment->payment_method,
                        'payment_methods' => $payment->payment_methods,
                        'amount' => $payment->amount,
                        'entry_amount' => $payment->entry_amount,
                        'remaining_amount' => $payment->remaining_amount,
                        'entry_date' => $payment->entry_date,
                        'payment_date' => $payment->payment_date,
                        'status' => $payment->status,
                    ];
                })->toArray(),
                'totals' => [
                    'total_items' => $order->items->sum('quantity'),
                    'total_sublimations' => $order->items->sum(function ($item) {
                        return $item->sublimations->count();
                    }),
                    'total_paid' => $order->payments->sum('entry_amount'),
                    'total_remaining' => $order->total - $order->payments->sum('entry_amount'),
                ],
                'timestamp' => now()->toDateTimeString(),
                'captured_by' => Auth::user()->name,
            ];

            // Calcular diferenças entre before e after
            $differences = $this->calculateDifferences(
                $editRequest->order_snapshot_before,
                $snapshotAfter
            );

            // Marcar como concluída e salvar snapshot after
            $editRequest->update([
                'status' => 'completed',
                'completed_at' => now(),
                'order_snapshot_after' => $snapshotAfter
            ]);

            // Atualizar pedido
            $editRequest->order->update([
                'last_updated_at' => now(),
                'is_modified' => true,
                'last_modified_at' => now()
            ]);

            // Criar log detalhado com as diferenças
            OrderLog::create([
                'order_id' => $editRequest->order_id,
                'user_id' => Auth::id(),
                'user_name' => Auth::user()->name,
                'action' => 'edit_completed',
                'description' => 'Edição implementada com sucesso - Ver detalhes das alterações no histórico',
                'old_value' => $editRequest->order_snapshot_before,
                'new_value' => $snapshotAfter,
                'details' => json_encode([
                    'differences' => $differences,
                    'changes_requested' => $editRequest->changes,
                    'admin_notes' => $editRequest->admin_notes
                ])
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Edição implementada com sucesso.',
                'differences' => $differences
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erro ao completar edição', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Erro ao marcar edição como concluída.'
            ], 500);
        }
    }

    /**
     * Calcular diferenças entre dois snapshots
     */
    private function calculateDifferences($before, $after)
    {
        $differences = [];

        if (!$before || !$after) {
            return $differences;
        }

        // Comparar dados do pedido
        foreach ($before['order'] as $key => $oldValue) {
            $newValue = $after['order'][$key] ?? null;
            if ($oldValue != $newValue) {
                $differences['order'][$key] = [
                    'old' => $oldValue,
                    'new' => $newValue,
                    'field' => $this->getFieldLabel($key)
                ];
            }
        }

        // Comparar dados do cliente
        foreach ($before['client'] as $key => $oldValue) {
            $newValue = $after['client'][$key] ?? null;
            if ($oldValue != $newValue) {
                $differences['client'][$key] = [
                    'old' => $oldValue,
                    'new' => $newValue,
                    'field' => $this->getFieldLabel($key)
                ];
            }
        }

        // Comparar itens
        $beforeItems = collect($before['items'])->keyBy('id');
        $afterItems = collect($after['items'])->keyBy('id');

        foreach ($beforeItems as $itemId => $beforeItem) {
            $afterItem = $afterItems->get($itemId);
            if ($afterItem) {
                foreach ($beforeItem as $key => $oldValue) {
                    if ($key === 'id') continue;
                    $newValue = $afterItem[$key] ?? null;
                    if ($oldValue != $newValue) {
                        $differences['items'][$itemId][$key] = [
                            'old' => $oldValue,
                            'new' => $newValue,
                            'field' => $this->getFieldLabel($key)
                        ];
                    }
                }
            }
        }

        return $differences;
    }

    /**
     * Obter label amigável para campos
     */
    private function getFieldLabel($field)
    {
        $labels = [
            // Pedido
            'seller' => 'Vendedor',
            'contract_type' => 'Tipo de Contrato',
            'nt' => 'Nome do Evento',
            'order_date' => 'Data do Pedido',
            'delivery_date' => 'Data de Entrega',
            'entry_date' => 'Data de Entrada',
            'subtotal' => 'Subtotal',
            'discount' => 'Desconto',
            'delivery_fee' => 'Taxa de Entrega',
            'total' => 'Total',
            'notes' => 'Observações',
            'cover_image' => 'Imagem de Capa do Pedido',
            'is_event' => 'É Evento',
            
            // Cliente
            'name' => 'Nome',
            'phone_primary' => 'Telefone Principal',
            'phone_secondary' => 'Telefone Secundário',
            'email' => 'Email',
            'cpf_cnpj' => 'CPF/CNPJ',
            'address' => 'Endereço',
            'city' => 'Cidade',
            'state' => 'Estado',
            'zip_code' => 'CEP',
            
            // Itens
            'item_number' => 'Número do Item',
            'print_type' => 'Tipo de Personalização',
            'art_name' => 'Nome da Arte',
            'art_notes' => 'Observações da Arte',
            'quantity' => 'Quantidade',
            'unit_price' => 'Preço Unitário',
            'fabric' => 'Tecido',
            'color' => 'Cor',
            'collar' => 'Gola',
            'model' => 'Modelo',
            'detail' => 'Detalhe',
            'sizes' => 'Tamanhos',
            'cover_image' => 'Imagem de Capa',
            
            // Sublimações
            'application_type' => 'Tipo de Aplicação',
            'size_name' => 'Tamanho',
            'size_dimensions' => 'Dimensões',
            'location_name' => 'Local',
            'color_count' => 'Nº de Cores',
            'color_details' => 'Detalhes das Cores',
            'has_neon' => 'Tem Neon',
            'total_price' => 'Preço Total',
            'seller_notes' => 'Observações do Vendedor',
            'application_image' => 'Imagem da Aplicação',
            
            // Pagamentos
            'method' => 'Método',
            'payment_method' => 'Forma de Pagamento',
            'payment_methods' => 'Formas de Pagamento',
            'amount' => 'Valor',
            'entry_amount' => 'Valor Pago',
            'remaining_amount' => 'Valor Restante',
            'payment_date' => 'Data do Pagamento',
            'status' => 'Status',
            
            // Arquivos
            'file_name' => 'Nome do Arquivo',
            'file_path' => 'Caminho do Arquivo',
            'file_type' => 'Tipo de Arquivo',
        ];

        return $labels[$field] ?? ucfirst(str_replace('_', ' ', $field));
    }

    public function index()
    {
        $editRequests = OrderEditRequest::with(['order.client', 'user', 'approvedBy'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Retornar view apropriada com base no tipo de usuário
        if (Auth::user()->isProducao() && !Auth::user()->isAdmin()) {
            return view('production.edit-requests', compact('editRequests'));
        }

        return view('admin.edit-requests.index', compact('editRequests'));
    }

    public function showChanges(OrderEditRequest $editRequest)
    {
        $differences = null;

        // Se a edição foi completada e temos snapshots, calcular diferenças
        if ($editRequest->status === 'completed' && $editRequest->order_snapshot_before && $editRequest->order_snapshot_after) {
            $differences = $this->calculateDifferences(
                $editRequest->order_snapshot_before,
                $editRequest->order_snapshot_after
            );
        }

        // Renderizar a view com os dados
        $html = view('admin.edit-requests.changes-content', [
            'editRequest' => $editRequest,
            'differences' => $differences
        ])->render();

        return response()->json([
            'success' => true,
            'html' => $html
        ]);
    }
}
