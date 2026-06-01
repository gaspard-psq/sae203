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
                            <a href="salle1.php" class="btn-slide">Découvrir</a>
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
                            <a href="salle2.php" class="btn-slide">Découvrir</a>
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
                            <a href="salle3.php" class="btn-slide">Découvrir</a>
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
                            <a href="salle4.php" class="btn-slide">Découvrir</a>
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

setInterval(() => goTo(current + 1), 4000);
</script>
