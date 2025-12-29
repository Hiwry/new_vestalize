<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Mail\VipListConfirmed;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class LeadController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:leads,email',
            'phone' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $lead = Lead::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'status' => 'new'
            ]);

            // Send confirmation email to the lead
            Mail::to($lead->email)->send(new VipListConfirmed($lead));

            // Optional: Notify Admin (log for now, or send another mail)
            Log::info("Novo Lead VIP cadastrado: {$lead->email}");

            return response()->json([
                'success' => true,
                'message' => 'Parabéns! Você está na lista VIP.'
            ]);

        } catch (\Exception $e) {
            Log::error("Erro ao salvar lead: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro ao processar cadastro via sistema.'
            ], 500);
        }
    }
}
