<?php
// Désactiver l'affichage des erreurs qui pourraient casser le JSON
error_reporting(0);
ini_set('display_errors', 0);

require_once 'includes/db.php';
header('Content-Type: application/json');

try {
    $stmt = $pdo->query("SELECT * FROM paniers_en_attente ORDER BY id DESC");
    $lignes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $sorties = [];
    foreach ($lignes as $l) {
        $sorties[] = [
            'id_db'  => (int)$l['id'],
            'client' => $l['nom_client'],
            'items'  => json_decode($l['produits_json'], true),
            'date'   => date('H:i', strtotime($l['date_attente']))
        ];
    }
    echo json_encode($sorties);
} catch (Exception $e) {
    echo json_encode([]); // Renvoie vide au lieu de planter
}
exit;
