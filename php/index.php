<?php $page_styles = ['style-index.css', 'style-carousel.css']; include('header.php'); ?>

<!-- HERO -->
<section class="hero">
    <div class="hero-pattern"></div>
    <div class="hero-content">
        <h1 class="pixel-title">E-llusion<span class="red-dot">.</span></h1>
        <p class="hero-sub">Une exposition réalisée par des étudiants en MMI.<br>Venez la découvrir dès maintenant en réservant votre créneau.</p>
        <a href="reservation.php" class="btn-hero">Réservez un créneau</a>
    </div>
</section>

<!-- PRÉSENTATION -->
<section class="intro-section">
    <h2 class="section-title">Bienvenue sur le site de l'exposition E-llusion !</h2>
    <div class="intro-divider"></div>
    <p class="intro-text">

<br><br>
Le thème de l'exposition ? E-llusion, venez la découvrir à travers un parcours réparti en 4 salles où vous attendent 12 œuvres originales (3 ou 4 œuvres par salle) qui explorent le numérique et les faux-semblants.
    </p>
    <a href="exposition.php" class="btn-secondary">En savoir plus</a>
</section>

<!-- CHIFFRES CLÉS -->
<section class="chiffres-section">
    <div class="chiffre-item">
        <span class="chiffre-nb">4</span>
        <span class="chiffre-label">Salles</span>
    </div>
    <div class="chiffre-sep"></div>
    <div class="chiffre-item">
        <span class="chiffre-nb">12</span>
        <span class="chiffre-label">Œuvres</span>
    </div>
    <div class="chiffre-sep"></div>
    <div class="chiffre-item">
        <span class="chiffre-nb">1</span>
        <span class="chiffre-label">Jour</span>
    </div>
    <div class="chiffre-sep"></div>
    <div class="chiffre-item">
        <span class="chiffre-nb">1ère</span>
        <span class="chiffre-label">Année MMI</span>
    </div>
</section>

<!-- CAROUSEL SALLES -->
<?php include('carousel_salles.php'); ?>

<?php include('footer.php'); ?>