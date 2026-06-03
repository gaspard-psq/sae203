<?php $page_styles = ['style-salle.css', 'style-carousel.css']; include('header.php'); ?>

<section class="intro-section">
    <h1 class="pixel-title">Salle 1<span class="red-dot">.</span></h1>
    <p>La salle 1 est le fruit du travail d'un groupe de <strong>8 étudiants</strong> qui ont conçu et réalisé leurs œuvres entièrement de A à Z de l'idée initiale jusqu'à l'installation finale.</p>
</section>

<div class="works-list">
    <div class="work-item">
        <div class="work-img-placeholder">Œuvre 1</div>
        <div class="work-text">
            <p>Pirate ipsum arrgh bounty warp jack. Grog coffer ballast maroon sink ipsum chains hands bilged maroon. Me avast of blow her coast topsail arrgh. Crimp reef boatswain hands tea.</p>
        </div>
    </div>
    <div class="work-item">
        <div class="work-img-placeholder">Œuvre 2</div>
        <div class="work-text">
            <p>L’œuvre Distorsion explore l’émancipation de notre identité dans un récit où notre image ne nous appartient plus. Elle montre que, dans l’univers numérique, notre visage devient une matière que les autres peuvent modifier, détourner ou réinventer. Cette transformation imposée crée une version de nous qui échappe à notre contrôle. <br><br>Dans Distorsion, cette image altérée est ensuite mise en vente, comme un produit parmi d’autres, révélant comment la société de consommation s’approprie jusqu’à notre identité.<br><br> Le visage devient un objet marchand, façonné par le regard collectif, que chacun peut acheter et  juger. La valeur de cette nouvelle identité dépend alors non plus de nous, mais de la réaction des autres.</p>
        </div>
    </div>
</div>

<div class="salle-nav-next">
    <a href="salle2.php" class="btn-salle-nav">Passer à la salle suivante →</a>
</div>

<?php include('carousel_salles.php'); ?>

<?php include('footer.php'); ?>
