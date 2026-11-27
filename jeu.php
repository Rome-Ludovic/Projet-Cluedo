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

function pieces_adjacentes(SQLite3 $sqlite, int $id_piece): array
{
    $sql  = 'SELECT p.id_piece, p.nom_piece FROM portes ';
    $sql .= 'INNER JOIN pieces AS p ON p.id_piece = portes.id_piece2 ';
    $sql .= 'WHERE portes.id_piece1 = :id ';
    $sql .= 'UNION ';
    $sql .= 'SELECT p.id_piece, p.nom_piece FROM portes ';
    $sql .= 'INNER JOIN pieces AS p ON p.id_piece = portes.id_piece1 ';
    $sql .= 'WHERE portes.id_piece2 = :id ';
    $sql .= 'ORDER BY nom_piece';

    $requete = $sqlite->prepare($sql);
    $requete->bindValue(':id', $id_piece, SQLITE3_INTEGER);
    return tout_lire($requete->execute());
}

if (isset($_POST['personnage'])) {
    $id_joueur = (int) $_POST['personnage'];

    $requete = $sqlite->prepare('SELECT nom_personnage FROM personnages WHERE id_personnage != :id');
    $requete->bindValue(':id', $id_joueur, SQLITE3_INTEGER);
    $suspects = tout_lire($requete->execute());

    $armes  = tout_lire($sqlite->query('SELECT nom_arme FROM armes'));
    $pieces = tout_lire($sqlite->query('SELECT nom_piece FROM pieces'));

    if (count($suspects) === 0 || count($armes) === 0 || count($pieces) === 0) {
        header('Location: index.php');
        exit;
    }

    $depart = $sqlite->querySingle("SELECT id_piece FROM pieces WHERE nom_piece = 'Hall'");
    if (!$depart) {
        $depart = $sqlite->querySingle('SELECT MIN(id_piece) FROM pieces');
    }

    $_SESSION['joueur_id'] = $id_joueur;
    $_SESSION['piece_id']  = (int) $depart;
    $_SESSION['crime'] = [
        $suspects[random_int(0, count($suspects) - 1)]['nom_personnage'],
        $armes[random_int(0, count($armes) - 1)]['nom_arme'],
        $pieces[random_int(0, count($pieces) - 1)]['nom_piece'],
    ];

    header('Location: jeu.php');
    exit;
}

if (!isset($_SESSION['joueur_id'], $_SESSION['crime'], $_SESSION['piece_id'])) {
    header('Location: index.php');
    exit;
}

if (isset($_POST['deplacer'])) {
    $destination = (int) $_POST['deplacer'];
    foreach (pieces_adjacentes($sqlite, $_SESSION['piece_id']) as $adj) {
        if ((int) $adj['id_piece'] === $destination) {
            $_SESSION['piece_id'] = $destination;
            break;
        }
    }
    header('Location: jeu.php');
    exit;
}

$requete = $sqlite->prepare('SELECT * FROM personnages WHERE id_personnage = :id');
$requete->bindValue(':id', $_SESSION['joueur_id'], SQLITE3_INTEGER);
$joueur = $requete->execute()->fetchArray(SQLITE3_ASSOC);

if (!$joueur) {
    unset($_SESSION['joueur_id']);
    header('Location: index.php');
    exit;
}

$requete = $sqlite->prepare('SELECT nom_piece FROM pieces WHERE id_piece = :id');
$requete->bindValue(':id', $_SESSION['piece_id'], SQLITE3_INTEGER);
$piece_actuelle = $requete->execute()->fetchArray(SQLITE3_ASSOC)['nom_piece'];

$adjacentes = pieces_adjacentes($sqlite, $_SESSION['piece_id']);

$sqlite->close();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="images/icone.png">
    <title>Jeu – Projet Cluedo</title>
    <link rel="preload" as="image" href="images/fonds/fond.png">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header class="entete">
    <a class="logo" href="index.php">Projet Cluedo</a>
    <nav>
        <a href="index.php">Accueil</a>
        <a href="regles.php">Règles</a>
    </nav>
    <a class="btn-entete" href="regles.php">Règles</a>
</header>

<main class="container">
    <div class="card">
        <h1>Test de jeu</h1>
        <p>Tu joues : <strong><?= htmlspecialchars($joueur['nom_personnage']) ?></strong></p>
        <p>La partie est lancée, le crime a été tiré au sort.</p>

        <h2>Déplacement</h2>
        <p>Tu es dans : <strong><?= htmlspecialchars($piece_actuelle) ?></strong></p>
        <form method="post" action="jeu.php">
            <?php foreach ($adjacentes as $adj): ?>
                <button type="submit" name="deplacer" value="<?= (int) $adj['id_piece'] ?>">
                    <?= htmlspecialchars($adj['nom_piece']) ?>
                </button>
            <?php endforeach; ?>
        </form>

        <h2>Crime (test)</h2>
        <ul>
            <li>Coupable : <?= htmlspecialchars($_SESSION['crime'][0]) ?></li>
            <li>Arme : <?= htmlspecialchars($_SESSION['crime'][1]) ?></li>
            <li>Pièce : <?= htmlspecialchars($_SESSION['crime'][2]) ?></li>
        </ul>
        <p><a href="index.php?nouvelle_partie=1">Nouvelle partie</a></p>
    </div>
</main>

</body>
</html>
