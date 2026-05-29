# URSSAFC

Outil CLI PHP permettant d'enregistrer des autoentrepreneurs et simuler des déclarations mensuelles.

---

# Installation

Cloner le projet :

```bash
git clone [URL_DU_REPO](https://github.com/lbrugiere/Urssafc.git)
cd urssafc
```

Installer les dépendances :

```bash
composer install
```

---

# Lancer le programme

## Ajouter une autoentreprise

```bash
php urssafc.php add "John Incubator Jones" 18812369758410 bic-vente ps
```

## Lister les autoentreprises

```bash
php urssafc.php ls
```

## Simuler une déclaration

```bash
php urssafc.php dry-declare 1 3235
```

---

# Régimes disponibles

## Activités

- bic-vente
- bic
- bnc

## Régimes fiscaux

- ps
- vfl

---

# Structure du projet

```text
src/
├── Contractor.php
├── ContractorRepository.php
├── AbstractActivityStrategy.php
├── BicStrategy.php
├── BicVenteStrategy.php
├── BncStrategy.php
├── ActivityStrategyFactory.php

data/
└── database.sqlite

urssafc.php
composer.json
```

---
