<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Status;
use App\Models\PersonalizationPrice;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;

class ProductionController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();
        
        // Super Admin (tenant_id === null) não deve ver dados de outros tenants
        if ($user->tenant_id === null) {
            $statuses = Status::orderBy('position')->get();
            return view('production.index', [
                'orders' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 20),
                'statuses' => $statuses,
                'personalizationTypes' => collect([]),
                'stores' => collect([]),
                'search' => null,
                'status' => null,
                'personalizationType' => null,
                'storeId' => null,
                'period' => 'week',
                'startDate' => Carbon::now()->startOfWeek(Carbon::MONDAY)->format('Y-m-d'),
                'endDate' => Carbon::now()->startOfWeek(Carbon::MONDAY)->addDays(4)->format('Y-m-d'),
                'totalOrders' => 0,
                'totalValue' => 0,
                'ordersByStatus' => collect([]),
                'ordersByPersonalization' => collect([]),
                'isSuperAdmin' => true
            ]);
        }

        $search = $request->get('search');
        $status = $request->get('status');
        $personalizationType = $request->get('personalization_type');
        $storeId = $request->get('store_id');
        $deliveryDate = $request->get('delivery_date');
        $entryDate = $request->get('entry_date');
        $period = $request->get('period', 'week'); // Default: semana (segunda a sexta)
        $rangeStart = $request->get('start_date');
        $rangeEnd = $request->get('end_date');

        if ($rangeStart && !$rangeEnd) {
            $rangeEnd = $rangeStart;
        }
        if ($rangeEnd && !$rangeStart) {
            $rangeStart = $rangeEnd;
        }
        $hasRange = !empty($rangeStart) && !empty($rangeEnd);
        $viewType = $request->get('type', 'production'); // 'production' or 'personalized'

        $tenant = $user->tenant;
        if ($tenant) {
            if ($viewType === 'personalized' && !$tenant->canAccess('personalized')) {
                abort(403, 'Seu plano não inclui o módulo de Personalizados.');
            }
            if ($viewType !== 'personalized' && !$tenant->canAccess('production')) {
                abort(403, 'Seu plano não inclui o módulo de Produção.');
            }
        }
        
        // Para períodos predefinidos, sempre recalcular as datas
        // Só usar start_date/end_date do request quando for "custom"
        if ($hasRange) {
            $period = 'custom';
            $startDate = $rangeStart;
            $endDate = $rangeEnd;
        } elseif ($period === 'custom') {
            $startDate = $request->get('start_date');
            $endDate = $request->get('end_date');
        } else {
            $startDate = null;
            $endDate = null;
        }
        
        // Definir datas baseadas no período selecionado
        if (!$startDate || !$endDate) {
            $now = Carbon::now();
            switch ($period) {
                case 'all':
                    // Todo o período: não define datas
                    $startDate = null;
                    $endDate = null;
                    break;
                case 'week':
                    // Segunda a Sexta da semana útil (se for fim de semana, pega a próxima)
                    $dayOfWeek = $now->dayOfWeek; // 0 = Domingo, 6 = Sábado
                    
                    if ($dayOfWeek == Carbon::SATURDAY || $dayOfWeek == Carbon::SUNDAY) {
                        // Se for sábado ou domingo, pega a próxima segunda
                        $monday = $now->copy()->next(Carbon::MONDAY);
                    } else {
                        // Se for dia útil, pega a segunda desta semana
                        $monday = $now->copy()->startOfWeek(Carbon::MONDAY);
                    }
                    
                    $startDate = $monday->format('Y-m-d');
                    $endDate = $monday->copy()->addDays(4)->format('Y-m-d'); // +4 dias = sexta
                    break;
                case 'month':
                    $startDate = $now->copy()->startOfMonth()->format('Y-m-d');
                    $endDate = $now->copy()->endOfMonth()->format('Y-m-d');
                    break;
                case 'day':
                default:
                    $startDate = $now->format('Y-m-d');
                    $endDate = $now->format('Y-m-d');
                    break;
            }
        }

        // Mostrar todos os pedidos em produção (não rascunhos e não cancelados)
        $query = Order::with(['client', 'status', 'items', 'store'])
            ->where('is_draft', false)
            ->where('is_pdv', false)
            ->where('is_cancelled', false);

        // Filter by origin based on view type
        if ($viewType === 'personalized') {
            $query->where('origin', 'personalized');
        } else {
            $query->where(function($q) {
                $q->where('origin', '!=', 'personalized')
                  ->orWhereNull('origin');
            });
        }

        // Se for vendedor, mostrar apenas os pedidos que ele criou
        if (Auth::user()->isVendedor()) {
            $query->where('user_id', Auth::id());
        }
        
        // Aplicar filtros de período
        if ($period === 'all') {
            // Todo o período: não aplica filtro de data
        } elseif (in_array($period, ['week', 'month'])) {
            // Para semana e mês: filtrar por DATA DE ENTREGA
            $query->whereNotNull('delivery_date')
                  ->whereBetween('delivery_date', [$startDate, $endDate]);
        } elseif ($period === 'custom') {
            // Para período personalizado: filtrar por data de entrega
            $query->whereNotNull('delivery_date')
                  ->whereBetween('delivery_date', [$startDate, $endDate]);
        } else {
            // Para "hoje": mostrar todos os pedidos ativos sem filtro
            // Isso permite visualizar todo o pipeline de produção
        }

        // Busca por número do pedido, nome do cliente ou nome da arte
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhereHas('client', function($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%")
                         ->orWhere('phone_primary', 'like', "%{$search}%");
                  })
                  ->orWhereHas('items', function($q3) use ($search) {
                      $q3->where('art_name', 'like', "%{$search}%");
                  });
            });
        }

        // Filtro por status
        if ($status) {
            $query->where('status_id', $status);
        }

        // Filtro por tipo de personalização
        if ($personalizationType) {
            $query->whereHas('items', function($q) use ($personalizationType) {
                $q->where('print_type', $personalizationType);
            });
        }

        // Filtro por loja
        if ($storeId) {
            $query->where('store_id', $storeId);
        }

        // Determination active Tenant for statuses
        // Using user's tenant or selected tenant
         $activeTenantId = $user->tenant_id;
        if ($activeTenantId === null) {
            $activeTenantId = session('selected_tenant_id');
        }
        if ($activeTenantId === null) {
            $firstStore = \App\Models\Store::first();
            $activeTenantId = $firstStore ? $firstStore->tenant_id : 1;
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(20);
        $statuses = Status::where('tenant_id', $activeTenantId)
            ->where('type', $viewType)
            ->orderBy('position')->get();
            
        $personalizationTypes = PersonalizationPrice::getPersonalizationTypes();
        $stores = Store::active()->orderBy('name')->get();

        // Estatísticas
        $totalOrders = $orders->total();
        $totalValue = $orders->sum('total');
        $ordersByStatus = $orders->groupBy('status_id');
        $ordersByPersonalization = $orders->groupBy(function($order) {
            return $order->items->first()->print_type ?? 'N/A';
        });

        $viewName = $viewType === 'personalized' ? 'production.list_personalized' : 'production.index';

        return view($viewName, compact(
            'orders', 
            'statuses', 
            'personalizationTypes',
            'stores',
            'search', 
            'status', 
            'personalizationType',
            'storeId',
            'period',
            'startDate', 
            'endDate',
            'totalOrders',
            'totalValue',
            'ordersByStatus',
            'ordersByPersonalization',
            'viewType'
        ));
    }

    public function kanban(Request $request): View
    {
        $search = $request->get('search');
        $personalizationType = $request->get('personalization_type');
        $period = $request->get('period', 'week'); // Default: semana (segunda a sexta)
        
        // Para períodos predefinidos, sempre recalcular as datas
        // Só usar start_date/end_date do request quando for "custom"
        if ($period === 'custom') {
            $startDate = $request->get('start_date');
            $endDate = $request->get('end_date');
        } else {
            $startDate = null;
            $endDate = null;
        }
        
        // Definir datas baseadas no período selecionado
        if (!$startDate || !$endDate) {
            $now = Carbon::now();
            switch ($period) {
                case 'all':
                    // Todo o período: não define datas
                    $startDate = null;
                    $endDate = null;
                    break;
                case 'week':
                    // Segunda a Sexta da semana útil (se for fim de semana, pega a próxima)
                    $dayOfWeek = $now->dayOfWeek; // 0 = Domingo, 6 = Sábado
                    
                    if ($dayOfWeek == Carbon::SATURDAY || $dayOfWeek == Carbon::SUNDAY) {
                        // Se for sábado ou domingo, pega a próxima segunda
                        $monday = $now->copy()->next(Carbon::MONDAY);
                    } else {
                        // Se for dia útil, pega a segunda desta semana
                        $monday = $now->copy()->startOfWeek(Carbon::MONDAY);
                    }
                    
                    $startDate = $monday->format('Y-m-d');
                    $endDate = $monday->copy()->addDays(4)->format('Y-m-d'); // +4 dias = sexta
                    break;
                case 'month':
                    $startDate = $now->copy()->startOfMonth()->format('Y-m-d');
                    $endDate = $now->copy()->endOfMonth()->format('Y-m-d');
                    break;
                case 'day':
                default:
                    $startDate = $now->format('Y-m-d');
                    $endDate = $now->format('Y-m-d');
                    break;
            }
        }

        $statuses = Status::withCount('orders')->orderBy('position')->get();
        
        // Mostrar todos os pedidos em produção (não rascunhos e não cancelados)
        $query = Order::with(['client', 'items', 'items.files'])
            ->withCount('comments')
            ->where('is_draft', false)
            ->where('is_pdv', false)
            ->where('is_cancelled', false);

        // Se for vendedor, mostrar apenas os pedidos que ele criou
        if (Auth::user()->isVendedor()) {
            $query->where('user_id', Auth::id());
        }
        
        // Aplicar filtros de período
        if ($period === 'all') {
            // Todo o período: não aplica filtro de data
        } elseif (in_array($period, ['week', 'month'])) {
            // Para semana e mês: filtrar por DATA DE ENTREGA
            $query->whereNotNull('delivery_date')
                  ->whereBetween('delivery_date', [$startDate, $endDate]);
        } elseif ($period === 'custom') {
            // Para período personalizado: filtrar por data de entrega
            $query->whereNotNull('delivery_date')
                  ->whereBetween('delivery_date', [$startDate, $endDate]);
        } else {
            // Para "hoje": mostrar todos os pedidos ativos sem filtro
            // Isso permite visualizar todo o pipeline de produção
        }
        
        // Aplicar busca se fornecida
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhereHas('client', function($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%")
                         ->orWhere('phone_primary', 'like', "%{$search}%");
                  })
                  ->orWhereHas('items', function($q3) use ($search) {
                      $q3->where('art_name', 'like', "%{$search}%");
                  });
            });
        }

        // Filtro por tipo de personalização
        if ($personalizationType) {
            $query->whereHas('items', function($q) use ($personalizationType) {
                $q->where('print_type', $personalizationType);
            });
        }
        
        $ordersByStatus = $query->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('status_id');

        $personalizationTypes = PersonalizationPrice::getPersonalizationTypes();

        return view('production.kanban', compact(
            'statuses', 
            'ordersByStatus', 
            'search', 
            'personalizationType',
            'period',
            'startDate',
            'endDate',
            'personalizationTypes'
        ));
    }

    public function downloadPdf(Request $request)
    {
        $search = $request->get('search');
        $status = $request->get('status');
        $personalizationType = $request->get('personalization_type');
        $storeId = $request->get('store_id');
        $deliveryDate = $request->get('delivery_date');
        $entryDate = $request->get('entry_date');
        $period = $request->get('period', 'week'); // Default: semana (segunda a sexta)
        $rangeStart = $request->get('start_date');
        $rangeEnd = $request->get('end_date');

        if ($rangeStart && !$rangeEnd) {
            $rangeEnd = $rangeStart;
        }
        if ($rangeEnd && !$rangeStart) {
            $rangeStart = $rangeEnd;
        }
        $hasRange = !empty($rangeStart) && !empty($rangeEnd);
        
        // Para períodos predefinidos, sempre recalcular as datas
        // Só usar start_date/end_date do request quando for "custom"
        if ($hasRange) {
            $period = 'custom';
            $startDate = $rangeStart;
            $endDate = $rangeEnd;
        } elseif ($period === 'custom') {
            $startDate = $request->get('start_date');
            $endDate = $request->get('end_date');
        } else {
            $startDate = null;
            $endDate = null;
        }
        
        // Definir datas baseadas no período selecionado
        if (!$startDate || !$endDate) {
            $now = Carbon::now();
            switch ($period) {
                case 'all':
                    // Todo o período: não define datas
                    $startDate = null;
                    $endDate = null;
                    break;
                case 'week':
                    // Segunda a Sexta da semana útil (se for fim de semana, pega a próxima)
                    $dayOfWeek = $now->dayOfWeek; // 0 = Domingo, 6 = Sábado
                    
                    if ($dayOfWeek == Carbon::SATURDAY || $dayOfWeek == Carbon::SUNDAY) {
                        // Se for sábado ou domingo, pega a próxima segunda
                        $monday = $now->copy()->next(Carbon::MONDAY);
                    } else {
                        // Se for dia útil, pega a segunda desta semana
                        $monday = $now->copy()->startOfWeek(Carbon::MONDAY);
                    }
                    
                    $startDate = $monday->format('Y-m-d');
                    $endDate = $monday->copy()->addDays(4)->format('Y-m-d'); // +4 dias = sexta
                    break;
                case 'month':
                    $startDate = $now->copy()->startOfMonth()->format('Y-m-d');
                    $endDate = $now->copy()->endOfMonth()->format('Y-m-d');
                    break;
                case 'day':
                default:
                    $startDate = $now->format('Y-m-d');
                    $endDate = $now->format('Y-m-d');
                    break;
            }
        }

        // Mostrar todos os pedidos em produção
        $query = Order::with(['client', 'status', 'items', 'store'])
            ->where('is_draft', false)
            ->where('is_pdv', false)
            ->where('is_cancelled', false);

        if (Auth::user()->isVendedor()) {
            $query->where('user_id', Auth::id());
        }
        
        // Aplicar filtros de período por DATA DE ENTREGA
        if ($period === 'all') {
            // Todo o período: não aplica filtro de data
        } elseif (!empty($startDate) && !empty($endDate)) {
            $query->whereNotNull('delivery_date')
                  ->whereBetween('delivery_date', [$startDate, $endDate]);
        } elseif ($deliveryDate) {
            $query->whereDate('delivery_date', $deliveryDate);
        }

        if ($entryDate) {
            $query->where(function($q) use ($entryDate) {
                $q->whereDate('entry_date', $entryDate)
                  ->orWhere(function($q2) use ($entryDate) {
                      $q2->whereNull('entry_date')->whereDate('created_at', $entryDate);
                  });
            });
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhereHas('client', function($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%")
                         ->orWhere('phone_primary', 'like', "%{$search}%");
                  })
                  ->orWhereHas('items', function($q3) use ($search) {
                      $q3->where('art_name', 'like', "%{$search}%");
                  });
            });
        }

        if ($status) {
            $query->where('status_id', $status);
        }

        if ($personalizationType) {
            $query->whereHas('items', function($q) use ($personalizationType) {
                $q->where('print_type', $personalizationType);
            });
        }

        // Filtro por loja
        if ($storeId) {
            $query->where('store_id', $storeId);
        }

        $orders = $query->orderBy('delivery_date', 'asc')
                       ->orderBy('created_at', 'desc')
                       ->get();

        // Buscar loja selecionada para exibir no PDF (já carregada via relacionamento se disponível)
        $selectedStore = $storeId ? Store::find($storeId) : null;

        // Gerar PDF
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', true);
        $options->set('defaultFont', 'Arial');
        
        $dompdf = new Dompdf($options);
        
        $html = view('production.pdf', [
            'orders' => $orders,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'period' => $period,
            'selectedStore' => $selectedStore
        ])->render();
        
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        
        $filename = 'lista_producao_' . $startDate . '_a_' . $endDate . '.pdf';
        
        return $dompdf->stream($filename);
    }
}
