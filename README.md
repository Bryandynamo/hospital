# Hospital Management System

## Description
Ce projet est un logiciel de gestion hospitalière développé en PHP et MySQL. Il permet de digitaliser l'ensemble des processus d'un établissement hospitalier avec des modules spécialisés, une gestion sécurisée des données médicales et des outils d'aide à la décision.

## Fonctionnalités principales
1. **Gestion financière**
   - Facturation automatisée intégrant assurances, tarifs variables et historiques de paiement
   - Comptabilité centralisée avec rapports financiers (CA, dépenses, balance comptable)
   - Génération de bons de commande électroniques avec workflow de validation hiérarchique

2. **Logistique et pharmacie**
   - Gestion de stock en temps réel pour :
     - Produits pharmaceutiques (lot, DLUO, fournisseurs)
     - Matériel médical et laboratoire
   - Alertes automatiques pour :
     - Niveaux de stock critiques (< seuil défini)
     - Péremption des produits (30/60/90 jours avant expiration)
     - Ruptures de stock prévisibles

3. **Gestion des patients**
   - Dossier médical électronique unifié (antécédents, prescriptions, comptes-rendus)
   - Portail patient sécurisé pour :
     - Prise de rendez-vous en ligne
     - Accès aux résultats d'analyses
     - Avis et évaluations des services
     - Système d'alertes pour rappels de consultations/vaccinations

4. **Module décisionnel**
   - IA analytique pour :
     - Prédiction des admissions urgentes
     - Optimisation des plannings médicaux
     - Détection d'interactions médicamenteuses
   - Tableaux de bord personnalisables (activité, performance, qualité)

5. **Sécurité et conformité**
   - Gestion fine des accès (RBAC) avec :
     - Profils utilisateurs (admin, médecin, pharmacien, etc.)
     - Journalisation des accès aux données sensibles
     - Chiffrement des données (AES-256) et sauvegardes quotidiennes hors-site
   - Conformité RGPD et certification HDS pour les données de santé

6. **Gestion des rendez-vous**
   - Système avancé de gestion des rendez-vous avec calendrier et rappels.
   - Intégration avec le dossier médical électronique pour la planification des consultations.

7. **Module de téléconsultation**
   - Téléconsultation via vidéo pour les consultations à distance.
   - Gestion des rendez-vous et des consultations vidéo sécurisées.

8. **Notifications et alertes**
   - Notifications en temps réel pour les événements critiques (ruptures de stock, alertes médicales, etc.).
   - Intégration avec des services de messagerie (email, SMS).

9. **Rapports et statistiques**
   - Génération de rapports et statistiques sur les opérations hospitalières.
   - Visualisation des données sous forme de graphiques.

## Installation
1. Cloner le dépôt :
   ```bash
   git clone https://github.com/Bryandynamo/hospital-management-system.git
   ```

2. Naviguer dans le répertoire du projet :
   ```bash
   cd hospital-management-system
   ```

3. Installer les dépendances via Composer :
   ```bash
   composer install
   ```

4. Configurer la base de données en éditant le fichier `config/config.php` avec vos paramètres de connexion.

5. Importer le script SQL pour initialiser la base de données :
   ```bash
   mysql -u root -p hospital_management < database/init.sql
   ```

6. Démarrer le serveur PHP :
   ```bash
   php -S localhost:8000 -t public
   ```

7. Accéder à l'application via le navigateur :
   ```url
   http://localhost:8000
   ```

## Contribuer
Les contributions sont les bienvenues. Veuillez soumettre une pull request ou ouvrir une issue pour discuter des modifications que vous souhaitez apporter.