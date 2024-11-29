<?php include('header.php'); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arcane | Breaker</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="styles_index.css">
</head>
<body>
    <header>
        <h1>Arcane Breaker</h1>
        <nav>
            <ul>
                <li><a href="index.php">Accueil</a></li>
                <?php if ($user_id): ?>
                <li><a id="play" href="javascript:void(0);" onclick="openGameWindow()">Jouer</a></li> <!-- Nouveau lien "Jouer" -->
                <?php endif; ?>
                <li><a href="deck.php">Deck</a></li>
                <li><a href="market.php">Boutique</a></li>
                <?php if ($user_id): ?>
                    <li><a href="logout.php">Se déconnecter</a></li>
                <?php else: ?>
                    <li><a href="login.php">Connexion</a></li>
                <?php endif; ?>
            </ul>
            <?php if ($user_id): ?>
                <!-- Afficher l'image de profil à droite de la navbar si l'utilisateur est connecté -->
                <div class="profile-container">
                    <a href="profile.php">
                        <img src="<?php echo $profile_picture; ?>" alt="Photo de profil">
                    </a>
                </div>
            <?php endif; ?>
        </nav>
    </header>
    
    <main>
    <section class="intro">
    <div class="logo">
        <img src="images/Arcanebg-removebg-preview.png" alt="Logo Arcane">
    </div>
</section>


<section class="game-rules">
    <h2>Les Règles du Jeu</h2>
    <div class="rule-container">
        <div class="rule-text">
            <h3>1. Composez votre Deck</h3>
            <p>Créez votre deck avec des cartes représentant vos personnages, attaques, et capacités spéciales. Votre stratégie commence ici.</p>
        </div>
        <div class="rule-image">
            <img src="images/deck-example.jpg" alt="Exemple de Deck">
        </div>
    </div>

    <div class="rule-container">
    <div class="rule-image">
            <img src="images/roles-example.jpg" alt="Exemple de Rôles">
        </div>
        <div class="rule-text">
            <h3>2. Choisissez votre Rôle</h3>
            <p>Le jeu se joue entre deux rôles : l'attaquant tente de réduire les PV de l'adversaire, tandis que le défenseur protège son arcane.</p>
        </div>
    </div>

    <div class="rule-container">
        <div class="rule-text">
            <h3>3. Attaquez et Défendez-vous</h3>
            <p>Jouez à tour de rôle en choisissant une carte pour attaquer ou défendre. Utilisez des stratégies pour renverser la partie.</p>
        </div>
        <div class="rule-image">
            <img src="images/attack-example.jpg" alt="Exemple d'Attaque">
        </div>
    </div>

    <div class="rule-container">
                <div class="rule-image">
            <img src="images/victory-example.jpg" alt="Exemple de Victoire">
        </div>
        <div class="rule-text">
            <h3>4. Gagnez la Partie</h3>
            <p>Réduisez les PV de l'arcane de votre adversaire à zéro pour remporter la victoire. Protégez votre propre arcane à tout prix.</p>
        </div>
    </div>
</section>


<section class="cards">
    <h2>Cartes Disponibles</h2>
    <ul class="card-list">
        <?php
        // Inclure le fichier de connexion à la base de données
        include('db_connect.php');

        // Récupération des cartes depuis la base de données
        $sql = "SELECT * FROM cards ORDER BY RAND() LIMIT 7";
        $stmt = $conn->prepare($sql);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                ?>
                <li class="card-item" data-id="<?php echo htmlspecialchars($row['id']); ?>">
                    <div class="card" onclick="openPopup(<?php echo htmlspecialchars($row['id']); ?>, '<?php echo htmlspecialchars($row['name']); ?>')">
                        <!-- Face avant de la carte -->
                        <div class="card-front" style="background-image: url('<?php echo htmlspecialchars($row['image']); ?>');">
                            <h4><?php echo htmlspecialchars($row['name']); ?></h4>
                        </div>
                        <!-- Face arrière de la carte -->
                        <div class="card-back" style="background-image: url('<?php echo htmlspecialchars($row['city_image']); ?>');">
                            <h4><?php echo htmlspecialchars($row['name']); ?></h4>
                            <p><strong>Points de Vie :</strong> <?php echo htmlspecialchars($row['health_points']); ?></p>
                            <p><strong>Attaque :</strong> <?php echo htmlspecialchars($row['attack']); ?></p>
                            <p><strong>Défense :</strong> <?php echo htmlspecialchars($row['defense']); ?></p>
                            <p><strong>Capacité Spéciale :</strong> <?php echo htmlspecialchars($row['special_ability']); ?></p>
                        </div>
                    </div>
                </li>
                <?php
            }
        } else {
            echo "<p>Aucune carte trouvée.</p>";
        }

        $conn = null; // Fermer la connexion PDO
        ?>
    </ul>
</section>


        <section class="call-to-action">
            <h2>Prêt à jouer ?</h2>
            <?php if ($user_id): ?>
                <p>Alors plonger dés maintenant dans l'univers d'Arcane Breaker !</p>
                <a id="game-launch" href="javascript:void(0);" class="cta-button" onclick="openGameWindow()">Jouer</a>
                <?php else: ?>
                <p>Inscrivez-vous maintenant et commencez à créer votre deck de cartes pour plonger dans l'univers d'Arcane Breaker !</p>
                <a href="register.php" class="cta-button">S'inscrire</a>
                <h2>Vous avez deja un compte ?</h2>
                <p>Alors connectez-vous et plonger dés maintenant dans l'univers d'Arcane Breaker !</p>
                <a href="login.php" class="cta-button">Connexion</a>
                <?php endif; ?>
        </section>
    </main>
    
    <footer>
        <p>&copy; 2024 Arcane Card Game. Tous droits réservés.</p>
    </footer>
    <script>
    function openGameWindow() {
        window.open('game.php', 'GameWindow', 'width=800,height=600');  // Ouvre le jeu dans une nouvelle fenêtre
    }
</script>

</body>
</html>

