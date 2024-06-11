document.addEventListener('DOMContentLoaded', () => {
    const billingTypeSelect = document.getElementById('billingType');
    const chargeTypeSelect = document.getElementById('chargeType');
    const chargeTypeDiv = document.getElementById('chargeTypeDiv');
    const subscriptionCycleDiv = document.getElementById('subscriptionCycleDiv');
    const maxInstallmentCountDiv = document.getElementById('maxInstallmentCountDiv');
    const valueInput = document.getElementById('value');
    const submitButton = document.getElementById('submit-button');

    // Máscara monetária (opcional - se estiver usando Cleave.js)
    const cleave = new Cleave('#value', {
        numeral: true,
        numeralThousandsGroupStyle: 'thousand',
        numeralDecimalMark: ',',
        delimiter: '.'
    });

    // Define a data de vencimento para amanhã
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    document.getElementById('endDate').value = tomorrow.toISOString().split('T')[0];

    // Atualiza a visibilidade dos campos com base no tipo de pagamento
    function updateFieldsVisibility() {
        const isCreditCard = billingTypeSelect.value === 'CREDIT_CARD';
        if (isCreditCard) {
            chargeTypeDiv.classList.remove('hidden');
            const chargeType = chargeTypeSelect.value;
            if (chargeType === 'RECURRENT') {
                subscriptionCycleDiv.classList.remove('hidden');
                maxInstallmentCountDiv.classList.add('hidden');
            } else if (chargeType === 'INSTALLMENT') {
                maxInstallmentCountDiv.classList.remove('hidden');
                subscriptionCycleDiv.classList.add('hidden');
            } else {
                subscriptionCycleDiv.classList.add('hidden');
                maxInstallmentCountDiv.classList.add('hidden');
            }
        } else {
            chargeTypeDiv.classList.add('hidden');
            subscriptionCycleDiv.classList.add('hidden');
            maxInstallmentCountDiv.classList.add('hidden');
        }
    }

    billingTypeSelect.addEventListener('change', updateFieldsVisibility);
    chargeTypeSelect.addEventListener('change', updateFieldsVisibility);

    // Inicializa a visibilidade dos campos na carga inicial da página
    updateFieldsVisibility();

    // Manipula o envio do formulário
    submitButton.addEventListener('click', (event) => {
        event.preventDefault();

        // Remove a máscara se estiver usando Cleave.js
        if (cleave) {
            valueInput.value = cleave.getRawValue().replace(',', '.');
        }

        const formData = new FormData(event.target.form);
        const data = Object.fromEntries(formData.entries());

        console.log('Dados do formulário:', data); // Log dos dados do formulário

        fetch(event.target.form.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        })
            .then(response => {
                console.log('Status da resposta:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('Resposta do servidor:', data); // Log da resposta do servidor
                if (data.message === 'Payment link created successfully!') {
                    window.location.href = `/payment/success?paymentUrl=${data.paymentUrl}`;
                } else {
                    // Tratar erro na criação do link de pagamento
                    const errorContainer = document.createElement('div');
                    errorContainer.classList.add('alert', 'alert-danger');
                    errorContainer.textContent = 'Erro ao criar o link de pagamento';
                    document.querySelector('#payment-form').prepend(errorContainer);
                }
            })
            .catch(error => {
                console.error('Erro ao enviar o formulário:', error);
                alert('Ocorreu um erro ao enviar o formulário. Por favor, tente novamente.');
            });
    });
});
