<?php
session_start();
require_once 'includes/db.php';

if (isset($_POST['login'])) {
    $email    = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id']    = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        header('Location: index.php');
        exit;
    } else {
        $error = "Email ou mot de passe incorrect !";
    }
}

// Création utilisateur test
$stmt = $pdo->query("SELECT COUNT(*) FROM users");
if ($stmt->fetchColumn() == 0) {
    $hash = password_hash('123456', PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
    $stmt->execute(['caissier@pos.com', $hash]);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Connexion • Caisse POS</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;           /* ← Espace en haut et en bas */
        }
        .login-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.25);
            width: 100%;
            max-width: 400px;             /* ← Carte plus petite */
            overflow: hidden;
        }
        .login-header {
            background: linear-gradient(135deg, #1e40af, #3b82f6);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        .login-header h2 {
            font-weight: 700;
            font-size: 1.75rem;
        }
        .card-body {
            padding: 20px 15px;
        }
        .form-control {
            border-radius: 12px;
            padding: 14px 16px;
        }
        .btn-login {
            background: linear-gradient(135deg, #3b82f6, #1e40af);
            border: none;
            border-radius: 12px;
            padding: 14px;
            font-weight: 600;
            font-size: 1.1rem;
        }
        .btn-login:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(59, 130, 246, 0.4);
        }
        .test-info {
            font-size: 0.85rem;
            color: #6b7280;
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-header">
            <h2> Connexion Caisse</h2>
            <p class="mb-0 opacity-75"></p>
        </div>

        <div class="card-body">
            <?php if(isset($error)): ?>
                <div class="alert alert-danger text-center"><?= $error ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-4">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="caissier@pos.com" required>
                </div>

                <div class="mb-4">
                    <label class="form-label">Mot de passe</label>
                    <input type="password" name="password" class="form-control" value="123456" required>
                </div>

                <button type="submit" name="login" class="btn btn-primary w-100 btn-login">
                    Se connecter
                </button>
            </form>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
