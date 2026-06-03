<?php
$page_styles = ['style-index.css', 'style-carousel.css'];
include('header.php');
?>

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
        Cette exposition a été imaginée et créée de toutes pièces par des étudiants en première année de BUT MMI (Métiers du multimédia et de l'internet).
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

<!-- AFFICHE -->
<section class="affiche-section">
    <div class="affiche-inner">

        <div class="affiche-texte">
            <h2 class="section-title" style="text-align:left">L'affiche de l'exposition</h2>
            <div class="intro-divider" style="margin:14px 0 24px"></div>
            <p class="intro-text">
                Dans le cadre de nos cours de création numérique, chaque étudiant a été invité à concevoir individuellement une affiche pour promouvoir l'exposition. Parmi la cinquantaine de propositions, une dizaine de projets ont été présélectionnés par les professeurs.
            </p>
            <p class="intro-text">
                À l'issue d'un vote impliquant l'ensemble des étudiants et des professeurs, l'affiche présentée ci-contre a été retenue.
            </p>
            <p class="intro-text">
                C'est à partir de la charte graphique de cette création que nous avons ensuite développé l'identité visuelle et l'interface de ce site web.
            </p>
        </div>

        <div class="affiche-visuel">
            <img src="../img/affiche.jpg" alt="Affiche de l'exposition E-LLUSION" class="affiche-img">
        </div>

    </div>
</section>

<?php include('carousel_salles.php'); ?>

<?php include('footer.php'); ?>
