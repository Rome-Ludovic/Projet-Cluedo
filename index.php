<?php
session_start();

$sqlite = new SQLite3('cluedo.db');

function tout_lire(SQLite3Result $resultat): array
{
    $lignes = [];
    while ($ligne = $resultat->fetchArray(SQLITE3_ASSOC)) {
        $lignes[] = $ligne;
    }
    return $lignes;
}

$personnages = tout_lire($sqlite->query('SELECT * FROM personnages'));
$armes       = tout_lire($sqlite->query('SELECT * FROM armes'));
$pieces      = tout_lire($sqlite->query('SELECT * FROM pieces'));

if (isset($_GET['nouvelle_partie'])) {
    unset($_SESSION['crime']);
    header('Location: jeu.php');
    exit;
}

if (!isset($_SESSION['crime'])) {
    $_SESSION['crime'] = [
        $personnages[random_int(0, count($personnages) - 1)]['nom_personnage'],
        $armes[random_int(0, count($armes) - 1)]['nom_arme'],
        $pieces[random_int(0, count($pieces) - 1)]['nom_piece'],
    ];
}

/* ---- Pièces adjacentes à la pièce choisie ---- */
$piece = 'Salle de billard';

$sql  = 'SELECT adj.id_piece, adj.nom_piece ';
$sql .= 'FROM pieces INNER JOIN portes ';
$sql .= 'ON portes.id_piece1 = pieces.id_piece OR portes.id_piece2 = pieces.id_piece ';
$sql .= 'INNER JOIN pieces AS adj ';
$sql .= 'ON portes.id_piece1 = adj.id_piece OR portes.id_piece2 = adj.id_piece ';
$sql .= 'WHERE adj.id_piece != pieces.id_piece ';
$sql .= 'AND pieces.nom_piece LIKE :piece';

$requete = $sqlite->prepare($sql);
$requete->bindValue(':piece', $piece, SQLITE3_TEXT);
$adjacentes = tout_lire($requete->execute());

$sqlite->close();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="images/icone.png">
    <title>Accueil – Projet Cluedo</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header class="entete">
    <a class="logo" href="index.php">Projet Cluedo</a>
    <nav>
        <a href="index.php" class="actif">Accueil</a>
        <a href="regles.php">Règles</a>
    </nav>
</header>

<main class="container">
    <div class="card">
        <h1>Pièces adjacentes à <?= htmlspecialchars($piece) ?></h1>
        <ul>
            <?php foreach ($adjacentes as $adj): ?>
                <li><?= htmlspecialchars($adj['nom_piece']) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
</main>

</body>
</html>
