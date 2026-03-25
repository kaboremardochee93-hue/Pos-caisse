<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
require_once 'includes/db.php';

header('Content-Type: text/plain; charset=utf-8');

$panier = json_decode($_POST['panier'] ?? '', true);

if (empty($panier)) {
    echo "Erreur : Panier vide";
    exit;
}

$total = 0;
foreach ($panier as $item) {
    $total += $item['prix'] * $item['quantite'];
}

$stmt = $pdo->prepare("INSERT INTO ventes (total) VALUES (?)");
$stmt->execute([$total]);
$vente_id = $pdo->lastInsertId();

$stmt = $pdo->prepare("INSERT INTO vente_details (vente_id, produit_id, quantite) VALUES (?, ?, ?)");
foreach ($panier as $item) {
    $stmt->execute([$vente_id, $item['id'], $item['quantite']]);
}

echo "✅ Vente enregistrée avec succès !\n";
echo "ID Vente : " . $vente_id . "\n";
echo "Total : " . number_format($total, 0) . " FCFA\n";
echo "Date : " . date('d/m/Y H:i');
?>