<?php include('header.php'); ?>

<div class="container">
    <section class="intro-section">
        <h1 class="pixel-title">Contact<span class="red-dot">.</span></h1>
        <p>Dites-nous en quelques mots l'objet de votre demande et vos besoins. Nous vous répondrons dans les plus brefs délais.</p>
    </section>

    <div class="contact-layout">

        <!-- Formulaire -->
        <div class="contact-form-wrapper">
            <form id="formContact" action="contact.php" method="POST">

                <div class="form-section-linear">
                    <h3><span class="section-icon">01</span> Vos coordonnées</h3>
                    <div class="input-group">
                        <label for="nom_prenom">Nom / Prénom <span class="red-dot">*</span></label>
                        <input type="text" id="nom_prenom" name="nom_prenom" placeholder="Ex : Lucas Dupont" required>
                    </div>
                    <div class="input-group">
                        <label for="telephone">Numéro de téléphone</label>
                        <input type="tel" id="telephone" name="telephone" placeholder="Ex : +33 6 00 00 00 00">
                    </div>
                    <div class="input-group" style="margin-bottom: 0;">
                        <label for="email">Adresse e-mail <span class="red-dot">*</span></label>
                        <input type="email" id="email" name="email" placeholder="email@exemple.fr" required>
                    </div>
                </div>

                <div class="form-section-linear">
                    <h3><span class="section-icon">02</span> Votre message</h3>
                    <div class="input-group">
                        <label for="objet">Objet</label>
                        <input type="text" id="objet" name="objet" placeholder="Ex : Question sur l'exposition">
                    </div>
                    <div class="input-group" style="margin-bottom: 0;">
                        <label for="message">Message <span class="red-dot">*</span></label>
                        <textarea id="message" name="message" rows="6" placeholder="Écrivez votre message ici…" required></textarea>
                    </div>
                </div>

            </form>
        </div>

        <!-- Infos bureau -->
        <aside class="contact-info">
            <div class="contact-info-block">
                <h4 class="contact-info-title">Bureau</h4>
                <address class="contact-address">
                    Chambéry<br>
                    IUT Savoie Mont Blanc
                </address>
                <p class="contact-name">François Piranda</p>
                <div class="contact-divider"></div>
                <p class="contact-label">Contact</p>
                <a href="tel:+330000000000" class="contact-phone">+33 0000000000</a>
            </div>
        </aside>

    </div>

    <div class="form-footer-linear" style="max-width: 850px; margin: 24px auto 80px;">
        <button type="submit" form="formContact" class="btn-submit-large">Envoyer</button>
    </div>
</div>

<?php include('footer.php'); ?>
