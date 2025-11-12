document.addEventListener('DOMContentLoaded', function() {
    // Auto-focus no campo de ID na seleção de conta
    const contaIdInput = document.querySelector('input[name="conta_id"]');
    if (contaIdInput) {
        contaIdInput.focus();
    }

    // Validação de formulários
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const inputs = this.querySelectorAll('input[required]');
            let valid = true;

            inputs.forEach(input => {
                if (!input.value.trim()) {
                    valid = false;
                    input.style.borderColor = '#e74c3c';
                } else {
                    input.style.borderColor = '';
                }
            });

            if (!valid) {
                e.preventDefault();
                alert('Por favor, preencha todos os campos obrigatórios.');
            }
        });
    });

    // Auto-remove notificações após 5 segundos
    const notifications = document.querySelectorAll('.notification');
    notifications.forEach(notification => {
        setTimeout(() => {
            notification.style.opacity = '0';
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 600);
        }, 5000);
    });

    // Formatação de valores monetários
    const moneyInputs = document.querySelectorAll('input[type="number"][step="0.01"]');
    moneyInputs.forEach(input => {
        input.addEventListener('blur', function() {
            if (this.value) {
                this.value = parseFloat(this.value).toFixed(2);
            }
        });
    });
});

function selecionarConta(element) {
    const email = element.getAttribute('data-email');
    if (email) {
        document.getElementById('conta_email').value = email;
        document.getElementById('conta_senha').focus();
        
        // Rolagem suave até o formulário de login
        document.querySelector('form').scrollIntoView({ 
            behavior: 'smooth',
            block: 'center'
        });
        
        // Destaque visual no campo de email
        const emailField = document.getElementById('conta_email');
        emailField.style.borderColor = '#1abc9c';
        emailField.style.boxShadow = '0 0 0 3px rgba(26, 188, 156, 0.2)';
        
        setTimeout(() => {
            emailField.style.borderColor = '';
            emailField.style.boxShadow = '';
        }, 2000);
    }
}