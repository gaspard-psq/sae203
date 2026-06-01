<?php include('header.php'); ?>

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
<section class="carousel-section">
    <h2 class="section-title">Découvrez les salles</h2>
    <div class="intro-divider"></div>

    <div class="carousel-wrapper">
        <button class="carousel-btn prev" id="prevBtn" aria-label="Précédent">&#8592;</button>

        <div class="carousel-track-container">
            <div class="carousel-track" id="carouselTrack">

                <div class="carousel-slide">
                    <div class="slide-bg slide-bg-1">
                        <div class="slide-overlay"></div>
                        <div class="slide-content">
                            <span class="slide-num">01</span>
                            <h3>Salle 002</h3>
                            <p>Exposition principale E-llusion</p>
                            <a href="salle.php?id=1" class="btn-slide">Découvrir</a>
                        </div>
                    </div>
                </div>

                <div class="carousel-slide">
                    <div class="slide-bg slide-bg-2">
                        <div class="slide-overlay"></div>
                        <div class="slide-content">
                            <span class="slide-num">02</span>
                            <h3>Salle 001</h3>
                            <p>Atelier interactif</p>
                            <a href="salle.php?id=2" class="btn-slide">Découvrir</a>
                        </div>
                    </div>
                </div>

                <div class="carousel-slide">
                    <div class="slide-bg slide-bg-3">
                        <div class="slide-overlay"></div>
                        <div class="slide-content">
                            <span class="slide-num">03</span>
                            <h3>Salle 005</h3>
                            <p>Zone VR</p>
                            <a href="salle.php?id=3" class="btn-slide">Découvrir</a>
                        </div>
                    </div>
                </div>

                <div class="carousel-slide">
                    <div class="slide-bg slide-bg-4">
                        <div class="slide-overlay"></div>
                        <div class="slide-content">
                            <span class="slide-num">04</span>
                            <h3>Salle 021</h3>
                            <p>Projection</p>
                            <a href="salle.php?id=4" class="btn-slide">Découvrir</a>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <button class="carousel-btn next" id="nextBtn" aria-label="Suivant">&#8594;</button>
    </div>

    <div class="carousel-dots" id="carouselDots">
        <span class="dot active" data-index="0"></span>
        <span class="dot" data-index="1"></span>
        <span class="dot" data-index="2"></span>
        <span class="dot" data-index="3"></span>
    </div>
</section>


<script>
const track  = document.getElementById('carouselTrack');
const dots   = document.querySelectorAll('.dot');
const slides = document.querySelectorAll('.carousel-slide');
let current  = 0;

function goTo(index) {
    current = (index + slides.length) % slides.length;
    track.style.transform = `translateX(-${current * 100}%)`;
    dots.forEach(d => d.classList.remove('active'));
    dots[current].classList.add('active');
}

document.getElementById('prevBtn').addEventListener('click', () => goTo(current - 1));
document.getElementById('nextBtn').addEventListener('click', () => goTo(current + 1));
dots.forEach(dot => dot.addEventListener('click', () => goTo(+dot.dataset.index)));

// Auto-play
setInterval(() => goTo(current + 1), 4000);
</script>

<?php include('footer.php'); ?>