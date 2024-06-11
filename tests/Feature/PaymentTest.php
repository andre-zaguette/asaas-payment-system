<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Services\AsaasService;
use Mockery;

class PaymentTest extends TestCase
{
    use RefreshDatabase;

    protected $asaasServiceMock;

    public function setUp(): void
    {
        parent::setUp();

        // Mocking AsaasService
        $this->asaasServiceMock = Mockery::mock(AsaasService::class);
        $this->app->instance(AsaasService::class, $this->asaasServiceMock);

        // Desabilitar CSRF durante os testes
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
    }

    public function testBoletoPaymentCreation()
    {
        $this->asaasServiceMock
            ->shouldReceive('createPaymentLink')
            ->once()
            ->andReturn([
                'id' => 'some-id',
                'url' => 'https://sandbox.asaas.com/c/6618sm3bs4mv392i',
            ]);

        $response = $this->post('/payment', [
            'name' => 'Test User',
            'description' => 'Test Description',
            'billingType' => 'BOLETO',
            'value' => 100.00,
            'endDate' => '2024-06-12',
            'dueDateLimitDays' => 3,
        ]);

        $response->assertStatus(200) // Status 200 significa sucesso na criação do link de pagamento
                 ->assertJson([
                     'message' => 'Payment link created successfully!',
                     'paymentType' => 'BOLETO',
                     'paymentUrl' => 'https://sandbox.asaas.com/c/6618sm3bs4mv392i',
                 ]);
    }

    public function testPixPaymentCreation()
    {
        $this->asaasServiceMock
            ->shouldReceive('createPaymentLink')
            ->once()
            ->andReturn([
                'id' => 'some-id',
                'url' => 'https://sandbox.asaas.com/c/6618sm3bs4mv392i',
                'qrCodeUrl' => 'https://sandbox.asaas.com/qr/6618sm3bs4mv392i',
                'copyPasteCode' => '12345678901234567890',
            ]);

        $response = $this->post('/payment', [
            'name' => 'Test User',
            'description' => 'Test Description',
            'billingType' => 'PIX',
            'value' => 100.00,
            'endDate' => '2024-06-12',
            'dueDateLimitDays' => 3,
        ]);

        $response->assertStatus(200) // Status 200 significa sucesso na criação do link de pagamento
                 ->assertJson([
                     'message' => 'Payment link created successfully!',
                     'paymentType' => 'PIX',
                     'paymentUrl' => 'https://sandbox.asaas.com/c/6618sm3bs4mv392i',
                     'qrCodeUrl' => 'https://sandbox.asaas.com/qr/6618sm3bs4mv392i',
                     'copyPasteCode' => '12345678901234567890',
                 ]);
    }

    public function testCreditCardPaymentCreation()
    {
        $this->asaasServiceMock
            ->shouldReceive('createPaymentLink')
            ->once()
            ->andReturn([
                'id' => 'some-id',
                'url' => 'https://sandbox.asaas.com/c/6618sm3bs4mv392i',
            ]);

        $response = $this->post('/payment', [
            'name' => 'Test User',
            'description' => 'Test Description',
            'billingType' => 'CREDIT_CARD',
            'chargeType' => 'DETACHED',
            'value' => 100.00,
            'endDate' => '2024-06-12',
            'dueDateLimitDays' => 3,
            'subscriptionCycle' => 'MONTHLY', // Adicionado para resolver a falha
            'maxInstallmentCount' => 1, // Adicionado para resolver a falha
        ]);

        $response->assertStatus(200) // Status 200 significa sucesso na criação do link de pagamento
                 ->assertJson([
                     'message' => 'Payment link created successfully!',
                     'paymentType' => 'CREDIT_CARD',
                     'paymentUrl' => 'https://sandbox.asaas.com/c/6618sm3bs4mv392i',
                 ]);
    }

    public function testCreditCardInstallmentPaymentCreation()
    {
        $this->asaasServiceMock
            ->shouldReceive('createPaymentLink')
            ->once()
            ->andReturn([
                'id' => 'some-id',
                'url' => 'https://sandbox.asaas.com/c/6618sm3bs4mv392i',
            ]);

        $response = $this->post('/payment', [
            'name' => 'Test User',
            'description' => 'Test Description',
            'billingType' => 'CREDIT_CARD',
            'chargeType' => 'INSTALLMENT',
            'maxInstallmentCount' => 3,
            'value' => 100.00,
            'endDate' => '2024-06-12',
            'dueDateLimitDays' => 3,
            'subscriptionCycle' => 'MONTHLY', // Adicionado para resolver a falha
        ]);

        $response->assertStatus(200) // Status 200 significa sucesso na criação do link de pagamento
                 ->assertJson([
                     'message' => 'Payment link created successfully!',
                     'paymentType' => 'CREDIT_CARD',
                     'paymentUrl' => 'https://sandbox.asaas.com/c/6618sm3bs4mv392i',
                 ]);
    }

    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}