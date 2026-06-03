<?php $page_styles = ['style-exposition.css']; include('header.php'); ?>

<div class="container">
    <section class="intro-section">
        <h1 class="pixel-title">Exposition<span class="red-dot">.</span></h1>
        <p>Embarquez dans notre parcours artistique réparti sur 4 salles, chacune avec son propre univers ! <br><br> Chaque salle vous emmène explorer une facette différente de notre réflexion, entre engagement, point de vue et créativité. </p>
    </section>

    <section class="salles-grid">
        <div class="salle-card">
            <h2>Salle 1</h2>
            <p>Societ-e</p>
            <a href="salle1.php" class="btn-salle">Découvrir la salle</a>
        </div>
        <div class="salle-card">
            <h2>Salle 2</h2>
            <p>Horizon</p>
            <a href="salle2.php" class="btn-salle">Découvrir la salle</a>
        </div>
        <div class="salle-card">
            <h2>Salle 3</h2>
            <p>L'envers du Décor</p>
            <a href="salle3.php" class="btn-salle">Découvrir la salle</a>
        </div>
        <div class="salle-card">
            <h2>Salle 4</h2>
            <p>La pépinière</p>
            <a href="salle4.php" class="btn-salle">Découvrir la salle</a>
        </div>
    </section>
</div>

<?php include('footer.php'); ?>
