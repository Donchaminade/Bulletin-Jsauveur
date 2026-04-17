<?php
// Configuration : liste des fichiers JSON disponibles
$dataFolder = "data";
$availableClasses = [
    "6ème" => "6-5ème.html",
    "5ème" => "6-5ème.html",
    "4ème" => "4-3ème.html",
    "3ème" => "4-3ème.html",
    "2nde A4" => "2nde A4.html",
    "2nde S" => "2nde S.html",
    "1ère A4" => "1ere A4.html",
    "1ère D" => "1er D.html",
    "Terminale A4" => "T A4.html",
    "Terminale D" => "T D.html"
];

$files = glob($dataFolder . "/*.json");
$filesData = [];

// Analyser les fichiers existants pour construire le catalogue
foreach ($files as $file) {
    // Le nom ressemble à: 2nde_A4_1.json, 6ème_2.json
    $basename = basename($file, ".json");
    // Extraire le trimestre (dernier chiffre après _ )
    $parts = explode("_", $basename);
    $trimestre = array_pop($parts);
    $classeSafe = implode(" ", $parts);
    
    // Remettre les tirets ou espaces correctement si on voulait
    // Pour simplifier, on lit direct le JSON pour prendre juste la liste des élèves (pas besoin de tout lire au début, mais faisable en AJAX)
    $filesData[] = [
        "filename" => basename($file),
        "classe_safe" => $classeSafe,
        "trimestre" => $trimestre
    ];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Centre de Correction des Bulletins</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { 
            font-family: 'Inter', sans-serif; 
            background-color: #f8fafc; 
            color: #1e293b; 
            background: radial-gradient(circle at top right, #f1f5f9, #ffffff);
            min-height: 100vh;
        }
        .hero { text-align: center; padding: 4rem 1rem 2rem; }
        .hero h1 { 
            font-size: 3rem; 
            color: var(--primary-color); 
            margin-bottom: 0.5rem; 
            font-weight: 800;
            letter-spacing: -0.025em;
        }
        .hero p { color: #64748b; font-size: 1.25rem; }
        
        .main-container { max-width: 1000px; margin: 0 auto; padding: 2rem; }
        
        .glass-panel {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(12px);
            border-radius: 24px;
            padding: 3rem;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.4);
            margin-bottom: 2rem;
            position: relative;
            overflow: hidden;
        }

        .glass-panel::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 6px;
            background: linear-gradient(90deg, var(--primary-color), #6366f1);
        }

        .form-group { margin-bottom: 2rem; }
        .form-label { 
            display: block; 
            font-weight: 700; 
            margin-bottom: 1rem; 
            color: #334155; 
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        #fileSelector {
            height: 60px;
            font-size: 1.1rem;
            border-radius: 14px;
            padding: 0 1.5rem;
            border: 2px solid #e2e8f0;
            background-color: #f8fafc;
            transition: all 0.3s ease;
        }

        #fileSelector:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
            background-color: white;
        }

        .student-list {
            margin-top: 3rem;
            display: none;
            animation: slideUp 0.4s ease-out;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .student-card {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.5rem 2rem;
            background: white;
            border-radius: 16px;
            margin-bottom: 1rem;
            transition: all 0.3s ease;
            border: 1px solid #f1f5f9;
        }
        .student-card:hover {
            transform: scale(1.02);
            box-shadow: 0 10px 30px rgba(0,0,0,0.06);
            border-color: var(--primary-color);
        }
        .student-info h3 { 
            margin: 0; 
            color: #0f172a; 
            font-size: 1.25rem; 
            font-weight: 700;
        }
        .student-info p { margin: 0.25rem 0 0; color: #64748b; font-size: 1rem; }
        
        .btn-action {
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 10px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .btn-action:hover {
            background: #1d4ed8;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(37, 99, 235, 0.3);
        }
        
    </style>
</head>
<body>
    <header class="premium-header">
        <a href="index.html" class="nav-link home">
            <i class="fas fa-home"></i> Accueil
        </a>
    </header>

    <div class="hero">
        <h1>Centre de Correction</h1>
        <p>Sélectionnez une classe et modifiez un bulletin généré précédemment.</p>
    </div>

    <main class="main-container">
        <!-- ETAPE 1 : Choix de la classe / Fichier -->
        <div class="glass-panel">
            <div class="form-group">
                <label class="form-label"><i class="fas fa-folder-open"></i> Sélectionner une archive :</label>
                <select id="fileSelector" class="form-control-premium" onchange="loadStudents()">
                    <option value="">-- Choisissez le dossier de classe --</option>
                    <?php foreach($filesData as $f): ?>
                        <option value="<?php echo htmlspecialchars($f['filename']); ?>" data-classe="<?php echo htmlspecialchars($f['classe_safe']); ?>">
                            <?php echo htmlspecialchars($f['classe_safe']); ?> - Trimestre <?php echo htmlspecialchars($f['trimestre']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <p id="loadingMsg" style="display:none; color: var(--primary-color);"><i class="fas fa-spinner fa-spin"></i> Chargement des élèves...</p>
            
            <!-- ETAPE 2 : Liste des élèves gérée en JS -->
            <div id="studentListContainer" class="student-list">
                <h3 style="margin-bottom: 1rem; color: #334155; border-bottom: 2px solid #e2e8f0; padding-bottom: 0.5rem;">Élèves Enregistrés</h3>
                <div id="studentsCards"></div>
            </div>
        </div>
    </main>

    <script>
        // Importer la map PHP vers JS pour la bonne redirection
        const htmlMap = <?php echo json_encode($availableClasses); ?>;
        
        async function loadStudents() {
            const fileSelect = document.getElementById('fileSelector');
            const filename = fileSelect.value;
            const container = document.getElementById('studentListContainer');
            const cardsDiv = document.getElementById('studentsCards');
            const loader = document.getElementById('loadingMsg');
            
            if(!filename) {
                container.style.display = 'none';
                return;
            }
            
            loader.style.display = 'block';
            container.style.display = 'none';
            
            try {
                // Fetch the JSON directly
                const response = await fetch('data/' + filename + '?t=' + new Date().getTime());
                const data = await response.json();
                
                cardsDiv.innerHTML = '';
                
                if(data.length === 0) {
                    cardsDiv.innerHTML = '<p>Aucun élève dans ce fichier.</p>';
                } else {
                    data.forEach(student => {
                        const hasRaw = student.raw_data ? true : false;
                        const actionBtn = hasRaw 
                            ? `<button class="btn-action" onclick="openCorrection('${encodeURIComponent(JSON.stringify(student.raw_data))}', '${student.raw_data.classe || fileSelect.options[fileSelect.selectedIndex].getAttribute('data-classe')}')"><i class="fas fa-pen"></i> Corriger</button>`
                            : `<span style="font-size:0.8rem; color:#94a3b8;">Format ancien (Non modifiable)</span>`;
                            
                        const card = document.createElement('div');
                        card.className = 'student-card';
                        card.innerHTML = `
                            <div class="student-info">
                                <h3>${student.nom}</h3>
                                <p>Moyenne: <strong>${parseFloat(student.moyenne).toFixed(2)}</strong> | Sexe: ${student.sexe}</p>
                            </div>
                            <div class="student-action">
                                ${actionBtn}
                            </div>
                        `;
                        cardsDiv.appendChild(card);
                    });
                }
                
                loader.style.display = 'none';
                container.style.display = 'block';
                
            } catch(e) {
                console.error("Erreur de chargement", e);
                loader.style.display = 'none';
                alert("Erreur lors de la récupération des données.");
            }
        }
        
        function openCorrection(rawDataJsonEncoded, classeName) {
            try {
                const rawData = JSON.parse(decodeURIComponent(rawDataJsonEncoded));
                localStorage.setItem('correctionData', JSON.stringify(rawData));
                
                // Extraire le bon nom de classe
                let safeClasse = classeName.replace(/_/g, ' ');
                // Nettoyer si c'est "2nde A4 1"
                if(safeClasse.includes(' 1') || safeClasse.includes(' 2') || safeClasse.includes(' 3')) {
                    safeClasse = safeClasse.substring(0, safeClasse.length - 2);
                }
                
                let targetHtml = htmlMap[safeClasse];
                if(!targetHtml && htmlMap[rawData.classe]) {
                    targetHtml = htmlMap[rawData.classe]; // Fallback raw_data classe
                }
                
                if (targetHtml) {
                    window.location.href = targetHtml;
                } else {
                    alert("Impossible de déterminer le fichier HTML pour la classe : " + safeClasse);
                }
            } catch(e) {
                alert("Erreur: " + e);
            }
        }
    </script>
</body>
</html>