<?php
$salles = [
    ['num' => 'Salle 021', 'titre' => 'Salle n°1', 'nom' => 'Societ-e',         'lien' => 'salle1.php'],
    ['num' => 'Salle 005', 'titre' => 'Salle n°2', 'nom' => 'Horizon',           'lien' => 'salle2.php'],
    ['num' => 'Salle 002', 'titre' => 'Salle n°3', 'nom' => "L'envers du Décor", 'lien' => 'salle3.php'],
    ['num' => 'Salle 001', 'titre' => 'Salle n°4', 'nom' => 'La pépinière',      'lien' => 'salle4.php'],
];
?>

<section class="carousel-section">
    <h2 class="section-title">Découvrez les salles</h2>
    <div class="intro-divider"></div>

    <div class="carousel-wrapper">
        <button class="carousel-btn prev" id="prevBtn" aria-label="Précédent">&#8592;</button>

        <div class="carousel-track-container">
            <div class="carousel-track" id="carouselTrack">
                <?php foreach ($salles as $i => $salle): ?>
                <div class="carousel-slide">
                    <div class="slide-bg slide-bg-<?= $i + 1 ?>">
                        <div class="slide-overlay"></div>
                        <div class="slide-content">
                            <span class="slide-num"><?= $salle['num'] ?></span>
                            <h3><?= $salle['titre'] ?></h3>
                            <p><?= $salle['nom'] ?></p>
                            <a href="<?= $salle['lien'] ?>" class="btn-slide">Découvrir</a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <button class="carousel-btn next" id="nextBtn" aria-label="Suivant">&#8594;</button>
    </div>

    <div class="carousel-dots" id="carouselDots">
        <?php foreach ($salles as $i => $salle): ?>
        <span class="dot <?= $i === 0 ? 'active' : '' ?>" data-index="<?= $i ?>"></span>
        <?php endforeach; ?>
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

// avance automatiquement toutes les 4 secondes
setInterval(() => goTo(current + 1), 4000);
</script>
