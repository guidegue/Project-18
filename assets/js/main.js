// main.js - JavaScript pour OrientPro
document.addEventListener('DOMContentLoaded', function() {
    // Confirmation suppression
    document.querySelectorAll('.btn-danger, a[onclick*="confirm"]').forEach(btn => {
        btn.addEventListener('click', function(e) {
            if (!confirm('⚠️ Êtes-vous sûr de vouloir supprimer cet élément ?')) {
                e.preventDefault();
            }
        });
    });
    
    // Auto-fermeture alertes
    document.querySelectorAll('.alert:not(.persistent)').forEach(alert => {
        setTimeout(() => { alert.style.opacity = '0'; setTimeout(() => alert.remove(), 300); }, 5000);
    });
    
    // Surlignage options questionnaire
    document.querySelectorAll('.option-item').forEach(item => {
        let radio = item.querySelector('input[type="radio"]');
        if (radio && radio.checked) item.classList.add('selected');
        if (radio) radio.addEventListener('change', function() {
            item.parentElement.querySelectorAll('.option-item').forEach(i => i.classList.remove('selected'));
            if (this.checked) item.classList.add('selected');
        });
    });
    
    // Animation barres progression
    document.querySelectorAll('.progress-fill').forEach(bar => {
        let width = bar.style.width;
        bar.style.width = '0%';
        setTimeout(() => { bar.style.transition = 'width 1s ease'; bar.style.width = width; }, 100);
    });
});