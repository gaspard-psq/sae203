<?php
session_start();
include('../php/config.php');

if (!empty($_SESSION['admin_connecte'])) {
    header('Location: admin.php');
    exit;
}

$erreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login    = trim($_POST['login'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($login !== '' && $password !== '') {
        $stmt = $db->prepare("SELECT * FROM admin WHERE login = ?");
        $stmt->execute([$login]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($admin && password_verify($password, $admin['password_hash'])) {
            $_SESSION['admin_connecte'] = true;
            $_SESSION['admin_login']    = $admin['login'];
            header('Location: admin.php');
            exit;
        } else {
            $erreur = 'Identifiant ou mot de passe incorrect.';
        }
    } else {
        $erreur = 'Veuillez remplir tous les champs.';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Espace admin — E-LLUSION</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/style-reservation.css">
    <link rel="stylesheet" href="../css/style-admin.css">
</head>
<body>
<main>

<div class="container">
    <section class="intro-section">
        <h1 class="pixel-title">Espace admin<span class="red-dot">.</span></h1>
        <p>Connectez-vous pour accéder à la gestion des réservations.</p>
    </section>

    <div class="admin-login-wrapper">
        <form method="POST" action="admin-login.php" class="admin-login-form">

            <?php if ($erreur): ?>
            <div class="error-banner"><?= htmlspecialchars($erreur) ?></div>
            <?php endif; ?>

            <div class="input-group">
                <label for="login">Identifiant <span class="red-dot">*</span></label>
                <input type="text" id="login" name="login" placeholder="admin" required>
            </div>

            <div class="input-group">
                <label for="password">Mot de passe <span class="red-dot">*</span></label>
                <input type="password" id="password" name="password" placeholder="••••••••" required>
            </div>

            <div class="form-footer-linear">
                <button type="submit" class="btn-submit-large">Se connecter</button>
            </div>
        </form>
    </div>
</div>

</main>
</body>
</html>
