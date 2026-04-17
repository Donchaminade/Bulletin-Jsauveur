<?php
/**
 * rangs.php
 * Génère le tableau de classement à partir des données JSON avec impression PDF native et mode correction.
 */

// Récupération des paramètres
$filename = isset($_GET['file']) ? $_GET['file'] : '';
$classe = isset($_GET['classe']) ? htmlspecialchars($_GET['classe']) : '';
$trimestre = isset($_GET['trimestre']) ? htmlspecialchars($_GET['trimestre']) : '';

$dataFolder = 'data';
$filePath = $dataFolder . '/' . $filename;

if (empty($filename) || !file_exists($filePath)) {
    die("Erreur : Fichier de données introuvable pour cette classe.");
}

// Lecture des données
$data = json_decode(file_get_contents($filePath), true);

// Action de réinitialisation
if (isset($_GET['action']) && $_GET['action'] == 'reset') {
    if (file_exists($filePath)) {
        unlink($filePath);
        header("Location: index.html"); // Redirection vers l'accueil après reset
        exit;
    }
}

// Map Classe -> Fichier HTML
$htmlFileMap = [
    '6ème' => '6-5ème.html',
    '5ème' => '6-5ème.html',
    '4ème' => '4-3ème.html',
    '3ème' => '4-3ème.html',
    '2nde A4' => '2nde A4.html',
    '2nde S' => '2nde S.html',
    '1ère A4' => '1ere A4.html',
    '1ère D' => '1er D.html',
    'Terminale A4' => 'T A4.html',
    'Terminale D' => 'T D.html'
];
$targetHtml = isset($htmlFileMap[$classe]) ? $htmlFileMap[$classe] : 'index.html';

// Tri par moyenne décroissante
usort($data, function($a, $b) {
    if ($a['moyenne'] == $b['moyenne']) return 0;
    return ($a['moyenne'] > $b['moyenne']) ? -1 : 1;
});

// Calcul des rangs avec gestion des ex-æquo
function formatRank($rank, $gender) {
    if ($rank == 1) return ($gender == 'Fille') ? '1ère' : '1er';
    return $rank . 'ème';
}

$rankedData = [];
$currentRank = 1;
$prevMoyenne = null;
$skip = 0;

foreach ($data as $index => $student) {
    if ($prevMoyenne !== null && $student['moyenne'] < $prevMoyenne) {
        $currentRank += $skip + 1;
        $skip = 0;
    } elseif ($prevMoyenne !== null && $student['moyenne'] == $prevMoyenne) {
        $skip++;
    }
    
    $student['rank_val'] = $currentRank;
    $student['rank_str'] = formatRank($currentRank, $student['sexe']);
    $rankedData[] = $student;
    $prevMoyenne = $student['moyenne'];
}

$classeSafe = str_replace(' ', '', $classe);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau des Rangs - <?php echo $classe; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f3f4f6; margin: 0; padding: 0; }
        .rank-container {
            max-width: 900px;
            margin: 30px auto;
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
        }
        .header-table {
            border-bottom: 2px solid var(--primary-color);
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            text-align: center;
        }
        .table-rangs {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .table-rangs th {
            background-color: #f8fafc;
            color: var(--primary-color);
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.05em;
            padding: 15px;
            border-bottom: 2px solid #eef2f6;
        }
        .table-rangs td {
            padding: 15px;
            border-bottom: 1px solid #f1f5f9;
            text-align: center;
        }
        .rank-badge {
            background: #eef2f6;
            padding: 5px 12px;
            border-radius: 20px;
            font-weight: 700;
            color: var(--primary-color);
        }
        .rank-1 { background: #fef3c7; color: #92400e; }
        .rank-2 { background: #f1f5f9; color: #475569; }
        .rank-3 { background: #ffedd5; color: #9a3412; }
        
        .btn-correct {
            border: none;
            background: #f1f5f9;
            color: var(--primary-color);
            padding: 8px 12px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s;
            font-weight: 600;
        }
        .btn-correct:hover {
            background: var(--primary-color);
            color: white;
        }

        /* Loader Styles */
        .pdf-loader-overlay {
            position: fixed;
            top: 0; left: 0; width: 100vw; height: 100vh;
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(5px);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease;
        }
        .pdf-loader-overlay.active {
            opacity: 1;
            pointer-events: all;
        }
        .pdf-spinner {
            width: 50px;
            height: 50px;
            border: 5px solid #e2e8f0;
            border-top-color: var(--primary-color);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        @keyframes spin { 100% { transform: rotate(360deg); } }

        /* Impression Optimisée */
        @media print {
            @page {
                size: A4 landscape;
                margin: 10mm;
            }
            body { background: white !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; overflow: visible !important; }
            .rank-container { box-shadow: none; margin: 0; width: 100%; max-width: 100%; padding: 0; }
            .no-print { display: none !important; }
            table { page-break-inside: auto; }
            tr { page-break-inside: avoid; page-break-after: auto; }
            thead { display: table-header-group; }
            .action-col { display: none !important; }
        }
    </style>
</head>
<body>
    <div class="pdf-loader-overlay" id="pdfLoader">
        <div class="pdf-spinner"></div>
        <h2 style="color: var(--primary-color); margin-top: 20px; font-weight: 600;">Génération du PDF...</h2>
        <p style="color: #64748b;">Veuillez patienter.</p>
    </div>

    <header class="premium-header no-print">
        <div style="display: flex; gap: 15px;">
            <a href="javascript:history.back()" class="nav-link back">
                <i class="fas fa-arrow-left"></i> Retour au Bulletin
            </a>
            <!-- Lien vers le menu central Correction (optionnel si déja dans Rangs) -->
            <a href="correction.php" class="nav-link home">
                <i class="fas fa-screwdriver-wrench"></i> Mode Correction
            </a>
            <button onclick="confirmReset()" class="nav-link" style="background: #ef4444; color: white; border: none; cursor: pointer; padding: 0.8rem 1.5rem; border-radius: 12px; font-weight: 600; display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-trash-alt"></i> Vider la classe
            </button>
        </div>
        <button onclick="triggerPDF()" class="nav-link" style="background: var(--primary-color); color: white; border: none; cursor: pointer; padding: 0.8rem 1.5rem; border-radius: 12px; font-weight: 600; display: flex; align-items: center; gap: 10px;">
            <i class="fas fa-file-pdf"></i> Télécharger Rang PDF
        </button>
    </header>

    <main class="rank-container" id="printableArea">
        <div class="header-table">
            <h1 style="color: var(--primary-color); margin-bottom: 5px;">CLASSEMENT DES ÉLÈVES</h1>
            <p style="color: #64748b; font-weight: 500;">Classe : <?php echo $classe; ?> | Trimestre : <?php echo $trimestre; ?></p>
        </div>

        <table class="table-rangs">
            <thead>
                <tr>
                    <th>Rang</th>
                    <th style="text-align: left;">Nom Complet</th>
                    <th>Sexe</th>
                    <th>Moyenne</th>
                    <th class="action-col no-print">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rankedData as $student): 
                    $badgeClass = '';
                    if ($student['rank_val'] == 1) $badgeClass = 'rank-1';
                    elseif ($student['rank_val'] == 2) $badgeClass = 'rank-2';
                    elseif ($student['rank_val'] == 3) $badgeClass = 'rank-3';
                    
                    // Sécurisation HTML pour JSON payload
                    $rawDataJson = isset($student['raw_data']) ? htmlspecialchars(json_encode($student['raw_data']), ENT_QUOTES, 'UTF-8') : 'null';
                ?>
                <tr>
                    <td><span class="rank-badge <?php echo $badgeClass; ?>"><?php echo $student['rank_str']; ?></span></td>
                    <td style="text-align: left; font-weight: 600;"><?php echo $student['nom']; ?></td>
                    <td><?php echo $student['sexe'] == 'Fille' ? 'F' : 'M'; ?></td>
                    <td style="font-weight: 700; color: var(--primary-color);"><?php echo number_format($student['moyenne'], 2, ',', ' '); ?></td>
                    <td class="action-col no-print">
                        <?php if(isset($student['raw_data'])): ?>
                            <button class="btn-correct" onclick="editStudent('<?php echo $rawDataJson; ?>')">
                                <i class="fas fa-pen"></i> Corriger
                            </button>
                        <?php else: ?>
                            <span style="font-size: 0.8rem; color: #94a3b8;">N/A (Ancien format)</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <footer style="margin-top: 3rem; text-align: center; font-size: 0.8rem; color: #94a3b8;">
            Généré automatiquement par GestBull_JS le <?php echo date('d/m/Y à H:i'); ?>
        </footer>
    </main>

    <script>
        function confirmReset() {
            if (confirm("Êtes-vous sûr de vouloir vider cette classe ? Toutes les notes enregistrées pour ce trimestre seront supprimées.")) {
                window.location.href = window.location.href + "&action=reset";
            }
        }

        function triggerPDF() {
            document.getElementById('pdfLoader').classList.add('active');
            var originalTitle = document.title;
            document.title = 'Rangs_<?php echo $classeSafe; ?>_T<?php echo $trimestre; ?>';
            
            setTimeout(() => {
                window.print();
                
                // Hide loader after a simulated delay (print dialog pauses executing in some browsers though)
                setTimeout(() => {
                    document.getElementById('pdfLoader').classList.remove('active');
                    document.title = originalTitle;
                }, 1000);
            }, 1500); // 1.5 seconds loader animation
        }

        function editStudent(rawDataJsonStr) {
            try {
                const rawData = JSON.parse(rawDataJsonStr);
                if (rawData) {
                    // Sauvegarde dans localStorage
                    localStorage.setItem('correctionData', JSON.stringify(rawData));
                    // Redirection vers le HTML
                    window.location.href = '<?php echo $targetHtml; ?>';
                }
            } catch(e) {
                alert("Erreur lors de la lecture des données de l'élève. " + e);
            }
        }
    </script>
</body>
</html>
