<?php
/**
 * rangs.php
 * Génère le tableau de classement à partir des données JSON.
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

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau des Rangs - <?php echo $classe; ?></title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f3f4f6; }
        .rank-container {
            max-width: 800px;
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
        
        @media print {
            .no-print { display: none !important; }
            .rank-container { box-shadow: none; margin: 0; width: 100%; }
        }
    </style>
</head>
<body>
    <header class="premium-header no-print">
        <div style="display: flex; gap: 15px;">
            <a href="javascript:history.back()" class="nav-link back">
                <i class="fas fa-arrow-left"></i> Retour au Bulletin
            </a>
            <button onclick="confirmReset()" class="nav-link" style="background: #ef4444; color: white; border: none; cursor: pointer; padding: 0.8rem 1.5rem; border-radius: 12px; font-weight: 600; display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-trash-alt"></i> Vider la classe
            </button>
        </div>
        <button onclick="downloadPDF()" class="nav-link" style="background: var(--primary-color); color: white; border: none; cursor: pointer; padding: 0.8rem 1.5rem; border-radius: 12px; font-weight: 600; display: flex; align-items: center; gap: 10px;">
            <i class="fas fa-file-pdf"></i> Télécharger Tableau PDF
        </button>
    </header>

    <main class="rank-container" id="printableArea">
        <div class="header-table">
            <h1 style="color: var(--primary-color); margin-bottom: 5px;">TABLEAU D'HONNEUR</h1>
            <p style="color: #64748b; font-weight: 500;">Classe : <?php echo $classe; ?> | Trimestre : <?php echo $trimestre; ?></p>
        </div>

        <table class="table-rangs">
            <thead>
                <tr>
                    <th>Rang</th>
                    <th style="text-align: left;">Nom Complet</th>
                    <th>Sexe</th>
                    <th>Moyenne</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rankedData as $student): 
                    $badgeClass = '';
                    if ($student['rank_val'] == 1) $badgeClass = 'rank-1';
                    elseif ($student['rank_val'] == 2) $badgeClass = 'rank-2';
                    elseif ($student['rank_val'] == 3) $badgeClass = 'rank-3';
                ?>
                <tr>
                    <td><span class="rank-badge <?php echo $badgeClass; ?>"><?php echo $student['rank_str']; ?></span></td>
                    <td style="text-align: left; font-weight: 600;"><?php echo $student['nom']; ?></td>
                    <td><?php echo $student['sexe'] == 'Fille' ? 'F' : 'M'; ?></td>
                    <td style="font-weight: 700; color: var(--primary-color);"><?php echo number_format($student['moyenne'], 2, ',', ' '); ?></td>
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

        function downloadPDF() {
            const element = document.getElementById('printableArea');
            const opt = {
                margin:       10,
                filename:     'Classement_<?php echo $classeSafe; ?>_T<?php echo $trimestre; ?>.pdf',
                image:        { type: 'jpeg', quality: 0.98 },
                html2canvas:  { scale: 2 },
                jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
            };
            html2pdf().set(opt).from(element).save();
        }
    </script>
</body>
</html>
