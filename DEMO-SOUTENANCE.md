# Guide de demonstration - Soutenance Licence IG

Guide officiel pour presenter le SIRH de Khadija Consulting (gestion des RH, employes, salaires, formations).

## 1. Demarrage rapide (2 min)
1. Ouvrir Laragon puis demarrer Apache + MySQL.
2. Le projet est installe dans `C:\laragon\www\sirh-khadija` (visible dans Laragon).
3. Ouvrir le navigateur sur `http://sirh-khadija.test` (page de connexion).
4. Si besoin de reconstruire la base : `php artisan migrate:fresh --seed` dans le dossier projet.

## 2. Comptes de demonstration
| Role | Email | Mot de passe |
| --- | --- | --- |
| Administrateur | admin@khadijaconsulting.sn | password |
| RH | rh@khadijaconsulting.sn | password |
| Manager | manager@khadijaconsulting.sn | password |

## 3. Deroulement de la demonstration (12-15 min)

### Scenario 1 - Connexion et tableau de bord (2 min)
1. Ouvrir `http://sirh-khadija.test`, la page redirige vers la connexion.
2. Se connecter avec le compte **Administrateur**.
3. Montrer le tableau de bord : effectif, masse salariale, graphiques (Chart.js), statut des conges.

### Scenario 2 - Gestion des employes (3 min)
1. Menu `Employes` : liste avec recherche, filtre par departement et statut.
2. Ouvrir la fiche detaillee d'un employe (identite, poste, contrat, bulletins).
3. `Ajouter un employe` : saisir les infos, le matricule `KC-xxx` est genere automatiquement.
4. Modifier un employe (changement de poste ou de salaire de base).

### Scenario 3 - Presences et conges (3 min)
1. Menu `Presences` : feuille de presence du jour, pointage, heures supplementaires.
2. Menu `Conges` : demander un conge puis le valider avec le compte **Manager** (garde une trace).

### Scenario 4 - Formations (2 min)
1. Menu `Formations` : plan de formation du cabinet.
2. Creer une formation puis inscrire des employes participants.

### Scenario 5 - Paie et bulletins (3 min)
1. Menu `Bulletins de paie` : generer les bulletins du mois.
2. Ouvrir un bulletin : salaire brut (base + primes + indemnites), cotisations (6%), impot (8%), net a payer.
3. Imprimer ou exporter le bulletin.

### Scenario 6 - Controle d'acces (1 min)
1. Montrer qu'un **employe** ne voit que son espace personnel (pas la liste des RH).

## 4. Points forts du projet
- Digitalisation complete du processus RH du cabinet.
- Architecture MVC (Laravel) : modele / vue / controleur.
- Base relationnelle de plus de 10 tables (employes, contrats, presences, conges, formations, primes, bulletins).
- Regles de gestion automatisees : matricule auto, calcul de paie, validation des conges.
- Controle d'acces par roles (admin / rh / manager / employe) avec middleware.
- Interface moderne et responsive (Tailwind CSS, Chart.js).

## 5. Questions frequentes du jury
- Pourquoi Laravel ? Framework MVC robuste, securite integree (CSRF, hachage des mots de passe), ecosysteme riche.
- Comment est calcule le salaire ? Brut = base + primes + heures supplementaires + indemnites; cotisations 6% puis impot 8%; net = brut - retenues.
- Comment est gere l'acces ? Chaque role possede des permissions via un middleware `role`; les donnees sensibles sont reservees a l'admin/RH.
- Limites et perspectives ? Taux parametrables, module recrutement et paie IPRES/CNSS a approfondir, sauvegarde automatique a mettre en place.
- Deploiement en production ? Possible via MySQL, Apache/Nginx natifs (ou Docker/Render pour le cloud).