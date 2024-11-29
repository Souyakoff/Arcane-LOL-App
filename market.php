<?php include('header.php'); ?>

<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Connexion à la base de données
require 'db_connect.php';

// Récupérer l'ID de l'utilisateur depuis la session
session_start();
if (!isset($_SESSION['user_id'])) {
    // Rediriger vers la page de connexion si l'utilisateur n'est pas connecté
    header("Location: login.php");
    exit();
}
$userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 1; // Utiliser 1 si pas d'utilisateur connecté

// Récupération des informations de l'utilisateur
$userQuery = $conn->prepare("SELECT * FROM users WHERE id = :id");
$userQuery->execute(['id' => $userId]);
$user = $userQuery->fetch(PDO::FETCH_ASSOC);

// Vérifier si l'utilisateur existe
if (!$user) {
    die("Utilisateur introuvable");
}

// Récupération des cartes disponibles
$query = $conn->query("SELECT * FROM cards");
$cards = $query->fetchAll(PDO::FETCH_ASSOC);

// Traitement d'un achat
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['card_id'])) {
    $cardId = intval($_POST['card_id']); // On s'assure que l'ID est un entier valide

    // Vérification de l'existence de la carte
    $priceQuery = $conn->prepare("SELECT id, price FROM cards WHERE id = :id");
    $priceQuery->execute(['id' => $cardId]);
    $card = $priceQuery->fetch(PDO::FETCH_ASSOC);

    if ($card) {
        if ($user['shards'] >= $card['price']) {
            // Déduire le prix des shards de l'utilisateur
            $updateUser = $conn->prepare("UPDATE users SET shards = shards - :price WHERE id = :id");
            $updateUser->execute(['price' => $card['price'], 'id' => $userId]);

            // Confirmer l'achat
            echo "<p style='color: green;'>Achat réussi : {$card['price']} shards déduits !</p>";
        } else {
            echo "<p style='color: red;'>Fonds insuffisants.</p>";
        }
    } else {
        echo "<p style='color: red;'>Carte introuvable.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="styles_market.css">
    <title>Marché des Cartes</title>
</head>
<header>
        <h1>Arcane Breaker</h1>
        <nav>
            <ul>
                <li><a href="index.php">Accueil</a></li>
                <li><a id="play" href="javascript:void(0);" onclick="openGameWindow()">Jouer</a></li> <!-- Nouveau lien "Jouer" -->
                <li><a href="deck.php">Deck</a></li>
                <li><a href="logout.php">Se déconnecter</a></li>
            </ul>
            <?php if ($user_id): ?>
                <!-- Afficher l'image de profil à droite de la navbar si l'utilisateur est connecté -->
                <div class="profile-container">
                    <a href="profile.php">
                        <img src="<?php echo $profile_picture; ?>" alt="Photo de profil">
                    </a>
                    <p id="user_currency"><?= htmlspecialchars($user['shards']); ?> <strong id="user_shards">shards</strong></p>
                </div>
            <?php endif; ?>
        </nav>
    </header>
<body>
    <h1>Marché des Cartes</h1>
    <section class="cards">
    <h2>Cartes Disponibles</h2>
    <ul class="card-list">
        <?php foreach ($cards as $card): ?>
            <li class="card-item" data-id="<?php echo htmlspecialchars($card['id']); ?>">
            <div class="card" onclick="openPopup(<?php echo htmlspecialchars($card['id']); ?>, '<?php echo htmlspecialchars($card['name']); ?>')">
                    <div class="card-front" style="background-image: url('<?php echo htmlspecialchars($card['image']); ?>');">
                        <h4><?php echo htmlspecialchars($card['name']); ?></h4>
                        <p class="card-price"><?php echo htmlspecialchars($card['price']); ?> Shards</p> <!-- Affichage du prix -->
                    </div>
                    <!-- Face arrière de la carte (détails) -->
                    <div class="card-back" style="background-image: url('<?php echo htmlspecialchars($card['city_image']); ?>');">
                        <h4><?php echo htmlspecialchars($card['name']); ?></h4>
                        <p><strong>Points de Vie :</strong> <?php echo htmlspecialchars($card['health_points']); ?></p>
                        <p><strong>Attaque :</strong> <?php echo htmlspecialchars($card['attack']); ?></p>
                        <p><strong>Défense :</strong> <?php echo htmlspecialchars($card['defense']); ?></p>
                        <p><strong>Capacité Spéciale :</strong> <?php echo htmlspecialchars($card['special_ability']); ?></p>
                    </div>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
</section>
<div id="popup" class="popup">
    <div class="popup-content">
        <span class="close-btn" onclick="closePopup()">&times;</span>
        <h3 id="popup-title">Aperçu de la carte</h3>
        <div id="popup-card-display" class="popup-card-display"></div>
        
        <div id="popup-price-info" class="popup-price-info"></div> <!-- Affichage du prix -->
        
        <!-- Affichage du bouton d'achat -->
        <button id="buy-button" class="buy-button" onclick="buyCard()" style="display: none;">Acheter la carte</button>

        <p id="insufficient-funds" style="color: red; display: none;">Vous n'avez pas assez de fonds pour acheter cette carte.</p>
    </div>
</div>

<script>
function openPopup(cardId, cardName, cardPrice) {
    const popup = document.getElementById('popup');
    const popupTitle = document.getElementById('popup-title');
    const popupCardId = document.getElementById('popup-card-id');
    const popupCardDisplay = document.getElementById('popup-card-display');
    const popupPriceInfo = document.getElementById('popup-price-info');
    const buyButton = document.getElementById('buy-button');
    const insufficientFunds = document.getElementById('insufficient-funds');

    // Met à jour les informations de la popup
    popupTitle.textContent = `Aperçu de la carte "${cardName}"`;
    popupCardId.value = cardId;  // ID de la carte sélectionnée
    popupPriceInfo.innerHTML = `<p>Prix : ${cardPrice} Shards</p>`; // Affiche le prix

    // Récupère les détails de la carte à afficher (exemple de données dynamiques)
    const card = document.querySelector(`.card-item[data-id='${cardId}']`);
    const cardHTML = card.innerHTML;

    // Affiche la carte dans le modal
    popupCardDisplay.innerHTML = `<div class="card-item-popup">${cardHTML}</div>`;

    // Vérifie si l'utilisateur a suffisamment de fonds
    const userFunds = <?php echo $user_funds; ?>; // Variable PHP contenant les fonds de l'utilisateur
    if (userFunds >= cardPrice) {
        buyButton.style.display = 'inline-block'; // Affiche le bouton d'achat
        insufficientFunds.style.display = 'none'; // Cache le message d'insuffisance de fonds
    } else {
        buyButton.style.display = 'none'; // Cache le bouton d'achat
        insufficientFunds.style.display = 'block'; // Affiche le message d'insuffisance de fonds
    }

    // Affiche la popup
    popup.style.display = 'block';
}

// Fonction pour acheter la carte
function buyCard() {
    const cardId = document.getElementById('popup-card-id').value;
    const userFunds = <?php echo $user_funds; ?>; // Fonds de l'utilisateur en Shards
    const cardPrice = parseInt(document.getElementById('popup-price-info').innerText.replace('Prix : ', '').replace(' Shards', ''));

    // Vérifie si l'utilisateur a assez de fonds
    if (userFunds >= cardPrice) {
        // Logique d'achat (déduire les fonds et ajouter la carte à l'inventaire)
        alert("Carte achetée avec succès !");
        // Vous pouvez faire une requête AJAX ici pour mettre à jour les fonds et l'inventaire

        // Fermer la popup après l'achat
        closePopup();
    } else {
        alert("Fonds insuffisants pour acheter cette carte.");
    }
}

// Fonction pour fermer la popup
function closePopup() {
    const popup = document.getElementById('popup');
    popup.style.display = 'none';
}
</script>
<script>
    function openGameWindow() {
        window.open('game.php', 'GameWindow', 'width=800,height=600');  // Ouvre le jeu dans une nouvelle fenêtre
    }
</script>
</body>
</html>
