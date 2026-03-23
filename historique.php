<?php
session_start();
require_once 'includes/db.php';

// Sécurité : redirection si non connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Historique des Ventes - Caisse POS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f7f6; }
        .table-container { background: white; border-radius: 15px; padding: 20px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
    </style>
</head>
<body class="p-4">
<div class="container table-container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-history text-primary"></i> Historique des Ventes</h2>
        <a href="index.php" class="btn btn-outline-primary"><i class="fas fa-arrow-left"></i> Retour à la Caisse</a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Ticket </th>
                    <th>Date & Heure</th>
                    <th>Montant Total</th>
                    <th class="text-center">Action</th>
                </tr>
            </thead>
            <tbody>
            <?php
            // On récupère les ventes
            $stmt = $pdo->query("SELECT * FROM ventes ORDER BY date DESC");
            while ($v = $stmt->fetch()) {
            ?>
                <tr>
                    <td><strong><?= $v['id'] ?></strong></td>
                    <td><?= date('d/m/Y H:i', strtotime($v['date'])) ?></td>
                    <td class="text-success fw-bold"><?= number_format($v['total'], 0, ',', ' ') ?> FCFA</td>
                    <td class="text-center">
                        <button class="btn btn-info btn-sm text-white" data-bs-toggle="modal" data-bs-target="#modal<?= $v['id'] ?>">
                            <i class="fas fa-eye"></i> Détails
                        </button>
                    </td>
                </tr>

                <div class="modal fade" id="modal<?= $v['id'] ?>" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header bg-light">
                                <h5 class="modal-title">Détails de la Vente #<?= $v['id'] ?></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p class="small text-muted mb-3">Date : <?= date('d/m/Y H:i', strtotime($v['date'])) ?></p>
                                <ul class="list-group list-group-flush mb-3">
                                <?php
                                // Récupération des articles de cette vente précise
                                $det = $pdo->prepare("SELECT vd.quantite, p.nom, p.prix 
                                                      FROM vente_details vd 
                                                      JOIN produits p ON vd.produit_id = p.id 
                                                      WHERE vd.vente_id = ?");
                                $det->execute([$v['id']]);
                                while ($d = $det->fetch()) {
                                    $sous_total = $d['prix'] * $d['quantite'];
                                    echo "<li class='list-group-item d-flex justify-content-between align-items-center'>";
                                    echo "<span><strong>" . htmlspecialchars($d['nom']) . "</strong> <br><small>" . $d['quantite'] . " x " . number_format($d['prix'], 0) . "</small></span>";
                                    echo "<span class='fw-bold'>" . number_format($sous_total, 0) . " FCFA</span>";
                                    echo "</li>";
                                }
                                ?>
                                </ul>
                                <div class="d-flex justify-content-between align-items-center p-2 bg-success bg-opacity-10 rounded">
                                    <h5 class="mb-0 text-success">TOTAL FINAL</h5>
                                    <h5 class="mb-0 text-success fw-bold"><?= number_format($v['total'], 0) ?> FCFA</h5>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                                <button type="button" class="btn btn-primary" onclick="window.print()"><i class="fas fa-print"></i> Re-imprimer</button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>