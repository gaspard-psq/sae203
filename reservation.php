<?php 
include('config.php');

// 1. TRAITEMENT DE L'INSCRIPTION
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_inscription'])) {
    $nom          = trim($_POST['nom'] ?? '');
    $prenom       = trim($_POST['prenom'] ?? '');
    $email        = trim($_POST['email'] ?? '');
    $id_categorie = (int)($_POST['id_categorie'] ?? 0);
    $id_creneau   = (int)($_POST['id_creneau'] ?? 0);
    $nb_personnes = (int)($_POST['nb_personnes'] ?? 1);
    $buffet       = isset($_POST['buffet']) ? 1 : 0;

    if (!empty($nom) && !empty($prenom) && !empty($email) && $id_creneau > 0 && $id_categorie > 0) {
        $stmtCheck = $db->prepare("SELECT places_restantes FROM creneau WHERE id_creneau = ?");
        $stmtCheck->execute([$id_creneau]);
        $creneau = $stmtCheck->fetch(PDO::FETCH_ASSOC);

        if ($creneau && $creneau['places_restantes'] >= $nb_personnes) {
            $token = bin2hex(random_bytes(16));
            try {
                $db->beginTransaction();
                $stmtInsert = $db->prepare("
                    INSERT INTO inscription (id_creneau, id_categorie, nom, prenom, email, nb_personnes, buffet, token) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                ");
                $stmtInsert->execute([$id_creneau, $id_categorie, $nom, $prenom, $email, $nb_personnes, $buffet, $token]);
                $stmtUpdate = $db->prepare("UPDATE creneau SET places_restantes = places_restantes - ? WHERE id_creneau = ?");
                $stmtUpdate->execute([$nb_personnes, $id_creneau]);
                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
            }
        }
    }
}

// 2. RÉCUPÉRATION DES DONNÉES
$categories = $db->query("SELECT * FROM categorie_visiteur ORDER BY id_categorie ASC")->fetchAll(PDO::FETCH_ASSOC);
$tous_creneaux = $db->query("SELECT * FROM creneau ORDER BY date, heure_debut")->fetchAll(PDO::FETCH_ASSOC);
$salles_db = $db->query("SELECT * FROM salle ORDER BY nom ASC")->fetchAll(PDO::FETCH_ASSOC);

// Index des places restantes par salle + jour + heure pour le JS
$jauge_index = [];
foreach ($tous_creneaux as $c) {
    $date_key  = (date('Y-m-d', strtotime($c['date'])) <= '2026-06-18') ? 'jeudi' : 'vendredi';
    $heure_key = date('H:i', strtotime($c['heure_debut']));
    $jauge_index[$c['id_salle']][$date_key][$heure_key] = [
        'id_creneau'       => $c['id_creneau'],
        'places_restantes' => (int)$c['places_restantes'],
    ];
}

$jauge_json = json_encode($jauge_index);

$page_styles = ['style-reservation.css'];
include('header.php');
?>

<div class="container">
    <section class="intro-section">
        <h1 class="pixel-title">Réservation<span class="red-dot">.</span></h1>
        <p>Planifiez votre visite de l'exposition E-llusion en remplissant les étapes ci-dessous.</p>
    </section>

    <div class="form-wrapper-linear">
        <form action="reservation.php" method="POST" id="formReservation">
            <input type="hidden" name="action_inscription" value="1">
            <input type="hidden" name="id_creneau" id="hidden_id_creneau" value="">

            <!-- ÉTAPE 01 : Coordonnées -->
            <div class="form-section-linear">
                <h3><span class="section-icon">01</span> Vos coordonnées</h3>
                <div class="input-group">
                    <label for="nom">Nom <span class="red-dot">*</span></label>
                    <input type="text" id="nom" name="nom" placeholder="Ex: Dupont" required>
                </div>
                <div class="input-group">
                    <label for="prenom">Prénom <span class="red-dot">*</span></label>
                    <input type="text" id="prenom" name="prenom" placeholder="Ex: Lucas" required>
                </div>
                <div class="input-group">
                    <label for="email">Adresse e-mail <span class="red-dot">*</span></label>
                    <input type="email" id="email" name="email" placeholder="lucas.dupont@etu.univ-smb.fr" required>
                </div>
            </div>

            <!-- ÉTAPE 02 : Profil -->
            <div class="form-section-linear">
                <h3><span class="section-icon">02</span> Votre profil</h3>
                <div class="radio-group-horizontal">
                    <?php foreach ($categories as $index => $cat): ?>
                        <label class="custom-radio">
                            <input type="radio" name="id_categorie" value="<?php echo $cat['id_categorie']; ?>" <?php echo $index === 0 ? 'checked' : ''; ?>>
                            <span class="radio-mark"></span> <?php echo htmlspecialchars($cat['libelle']); ?>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- ÉTAPE 03 : Salle + jour + créneau + nb personnes -->
            <div class="form-section-linear">
                <h3><span class="section-icon">03</span> Choix de la salle et de l'horaire</h3>
                <p style="margin-bottom: 20px; color: #555;">Sélectionnez la salle que vous souhaitez visiter :</p>

                <div class="salles-accordion-list">
                    <?php foreach ($salles_db as $salle):
                        $id_salle = $salle['id_salle'];
                    ?>
                    <details class="salle-accordion" data-salle-id="<?php echo $id_salle; ?>">
                        <summary class="salle-summary">
                            <div class="salle-summary-text">
                                <h4><?php echo htmlspecialchars($salle['nom']); ?></h4>
                                <small>Capacité maximale : <?php echo $salle['capacite_max']; ?> personnes</small>
                            </div>
                            <span class="accordion-arrow">▼</span>
                        </summary>

                        <div class="horaires-dropdown-content">

                            <!-- Choix du jour -->
                            <div class="jour-selector">
                                <p>Choisissez un jour :</p>
                                <div class="jour-toggle-group">
                                    <label class="jour-toggle-btn">
                                        <input type="radio" name="jour_salle_<?php echo $id_salle; ?>" value="jeudi" class="jour-radio" data-salle="<?php echo $id_salle; ?>">
                                        <span>Jeudi 18 juin</span>
                                    </label>
                                    <label class="jour-toggle-btn">
                                        <input type="radio" name="jour_salle_<?php echo $id_salle; ?>" value="vendredi" class="jour-radio" data-salle="<?php echo $id_salle; ?>">
                                        <span>Vendredi 19 juin</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Créneaux injectés via JS -->
                            <div class="creneaux-zone" id="creneaux_salle_<?php echo $id_salle; ?>">
                                <p class="creneaux-hint">↑ Sélectionnez d'abord un jour pour voir les créneaux disponibles.</p>
                            </div>

                            <!-- Nombre de personnes — affiché après sélection d'un créneau -->
                            <div class="nb-personnes-zone" id="nb_zone_<?php echo $id_salle; ?>" style="display:none; margin-top: 20px;">
                                <div class="input-group" style="max-width: 250px;">
                                    <label for="nb_personnes_<?php echo $id_salle; ?>">Nombre de personnes <span class="red-dot">*</span></label>
                                    <input type="number" id="nb_personnes_<?php echo $id_salle; ?>" name="nb_personnes" min="1" max="12" value="1">
                                    <small id="jauge-info-<?php echo $id_salle; ?>" class="jauge-info-txt"></small>
                                </div>
                            </div>

                        </div>
                    </details>
                    <?php endforeach; ?>
                </div>


            </div>

            <!-- ÉTAPE 04 : Buffet -->
            <div class="form-section-linear">
                <h3><span class="section-icon">04</span> Informations de visite</h3>
                <div class="checkbox-group">
                    <label class="custom-checkbox">
                        <input type="checkbox" name="buffet" value="1">
                        <span class="checkmark"></span>
                        <span class="checkbox-text">Je souhaite participer au buffet de clôture</span>
                    </label>
                </div>
            </div>

            <div class="form-footer-linear">
                <button type="submit" class="btn-submit-large">Confirmer ma réservation</button>
            </div>
        </form>
    </div>
</div>

<script>
const JAUGE_INDEX = <?php echo $jauge_json; ?>;

const CRENEAUX_DATA = {
    jeudi: {
        label: 'Jeudi 18 juin',
        slots: ['15:00','15:30','16:00','16:30','17:00','17:30','18:00','19:00','19:30','20:00']
    },
    vendredi: {
        label: 'Vendredi 19 juin',
        slots: ['09:30','10:00','10:30','11:00']
    }
};

let selectedSalleId   = null;
let selectedCreneauId = null;

// Changement de jour → affiche les créneaux
document.querySelectorAll('.jour-radio').forEach(radio => {
    radio.addEventListener('change', function () {
        const salleId = this.dataset.salle;
        const jourKey = this.value;
        renderCreneaux(salleId, jourKey);

        // Réinitialise la sélection de créneau
        selectedCreneauId = null;
        document.getElementById('hidden_id_creneau').value = '';
        document.getElementById('nb_zone_' + salleId).style.display = 'none';
    });
});

function renderCreneaux(salleId, jourKey) {
    const zone       = document.getElementById('creneaux_salle_' + salleId);
    const jourData   = CRENEAUX_DATA[jourKey];
    const jaugesSalle = (JAUGE_INDEX[salleId] && JAUGE_INDEX[salleId][jourKey]) ? JAUGE_INDEX[salleId][jourKey] : {};

    let html = '<div class="horaires-badges-grid">';

    jourData.slots.forEach(slot => {
        const jaugeInfo  = jaugesSalle[slot] || null;
        const idCreneau  = jaugeInfo ? jaugeInfo.id_creneau : null;
        const placesRest = jaugeInfo ? jaugeInfo.places_restantes : 12;

        html += `
            <label class="horaire-badge-item"
                data-id-creneau="${idCreneau}"
                data-places="${placesRest}"
                data-salle="${salleId}"
                data-jour="${jourKey}"
                data-slot="${slot}">
                <span class="badge-content">
                    <span class="badge-time">${slot.replace(':','h')}</span>
                    <span class="badge-places">${placesRest} places rest.</span>
                </span>
            </label>
        `;
    });

    html += '</div>';
    zone.innerHTML = html;

    // Écouteurs sur les badges
    zone.querySelectorAll('.horaire-badge-item').forEach(badge => {
        badge.addEventListener('click', function () {
            // Déselectionne tout
            document.querySelectorAll('.horaire-badge-item').forEach(b => b.classList.remove('selected'));
            this.classList.add('selected');

            selectedCreneauId = this.dataset.idCreneau;
            selectedSalleId   = this.dataset.salle;
            const placesMax   = parseInt(this.dataset.places);

            document.getElementById('hidden_id_creneau').value = selectedCreneauId || '';

            // Affiche + met à jour le champ nb personnes de cette salle
            const nbZone = document.getElementById('nb_zone_' + salleId);
            nbZone.style.display = 'block';
            const nbInput = document.getElementById('nb_personnes_' + salleId);
            nbInput.max = placesMax;
            if (parseInt(nbInput.value) > placesMax) nbInput.value = placesMax;
            document.getElementById('jauge-info-' + salleId).textContent = placesMax + ' place(s) disponible(s) sur ce créneau.';

            // Récap
            const slotLabel  = this.dataset.slot.replace(':','h');
            const jourLabel  = CRENEAUX_DATA[this.dataset.jour].label;
            const salleEl    = document.querySelector(`details[data-salle-id="${salleId}"]`);
            const salleNom   = salleEl ? salleEl.querySelector('.salle-summary-text h4').textContent : '';
        });
    });
}

// Ferme les autres accordéons quand on en ouvre un
document.querySelectorAll('.salle-accordion').forEach(details => {
    details.addEventListener('toggle', function () {
        if (this.open) {
            document.querySelectorAll('.salle-accordion').forEach(other => {
                if (other !== this) other.removeAttribute('open');
            });
        }
    });
});

// Validation avant envoi
document.getElementById('formReservation').addEventListener('submit', function(e) {
    const idCreneau = document.getElementById('hidden_id_creneau').value;
    if (!idCreneau) {
        e.preventDefault();
        alert('Veuillez sélectionner un créneau horaire avant de confirmer.');
    }
});
</script>

<?php include('footer.php'); ?>