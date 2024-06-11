<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AsaasService;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    protected $asaasService;

    public function __construct(AsaasService $asaasService)
    {
        $this->asaasService = $asaasService;
    }

    public function create()
    {
        return view('payment.create');
    }

    public function store(Request $request)
    {
        Log::info('Requisição recebida', ['data' => $request->all()]);

        // Validação dos dados recebidos
        $validatedData = $request->validate([
            'name' => 'required|string',
            'description' => 'required|string',
            'billingType' => 'required|string|in:BOLETO,CREDIT_CARD,PIX',
            'chargeType' => 'required_if:billingType,CREDIT_CARD|in:DETACHED,RECURRENT,INSTALLMENT',
            'subscriptionCycle' => 'required_if:billingType,CREDIT_CARD,chargeType,RECURRENT|in:WEEKLY,BIWEEKLY,MONTHLY,QUARTERLY,SEMIANNUALLY,YEARLY',
            'maxInstallmentCount' => 'required_if:billingType,CREDIT_CARD,chargeType,INSTALLMENT|integer|in:1,2,3,4,5,6,7,8,9,10,11,12',
            'value' => 'required|numeric|min:0.01|max:500', // Ajuste no valor máximo
            'endDate' => 'required|date',
            'dueDateLimitDays' => 'required|integer|min:1',
        ]);

        // Preparação dos dados para a criação do link de pagamento
        $data = [
            'name' => $validatedData['name'],
            'description' => $validatedData['description'],
            'billingType' => $validatedData['billingType'],
            'chargeType' => $validatedData['chargeType'] ?? 'DETACHED',
            'value' => $validatedData['value'],
            'endDate' => $validatedData['endDate'],
            'dueDateLimitDays' => $validatedData['dueDateLimitDays'],
            'notificationEnabled' => true,
        ];

        // Configuração adicional para cartão de crédito
        if ($validatedData['billingType'] == 'CREDIT_CARD') {
            if ($validatedData['chargeType'] == 'RECURRENT') {
                $data['subscriptionCycle'] = $validatedData['subscriptionCycle'];
            } elseif ($validatedData['chargeType'] == 'INSTALLMENT') {
                $data['maxInstallmentCount'] = $validatedData['maxInstallmentCount'];
            }
        }

        try {
            $response = $this->asaasService->createPaymentLink($data);
            Log::info('Resposta da API Asaas', ['response' => $response]);

            if (isset($response['id'])) {
                $paymentUrl = $response['url'];

                Log::info('Payment link created', [
                    'paymentUrl' => $paymentUrl,
                ]);

                return response()->json([
                    'message' => 'Payment link created successfully!',
                    'paymentType' => $validatedData['billingType'],
                    'paymentUrl' => $paymentUrl,
                    'qrCodeUrl' => $response['qrCodeUrl'] ?? null,
                    'copyPasteCode' => $response['copyPasteCode'] ?? null,
                ]);
            } else {
                Log::error('Erro ao criar link de pagamento', ['response' => $response]);
                return response()->json(['message' => 'Erro ao criar link de pagamento.', 'error' => $response], 500);
            }
        } catch (\Exception $e) {
            // Tratamento de exceções e retorno de erro
            Log::error('Erro ao criar link de pagamento', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'An error occurred while creating the payment link', 'error' => $e->getMessage()], 500);
        }
    }

    public function success(Request $request)
    {
        $paymentUrl = $request->query('paymentUrl');

        Log::info('Success Page', [
            'paymentUrl' => $paymentUrl,
        ]);

        return view('payment.success', compact('paymentUrl'));
    }
}