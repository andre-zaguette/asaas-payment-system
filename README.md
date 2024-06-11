## Sistema de Pagamento Asaas

Este projeto oferece uma integração completa com o gateway de pagamento Asaas, permitindo criar facilmente links de pagamento para transações com Boleto, PIX e Cartão de Crédito.

## Instalação

clone o repositorio

```bash
git clone https://github.com/orochidrake/asaas-payment-system
cd asaas-payment-system
```

### Instale as dependências:

```bash
composer install
```

### Configuração

Copie o arquivo de ambiente:

```bash
cp .env.example .env
```

## Variáveis de Ambiente

Para rodar esse projeto, você vai precisar adicionar as seguintes variáveis de ambiente no seu .env

### Edite o arquivo .env:

`ASAAS_API_KEY`
Forneça sua chave de API Asaas ().

Defina suas credenciais do banco de dados (DB_CONNECTION, DB_HOST, etc.).

### Execute as migrações do banco de dados:

```bash
php artisan migrate
```

### Inicie o servidor:

```bash
php artisan serve
```

### Executando Testes

Execute os testes unitários e de integração:

```bash
php artisan test
```

### Usando a API

Criando um Link de Pagamento
Endpoint: POST /payment
Payload: Veja os exemplos abaixo para Boleto, PIX e Cartão de Crédito.
Exemplos de Requisições:

```bash
# Boleto
curl -X POST http://localhost:8000/payment \
-H "Content-Type: application/json" \
-d '{
"name": "Usuário de Teste",
"description": "Descrição de Teste",
"billingType": "BOLETO",
"value": 100.00,
"endDate": "2024-06-12",
"dueDateLimitDays": 3
}'

# PIX
# (similar ao Boleto, mas com "billingType": "PIX")

# Cartão de Crédito
curl -X POST http://localhost:8000/payment \
-H "Content-Type: application/json" \
-d '{
"name": "Usuário de Teste",
"description": "Descrição de Teste",
"billingType": "CREDIT_CARD",
"chargeType": "DETACHED",
"value": 100.00,
"endDate": "2024-06-12",
"dueDateLimitDays": 3,
"subscriptionCycle": "MONTHLY",  // Necessário para RECURRENT
"maxInstallmentCount": 1         // Necessário para INSTALLMENT
}'

```

### Resposta de Sucesso:

```bash
JSON
{
"message": "Link de pagamento criado com sucesso!",
"paymentType": "BOLETO",
"paymentUrl": "[https://sandbox.asaas.com/c/6618sm3bs4mv392i](https://sandbox.asaas.com/c/6618sm3bs4mv392i)",
"qrCodeUrl": null, // Apenas para PIX
"copyPasteCode": null // Apenas para PIX
}
```

Rota de Sucesso
Após criar um link de pagamento, você será redirecionado para:

Endpoint: GET /payment/success

Parâmetro: paymentUrl (o link gerado)

### Estrutura do Projeto

Controllers: Lida com a lógica das requisições.

Services: Integra com serviços externos (Asaas).

Routes: Define as rotas da aplicação.

Tests: Contém testes unitários e de integração.

## Licença

Este projeto está licenciado sob a MIT License.
[MIT](https://choosealicense.com/licenses/mit/)
