<?php 
include('header.php'); 

// On récupère le numéro de la salle dans l'URL (ex: salle.php?id=2), par défaut 1
$id_salle = isset($_GET['id']) ? (int)$_GET['id'] : 1;
if($id_salle < 1 || $id_salle > 4) { $id_salle = 1; }
?>

<section class="intro-section">
    <h1 class="pixel-title"><span class="red-dot">• </span>Salle <?php echo $id_salle; ?></h1>
    <p>Pirate ipsum arrgh bounty warp jack. Grog coffer ballast maroon sink ipsum chains hands bilged maroon. Me avast of blow her coast topsail arrgh. Crimp reef boatswain hands tea.</p>
</section>

<div class="works-list">
    <?php for($i = 1; $i <= 4; $i++): ?>
    <div class="work-item">
        <div class="work-img-placeholder">Œuvre <?php echo $i; ?></div>
        <div class="work-text">
            <p>Pirate ipsum arrgh bounty warp jack. Grog coffer ballast maroon sink ipsum chains hands bilged maroon. Me avast of blow her coast topsail arrgh. Crimp reef boatswain hands tea.</p>
        </div>
    </div>
    <?php endfor; ?>
</div>

<div style="text-align: center; margin: 40px 0;">
    <?php $suivante = ($id_salle < 4) ? $id_salle + 1 : 1; ?>
    <a href="salle.php?id=<?php echo $suivante; ?>" class="btn-secondary">Découvrir la salle <?php echo $suivante; ?></a>
</div>

<?php include('footer.php'); ?>