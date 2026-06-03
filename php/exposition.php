<?php
$page_styles = ['style-exposition.css'];
include('header.php');

$salles = [
    ['num' => 1, 'nom' => 'Societ-e'],
    ['num' => 2, 'nom' => 'Horizon'],
    ['num' => 3, 'nom' => "L'envers du Décor"],
    ['num' => 4, 'nom' => 'La pépinière'],
];
?>

<div class="container">
    <section class="intro-section">
        <h1 class="pixel-title">Exposition<span class="red-dot">.</span></h1>
        <p>Embarquez dans notre parcours artistique réparti sur 4 salles, chacune avec son propre univers !<br><br>
           Chaque salle vous emmène explorer une facette différente de notre réflexion, entre engagement, point de vue et créativité.</p>
    </section>

    <section class="salles-grid">
        <?php foreach ($salles as $salle): ?>
        <div class="salle-card">
            <h2>Salle <?= $salle['num'] ?></h2>
            <p><?= $salle['nom'] ?></p>
            <a href="salle<?= $salle['num'] ?>.php" class="btn-salle">Découvrir la salle</a>
        </div>
        <?php endforeach; ?>
    </section>
</div>

<?php include('footer.php'); ?>
