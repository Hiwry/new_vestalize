<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Mail\VipListConfirmed;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class LeadController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:leads,email',
            'phone' => 'nullable|string|max:20',
            'g-recaptcha-response' => 'required',
        ], [
            'g-recaptcha-response.required' => 'Por favor, confirme que você não é um robô.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Validate reCAPTCHA
        $recaptchaResponse = $request->input('g-recaptcha-response');
        $recaptchaSecret = config('services.recaptcha.secret_key');
        
        if ($recaptchaSecret && $recaptchaSecret !== 'YOUR_SECRET_KEY') {
            $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => $recaptchaSecret,
                'response' => $recaptchaResponse,
                'remoteip' => $request->ip(),
            ]);

            $recaptchaResult = $response->json();
            
            if (!($recaptchaResult['success'] ?? false)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Verificação de segurança falhou. Tente novamente.'
                ], 422);
            }
        }

        try {
            $lead = Lead::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'status' => 'new'
            ]);

            // Send confirmation email to the lead
            try {
                Mail::to($lead->email)->send(new VipListConfirmed($lead));
            } catch (\Exception $e) {
                Log::error("Erro ao enviar email VIP: " . $e->getMessage());
            }

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
