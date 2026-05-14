# 📑 CONCEPTION TECHNIQUE TAXTPS V6.0 : LE RÉFÉRENTIEL UNIQUE

## 📌 INTRODUCTION

Ce document constitue le cahier des charges technique définitif. Il fusionne la puissance de **Laravel 13/Livewire 4** avec les données réelles du terrain pour sécuriser le recouvrement de la Taxe de Promotion de la Santé (TPS).

---

## ÉTAPE 0 : SOCLE TECHNIQUE ET AUTHENTIFICATION

### 1. Stack Technologique Finale

* **Framework :** Laravel 13+ avec MySQL.
* **Authentification :** Laravel Fortify (Livewire Starter Kit).
* 
**Frontend :** Livewire v4 + Volt (Single File Components) + Livewire Flux.


* **Sécurité & Rôles :** Spatie Laravel Permission (Admin, Manager, Agent).
* 
**Architecture :** Utilisation systématique de **Services** pour la logique métier et de **Traits** pour les comportements partagés (ex: `HasOffice`).



### 2. Architecture des Tables "Lieux" et "Utilisateurs"

* **`provinces`** : `id`, `name`.
* 
**`customs_offices`** : `id`, `province_id`, `name`, `code_bureau` (ex: 701B), `latitude`, `longitude`, `gps_required` (boolean).


* 
**`users`** : Ajout de `customs_office_id` (FK) et `role`.



---

## ÉTAPE 1 : LE SOCLE DE DONNÉES (DCL & PREUVES)

### 1. Table `declarations` (Structure Réaliste)

Inspirée de votre fichier SYDONIA, elle doit contenir :

* `numero_dcl` (string, unique), `date_enreg` (date), `annee` (integer).
* `importateur` (string), `nif_importateur` (string).
* `code_sh` (string), `montant_cif` (decimal 15,2), `montant_tps_sydonia` (decimal 15,2).
* 
`status` (en_attente, suspect, valide, litige) et `priority_score` (0-10).


* `customs_office_id` (FK vers le bureau de douane).

### 2. Table `evidences` (Preuves de Terrain)

* `declaration_id` (FK), `image_path` (Photo du RRJ/Note), `latitude_validation`, `longitude_validation`, `is_within_geofence` (boolean), `validated_at`.

---

## ÉTAPE 2 : STRATÉGIE DE DONNÉES (SEEDER VS API)

Puisque l'API n'est pas encore disponible, nous utilisons un **Database Seeder** pour simuler un environnement de production.

#### PROMPT : Génération du Seeder "SYDONIA_FAKE"

> "Génère un Seeder Laravel `SydoniaSeeder`.
> 1. Il doit créer 500 déclarations aléatoires réparties sur les bureaux de douane existants.
> 2. **Logique de suspicion :** 20% des dossiers doivent avoir un `montant_tps_sydonia` à 0 alors que leur `code_sh` n'est pas dans la liste des exonérations (pour tester le moteur de risque).
> 3. Utilise les noms de colonnes : `REF_ENREG` pour le numéro, `CIF_GENERAL` pour le montant, et `CODE_TARIF` pour le code SH."
> 
> 

---

## ÉTAPE 3 : RÉFÉRENTIELS INTÉGRÉS (DONNÉES À COPIER-COLLER)

### 1. Liste des Bureaux de Douane (Extrait du PDF)

Utilisez ces codes pour vos tests et votre base de données:

| Code Bureau | Nom / Entrepôt associé | Province (Cible) |
| --- | --- | --- |
| **701B** | NIK INTERNATIONAL / SOCODAM / EP VILLE | Haut-Katanga |
| **702B** | DHL / AERO LUANO | Haut-Katanga |
| **703B** | SNCC | Haut-Katanga |
| **705B** | KASUMBALESA (Frontière) | Haut-Katanga |
| **510B** | KASENYI | Ituri |
| **722B** | WISKY / KBP EXPORT | Haut-Katanga |

### 2. Liste Exhaustive des Exonérations (Arrêté 2026)

Ces produits pharmaceutiques ne doivent pas être marqués comme "Suspects" si la taxe est à 0:

| Rubrique Tarière | Désignation du Produit |
| --- | --- |
| **1108.11.10** | Amidon de froment |
| **1108.12.10** | Amidon de Maïs |
| **1108.19.10** | Glycolate sodique d'amidon |
| **1211.90.10** | Plantes médicinales / Écorces de quinquina |
| **1301.20.10** | Gomme Arabique |
| **1301.90.10** | Balsam Perou / Gomme Acacia |
| **1515.30.00** | Huile de Ricin |
| **1702.11.00** | Lactose et sirop de lactose |
| **1702.30.10** | Dextrose blanc (Glucose) |
| **2501.00.10** | Chlorure de Sodium |
| **2520.10.10** | Gypse / Sulfate de Calcium |
| **2801.20.00** | Iode |
| **2833.21.00** | Sulfate de Magnésium |
| **2936.xx.xx** | Toutes les Vitamines et leurs dérivés |
| **3004.xx.xx** | Médicaments constitués par des produits mélangés |

---

## ÉTAPE 4 : MOBILITÉ ET "WAR ROOM" (DÉTAILS V5.0 RÉINTÉGRÉS)

* 
**Résilience Mobile :** L'application mobile doit intégrer la compression d'image (800px max, < 500 Ko) et le stockage `AsyncStorage` pour le mode offline.


* 
**Géofencing :** Si `gps_required` est vrai pour un bureau, la validation est bloquée si la distance Haversine entre l'agent et le bureau est > 500m.


* 
**War Room (Flux) :** Dashboard avec KPIs en temps réel (Total SYDONIA vs Réel recouvré) et carte de chaleur Leaflet montrant les zones de fraude potentielle.


* 
**Audit Trail :** Log systématique (Who, What, When, Where) de chaque modification de statut ou de montant.



---

## 📑 PLAN DE VALIDATION IMMÉDIAT

1. **Peuplement :** Lancer le `SydoniaSeeder` pour simuler l'activité de 10 bureaux de douane.
2. **Ciblage :** Vérifier que les produits pharmaceutiques ci-dessus ne sont pas marqués "Suspects".
3. **Terrain :** Simuler une validation mobile et vérifier l'apparition instantanée dans la "War Room" Livewire Flux.