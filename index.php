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
    <h2 class="section-title">Venez découvrir notre exposition</h2>
    <div class="intro-divider"></div>
    <p class="intro-text">
        Cette exposition a été imaginée et créée de toute pièce par des étudiants en première année de BUT MMI (Métiers du multimédia et de l'internet). 
        Le thème est <strong>E-llusion</strong> — une plongée dans les illusions du numérique, entre perception et réalité.
        Répartie en 4 salles avec 3 à 4 œuvres chacune, elle vous invite à questionner vos sens.
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
        <span class="chiffre-nb">16</span>
        <span class="chiffre-label">Œuvres</span>
    </div>
    <div class="chiffre-sep"></div>
    <div class="chiffre-item">
        <span class="chiffre-nb">2</span>
        <span class="chiffre-label">Jours</span>
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