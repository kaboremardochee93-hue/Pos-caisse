<?php
require_once 'includes/db.php';
header('Content-Type: application/json');

if (isset($_GET['index'])) {
    $id = (int)$_GET['index']; // Ici 'index' recevra l'ID de la DB

    $stmt = $pdo->prepare("SELECT produits_json FROM paniers_en_attente WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch();

    if ($row) {
        // Supprimer de la table car on va le remettre dans le panier actif
        $del = $pdo->prepare("DELETE FROM paniers_en_attente WHERE id = ?");
        $del->execute([$id]);
        
        echo $row['produits_json']; // On renvoie direct le JSON
    } else {
        echo json_encode([]);
    }
}
?>