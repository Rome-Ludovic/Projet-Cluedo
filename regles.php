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

function h($texte): string
{
    return htmlspecialchars((string) $texte);
}

$personnages = tout_lire($sqlite->query('SELECT * FROM personnages ORDER BY id_personnage'));
$armes       = tout_lire($sqlite->query('SELECT * FROM armes ORDER BY id_arme'));
$pieces      = tout_lire($sqlite->query('SELECT * FROM pieces ORDER BY id_piece'));


$sql  = 'SELECT p.nom_piece, adj.nom_piece AS voisine ';
$sql .= 'FROM pieces AS p INNER JOIN portes ';
$sql .= 'ON portes.id_piece1 = p.id_piece OR portes.id_piece2 = p.id_piece ';
$sql .= 'INNER JOIN pieces AS adj ';
$sql .= 'ON (adj.id_piece = portes.id_piece1 OR adj.id_piece = portes.id_piece2) ';
$sql .= 'AND adj.id_piece != p.id_piece ';
$sql .= 'ORDER BY p.nom_piece, adj.nom_piece';

$voisines = [];
foreach ($pieces as $p) {
    $voisines[$p['nom_piece']] = [];
}
foreach (tout_lire($sqlite->query($sql)) as $ligne) {
    $voisines[$ligne['nom_piece']][] = $ligne['voisine'];
}

$nb_personnages = count($personnages);
$nb_armes       = count($armes);
$nb_pieces      = count($pieces);
$nb_cartes      = $nb_personnages + $nb_armes + $nb_pieces;
$nb_a_distribuer = $nb_cartes - 3;

// Exemple d'hypothèse tiré au hasard
$ex_personnage = $personnages[random_int(0, $nb_personnages - 1)]['nom_personnage'];
$ex_arme       = $armes[random_int(0, $nb_armes - 1)]['nom_arme'];
$ex_piece      = $pieces[random_int(0, $nb_pieces - 1)]['nom_piece'];

$partie_en_cours = isset($_SESSION['joueur_id'], $_SESSION['crime'], $_SESSION['piece_id']);

$sqlite->close();

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="images/icone.png">
    <title>Règles – Projet Cluedo</title>
    <link rel="preload" as="image" href="images/fonds/fond.png">
    <link rel="stylesheet" href="style.css">
</head>
<body class="regles">
<header class="entete">
    <a class="logo" href="index.php">Projet Cluedo</a>
    <nav>
        <a href="index.php">Accueil</a>
        <a href="regles.php" class="actif">Règles</a>
    </nav>
    <?php if ($partie_en_cours): ?>
        <a class="btn-entete" href="jeu.php">Retour au jeu</a>
    <?php endif; ?>
</header>

<main class="container">
    <div class="card vintage-grid">

        <h1>CLUEDO – Règles du jeu</h1>

        <h2>Introduction</h2>
        <p>
            Le docteur Lenoir a été assassiné dans sa villa. Les joueurs
            sont des détectives : ils doivent découvrir <strong>qui</strong>
            est le meurtrier, <strong>avec quelle arme</strong> et
            <strong>dans quelle pièce</strong> le crime a eu lieu.
        </p>

        <h2>1. Le matériel</h2>

        <h3>Les <?= $nb_personnages ?> suspects</h3>
        <ul>
            <?php foreach ($personnages as $p): ?>
                <li><?= h($p['nom_personnage']) ?></li>
            <?php endforeach; ?>
        </ul>

        <h3>Les <?= $nb_armes ?> armes</h3>
        <ul>
            <?php foreach ($armes as $a): ?>
                <li><?= h($a['nom_arme']) ?></li>
            <?php endforeach; ?>
        </ul>

        <h3>Les <?= $nb_pieces ?> pièces de la villa</h3>
        <ul>
            <?php foreach ($pieces as $p): ?>
                <li><?= h($p['nom_piece']) ?></li>
            <?php endforeach; ?>
        </ul>

        <p>
            Cela fait <strong><?= $nb_cartes ?> cartes</strong>
            (<?= $nb_personnages ?> suspects + <?= $nb_armes ?> armes
            + <?= $nb_pieces ?> pièces), plus un carnet de notes par joueur.
        </p>

        <h2>2. Préparation</h2>
        <ol>
            <li>Séparez les cartes en trois paquets : suspects, armes et pièces.</li>
            <li>Mélangez chaque paquet séparément.</li>
            <li>Tirez au hasard <strong>une carte de chaque paquet</strong> et glissez-les, sans les regarder, dans l'enveloppe du crime : ce sont le coupable, l'arme et le lieu du crime.</li>
            <li>Mélangez les <strong><?= $nb_a_distribuer ?> cartes restantes</strong> et distribuez-les une par une, face cachée, à tous les joueurs.</li>
            <li>Chaque joueur regarde ses cartes sans les montrer : ce sont des éléments <em>innocents</em>, qui ne font pas partie du crime.</li>
        </ol>

        <h2>3. Se déplacer dans la villa</h2>
        <p>
            La villa est composée de <?= $nb_pieces ?> pièces reliées entre elles
            par des portes. À son tour, un joueur peut passer dans une pièce
            voisine, c'est-à-dire reliée par une porte à la pièce où il se trouve.
        </p>

        <table class="portes">
            <thead>
                <tr><th>Pièce</th><th>Portes vers</th></tr>
            </thead>
            <tbody>
                <?php foreach ($voisines as $nom => $liste): ?>
                    <tr>
                        <td><strong><?= h($nom) ?></strong></td>
                        <td><?= h(implode(', ', $liste)) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h2>4. Formuler une hypothèse</h2>
        <p>
            Quand un joueur est dans une pièce, il peut formuler une hypothèse
            en nommant :
        </p>
        <ol>
            <li>un suspect ;</li>
            <li>une arme ;</li>
            <li>la pièce <strong>où il se trouve</strong>.</li>
        </ol>
        <p>
            Exemple : « Je pense que <?= h($ex_personnage) ?> a commis le meurtre
            dans la pièce « <?= h($ex_piece) ?> » avec l'arme « <?= h($ex_arme) ?> » ! »
        </p>

        <h3>Interroger les autres joueurs</h3>
        <ol>
            <li>Le joueur placé à gauche de celui qui fait l'hypothèse répond en premier.</li>
            <li>S'il possède au moins une des trois cartes citées, il doit en montrer une, <strong>en secret</strong>, à celui qui a fait l'hypothèse.</li>
            <li>S'il n'en possède aucune, c'est au joueur suivant de répondre, et ainsi de suite.</li>
            <li>L'interrogatoire s'arrête dès qu'une carte a été montrée, ou quand tous les joueurs ont répondu sans pouvoir montrer de carte.</li>
        </ol>
        <p>
            Si personne ne peut montrer de carte, et que tu n'en as aucune toi-même,
            les trois éléments cités sont peut-être dans l'enveloppe du crime!
        </p>

        <h2>5. Prendre des notes</h2>
        <p>Chaque joueur note dans son carnet :</p>
        <ul>
            <li>les suspects, armes et pièces qu'il a vus dans sa main ou qu'on lui a montrés (ils sont innocents) ;</li>
            <li>les réponses données aux hypothèses des autres joueurs ;</li>
            <li>ce qu'il en déduit, par élimination, sur le contenu de l'enveloppe.</li>
        </ul>

        <h2>6. L'accusation finale</h2>
        <p>
            Quand un joueur pense connaître la solution, il peut l'annoncer à son tour :
            le suspect, l'arme et la pièce. Il regarde alors, sans les montrer, les trois
            cartes de l'enveloppe.
        </p>

        <h3>Si l'accusation est correcte</h3>
        <p>Il révèle les trois cartes et remporte la partie.</p>

        <h3>Si l'accusation est fausse</h3>
        <p>
            Il est éliminé : il ne peut plus se déplacer ni accuser. Il reste
            cependant dans la partie pour répondre aux hypothèses des autres joueurs.
        </p>

        <h2>7. Fin de la partie</h2>
        <p>
            Le premier joueur qui désigne correctement le suspect, l'arme et la pièce
            du crime gagne la partie.
        </p>

        <h2>Bonne enquête à tous les détectives !</h2>
    </div>
</main>

</body>
</html>
