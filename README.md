# Symfony-formation-form
Inscription d'un utilisateur
Créer un formulaire qui permet d'inscrire un utilisateur avec les champs suivants : nom, prénom, email, numéro de téléphone, mot de passe.

Enregistrer en base de données la date de création de l'utilisateur ainsi que son pseudo (format : [nom]_[prénom]_[id]).

Quand l'utilisateur se connecte au site, enregistrer la date de sa dernière connexion.

Gestion des formations
Chaque utilisateur peut afficher, créer ou modifier ses formations avec les champs suivants : nom, date de création, date de modification.

Mettre en place un cache pour améliorer les performances lorsqu'un utilisateur peut ajouter des milliers de formations.

Envoi de données à un partenaire
A chaque création d'une formation, envoyer les données de la formation à un partenaire via API https://webhook.site/ (visiter le site avant pour avoir une url avec un token). Prévoir l'ajout de nouveaux partenaires dans le futur.
