// validation.js

function showError(fieldId, message) {
    const field = document.getElementById(fieldId);
    if (!field) return;
    const formGroup = field.closest('.form-group');
    formGroup.classList.add('has-error');
    const oldError = formGroup.querySelector('.error-msg');
    if (oldError) oldError.remove();
    const errorSpan = document.createElement('span');
    errorSpan.className = 'error-msg';
    errorSpan.innerHTML = '<i class="fas fa-exclamation-circle"></i> ' + message;
    formGroup.appendChild(errorSpan);
}

function clearErrors() {
    document.querySelectorAll('.has-error').forEach(el => el.classList.remove('has-error'));
    document.querySelectorAll('.error-msg').forEach(el => el.remove());
}

// Validation formulaire utilisateur (BackOffice)
function validateUserForm() {
    clearErrors();
    let isValid = true;
    
    const nom = document.getElementById('nom');
    if (nom && !nom.value.trim()) {
        showError('nom', 'Le nom est obligatoire');
        isValid = false;
    } else if (nom && nom.value.trim().length < 2) {
        showError('nom', 'Minimum 2 caractères');
        isValid = false;
    }
    
    const prenom = document.getElementById('prenom');
    if (prenom && !prenom.value.trim()) {
        showError('prenom', 'Le prénom est obligatoire');
        isValid = false;
    } else if (prenom && prenom.value.trim().length < 2) {
        showError('prenom', 'Minimum 2 caractères');
        isValid = false;
    }
    
    const email = document.getElementById('email');
    if (email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!email.value.trim()) {
            showError('email', "L'email est obligatoire");
            isValid = false;
        } else if (!emailRegex.test(email.value.trim())) {
            showError('email', "Format email invalide");
            isValid = false;
        }
    }
    
    const password = document.getElementById('password');
    if (password && password.value) {
        if (password.value.length < 8) {
            showError('password', 'Minimum 8 caractères');
            isValid = false;
        } else if (!/[A-Z]/.test(password.value)) {
            showError('password', 'Au moins une majuscule');
            isValid = false;
        } else if (!/[0-9]/.test(password.value)) {
            showError('password', 'Au moins un chiffre');
            isValid = false;
        }
    }
    
    const phone = document.getElementById('phone');
    if (phone && phone.value.trim()) {
        const phoneRegex = /^[0-9+\-\s]{8,15}$/;
        if (!phoneRegex.test(phone.value.trim())) {
            showError('phone', 'Format téléphone invalide');
            isValid = false;
        }
    }
    
    return isValid;
}

// Validation login
function validateLogin() {
    clearErrors();
    let isValid = true;
    
    const email = document.getElementById('email');
    if (email && !email.value.trim()) {
        showError('email', "L'email est obligatoire");
        isValid = false;
    }
    
    const password = document.getElementById('password');
    if (password && !password.value) {
        showError('password', "Le mot de passe est obligatoire");
        isValid = false;
    }
    
    return isValid;
}

// Validation register
function validateRegister() {
    clearErrors();
    let isValid = true;
    
    const nom = document.getElementById('nom');
    if (nom && !nom.value.trim()) {
        showError('nom', "Le nom est obligatoire");
        isValid = false;
    } else if (nom && nom.value.trim().length < 2) {
        showError('nom', "Minimum 2 caractères");
        isValid = false;
    }
    
    const prenom = document.getElementById('prenom');
    if (prenom && !prenom.value.trim()) {
        showError('prenom', "Le prénom est obligatoire");
        isValid = false;
    } else if (prenom && prenom.value.trim().length < 2) {
        showError('prenom', "Minimum 2 caractères");
        isValid = false;
    }
    
    const email = document.getElementById('email');
    if (email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!email.value.trim()) {
            showError('email', "L'email est obligatoire");
            isValid = false;
        } else if (!emailRegex.test(email.value.trim())) {
            showError('email', "Format email invalide");
            isValid = false;
        }
    }
    
    const password = document.getElementById('password');
    if (password) {
        if (!password.value) {
            showError('password', "Le mot de passe est obligatoire");
            isValid = false;
        } else if (password.value.length < 8) {
            showError('password', "Minimum 8 caractères");
            isValid = false;
        } else if (!/[A-Z]/.test(password.value)) {
            showError('password', "Au moins une majuscule");
            isValid = false;
        } else if (!/[0-9]/.test(password.value)) {
            showError('password', "Au moins un chiffre");
            isValid = false;
        }
    }
    
    const confirm = document.getElementById('confirm_password');
    if (confirm && confirm.value !== password.value) {
        showError('confirm_password', "Les mots de passe ne correspondent pas");
        isValid = false;
    }
    
    return isValid;
}

// Attacher les événements
document.addEventListener('DOMContentLoaded', function() {
    const userForm = document.getElementById('userForm');
    if (userForm) {
        userForm.addEventListener('submit', function(e) {
            if (!validateUserForm()) e.preventDefault();
        });
    }
    
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            if (!validateLogin()) e.preventDefault();
        });
    }
    
    const registerForm = document.getElementById('registerForm');
    if (registerForm) {
        registerForm.addEventListener('submit', function(e) {
            if (!validateRegister()) e.preventDefault();
        });
    }
    
    // Recherche en direct
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const term = this.value.toLowerCase();
            const rows = document.querySelectorAll('#usersTable tbody tr');
            rows.forEach(row => {
                row.style.display = row.textContent.toLowerCase().includes(term) ? '' : 'none';
            });
        });
    }
    
    // Auto-fermeture des alertes
    const alert = document.querySelector('.alert');
    if (alert) {
        setTimeout(() => {
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        }, 4000);
    }
});