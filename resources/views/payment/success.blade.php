@extends('layouts.main')

@section('content')
<div class="container">
    @if(isset($paymentUrl))
    <h2>Pagamento</h2>
    <p>Seu pagamento foi gerado com sucesso!</p>
    <a href="{{ $paymentUrl }}" target="_blank" class="btn btn-primary">Acessar Link de Pagamento</a>
    @else
    <h2>Erro</h2>
    <p>Ocorreu um erro ao processar seu pagamento. Por favor, tente novamente.</p>
    @endif
</div>
@endsection
