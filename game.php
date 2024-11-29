<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Connexion à la base de données pour récupérer les informations du joueur et ses cartes
include('db_connect.php');

// Démarrer la session si nécessaire
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id']; // ID de l'utilisateur connecté

// Récupérer les cartes du joueur
$sql = "
    SELECT c.* 
    FROM cards c
    JOIN deck_cards dc ON c.id = dc.card_id
    JOIN decks d ON dc.deck_id = d.deck_id
    WHERE d.user_id = :user_id
    LIMIT 5";  // Limiter le nombre de cartes retournées si nécessaire
$stmt = $conn->prepare($sql);
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$cards = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arcane - Jeu</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="styles_game.css">
</head>
<body>
    <header>
        <h1>Arcane Breaker</h1>
        <nav>
            <ul>
                <li><a href="javascript:void(0);" onclick="window.close()">Fermer</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <!-- Suppression du game-container pour que le jeu occupe toute la page -->
        <h2>Affrontement contre le Bot</h2>

        <!-- Section des cartes du joueur -->
        <section class="player-deck">
            <h3>Votre Deck</h3>
            <div class="card-container">
                <?php
                foreach ($cards as $card) {
                    echo '<div class="card">';
                    echo '<div class="card-front">';
                    echo '<img src="' . $card['image'] . '" alt="' . $card['name'] . '">';
                    echo '<h4>' . $card['name'] . '</h4>';
                    echo '</div>';
                    echo '<div class="card-back">';
                    echo '<p><strong>PV:</strong> ' . $card['health_points'] . '</p>';
                    echo '<p><strong>Attaque:</strong> ' . $card['attack'] . '</p>';
                    echo '<p><strong>Défense:</strong> ' . $card['defense'] . '</p>';
                    echo '<p><strong>Capacité Spéciale:</strong> ' . $card['special_ability'] . '</p>';
                    echo '</div>';
                    echo '</div>';
                }
                ?>
            </div>
        </section>

        <!-- Section de combat -->
        <section class="game-play">
            <h3>Votre Tour</h3>
            <button id="attack">Attaquer</button>
            <button id="defend">Défendre</button>
            <button id="special-ability">Utiliser Capacité Spéciale</button>
        </section>

        <section class="game-status">
            <h3>État du Jeu</h3>
            <p><strong>PV Bot :</strong> <span id="bot-pv">100</span></p>
            <p><strong>Vos PV :</strong> <span id="player-pv">100</span></p>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 Arcane Card Game. Tous droits réservés.</p>
    </footer>

    <script>
        // Lancer la logique de jeu ici, comme les attaques, la défense, etc.
        document.getElementById('attack').addEventListener('click', function() {
            // Code pour attaquer...
        });

        document.getElementById('defend').addEventListener('click', function() {
            // Code pour défendre...
        });

        document.getElementById('special-ability').addEventListener('click', function() {
            // Code pour utiliser la capacité spéciale...
        });
    </script>
</body>
</html>
