<?php
/**
 * selection_rangs.php
 * Interface de sélection pour l'impression des rangs par classe.
 */
$dataFolder = "data";
$files = glob($dataFolder . "/*.json");
$filesData = [];

foreach ($files as $file) {
    $basename = basename($file, ".json");
    $parts = explode("_", $basename);
    $trimestre = array_pop($parts);
    $classeSafe = implode(" ", $parts);
    
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
    <title>Impression des Rangs | Sélection</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { 
            background: radial-gradient(circle at top right, #f1f5f9, #ffffff);
            min-height: 100vh;
        }
        .main-container { max-width: 800px; margin: 0 auto; padding: 4rem 2rem; }
        
        .selection-panel {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(12px);
            border-radius: 24px;
            padding: 3rem;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.4);
            text-align: center;
        }

        .icon-header {
            width: 80px;
            height: 80px;
            background: var(--secondary-color);
            color: white;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin: 0 auto 2rem;
            box-shadow: 0 10px 20px rgba(139, 92, 246, 0.2);
        }

        #fileSelector {
            height: 65px;
            font-size: 1.1rem;
            border-radius: 16px;
            margin-bottom: 2rem;
            text-align: center;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <header class="premium-header">
        <a href="index.html" class="nav-link home">
            <i class="fas fa-home"></i> Accueil
        </a>
    </header>

    <main class="main-container">
        <div class="selection-panel animate-fade-in">
            <div class="icon-header">
                <i class="fas fa-list-ol"></i>
            </div>
            
            <h1 style="margin-bottom: 1rem;">Impression des Rangs</h1>
            <p class="description" style="margin-bottom: 3rem;">Sélectionnez une classe archivée pour visualiser et imprimer le tableau des rangs.</p>

            <form action="rangs.php" method="GET">
                <div class="form-group">
                    <label class="form-label" style="text-align: center; justify-content: center;">
                        <i class="fas fa-folder-open"></i> Archive de classe
                    </label>
                    <select id="fileSelector" name="file" class="form-control-premium" required onchange="updateHiddenFields()">
                        <option value="">-- Choisissez une classe --</option>
                        <?php foreach($filesData as $f): ?>
                            <option value="<?php echo htmlspecialchars($f['filename']); ?>" 
                                    data-classe="<?php echo htmlspecialchars($f['classe_safe']); ?>"
                                    data-trimestre="<?php echo htmlspecialchars($f['trimestre']); ?>">
                                <?php echo htmlspecialchars($f['classe_safe']); ?> - Trimestre <?php echo htmlspecialchars($f['trimestre']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Champs cachés pour passer classe et trimestre à rangs.php -->
                <input type="hidden" name="classe" id="hiddenClasse">
                <input type="hidden" name="trimestre" id="hiddenTrimestre">

                <button type="submit" class="btn-premium" style="width: 100%; justify-content: center; background: var(--secondary-color);">
                    <i class="fas fa-check-circle"></i>
                    <span>Valider & Voir le Classement</span>
                </button>
            </form>
        </div>
    </main>

    <script>
        function updateHiddenFields() {
            const selector = document.getElementById('fileSelector');
            const selectedOption = selector.options[selector.selectedIndex];
            
            if (selectedOption.value) {
                document.getElementById('hiddenClasse').value = selectedOption.getAttribute('data-classe');
                document.getElementById('hiddenTrimestre').value = selectedOption.getAttribute('data-trimestre');
            }
        }
    </script>
</body>
</html>
