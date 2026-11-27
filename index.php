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

if (isset($_GET['nouvelle_partie'])) {
    unset($_SESSION['crime'], $_SESSION['joueur_id'], $_SESSION['piece_id']);
    header('Location: index.php');
    exit;
}

$partie_en_cours = isset($_SESSION['joueur_id'], $_SESSION['crime'], $_SESSION['piece_id']);

$sqlite->close();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="images/icone.png">
    <title>Accueil – Projet Cluedo</title>
    <link rel="preload" as="image" href="images/fonds/fond.png">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header class="entete">
    <a class="logo" href="index.php">Projet Cluedo</a>
    <nav>
        <a href="index.php" class="actif">Accueil</a>
        <a href="regles.php">Règles</a>
    </nav>
    <?php if ($partie_en_cours): ?>
        <a class="btn-entete" href="jeu.php">Retour au jeu</a>
    <?php endif; ?>
</header>

<main class="container">
    <div class="card vintage-grid accueil">
        <h1>CLUEDO</h1>
        <p class="sous-titre">
            Le docteur Lenoir a été assassiné dans sa villa.<br>
            Qui est le meurtrier, avec quelle arme et dans quelle pièce ?
        </p>

        <form method="post" action="jeu.php">
            <label for="personnage">Choisis ton personnage</label>
            <select name="personnage" id="personnage" required>
                <?php foreach ($personnages as $p): ?>
                    <option value="<?= (int) $p['id_personnage'] ?>">
                        <?= htmlspecialchars($p['nom_personnage']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit">Jouer</button>
        </form>
    </div>
</main>

</body>
</html>
