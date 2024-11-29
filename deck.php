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

// Récupérer les decks de l'utilisateur connecté
$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM decks WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->execute([$user_id]);
$decks = $stmt->fetchAll();

// Récupérer toutes les cartes disponibles
$query_cards = "SELECT * FROM cards";
$stmt_cards = $conn->prepare($query_cards);
$stmt_cards->execute();
$cards = $stmt_cards->fetchAll();

// Ajouter un nouveau deck
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create_deck'])) {
    // Assurez-vous que le formulaire a bien envoyé les données
    $deck_name = htmlspecialchars($_POST['deck_name']);
    
    // Vérifiez si le nom du deck n'est pas vide
    if (!empty($deck_name)) {
        $query_insert = "INSERT INTO decks (user_id, deck_name) VALUES (?, ?)";
        $stmt_insert = $conn->prepare($query_insert);
        $stmt_insert->execute([$user_id, $deck_name]);

        // Assurez-vous que la redirection a bien lieu après l'exécution de la requête
        header("Location: deck.php"); // Recharger la page après la création du deck
        exit();
    } else {
        echo "<p>Le nom du deck ne peut pas être vide.</p>";
    }
}
?>
<?php include('header.php'); ?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arcane | Breaker - Mes Decks</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="styles_deck.css">
</head>
<body>
    <header>
        <h1>Mes Decks</h1>
        <nav>
            <ul>
               <li><a href="index.php">Accueil</a></li>
               <li><a id="play" href="javascript:void(0);" onclick="openGameWindow()">Jouer</a></li> <!-- Nouveau lien "Jouer" -->
                <li><a href="market.php">Boutique</a></li>
                <li><a href="logout.php">Se déconnecter</a></li>
            </ul>
            <?php if ($user_id): ?>
                <!-- Afficher l'image de profil à droite de la navbar si l'utilisateur est connecté -->
                <div class="profile-container">
                    <a href="profile.php">
                        <img src="<?php echo $profile_picture; ?>" alt="Photo de profil">
                    </a>
                </div>
            <?php endif; ?>
    </header>

    <div= class="container">
    <section class="decks">
    <h2>Mes Decks</h2>
    <?php if (count($decks) > 0): ?>
    <ul class="deck-list">
        <?php foreach ($decks as $deck): ?>
            <li class="deck-item">
                <div class="deck-card">
                    <h3><?php echo htmlspecialchars($deck['deck_name']); ?></h3>
                    <a href="view_deck.php?deck_id=<?php echo $deck['deck_id']; ?>" class="btn-view">Voir le Deck</a>
                    <!-- Bouton de suppression -->
                    <form action="delete_deck.php" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce deck ?');">
                        <input type="hidden" name="deck_id" value="<?php echo $deck['deck_id']; ?>">
                        <button type="submit" name="delete_deck" class="btn-delete">Supprimer le Deck</button>
                    </form>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
<?php else: ?>
    <p>Vous n'avez encore créé aucun deck. Créez-en un maintenant !</p>
<?php endif; ?>


    <form action="deck.php" method="POST" class="form-create-deck">
        <label for="deck_name">Nom du deck :</label>
        <input type="text" id="deck_name" name="deck_name" required>
        <button type="submit" name="create_deck" class="btn-create">Créer le Deck</button>
    </form>
</section>


<section class="cards">
    <h2>Cartes Disponibles</h2>
    <ul class="card-list">
        <?php foreach ($cards as $card): ?>
            <li class="card-item" data-id="<?php echo htmlspecialchars($card['id']); ?>">
                <div class="card" onclick="openPopup(<?php echo htmlspecialchars($card['id']); ?>, '<?php echo htmlspecialchars($card['name']); ?>')">
                    <!-- Face avant de la carte -->
                    <div class="card-front" style="background-image: url('<?php echo htmlspecialchars($card['image']); ?>');">
                        <h4><?php echo htmlspecialchars($card['name']); ?></h4>
                        <!-- L'image est maintenant gérée par le background CSS -->
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
</div>
<!--Popup modale-->
<div id="popup" class="popup">
    <div class="popup-content">
        <span class="close-btn" onclick="closePopup()">&times;</span>
        <h3 id="popup-title">Ajouter la carte à un deck</h3>
        <div id="popup-card-display" class="popup-card-display">
        </div>
        <form action="add_card_to_deck.php" method="POST" onsubmit="return handleCardAddition(event)">
            <input type="hidden" id="popup-card-id" name="card_id">
            <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
            <label for="deck_select_popup">Choisir un deck :</label>
            <select name="deck_id" id="deck_select_popup" required>
                <?php foreach ($decks as $deck): ?>
                    <option value="<?php echo $deck['deck_id']; ?>"><?php echo htmlspecialchars($deck['deck_name']); ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" name="add_card">Ajouter au Deck</button>
        </form>
    </div>
</div>

<script>
function openPopup(cardId, cardName) {
    const popup = document.getElementById('popup');
    const popupTitle = document.getElementById('popup-title');
    const popupCardId = document.getElementById('popup-card-id');
    const popupCardDisplay = document.getElementById('popup-card-display');

    // Met à jour les informations de la popup
    popupTitle.textContent = `Ajouter la carte "${cardName}" à un deck`;
    popupCardId.value = cardId;  // ID de la carte sélectionnée

    // Récupère les détails de la carte à afficher (exemple de données dynamiques)
    const card = document.querySelector(`.card-item[data-id='${cardId}']`);
    const cardHTML = card.innerHTML;

    // Affiche la carte dans le modal
    popupCardDisplay.innerHTML = `<div class="card-item-popup">${cardHTML}</div>`;

    // Affiche la popup
    popup.style.display = 'block';
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
