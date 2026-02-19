<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Client;
use Illuminate\Http\Request;
use App\Helpers\StoreHelper;

class SearchController extends Controller
{
    public function orders(Request $request)
    {
        $query = $request->get('q');
        if (!$query) return response()->json([]);

        $orders = Order::where(function($q) use ($query) {
                $q->where('id', 'like', "%{$query}%")
                  ->orWhere('art_name', 'like', "%{$query}%");
            })
            ->limit(5)
            ->get();

        $results = $orders->map(function($order) {
            return [
                'id' => $order->id,
                'title' => "Pedido #{$order->id} - {$order->art_name}",
                'url' => route('orders.show', $order->id),
                'type' => 'order'
            ];
        });

        return response()->json($results);
    }

    public function clients(Request $request)
    {
        $query = $request->get('q');
        if (!$query) return response()->json([]);

        $clients = Client::where(function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('email', 'like', "%{$query}%")
                  ->orWhere('phone_primary', 'like', "%{$query}%");
            })
            ->limit(5)
            ->get();

        $results = $clients->map(function($client) {
            return [
                'id' => $client->id,
                'title' => "Cliente: {$client->name}",
                'url' => route('clients.show', $client->id),
                'type' => 'client'
            ];
        });

        return response()->json($results);
    }
}
