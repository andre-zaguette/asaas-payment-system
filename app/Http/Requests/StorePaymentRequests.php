<?php


namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentRequest extends FormRequest
{
    public function rules()
    {
        return [
            'customer' => 'required|array',
            'customer.name' => 'required|string',
            'customer.email' => 'required|email',
            'billingType' => 'required|in:BOLETO,CREDIT_CARD,PIX',
            'dueDate' => 'required|date',
            'value' => 'required|numeric|min:0',
        ];
    }
}