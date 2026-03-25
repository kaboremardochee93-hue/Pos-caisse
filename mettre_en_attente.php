<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if (!isset($_SESSION['panier'])) {
    $_SESSION['panier'] = [];
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Caisse POS • Interface Caissier</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    
    <style>
        body { font-family: 'Poppins', sans-serif; background: #f8fafc; min-height: 100vh; }
        .header { background: white; box-shadow: 0 4px 15px rgba(0,0,0,0.1); padding: 20px 0; position: sticky; top: 0; z-index: 100; }
        .caissier-name { font-size: 1.3rem; font-weight: 600; background: #1e40af; color: white; padding: 12px 25px; border-radius: 50px; }
    </style>
</head>
<body>

<!-- HEADER (toujours visible) -->
<div class="header">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-5">
                <h2 class="mb-0"><i class="fas fa-cash-register text-primary"></i> Caisse POS</h2>
                <span class="caissier-name">
                    👤 Caissier : <?= htmlspecialchars($_SESSION['user_email']) ?>
                </span>
            </div>
            
            <div>
                <a href="historique.php" class="btn btn-outline-primary btn-10g me-2">
                    <i class="fas fa-history"></i> Historique
                </a>
                <a href="logout.php" class="btn btn-outline-danger btn-l0g">
                    <i class="fas fa-sign-out-alt"></i> Déconnexion
                </a>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid mt-4">
    <div class="row">
        <!-- PRODUITS -->
        <div class="col-lg-8 p-4">
            <div class="row g-4">
                <?php
                $stmt = $pdo->query("SELECT * FROM produits ORDER BY nom");
                while ($p = $stmt->fetch()) {
                ?>
                <div class="col-md-4 col-sm-6">
                    <div class="card h-100 text-center shadow-sm">
                        <div class="card-body">
                            <h5><?= htmlspecialchars($p['nom']) ?></h5>
                            <p class="text-success fw-bold fs-4"><?= number_format($p['prix'], 0) ?> FCFA</p>
                            <button onclick="ajouterAuPanier(<?= $p['id'] ?>, '<?= addslashes($p['nom']) ?>', <?= $p['prix'] ?>)" 
                                    class="btn btn-primary w-100 py-3">
                                <i class="fas fa-cart-plus"></i> Ajouter
                            </button>
                        </div>
                    </div>
                </div>
                <?php } ?>
            </div>
        </div>

        <!-- PANIER -->
        <div class="col-lg-4">
            <div class="cart-sidebar" style="position: fixed; right: 0; top: 100px; height: calc(100vh - 100px); width: 390px; background: white; box-shadow: -10px 0 30px rgba(0,0,0,0.2); padding: 25px; overflow-y: auto;">
                <h4><i class="fas fa-shopping-cart"></i> Panier (<span id="nbArticles">0</span>)</h4>
                <hr>
                <div id="listePanier"></div>

                <div class="border-top pt-4 mt-4">
                    <h5 class="text-end fw-bold" id="total">0 FCFA</h5>
                    <button onclick="validerVente()" class="btn btn-success w-100 py-3 mt-3">VALIDER LA VENTE</button>
                    <button onclick="viderPanier()" class="btn btn-outline-danger w-100 mt-2">Vider le panier</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
let panier = [];

function ajouterAuPanier(id, nom, prix) {
    let existe = panier.find(p => p.id === id);
    if (existe) existe.quantite++;
    else panier.push({id: id, nom: nom, prix: prix, quantite: 1});
    afficherPanier();
}

function afficherPanier() {
    let html = '', total = 0, nb = 0;
    panier.forEach((item, i) => {
        let st = item.prix * item.quantite;
        total += st; nb += item.quantite;
        html += `<div class="mb-3 border-bottom pb-3"><strong>${item.nom}</strong><br><small>${item.quantite} × ${item.prix} FCFA</small><div class="float-end"><button onclick="modifier(${i},-1)" class="btn btn-sm btn-outline-secondary">-</button><button onclick="modifier(${i},1)" class="btn btn-sm btn-outline-secondary">+</button><button onclick="supprimer(${i})" class="btn btn-sm btn-danger ms-2">×</button></div></div>`;
    });
    document.getElementById('listePanier').innerHTML = html || '<p class="text-muted text-center">Votre panier est vide</p>';
    document.getElementById('total').textContent = total.toLocaleString('fr-FR') + ' FCFA';
    document.getElementById('nbArticles').textContent = nb;
}

function modifier(i, delta) { panier[i].quantite += delta; if (panier[i].quantite <= 0) panier.splice(i, 1); afficherPanier(); }
function supprimer(i) { panier.splice(i, 1); afficherPanier(); }
function viderPanier() { if (confirm('Vider le panier ?')) { panier = []; afficherPanier(); } }

function validerVente() {
    if (panier.length === 0) return alert("Le panier est vide !");
    if (!confirm("Valider la vente ?")) return;
    fetch('valider_vente.php', { method: 'POST', headers: {'Content-Type': 'application/x-www-form-urlencoded'}, body: 'panier=' + encodeURIComponent(JSON.stringify(panier)) })
    .then(r => r.text())
    .then(text => { alert(text); if (confirm('Imprimer le reçu ?')) window.print(); panier = []; afficherPanier(); });
}

afficherPanier();
</script>
</body>
</html>
