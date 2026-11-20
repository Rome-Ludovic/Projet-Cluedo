<?php
session_start();
 
$db = new PDO('sqlite:cluedo.db');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
 
$personnages = $db->query("SELECT * FROM personnages")->fetchAll(PDO::FETCH_ASSOC);
$armes = $db->query("SELECT * FROM armes")->fetchAll(PDO::FETCH_ASSOC);
$pieces = $db->query("SELECT * FROM pieces")->fetchAll(PDO::FETCH_ASSOC);
 
if (isset($_GET['nouvelle_partie'])) {
    unset($_SESSION['crime']);
    header('Location: jeu.php');
    exit;
}
 
if (!isset($_SESSION['crime'])) {
    $person_coupable = $personnages[random_int(0, count($personnages) - 1)];
    $arme_coupable   = $armes[random_int(0, count($armes) - 1)];
    $piece_coupable  = $pieces[random_int(0, count($pieces) - 1)];
 
    $_SESSION['crime'] = [
        $person_coupable['nom_personnage'],
        $arme_coupable['nom_arme'],
        $piece_coupable['nom_piece']
    ];
}
 
$crime = $_SESSION['crime'];
$enquete = [];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
	<link rel="icon" type="image/png" href="images/icone.png">
    <meta charset="UTF-8">
    <title>Projet Cluedo</title>
    <link rel="stylesheet" href="style.css">
</head>
<?php
// header.php
$page = basename($_SERVER['PHP_SELF']);
?>
<header class="entete">
    <a class="logo" href="index.php">Projet Cluedo</a>

    <nav>
        <a href="index.php" class="<?= $page === 'index.php' ? 'actif' : '' ?>">Accueil</a>
        <a href="regles.php" class="<?= $page === 'regles.php' ? 'actif' : '' ?>">Règles</a>
    </nav>
</header>

<?php

if ($page === 'index.php')
{

    // Documentation officielle de PHP pour SQLite :
    // https://www.php.net/manual/en/book.sqlite3.php


    /* =====================================================
       1. PARAMÈTRES
       ===================================================== */

    // Nom du fichier contenant la base de données SQLite.
    $bdd_fichier = 'cluedo.db';

    // Nom de la pièce dont on souhaite connaître les pièces adjacentes.
    $piece = 'Salle de billard';


    /* =====================================================
       2. CONNEXION À LA BASE DE DONNÉES
       ===================================================== */

    // On ouvre la base de données SQLite.
    // Si le fichier n'existe pas, SQLite peut le créer.
    $sqlite = new SQLite3($bdd_fichier);


    /* =====================================================
       3. CONSTRUCTION DE LA REQUÊTE SQL
       ===================================================== */

    // On sélectionne l'identifiant et le nom des pièces adjacentes.
    // Le nom adj est un alias qui permet de distinguer
    // les pièces voisines de la pièce de départ.
    $sql = 'SELECT adj.id_piece, adj.nom_piece ';

    // On relie la table pieces à la table portes.
    // Une porte peut relier deux pièces :
    // - id_piece1 : identifiant de la première pièce ;
    // - id_piece2 : identifiant de la deuxième pièce.
    //
    // Le OR permet de retrouver les portes pour lesquelles
    // la pièce recherchée est située d'un côté ou de l'autre.
    $sql .= 'FROM pieces INNER JOIN portes ';
    $sql .= 'ON portes.id_piece1 = pieces.id_piece ';
    $sql .= 'OR portes.id_piece2 = pieces.id_piece ';

    // On effectue une deuxième jointure avec la table pieces.
    // Cette fois, on utilise l'alias adj pour représenter
    // la pièce située de l'autre côté de la porte.
    //
    // On vérifie si cette pièce correspond à id_piece1
    // ou à id_piece2 de la table portes.
    $sql .= 'INNER JOIN pieces AS adj ';
    $sql .= 'ON portes.id_piece1 = adj.id_piece ';
    $sql .= 'OR portes.id_piece2 = adj.id_piece ';

    // On applique deux conditions :
    //
    // 1. adj.id_piece != pieces.id_piece :
    //    On exclut la pièce de départ pour ne pas la retrouver
    //    dans la liste de ses propres pièces adjacentes.
    //
    // 2. pieces.nom_piece LIKE :piece :
    //    On ne conserve que les résultats correspondant
    //    au nom de la pièce indiqué dans le paramètre.
    //
    // :piece est un paramètre qui sera remplacé plus bas.
    $sql .= 'WHERE adj.id_piece != pieces.id_piece ';
    $sql .= 'AND pieces.nom_piece LIKE :piece';


    /* =====================================================
       4. PRÉPARATION DE LA REQUÊTE ET DES PARAMÈTRES
       ===================================================== */

    // On prépare la requête SQL.
    // Cela permet notamment d'utiliser des paramètres
    // sans insérer directement les valeurs dans la requête.
    $requete = $sqlite->prepare($sql);

    // On associe la valeur de $piece au paramètre :piece.
    // SQLITE3_TEXT indique que la valeur est de type texte.
    //
    // Cela permet également de limiter les risques
    // d'injection SQL.
    $requete->bindValue(':piece', $piece, SQLITE3_TEXT);


    /* =====================================================
       5. EXÉCUTION DE LA REQUÊTE
       ===================================================== */

    // On exécute la requête SQL.
    // Le résultat contient les pièces correspondant
    // aux conditions définies dans la requête.
    $result = $requete->execute();


    /* =====================================================
       6. GÉNÉRATION DE LA PAGE HTML
       ===================================================== */

    // On indique au navigateur que le document est une page HTML5.
    // \n permet d'ajouter un saut de ligne dans le code HTML généré.
    echo "<!DOCTYPE html>\n";

    // On définit la langue de la page en français.
    // On précise également l'encodage UTF-8 pour gérer les accents.
    // Les guillemets doivent être précédés de \ car on utilise
    // des guillemets doubles pour délimiter la chaîne PHP.
    echo "<html lang=\"fr\"><head><meta charset=\"UTF-8\">\n";

    // On définit le titre de la page affiché dans l'onglet du navigateur.
    // La variable $piece est remplacée par sa valeur.
    echo "<title>Pièces adjacentes à $piece</title>\n";

    // On ferme la partie head de la page HTML.
    echo "</head>\n";


    /* =====================================================
       7. AFFICHAGE DES PIÈCES ADJACENTES
       ===================================================== */

    // On ouvre le corps de la page HTML.
    echo "<body>\n";

    // On affiche un titre de niveau 1 contenant le nom de la pièce.
    echo "<h1>Pièces adjacentes à $piece</h1>\n";

    // On ouvre une liste HTML non ordonnée.
    // Chaque pièce sera affichée sous forme d'un élément <li>.
    echo "<ul>";

    // On parcourt les résultats de la requête SQL.
    //
    // fetchArray(SQLITE3_ASSOC) récupère une ligne à chaque tour
    // de boucle sous forme de tableau associatif.
    //
    // Les colonnes sont accessibles grâce à leur nom :
    // $adj['id_piece'] et $adj['nom_piece'].
    //
    // La boucle s'arrête automatiquement lorsqu'il n'y a
    // plus de résultats à récupérer.
    while ($adj = $result->fetchArray(SQLITE3_ASSOC)) {

        // On affiche le nom de la pièce dans un élément de liste HTML.
        echo "<li>" . $adj['nom_piece'] . "</li>\n";
    }

    // On ferme la liste HTML.
    echo "</ul>";

    // On ferme le corps de la page.
    echo "</body>\n";

    // On ferme le document HTML.
    echo "</html>\n";


    /* =====================================================
       8. FERMETURE DE LA CONNEXION
       ===================================================== */

    // On ferme la connexion à la base de données SQLite.
    // C'est une bonne pratique pour libérer les ressources utilisées.
    $sqlite->close();

} 

else 
{
	echo "
		<h1>CLUEDO – Règles du jeu</h1>

		<h2>Introduction</h2>
		<p>
			Le docteur Lenoir a été assassiné dans sa villa.
			Les joueurs doivent résoudre le mystère en découvrant
			le meurtrier, l'arme utilisée et le lieu du crime.
		</p>

		<h2>1. Le matériel</h2>

		<h3>Les 6 personnages</h3>
		<ul>
			<li>Colonel Moutarde</li>
			<li>Docteur Olive</li>
			<li>Professeur Violet</li>
			<li>Madame Pervenche</li>
			<li>Mademoiselle Rose</li>
			<li>Madame Leblanc</li>
		</ul>

		<h3>Les 6 armes</h3>
		<ul>
			<li>Poignard</li>
			<li>Chandelier</li>
			<li>Revolver</li>
			<li>Corde</li>
			<li>Matraque</li>
			<li>Clé anglaise</li>
		</ul>

		<h3>Les autres éléments</h3>
		<ul>
			<li>21 cartes</li>
			<li>2 dés</li>
			<li>1 étui secret</li>
			<li>1 carnet de notes par joueur</li>
		</ul>

		<h2>2. Préparation du jeu</h2>
		<ol>
			<li>Placez les six pions sur leurs cases de départ.</li>
			<li>Placez l'étui secret sur la case X.</li>
			<li>Séparez les cartes en trois catégories : suspects, armes et lieux.</li>
			<li>Mélangez séparément les trois paquets.</li>
			<li>Placez secrètement une carte de chaque catégorie dans l'étui.</li>
			<li>Mélangez les cartes restantes et distribuez-les aux joueurs.</li>
		</ol>

		<h2>3. Distribution des cartes</h2>
		<ol>
			<li>Chaque joueur lance les dés.</li>
			<li>Le joueur ayant obtenu le résultat le plus élevé distribue les cartes.</li>
			<li>Les cartes sont distribuées une par une, face cachée.</li>
			<li>Chaque joueur garde ses cartes secrètes.</li>
		</ol>

		<h2>4. Les déplacements</h2>
		<ul>
			<li>Le joueur lance les deux dés et avance du nombre de cases indiqué.</li>
			<li>Les déplacements se font horizontalement ou verticalement.</li>
			<li>Les déplacements en diagonale sont interdits.</li>
			<li>Il est interdit de traverser une case occupée.</li>
			<li>Les passages secrets permettent de rejoindre certaines pièces.</li>
		</ul>

		<h2>5. Formuler une hypothèse</h2>
		<p>
			Lorsqu'un joueur entre dans une pièce, il peut formuler
			une hypothèse en désignant :
		</p>
		<ol>
			<li>Un suspect.</li>
			<li>Une arme.</li>
			<li>La pièce dans laquelle il se trouve.</li>
		</ol>

		<p>
			Exemple : « Je pense que Madame Leblanc a commis le meurtre
			dans le Bureau avec le Chandelier ! »
		</p>

		<h3>Interroger les autres joueurs</h3>
		<ol>
			<li>Le joueur situé à gauche est interrogé en premier.</li>
			<li>S'il possède une carte correspondant à l'hypothèse,
				il doit en montrer une secrètement.</li>
			<li>S'il ne possède aucune carte correspondante,
				le joueur suivant est interrogé.</li>
			<li>Les interrogatoires continuent jusqu'à ce qu'une carte
				soit montrée ou que tous les joueurs aient répondu.</li>
		</ol>

		<h2>6. Prendre des notes</h2>
		<p>Chaque joueur doit noter les informations obtenues :</p>
		<ul>
			<li>Les suspects éliminés.</li>
			<li>Les armes déjà identifiées.</li>
			<li>Les pièces qui peuvent être éliminées.</li>
			<li>Les cartes montrées par les autres joueurs.</li>
		</ul>

		<h2>7. L'accusation finale</h2>
		<p>
			Lorsqu'un joueur pense avoir découvert la solution,
			il peut formuler une accusation.
		</p>

		<ol>
			<li>Il annonce le nom du meurtrier.</li>
			<li>Il annonce l'arme utilisée.</li>
			<li>Il annonce le lieu du crime.</li>
			<li>Il vérifie les trois cartes dans l'étui secret.</li>
		</ol>

		<h3>Si l'accusation est correcte</h3>
		<p>Le joueur révèle les trois cartes et remporte la partie.</p>

		<h3>Si l'accusation est fausse</h3>
		<p>
			Le joueur ne peut plus se déplacer, interroger ni formuler
			d'accusation. Il reste cependant dans la partie pour
			répondre aux hypothèses des autres joueurs.
		</p>

		<h2>8. Fin de la partie</h2>
		<p>
			Le premier joueur qui identifie correctement le suspect,
			l'arme et le lieu du crime remporte la partie.
		</p>

		<h2>Bonne enquête à tous les détectives !</h2>
		";
}
?>
