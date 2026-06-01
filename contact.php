<?php include('header.php'); ?>

<div class="form-container">
    <h1>Contactez-nous</h1>
    <p style="text-align: center; margin-bottom: 30px;">Dites-nous en quelques mots l'objet de votre demande et vos besoins. Nous vous répondrons dans les plus brefs délais avec une solution adaptée !</p>

    <form action="contact.php" method="POST">
        <div class="form-group">
            <input type="text" name="nom_prenom" placeholder="Nom / Prénom" required>
        </div>
        <div class="form-group">
            <input type="tel" name="telephone" placeholder="Numéro de téléphone">
        </div>
        <div class="form-group">
            <input type="email" name="email" placeholder="email@gmail.com" required>
        </div>
        <div class="form-group">
            <input type="text" name="objet" placeholder="Objet">
        </div>
        <div class="form-group">
            <textarea name="message" rows="5" placeholder="Écrivez votre message ici" required></textarea>
        </div>
        <button type="submit" class="btn-submit" style="background-color: var(--cyan-medium);">ENVOYER</button>
    </form>
</div>

<?php include('footer.php'); ?>