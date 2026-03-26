<?php
session_start();
require_once 'includes/db.php';

$success = null;
$error = null;

if (isset($_POST['register'])) {
    $email    = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm  = $_POST['confirm_password'];

    // Validation simple
    if ($password !== $confirm) {
        $error = "Les mots de passe ne correspondent pas !";
    } elseif (strlen($password) < 6) {
        $error = "Le mot de passe doit faire au moins 6 caractères.";
    } else {
        // Vérifier si l'email existe déjà
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->fetch()) {
            $error = "Cet email est déjà utilisé.";
        } else {
            // Hachage du mot de passe et insertion
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
            
            if ($stmt->execute([$email, $hash])) {
                $success = "Compte créé avec succès ! <a href='login.php' class='alert-link'>Connectez-vous ici</a>";
            } else {
                $error = "Une erreur est survenue lors de l'inscription.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Inscription • Caisse POS</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* On réutilise le même style que login.php pour la cohérence */
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%);
            height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px;
        }
        .register-card {
            background: white; border-radius: 20px; box-shadow: 0 20px 40px rgba(0, 0, 0, 0.25);
            width: 100%; max-width: 450px; overflow: hidden;
        }
        .register-header {
            background: linear-gradient(135deg, #1e40af, #3b82f6);
            color: white; padding: 25px; text-align: center;
        }
        .card-body { padding: 30px; }
        .form-control { border-radius: 10px; padding: 12px; }
        .btn-register {
            background: linear-gradient(135deg, #3b82f6, #1e40af);
            border: none; border-radius: 10px; padding: 12px; font-weight: 600;
        }
    </style>
</head>
<body>

<div class="register-card">
    <div class="register-header">
        <h2>Créer un compte</h2>
        <p class="mb-0 opacity-75">Rejoignez le système de caisse</p>
    </div>

    <div class="card-body">
        <?php if($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <?php if($success): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Adresse Email</label>
                <input type="email" name="email" class="form-control" placeholder="exemple@pos.com" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Mot de passe</label>
                <input type="password" name="password" class="form-control" placeholder="6 caractères min." required>
            </div>

            <div class="mb-4">
                <label class="form-label">Confirmer le mot de passe</label>
                <input type="password" name="confirm_password" class="form-control" required>
            </div>

            <button type="submit" name="register" class="btn btn-primary w-100 btn-register text-white">
                S'inscrire maintenant
            </button>
        </form>

        <hr class="my-4">
        <div class="text-center">
            <a href="login.php" class="text-decoration-none small text-primary fw-bold">← Retour à la connexion</a>
        </div>
    </div>
</div>

</body>
</html>