<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bulletin de Notes</title>
    <script src="bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    

    <header class="premium-header no-print">
        <div style="display: flex; gap: 15px;">
            <a href="index.html" class="nav-link home">
                <i class="fas fa-home"></i> Accueil
            </a>
            <a href="T D.html" class="nav-link back">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
        </div>
        <button onclick="downloadPDF()" class="nav-link" style="background: var(--primary-color); color: white; border: none; cursor: pointer; padding: 0.8rem 1.5rem; border-radius: 12px; font-weight: 600; display: flex; align-items: center; gap: 10px; transition: all 0.3s ease;">
            <i class="fas fa-file-pdf"></i> Télécharger PDF
        </button>
    </header>

    
</head>

<body>
<?php
// Vérification si les données ont été soumises
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Récupération des données du formulaire
    $anneeScolaire = htmlspecialchars($_POST['anneeScolaire']);
    $trimestre = htmlspecialchars($_POST['trimestre']);
    $classe = htmlspecialchars($_POST['classe']);
    $effectif = htmlspecialchars($_POST['effectif']);
    $nomEleve = htmlspecialchars($_POST['nomEleve']);
    $gender = htmlspecialchars($_POST['gender']);
    $directeur = htmlspecialchars($_POST['directeur']);
    $titulaire = htmlspecialchars($_POST['titu']);

    // Récupération des notes
    $noteClasseFrancaise1 = (float) htmlspecialchars($_POST['noteClasseFrancaise1']);
    $noteClasseFrancaise2 = (float) htmlspecialchars($_POST['noteClasseFrancaise2']);
    $noteCompFrancaise = (float) htmlspecialchars($_POST['noteCompFrancaise']);
    $profCompFrancaise = htmlspecialchars($_POST['profCompFrancaise']);

    $noteClasseAnglais1 = (float) htmlspecialchars($_POST['noteClasseAnglais1']);
    $noteClasseAnglais2 = (float) htmlspecialchars($_POST['noteClasseAnglais2']);
    $noteCompAnglais = (float) htmlspecialchars($_POST['noteCompAnglais']);
    $profCompAnglais = htmlspecialchars($_POST['profCompAnglais']);

    $noteClasseHistoireGeo1 = (float) htmlspecialchars($_POST['noteClasseHistoireGeo1']);
    $noteClasseHistoireGeo2 = (float) htmlspecialchars($_POST['noteClasseHistoireGeo2']);
    $noteCompHistoireGeo = (float) htmlspecialchars($_POST['noteCompHistoireGeo']);
    $profCompHistoireGeo = htmlspecialchars($_POST['profCompHistoireGeo']);

    // $noteClasseECM1 = (float) htmlspecialchars($_POST['noteClasseECM1']);
    // $noteClasseECM2 = (float) htmlspecialchars($_POST['noteClasseECM2']);
    // $noteCompECM = (float) htmlspecialchars($_POST['noteCompECM']);
    // $profCompECM = htmlspecialchars($_POST['profCompECM']);

    $noteClassePhilo1 = (float) htmlspecialchars($_POST['noteClassePhilo1']);
    $noteClassePhilo2 = (float) htmlspecialchars($_POST['noteClassePhilo2']);
    $noteCompPhilo = (float) htmlspecialchars($_POST['noteCompPhilo']);
    $profCompPhilo = htmlspecialchars($_POST['profCompPhilo']);

    $noteClasseMath1 = (float) htmlspecialchars($_POST['noteClasseMath1']);
    $noteClasseMath2 = (float) htmlspecialchars($_POST['noteClasseMath2']);
    $noteCompMath = (float) htmlspecialchars($_POST['noteCompMath']);
    $profCompMath = htmlspecialchars($_POST['profCompMath']);

    $noteClasseSVT1 = (float) htmlspecialchars($_POST['noteClasseSVT1']);
    $noteClasseSVT2 = (float) htmlspecialchars($_POST['noteClasseSVT2']);
    $noteCompSVT = (float) htmlspecialchars($_POST['noteCompSVT']);
    $profCompSVT = htmlspecialchars($_POST['profCompSVT']);

    $noteClassePhysique1 = (float) htmlspecialchars($_POST['noteClassePhysique1']);
    $noteClassePhysique2 = (float) htmlspecialchars($_POST['noteClassePhysique2']);
    $noteCompPhysique = (float) htmlspecialchars($_POST['noteCompPhysique']);
    $profCompPhysique = htmlspecialchars($_POST['profCompPhysique']);

    // Education Physique et Sportive
    $noteClasseEPS1 = isset($_POST['noteClasseEPS1']) && $_POST['noteClasseEPS1'] !== '' ? (float) htmlspecialchars($_POST['noteClasseEPS1']) : 0;
    $noteClasseEPS2 = isset($_POST['noteClasseEPS2']) && $_POST['noteClasseEPS2'] !== '' ? (float) htmlspecialchars($_POST['noteClasseEPS2']) : 0;
    $noteCompEPS = isset($_POST['noteCompEPS']) && $_POST['noteCompEPS'] !== '' ? (float) htmlspecialchars($_POST['noteCompEPS']) : 0;
    $profCompEPS = isset($_POST['profCompEPS']) && trim($_POST['profCompEPS']) !== '' ? htmlspecialchars($_POST['profCompEPS']) : '';
    $coefE = isset($_POST['coefE']) && $_POST['coefE'] !== '' ? htmlspecialchars($_POST['coefE']) : 0;

    // $noteClasseEsp1 = isset($_POST['noteClasseEsp1']) && $_POST['noteClasseEsp1'] !== '' ? (float) htmlspecialchars($_POST['noteClasseEsp1']) : 0;
    // $noteClasseEsp2 = isset($_POST['noteClasseEsp2']) && $_POST['noteClasseEsp2'] !== '' ? (float) htmlspecialchars($_POST['noteClasseEsp2']) : 0;
    // $noteCompEsp = isset($_POST['noteCompEsp']) && $_POST['noteCompEsp'] !== '' ? (float) htmlspecialchars($_POST['noteCompEsp']) : 0;
    // $profCompEsp = isset($_POST['profCompEsp']) && trim($_POST['profCompEsp']) !== '' ? htmlspecialchars($_POST['profCompEsp']) : '';
    // $coefEsp = isset($_POST['coefEsp']) && $_POST['coefEsp'] !== '' ? htmlspecialchars($_POST['coefEsp']) : 0;

    // Matière facultative
    $noteClasseFac1 = isset($_POST['noteClasseFac1']) && $_POST['noteClasseFac1'] !== '' ? (float) htmlspecialchars($_POST['noteClasseFac1']) : 0;
    $noteClasseFac2 = isset($_POST['noteClasseFac2']) && $_POST['noteClasseFac2'] !== '' ? (float) htmlspecialchars($_POST['noteClasseFac2']) : 0;
    $noteCompFac = isset($_POST['noteCompFac']) && $_POST['noteCompFac'] !== '' ? (float) htmlspecialchars($_POST['noteCompFac']) : 0;
    $profCompFac = isset($_POST['profCompFac']) && trim($_POST['profCompFac']) !== '' ? htmlspecialchars($_POST['profCompFac']) : '';
    $coef = isset($_POST['coef']) && $_POST['coef'] !== '' ? htmlspecialchars($_POST['coef']) :0;

    // Fonction pour calculer la moyenne
    function calculerMoyenne($noteClasse1, $noteClasse2) {
        return ($noteClasse1 + $noteClasse2) / 2;
    }

    // Calcul des moyennes
    $moyenneFrancaise = calculerMoyenne($noteClasseFrancaise1, $noteClasseFrancaise2);
    $moyenneAnglais = calculerMoyenne($noteClasseAnglais1, $noteClasseAnglais2);
    $moyenneHistoireGeo = calculerMoyenne($noteClasseHistoireGeo1, $noteClasseHistoireGeo2);
    // $moyenneECM = calculerMoyenne($noteClasseECM1, $noteClasseECM2);
    $moyennePhilo = calculerMoyenne($noteClassePhilo1, $noteClassePhilo2);
    $moyenneMath = calculerMoyenne($noteClasseMath1, $noteClasseMath2);
    $moyenneSVT = calculerMoyenne($noteClasseSVT1, $noteClasseSVT2);
    $moyennePhysique = calculerMoyenne($noteClassePhysique1, $noteClassePhysique2);
    // $moyenneEsp = calculerMoyenne($noteClasseEsp1, $noteClasseEsp2);
    $moyenneEPS = calculerMoyenne($noteClasseEPS1, $noteClasseEPS2);
    $moyenneFac = calculerMoyenne($noteClasseFac1, $noteClasseFac2);

    // Fonction pour déterminer l'observation sur 20
    function determinerObservation($moyenne) {
        if ($moyenne >= 20) {
            return "Excellent";
        } elseif ($moyenne >= 16) {
            return "Très Bien";
        } elseif ($moyenne >= 14) {
            return "Bien";
        } elseif ($moyenne >= 12) {
            return "Assez Bien";
        } elseif ($moyenne >= 10) {
            return "Passable";
        } elseif ($moyenne >= 6) {
            return "Insuffisant";
        } elseif ($moyenne >= 4) {
            return "Très Insuffisant";
        } else {
            return "Très Insuffisant";
        }
    }

    // Calcul du total des coefficients
    $totalcoef = 19 + (float)$coef + (float)$coefE ;

    // Calcul de la somme des moyennes avec coefficients
    $sommeMoyennes = (
        ((($noteCompFrancaise + $moyenneFrancaise) / 2) * 2) +
        ((($noteCompAnglais + $moyenneAnglais) / 2) * 2) +
        ((($noteCompHistoireGeo + $moyenneHistoireGeo) / 2) * 2) +
        // ((($noteCompECM + $moyenneECM) / 2) * 2) +
        ((($noteCompMath + $moyenneMath) / 2) * 3) +
        ((($noteCompSVT + $moyenneSVT) / 2) * 4) +
        ((($moyennePhilo + $noteCompPhilo) / 2)* 2)+ //philo
        // ((($moyenneEsp + $noteCompEsp) / 2) *$coefEsp)+ //espagnol
        ((($noteCompPhysique + $moyennePhysique) / 2) * 3) +
        ((($noteCompEPS + $moyenneEPS) / 2) * $coefE) +
        ((($noteCompFac + $moyenneFac) / 2) * $coef));

    // Fonction pour éviter la division par zéro
    function calculerValeur($sommeMoyennes, $totalCoef) {
        if ($totalCoef == 0) {
            return "Erreur : le total des coefficients ne peut pas être égal à zéro.";
        }
        return $sommeMoyennes / $totalCoef;
    }

    // Calcul final
    $resultat = calculerValeur($sommeMoyennes, $totalcoef);

    // Calcul des observations pour chaque matière
    $observationFrancaise = determinerObservation(($moyenneFrancaise + $noteCompFrancaise) / 2);
    $observationAnglais = determinerObservation(($moyenneAnglais + $noteCompAnglais) / 2);
    $observationHistoireGeo = determinerObservation(($moyenneHistoireGeo + $noteCompHistoireGeo) / 2);
    // $observationECM = determinerObservation(($moyenneECM + $noteCompECM) / 2);
    $observationPhilo = determinerObservation(($moyennePhilo + $noteCompPhilo) / 2);
    $observationMath = determinerObservation(($moyenneMath + $noteCompMath) / 2);
    $observationSVT = determinerObservation(($moyenneSVT + $noteCompSVT) / 2);
    $observationPhysique = determinerObservation(($moyennePhysique + $noteCompPhysique) / 2);
    // $observationEsp = determinerObservation(($moyenneEsp + $noteCompEsp) / 2);
    $observationEPS = determinerObservation(($moyenneEPS + $noteCompEPS) / 2);
    $observationFac = determinerObservation(($moyenneFac + $noteCompFac) / 2);

    // Récupération sécurisée de la valeur du trimestre depuis le formulaire
    $trimestre = htmlspecialchars($_POST['trimestre']);
    // Affichage en fonction du trimestre
    if ($trimestre == 1) {
        // echo ": " . $resultat;
    } elseif ($trimestre == 2) {
        // echo ": " . $resultat;
    } elseif ($trimestre == 3) {
        // echo ": " . $resultat;
    } else {
        // echo "Trimestre invalide.";
    }
}
?>




     <!-- Bouton de téléchargement -->
    
      <div class="container" style="background-color: #ffffff;">
        <div class="header">
        <div class="header-left">
        <strong>&nbsp; &nbsp; &nbsp; &nbsp;&nbsp;&nbsp; &nbsp; &nbsp; &nbsp;&nbsp;&nbsp; &nbsp; &nbsp; &nbsp;&nbsp; MINISTÈRE DES ENSEIGNEMENTS <br>&nbsp;&nbsp;&nbsp; &nbsp; &nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;PRIMAIRES SECONDAIRES,TECHNIQUES <br> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ET DE L'ARTISANAT</strong><br>
                <em>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;<b>Complexe Scolaire "LE TRESOR"</b></em><br>
                <span>&nbsp;&nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp; &nbsp; ADETIKOPE - ADOGLOVE</span><br>
                <span>&nbsp;&nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp; &nbsp;Tel: 90 74 78 77 / 92 30 88 33</span><br>               
            </div>
                <div class="header-right">
                    <strong>RÉPUBLIQUE TOGOLAISE</strong><br>
                    <em>Travail - Liberté - Patrie</em><br>
                    <img src="hd.png" alt="Logo" >
                    </div>
        </div>

        <table>
            <tr>
                <td><strong>Année Scolaire:</strong> <?php echo $anneeScolaire; ?></td>
                <td><strong>Semestre:</strong> <?php echo $trimestre; ?></td>
                <td><strong>Classe:</strong> <?php echo $classe; ?></td>
                <td><strong>Effectif:</strong> <?php echo $effectif; ?></td>
            </tr>
        </table>

        <div class="checkboxes">
            <div>Nouveau <input type="checkbox" <?php if (isset($_POST['status']) && $_POST['status'] == 'Nouveau') echo 'checked'; ?>></div>
            <div>Redoublant <input type="checkbox" <?php if (isset($_POST['status']) && $_POST['status'] == 'Redoublant') echo 'checked'; ?>></div>
            <div>Garçon <input type="checkbox" <?php if ($gender == 'Garçon') echo 'checked'; ?>></div>
            <div>Fille <input type="checkbox" <?php if ($gender == 'Fille') echo 'checked'; ?>></div>
           
            
        </div>

        <div class="text-left"><strong>NOM ET PRENOM(S) DE L'ELEVE:&nbsp;&nbsp;&nbsp;&nbsp;</strong> <?php echo $nomEleve; ?></div>
        <br>
        <table>
            <thead>


                <tr>
                    <th>Matières</th>
                    <th>Note 1</th>
                    <th>Note 2</th>
                    <th>Moy./20</th>
                    <th>Note Compos</th>
                    <th>Moy./20</th>
                    <th>Coef</th>
                    <th>Moy.Def</th>
                    <th>Observ.</th>
                    <th>Prof</th>
                    <th>Signature</th>
                </tr>
                <tr>
    <td colspan="11" style="text-align: center; font-weight: bold; border-top: 1px solid black; padding: 5px;">MATIERES LITTERAIRES</td>
</tr>

            </thead>
            <tbody>
                <tr>
                    <td>Comp. Français</td>
                    <td><?php echo $noteClasseFrancaise1; ?></td>
                    <td><?php echo $noteClasseFrancaise2; ?></td>
                    
                    <td><?php echo number_format($moyenneFrancaise, 2); ?></td>
                    <td><?php echo $noteCompFrancaise; ?></td>
                    <td><?php echo number_format(($moyenneFrancaise + $noteCompFrancaise) / 2, 2); ?></td>
                    <td>2</td>
                    <td><?php echo number_format((($noteCompFrancaise + $moyenneFrancaise)/2) * 2,2);?></td>
                    <td><?php echo $observationFrancaise; ?></td>
                    <td><?php echo $profCompFrancaise; ?></td>
                    <td></td>
                </tr>

 <!-- ECM -->
          

            <tr>
                <td>Philosophie</td>
                <td><?php echo $noteClassePhilo1; ?></td>
                <td><?php echo $noteClassePhilo2; ?></td>
                
                <td><?php echo number_format($moyennePhilo, 2); ?></td>
                <td><?php echo $noteCompPhilo; ?></td>
                <td><?php echo number_format(($moyennePhilo + $noteCompPhilo) / 2, 2); ?></td>
                <td>2</td>
                <td><?php echo number_format((($noteCompPhilo + $moyennePhilo)/2) * 2,2);?></td>
                <td><?php echo $observationPhilo; ?></td>
               <td><?php echo $profCompPhilo; ?></td>
                <td></td>
            </tr>


                <tr>
                    <td>Comp. Anglais</td>
                    <td><?php echo $noteClasseAnglais1; ?></td>
                    <td><?php echo $noteClasseAnglais2; ?></td>
                   
                    <td><?php echo number_format($moyenneAnglais, 2); ?></td>
                    <td><?php echo $noteCompAnglais; ?></td>
                    <td><?php echo number_format(($moyenneAnglais + $noteCompAnglais) / 2, 2); ?></td>
                    <td>2</td>
                    <td><?php echo number_format((($noteCompAnglais + $moyenneAnglais)/2) * 2,2);?></td>
                    <td><?php echo $observationAnglais; ?></td>
                   <td><?php echo $profCompAnglais; ?></td>                   
                    <td></td>

                   
                </tr>

                <!-- Histoire-géo -->
            <tr>
                <td>Histoire-géo</td>
                <td><?php echo $noteClasseHistoireGeo1; ?></td>
                <td><?php echo $noteClasseHistoireGeo2; ?></td>
                
                <td><?php echo number_format($moyenneHistoireGeo, 2); ?></td>
                <td><?php echo $noteCompHistoireGeo; ?></td>
                <td><?php echo number_format(($moyenneHistoireGeo + $noteCompHistoireGeo) / 2, 2); ?></td>
                <td>2</td>
                <td><?php echo number_format((($noteCompHistoireGeo + $moyenneHistoireGeo)/2) * 2,2);?></td>
                <td><?php echo $observationHistoireGeo; ?></td>
               <td><?php echo $profCompHistoireGeo; ?></td>
                <td></td>
            </tr>
            
           
            <tr>
                <td colspan="11" style="text-align: center; font-weight: bold; border-top: 1px solid black; padding: 5px;">MATIERES SCIENTIFIQUES</td>
            </tr>

            
            <!-- Mathématiques -->
            <tr>
                <td>Mathématiques</td>
                <td><?php echo $noteClasseMath1; ?></td>
                <td><?php echo $noteClasseMath2; ?></td>
                
                <td><?php echo number_format($moyenneMath, 2); ?></td>
                <td><?php echo $noteCompMath; ?></td>
                <td><?php echo number_format(($moyenneMath + $noteCompMath) / 2, 2); ?></td>
                <td>3</td>
                <td><?php echo number_format ((($noteCompMath + $moyenneMath)/2)* 3,2);?></td>
                <td><?php echo $observationMath; ?></td>
                <td><?php echo $profCompMath; ?></td>
                <td></td>
            </tr>
            
            <!-- Science de la Vie et de la Terre (SVT) -->
            <tr>
                <td>SVT</td>
                <td><?php echo $noteClasseSVT1; ?></td>
                <td><?php echo $noteClasseSVT2; ?></td>
                
                <td><?php echo number_format($moyenneSVT, 2); ?></td>
                <td><?php echo $noteCompSVT; ?></td>
                <td><?php echo number_format(($moyenneSVT + $noteCompSVT) / 2, 2); ?></td>
                <td>4</td>
                <td><?php echo number_format((($noteCompSVT + $moyenneSVT)/2)* 4,2);?></td>
                <td><?php echo $observationSVT; ?></td>
                <td><?php echo $profCompSVT; ?></td>
                <td></td>
            </tr>
            
            <!-- Science Physique -->
            <tr>
                <td>P.C.T</td>
                <td><?php echo $noteClassePhysique1; ?></td>
                <td><?php echo $noteClassePhysique2; ?></td>
              
                <td><?php echo number_format($moyennePhysique, 2); ?></td>
                <td><?php echo $noteCompPhysique; ?></td>
                <td><?php echo number_format(($moyennePhysique + $noteCompPhysique) / 2, 2); ?></td>
                <td>3</td>
                <td><?php echo number_format((($noteCompPhysique + $moyennePhysique)/2)* 3,2);?></td>
                <td><?php echo $observationPhysique; ?></td>
               <td><?php echo $profCompPhysique; ?></td>
                <td></td>
            </tr>


            <tr>
                <td>EPS</td>
                <td><?php echo $noteClasseEPS1 ?: ''; ?></td>
                <td><?php echo $noteClasseEPS2 ?: ''; ?></td>

                <td><?php echo $noteClasseEPS1 || $noteClasseEPS2 ? number_format($moyenneEPS, 2) : ''; ?></td>
                <td><?php echo (float)$noteCompEPS ?: ''; ?></td>
                <td><?php echo ($moyenneEPS && $noteCompEPS) ? number_format(($moyenneEPS + $noteCompEPS) / 2, 2) : ''; ?></td>
                <td><?php echo $coefE ?: ''; ?></td>
                <td><?php echo ($noteCompEPS && $moyenneEPS) ? number_format((($noteCompEPS + $moyenneEPS) / 2) * $coefE, 2) : ''; ?></td>
                <td><?php echo ($noteClasseEPS1 || $noteClasseEPS2 || $noteCompEPS || $moyenneEPS || $coefE || $profCompEPS) ? $observationEPS : ''; ?></td>
                <td><?php echo $profCompEPS ?: ''; ?></td>
                <td></td>
            </tr>
            
                    <tr>
            <td colspan="11" style="text-align: center; font-weight: bold; border-top: 1px solid black; padding: 5px;">MATIERES FACULTATIVES</td>
        </tr>

            <tr>
                <td>Musique/EM/Ewe/Kabye/Couture/Dessin</td>
                <td><?php echo $noteClasseFac1 ?: ''; ?></td>
                <td><?php echo $noteClasseFac2 ?: ''; ?></td>

                <td><?php echo $noteClasseFac1 || $noteClasseFac2 ? number_format($moyenneFac, 2) : ''; ?></td>
                <td><?php echo (float)$noteCompFac ?: ''; ?></td>
                <td><?php echo ($moyenneFac && $noteCompFac) ? number_format(($moyenneFac + $noteCompFac) / 2, 2) : ''; ?></td>
                <td><?php echo $coef ?: ''; ?></td>
                <td><?php echo ($noteCompFac && $moyenneFac) ? number_format((($noteCompFac + $moyenneFac) / 2) * $coef, 2) : ''; ?></td>
                <td><?php echo ($noteClasseFac1 || $noteClasseFac2 || $noteCompFac || $moyenneFac || $coef || $profCompFac) ? $observationFac : ''; ?></td>
                <td><?php echo $profCompFac ?: ''; ?></td>
                <td></td>
            </tr>


            
    

    
           

            <tr>
          <tr>
          <td colspan="6" style="text-align: left; padding: 10px; font-weight: bold;">Total: 
         

         </td>
         <td style="text-align: center; padding: 10px;"><?php echo $totalcoef?></td>                
       
        <td style="text-align: center; padding: 10px;"> <?php
echo number_format($sommeMoyennes, 2)
?></td>
    </tr>



   
            </tbody>
        </table>


<table border="1" style="width: 100%; border-collapse: collapse; text-align: left; margin-bottom: 20px;">
        <thead>
            <tr>
                <th colspan="2" style="text-align: center; font-weight: bold; padding: 8px; background-color: #f2f2f2;">Tableau d'Honneur et Observations</th>
                <th colspan="4" style="text-align: center; font-weight: bold; padding: 8px; background-color: #f2f2f2;">Moyennes et Rang</th>
            </tr>
        </thead>
    <tbody>
        <tr>
            <td>Tableau d’honneur:</td>
            <td>..................</td>
            <td>Moyenne du 1er Semestre:</td>
            <td>
                <?php if ($trimestre == 1) echo number_format($resultat, 2); ?>
            </td>
            <td>Rang: ..............</td>
            <td>Moy. la plus forte: ...............</td>
        </tr>

        <tr>
            <td>Encouragement:</td>
            <td>..................</td>
            <td>Moyenne du 2ème Semestre:</td>
            <td>
                <?php if ($trimestre == 2) echo number_format($resultat, 2); ?>
            </td>
                <td>Rang: ..............</td>
                <td>Moy. la plus faible: ...............</td>
        </tr>
        <tr>
            <td>Félicitations:</td>
            <td>..................</td>
            <td>Moyenne du 3ème Semestre:</td>
            <td>
                <?php if ($trimestre == 3) echo number_format($resultat, 2); ?>
            </td>
            <td>Rang: ..............</td>
            <td>Moy. Generale: ...............</td>
                
            </td>
        </tr>
        <tr>
            <td>Retards:</td>
            <td>
                .................
            </td>
            <td>Moyenne annuelle:</td>
            <td colspan="">.................</td>
            <td colspan="2">Rang .....................</td>
            
        </tr>
        <tr>
            <td>Absences:</td>
            <td>
                <?php if ($trimestre == 1 || $trimestre == 2 || $trimestre == 3) echo "................"; ?>
            </td>
            <td>Moyenne en lettre:</td>
            <td colspan="3">............................</td>
        </tr>
    </tbody>
</table>

<div>
 
        
        &nbsp; &nbsp; &nbsp;&nbsp;&nbsp;&nbsp; &nbsp; &nbsp; Observations: .............................. &nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;Decision finale:.................................<br>
                <br>
            &nbsp; &nbsp; &nbsp;&nbsp;&nbsp;&nbsp; &nbsp; &nbsp;&nbsp;Titulaire: &nbsp;&nbsp;<?php echo '  '.'   '.$titulaire?> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp;&nbsp;&nbsp; &nbsp; &nbsp;&nbsp; &nbsp; 

            <div style="display: ;">
                    <p> &nbsp; &nbsp; &nbsp;&nbsp;&nbsp;&nbsp; &nbsp; &nbsp;&nbsp;Adétikopé, le ……/……/ ………&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp;&nbsp;&nbsp; &nbsp; &nbsp;Proviseur: &nbsp;<?php echo '    '. ' '.$directeur?></p>
            </div>
        <br>
        <br>
</div>

   

 <!-- Script html2pdf.js -->


 
 <script>
   function downloadPDF() {
    const element = document.body; // Utilise le corps de la page comme contenu à convertir en PDF
    const buttons = document.querySelectorAll(".no-print"); // Sélectionne tous les éléments avec la classe no-print

    // Masquez les boutons avant de générer le PDF
    buttons.forEach((button) => {
        button.style.display = "none";
    });

    const options = {
        margin: 0,
        filename: 'bulletin_notes.pdf',
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2 },
        jsPDF: { unit: 'cm', format: 'a4', orientation: 'portrait' }
    };

    html2pdf()
        .set(options)
        .from(element)
        .save()
        .then(() => {
            // Réaffichez les boutons après la génération du PDF
            buttons.forEach((button) => {
                button.style.display = "inline-block";
            });
        });
}

</script>
    
 <br>
 <br>
 <br>
 <br>
 <br>
 <br>
    
   
 
</body>

</html>
