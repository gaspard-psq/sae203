</main>

<footer class="site-footer">

    <!-- Wavy top edge -->
    <div class="footer-wave-top">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 80" preserveAspectRatio="none">
            <path d="M0,48 C120,80 240,16 360,48 C480,80 600,10 720,48 C840,86 960,12 1080,48 C1200,80 1320,18 1440,48 L1440,80 L0,80 Z" fill="var(--cyan-dark)"/>
        </svg>
    </div>

    <div class="footer-main">

        <!-- Organic swirl pattern (mirrors the site motif) -->
        <svg class="footer-swirl-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 460" preserveAspectRatio="xMidYMid slice" aria-hidden="true">
            <g fill="rgba(255,255,255,0.06)">
                <path d="M-250,80 C50,-40 180,280 420,200 C660,120 560,400 780,340 C1000,280 880,20 1120,120 C1360,220 1400,10 1700,80 L1700,560 L-250,560 Z"/>
            </g>
            <g fill="rgba(255,255,255,0.045)">
                <path d="M-150,380 C80,220 260,480 480,360 C700,240 620,440 860,390 C1100,340 980,80 1220,200 C1460,320 1430,100 1700,260 L1700,560 L-150,560 Z"/>
            </g>
            <g fill="rgba(255,255,255,0.03)">
                <ellipse cx="160"  cy="300" rx="320" ry="200" transform="rotate(-25 160 300)"/>
                <ellipse cx="880"  cy="420" rx="360" ry="190" transform="rotate(18 880 420)"/>
                <ellipse cx="1320" cy="140" rx="280" ry="200" transform="rotate(-12 1320 140)"/>
            </g>
        </svg>

        <div class="footer-container">

            <!-- Brand & contact -->
            <div class="footer-col footer-info">
                <p class="footer-brand-name">E·LLUSION</p>
                <p class="footer-tagline">Exposition interactive &amp; immersive<br>par les étudiants MMI</p>
                <address>
                    28 Av. du Lac d'Annecy<br>
                    73370 Le Bourget-du-Lac
                </address>
                <a href="mailto:francois.piranda@univ-smb.fr">francois.piranda@univ-smb.fr</a>
            </div>

            <!-- Logos MMI + IUT -->
            <div class="footer-col footer-logos">

                <div class="footer-logo-wrap">
                    <svg viewBox="0 0 148 72" xmlns="http://www.w3.org/2000/svg">
                        <rect width="148" height="72" rx="10" fill="rgba(255,255,255,0.13)" stroke="rgba(255,255,255,0.22)" stroke-width="1"/>
                        <!-- mmi lettering -->
                        <text x="74" y="30" text-anchor="middle"
                              font-family="Helvetica Neue, Arial, sans-serif"
                              font-weight="900" font-size="23"
                              fill="#ffffff" letter-spacing="4">mmi</text>
                        <line x1="24" y1="39" x2="124" y2="39" stroke="rgba(255,255,255,0.22)" stroke-width="0.6"/>
                        <text x="74" y="56" text-anchor="middle"
                              font-family="Helvetica Neue, Arial, sans-serif"
                              font-weight="400" font-size="10"
                              fill="rgba(255,255,255,0.72)" letter-spacing="2.5">CHAMBÉRY</text>
                    </svg>
                </div>

                <div class="footer-logo-wrap">
                    <svg viewBox="0 0 148 72" xmlns="http://www.w3.org/2000/svg">
                        <rect width="148" height="72" rx="10" fill="rgba(255,255,255,0.13)" stroke="rgba(255,255,255,0.22)" stroke-width="1"/>
                        <!-- IUT lettering -->
                        <text x="74" y="30" text-anchor="middle"
                              font-family="Helvetica Neue, Arial, sans-serif"
                              font-weight="900" font-size="23"
                              fill="#ffffff" letter-spacing="4">IUT</text>
                        <line x1="24" y1="39" x2="124" y2="39" stroke="rgba(255,255,255,0.22)" stroke-width="0.6"/>
                        <text x="74" y="56" text-anchor="middle"
                              font-family="Helvetica Neue, Arial, sans-serif"
                              font-weight="400" font-size="10"
                              fill="rgba(255,255,255,0.72)" letter-spacing="2.5">CHAMBÉRY</text>
                    </svg>
                </div>

            </div>

            <!-- Social -->
            <div class="footer-col footer-social">
                <p class="footer-social-heading">Suivez-nous</p>
                <a href="https://www.instagram.com/mmichambery/"
                   class="footer-ig-btn"
                   target="_blank"
                   rel="noopener noreferrer">
                    <svg class="ig-svg" viewBox="0 0 24 24"
                         xmlns="http://www.w3.org/2000/svg"
                         fill="none" stroke="currentColor"
                         stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="2" y="2" width="20" height="20" rx="5" ry="5"/>
                        <circle cx="12" cy="12" r="4"/>
                        <path d="M17.5 6.5h.01" stroke-width="2.5"/>
                    </svg>
                    <span>@mmichambery</span>
                </a>
            </div>

        </div>

        <div class="footer-bottom">
            <span>© <?php echo date('Y'); ?> E-LLUSION</span>
            <span class="footer-sep">·</span>
            <span>MMI Chambéry — Université Savoie Mont Blanc</span>
        </div>

    </div>
</footer>
</body>
</html>
