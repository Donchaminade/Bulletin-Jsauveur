/**
 * validator.js
 * Bloque la saisie des notes à 2 chiffres avant la virgule et 2 après (Max 99.99).
 */
document.addEventListener('input', function (e) {
    // On cible uniquement les inputs de type number dont le nom commence par 'note'
    if (e.target.type === 'number' && (e.target.name.startsWith('note') || e.target.classList.contains('form-control-premium'))) {
        let val = e.target.value;
        if (!val) return;

        // On sépare la partie entière de la partie décimale
        let parts = val.split('.');
        
        // 1. Limiter à 2 chiffres la partie entière
        if (parts[0].length > 2) {
            // On ne garde que les 2 premiers chiffres
            let truncatedPart0 = parts[0].slice(0, 2);
            e.target.value = truncatedPart0 + (parts[1] !== undefined ? '.' + parts[1] : '');
            val = e.target.value;
            parts = val.split('.');
        }

        // 2. Limiter à 2 chiffres la partie décimale
        if (parts[1] !== undefined && parts[1].length > 2) {
            e.target.value = parts[0] + '.' + parts[1].slice(0, 2);
        }

        // 3. Sécurité supplémentaire : Bloquer à 99.99
        if (parseFloat(e.target.value) > 99.99) {
            e.target.value = "99.99";
        }
    }
});

// Empêcher la saisie du caractère 'e', '+', '-' qui sont valides pour type="number" mais pas pour nos notes
document.addEventListener('keydown', function (e) {
    if (e.target.type === 'number' && (e.target.name.startsWith('note') || e.target.classList.contains('form-control-premium'))) {
        if (['e', 'E', '+', '-'].includes(e.key)) {
            e.preventDefault();
        }
    }
});

// --- PONT JS POUR LE MODE CORRECTION ---
document.addEventListener('DOMContentLoaded', function () {
    const correctionDataStr = localStorage.getItem('correctionData');
    if (correctionDataStr) {
        try {
            const rawData = JSON.parse(correctionDataStr);
            
            for (const key in rawData) {
                if (rawData.hasOwnProperty(key)) {
                    const value = rawData[key];
                    
                    if (key === 'gender') {
                        const radio = document.querySelector(`input[name="gender"][value="${value}"]`);
                        if (radio) radio.checked = true;
                    } else {
                        const el = document.getElementById(key) || document.querySelector(`input[name="${key}"]`);
                        if (el && el.type !== 'radio' && el.type !== 'checkbox') {
                            el.value = value;
                        }
                    }
                }
            }
            
            localStorage.removeItem('correctionData');
            
            let header = document.querySelector('h1');
            if (header) {
                header.innerHTML += ' <span style="background: #ef4444; color: white; padding: 2px 8px; border-radius: 8px; font-size: 0.8rem; vertical-align: middle; margin-left: 10px;">Mode Correction</span>';
            }
            
        } catch (e) {
            console.error("Erreur lors de l'injection des données de correction:", e);
        }
    }
});