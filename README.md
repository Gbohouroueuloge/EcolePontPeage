# Application de Gestion de Pont à Péage

## Description

L'application de gestion de pont à péage est un système numérique intégré permettant d'automatiser et de superviser l'ensemble des opérations liées au passage des véhicules. Elle couvre la détection des véhicules, l'encaissement des droits de passage, la gestion des agents de cabine, et la production de rapports statistiques à destination de la direction.

## Objectifs

Trois grandes dimensions définissent le projet :
- **Opérationnelle** : automatisation des passages, calcul tarifaire et levée de barrière en temps réel
- **Financière** : encaissement multi-modes (espèces, carte, abonnement)
- **Administrative** : gestion des agents, planification des équipes et rapports de performance

## Développement

Le projet est en cours de développement et est basé sur les technologies suivantes :
- **Php 8.2** : langage de programmation principal
- **HTML** : langage de marquage pour la structure de la page
- **JavaScript** : langage de programmation pour le frontend 
- **Tailwind CSS** : framework CSS pour le frontend

## Installation

Pour installer l'application, vous devez suivre les instructions suivantes :
1. Cloner le repository sur votre machine locale
2. Installer les dependencies avec :
```bash 
composer install 
npm install
```

3. Lancer l'application avec :
```bash
php -S localhost:8000 -t ./public
```