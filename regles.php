<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="images/icone.png">
    <title>Règles – Projet Cluedo</title>
    <link rel="stylesheet" href="style.css">
    <style>body { background-image: url("images/fonds/regles.png"); }</style>
</head>
<body>
<header class="entete">
    <a class="logo" href="index.php">Projet Cluedo</a>
    <nav>
        <a href="index.php">Accueil</a>
        <a href="regles.php" class="actif">Règles</a>
    </nav>
</header>

<main class="container">
    <div class="card">

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
    </div>
</main>

</body>
</html>
