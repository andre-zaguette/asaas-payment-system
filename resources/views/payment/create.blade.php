@extends('layouts.main')

@section('content')
<div class="container mt-5">
    <h2 class="mb-4">Criar Link de Pagamento</h2>

    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form id="payment-form" action="{{ route('payment.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="name">Nome:</label>
            <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
        </div>

        <div class="form-group">
            <label for="description">Descrição do produto:</label>
            <input type="text" class="form-control" id="description" name="description" value="{{ old('description') }}"
                required>
        </div>

        <div class="form-group">
            <label for="value">Valor do pagamento (R$):</label>
            <input type="text" class="form-control" id="value" name="value" value="{{ old('value') }}" required>
        </div>

        <div class="form-group">
            <label for="endDate">Data de Vencimento:</label>
            <input type="date" class="form-control" id="endDate" name="endDate" value="{{ old('endDate') }}" required>
        </div>

        <div class="form-group">
            <label for="billingType">Método de pagamento:</label>
            <select class="form-control" id="billingType" name="billingType" required>
                @foreach(['BOLETO' => 'Boleto', 'CREDIT_CARD' => 'Cartão de Crédito', 'PIX' => 'PIX'] as $type =>
                $label)
                <option value="{{ $type }}" {{ old('billingType') == $type ? 'selected' : '' }}>
                    {{ $label }}
                </option>
                @endforeach
            </select>
        </div>

        <div id="chargeTypeDiv" class="form-group hidden">
            <label for="chargeType">Tipo de pagamento:</label>
            <select class="form-control" id="chargeType" name="chargeType">
                @foreach(['DETACHED' => 'À vista', 'RECURRENT' => 'Recorrente', 'INSTALLMENT' => 'Parcelado'] as $type
                => $label)
                <option value="{{ $type }}" {{ old('chargeType') == $type ? 'selected' : '' }}>
                    {{ $label }}
                </option>
                @endforeach
            </select>
        </div>

        <div id="subscriptionCycleDiv" class="form-group hidden">
            <label for="subscriptionCycle">Ciclo de assinatura:</label>
            <select class="form-control" id="subscriptionCycle" name="subscriptionCycle">
                @foreach(['WEEKLY' => 'Semanal', 'BIWEEKLY' => 'Quinzenal', 'MONTHLY' => 'Mensal', 'QUARTERLY' =>
                'Trimestral', 'SEMIANNUALLY' => 'Semestral', 'YEARLY' => 'Anual'] as $cycle => $label)
                <option value="{{ $cycle }}" {{ old('subscriptionCycle') == $cycle ? 'selected' : '' }}>
                    {{ $label }}
                </option>
                @endforeach
            </select>
        </div>

        <div id="maxInstallmentCountDiv" class="form-group hidden">
            <label for="maxInstallmentCount">Número máximo de parcelas:</label>
            <select class="form-control" id="maxInstallmentCount" name="maxInstallmentCount">
                @for ($i = 1; $i <= 12; $i++) <option value="{{ $i }}"
                    {{ old('maxInstallmentCount') == $i ? 'selected' : '' }}>{{ $i }}</option>
                    @endfor
            </select>
        </div>

        <div class="form-group">
            <label for="dueDateLimitDays">Dias para Vencimento:</label>
            <input type="number" class="form-control" id="dueDateLimitDays" name="dueDateLimitDays"
                value="{{ old('dueDateLimitDays', 3) }}" min="1">
        </div>

        <button type="submit" id="submit-button" class="btn btn-primary">Criar Link de Pagamento</button>
    </form>
</div>
@endsection