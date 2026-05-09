// validation.js - Validations JavaScript pour Green Assurance
document.addEventListener('DOMContentLoaded', function() {
    // Animation d'apparition des éléments
    const cards = document.querySelectorAll('.card, .stat-card, .service-card');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        setTimeout(() => {
            card.style.transition = 'all 0.6s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
    
    // Validation du formulaire d'inscription
    const registerForm = document.getElementById('registerForm');
    if (registerForm) {
        registerForm.addEventListener('submit', function(e) {
            let isValid = true;
            const errors = {};
            
            const nom = document.getElementById('nom');
            if (nom && nom.value.trim().length < 2) {
                errors.nom = 'Le nom doit contenir au moins 2 caractères';
                isValid = false;
                showErrorAnimation(nom);
            }
            
            const prenom = document.getElementById('prenom');
            if (prenom && prenom.value.trim().length < 2) {
                errors.prenom = 'Le prénom doit contenir au moins 2 caractères';
                isValid = false;
                showErrorAnimation(prenom);
            }
            
            const email = document.getElementById('email');
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (email && !emailRegex.test(email.value)) {
                errors.email = 'Email invalide';
                isValid = false;
                showErrorAnimation(email);
            }
            
            const phone = document.getElementById('phone');
            const phoneRegex = /^[0-9+\-\s]{8,15}$/;
            if (phone && phone.value && !phoneRegex.test(phone.value)) {
                errors.phone = 'Format téléphone invalide (ex: 0612345678)';
                isValid = false;
                showErrorAnimation(phone);
            }
            
            const password = document.getElementById('password');
            if (password) {
                if (password.value.length < 8) {
                    errors.password = 'Le mot de passe doit contenir au moins 8 caractères';
                    isValid = false;
                    showErrorAnimation(password);
                } else if (!/[A-Z]/.test(password.value)) {
                    errors.password = 'Le mot de passe doit contenir au moins une majuscule';
                    isValid = false;
                    showErrorAnimation(password);
                } else if (!/[0-9]/.test(password.value)) {
                    errors.password = 'Le mot de passe doit contenir au moins un chiffre';
                    isValid = false;
                    showErrorAnimation(password);
                }
            }
            
            const confirm = document.getElementById('confirm_password');
            if (confirm && password && password.value !== confirm.value) {
                errors.confirm_password = 'Les mots de passe ne correspondent pas';
                isValid = false;
                showErrorAnimation(confirm);
            }
            
            if (!isValid) {
                e.preventDefault();
                displayErrors(errors);
            }
        });
    }
    
    // Validation du formulaire utilisateur (backoffice)
    const userForm = document.getElementById('userForm');
    if (userForm) {
        userForm.addEventListener('submit', function(e) {
            let isValid = true;
            const errors = {};
            
            const nom = document.getElementById('nom');
            if (nom && nom.value.trim().length < 2) {
                errors.nom = 'Le nom doit contenir au moins 2 caractères';
                isValid = false;
                showErrorAnimation(nom);
            }
            
            const prenom = document.getElementById('prenom');
            if (prenom && prenom.value.trim().length < 2) {
                errors.prenom = 'Le prénom doit contenir au moins 2 caractères';
                isValid = false;
                showErrorAnimation(prenom);
            }
            
            const email = document.getElementById('email');
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (email && !emailRegex.test(email.value)) {
                errors.email = 'Email invalide';
                isValid = false;
                showErrorAnimation(email);
            }
            
            const phone = document.getElementById('phone');
            const phoneRegex = /^[0-9+\-\s]{8,15}$/;
            if (phone && phone.value && !phoneRegex.test(phone.value)) {
                errors.phone = 'Format téléphone invalide';
                isValid = false;
                showErrorAnimation(phone);
            }
            
            const password = document.getElementById('password');
            if (password && password.value) {
                if (password.value.length < 8) {
                    errors.password = 'Minimum 8 caractères';
                    isValid = false;
                    showErrorAnimation(password);
                } else if (!/[A-Z]/.test(password.value)) {
                    errors.password = 'Au moins une majuscule';
                    isValid = false;
                    showErrorAnimation(password);
                } else if (!/[0-9]/.test(password.value)) {
                    errors.password = 'Au moins un chiffre';
                    isValid = false;
                    showErrorAnimation(password);
                }
            }
            
            if (!isValid) {
                e.preventDefault();
                displayErrors(errors);
            }
        });
    }
    
    // Animation sur les champs input
    const inputs = document.querySelectorAll('input, select, textarea');
    inputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.parentElement.classList.add('focused');
        });
        input.addEventListener('blur', function() {
            this.parentElement.classList.remove('focused');
        });
    });
    
    // Animation des boutons
    const buttons = document.querySelectorAll('.btn');
    buttons.forEach(btn => {
        btn.addEventListener('click', function(e) {
            let ripple = document.createElement('span');
            ripple.classList.add('ripple');
            this.appendChild(ripple);
            let x = e.clientX - e.target.offsetLeft;
            let y = e.clientY - e.target.offsetTop;
            ripple.style.left = `${x}px`;
            ripple.style.top = `${y}px`;
            setTimeout(() => {
                ripple.remove();
            }, 600);
        });
    });
});

// Fonction d'affichage des erreurs avec animation
function displayErrors(errors) {
    document.querySelectorAll('.error-msg').forEach(el => el.remove());
    document.querySelectorAll('.has-error').forEach(el => el.classList.remove('has-error'));
    
    for (const [field, message] of Object.entries(errors)) {
        const input = document.getElementById(field);
        if (input) {
            input.classList.add('has-error');
            const errorSpan = document.createElement('span');
            errorSpan.className = 'error-msg';
            errorSpan.style.cssText = 'color: #dc2626; font-size: 0.7rem; margin-top: 5px; display: block; animation: slideInLeft 0.3s ease;';
            errorSpan.innerHTML = `<i class="fas fa-exclamation-circle"></i> ${message}`;
            
            const parent = input.parentElement;
            if (parent && !parent.querySelector('.error-msg')) {
                parent.appendChild(errorSpan);
            }
        }
    }
}

function showErrorAnimation(input) {
    input.style.borderColor = '#dc2626';
    input.style.backgroundColor = '#fee2e2';
    setTimeout(() => {
        input.style.borderColor = '#e0e0e0';
        input.style.backgroundColor = '#fafafa';
    }, 1500);
}

function confirmDelete(message) {
    return confirm(message || 'Êtes-vous sûr de vouloir supprimer cet élément ? Cette action est irréversible.');
}

function formatDate(dateString) {
    if (!dateString) return '—';
    const date = new Date(dateString);
    return date.toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit', year: 'numeric' });
}

function validateEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

function validatePhone(phone) {
    const phoneRegex = /^[0-9+\-\s]{8,15}$/;
    return phoneRegex.test(phone);
}

function showLoading(buttonId) {
    const button = document.getElementById(buttonId);
    if (button) {
        const originalText = button.innerHTML;
        button.disabled = true;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Chargement...';
        return () => {
            button.disabled = false;
            button.innerHTML = originalText;
        };
    }
    return () => {};
}

function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.innerHTML = `<i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i> <span>${message}</span>`;
    toast.style.cssText = `
        position: fixed;
        bottom: 20px;
        right: 20px;
        background: ${type === 'success' ? '#4caf50' : '#f44336'};
        color: white;
        padding: 12px 20px;
        border-radius: 12px;
        z-index: 9999;
        animation: slideInRight 0.3s ease;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 0.9rem;
    `;
    document.body.appendChild(toast);
    setTimeout(() => {
        toast.style.animation = 'slideOutRight 0.3s ease';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// Styles pour les animations ripple
const style = document.createElement('style');
style.textContent = `
    .btn { position: relative; overflow: hidden; }
    .ripple {
        position: absolute;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.5);
        transform: scale(0);
        animation: ripple 0.6s linear;
        pointer-events: none;
    }
    @keyframes ripple {
        to { transform: scale(4); opacity: 0; }
    }
    @keyframes slideInRight {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    @keyframes slideOutRight {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-5px); }
        75% { transform: translateX(5px); }
    }
    .has-error {
        animation: shake 0.3s ease;
    }
    .focused label {
        color: olivedrab !important;
    }
`;
document.head.appendChild(style);