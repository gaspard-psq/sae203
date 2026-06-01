<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-LLUSION - Exposition MMI</title>
    <link rel="stylesheet" href="../css/style.css?v=<?php echo filemtime('../css/style.css'); ?>">
    <?php if (!empty($page_styles)) foreach ($page_styles as $css): ?>
    <link rel="stylesheet" href="../css/<?php echo $css; ?>?v=<?php echo filemtime('../css/' . $css); ?>">
    <?php endforeach; ?>
</head>
<body>
    <header>
        <nav>
            <ul>
                <li><a href="index.php">Accueil</a></li>
                <li><a href="exposition.php">Exposition</a></li>
                <li><a href="reservation.php">Réservation</a></li>
                <li><a href="contact.php">Contact</a></li>
            </ul>
        </nav>
    </header>
    <main>