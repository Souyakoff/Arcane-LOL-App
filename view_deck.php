<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Connexion à la base de données
include 'db_connect.php'; // Assurez-vous que ce fichier contient les informations nécessaires

session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Rediriger vers la page de login si non connecté
    exit();
}

// Vérifier si l'ID du deck est passé dans l'URL
if (!isset($_GET['deck_id'])) {
    echo "Deck non trouvé.";
    exit();
}

// Récupérer l'ID du deck depuis l'URL
$deck_id = $_GET['deck_id'];

// Récupérer les informations du deck
$query_deck = "SELECT * FROM decks WHERE deck_id = ? AND user_id = ?";
$stmt_deck = $conn->prepare($query_deck);
$stmt_deck->execute([$deck_id, $_SESSION['user_id']]);
$deck = $stmt_deck->fetch();

// Vérifier si le deck existe et appartient à l'utilisateur
if (!$deck) {
    echo "Ce deck n'existe pas ou vous n'avez pas accès à ce deck.";
    exit();
}

// Récupérer les cartes associées au deck
$query_cards_in_deck = "
    SELECT cards.* 
    FROM cards 
    INNER JOIN deck_cards ON cards.id = deck_cards.card_id 
    WHERE deck_cards.deck_id = ?";
$stmt_cards_in_deck = $conn->prepare($query_cards_in_deck);
$stmt_cards_in_deck->execute([$deck_id]);
$cards_in_deck = $stmt_cards_in_deck->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voir le Deck - Mon jeu de cartes</title>
    <link rel="stylesheet" href="styles.css"> <!-- Assurez-vous que ce fichier existe -->
    <link rel="stylesheet" href="styles_viewdeck.css">
</head>
<body>
    <header>
        <h1>Mon Deck : <?php echo htmlspecialchars($deck['deck_name']); ?></h1>
        <nav>
            <ul>
                <li><a href="index.php">Accueil</a></li>
                <li><a href="profile.php">Mon Profil</a></li>
                <li><a id="play" href="javascript:void(0);" onclick="openGameWindow()">Jouer</a></li> <!-- Nouveau lien "Jouer" -->
                <li><a href="deck.php">Mes Decks</a></li>
                <li><a href="logout.php">Se déconnecter</a></li>
            </ul>
        </nav>
    </header>

    <div class="container">
        <section class="deck-details">
            <h2>Détails du Deck</h2>
            <p><strong>Nom du Deck :</strong> <?php echo htmlspecialchars($deck['deck_name']); ?></p>

            <h3>Cartes dans ce Deck</h3>

            <?php if (count($cards_in_deck) > 0): ?>
                <div class="card-list">
                    <?php foreach ($cards_in_deck as $card): ?>
                        <div class="card-item">
                            <img src="<?php echo htmlspecialchars($card['image']); ?>" alt="Image de <?php echo htmlspecialchars($card['name']); ?>" class="card-image">
                            <div class="card-details">
                                <h4><?php echo htmlspecialchars($card['name']); ?></h4>
                                <p><strong>Points de Vie :</strong> <?php echo htmlspecialchars($card['health_points']); ?></p>
                                <p><strong>Attaque :</strong> <?php echo htmlspecialchars($card['attack']); ?></p>
                                <p><strong>Défense :</strong> <?php echo htmlspecialchars($card['defense']); ?></p>
                                <p><strong>Capacité Spéciale :</strong> <?php echo htmlspecialchars($card['special_ability']); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p>Ce deck ne contient aucune carte pour le moment.</p>
            <?php endif; ?>
        </section>
    </div>
</body>
</html>
