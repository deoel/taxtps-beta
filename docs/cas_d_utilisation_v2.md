# CAS D'UTILISATION EXHAUSTIFS : SYSTÈME TAXTPS

## 1. ADMINISTRATION ET GESTION DU RÉFÉRENTIEL

**Acteurs :** Administrateur Système / Super-utilisateur

### Cas d'utilisation : Configuration de la structure administrative

* **Fonctionnalités :**
* Gestion (CRUD) complète des **Provinces**.
* Gestion des **Bureaux de Douane** : création, modification, suppression.
* Paramétrage géographique : Saisie des coordonnées GPS (Latitude/Longitude) par bureau.
* Activation/Désactivation de la contrainte `gps_required` par bureau pour forcer ou non le géofencing.



### Cas d'utilisation : Gestion des Utilisateurs et Habilitations

* **Fonctionnalités :**
* Gestion des comptes : Admin (War Room), Manager (Supervision), Agent (Terrain).
* **Rattachement Bureau :** Liaison technique obligatoire d'un Agent à un Bureau de Douane via le Trait `HasOffice` pour filtrer ses données mobiles.
* Gestion des permissions via Spatie (Laravel Permission).



### Cas d'utilisation : Gestion des Exemptions

* **Fonctionnalités :**
* Maintenance de la table des **Codes SH exemptés** (basée sur l'Annexe I de l'Arrêté).
* Interface permettant d'ajouter ou retirer des produits de la liste d'exemption pour impacter immédiatement le moteur de risque.



---

## 2. FLUX DE DONNÉES ET INTELLIGENCE (BACKEND)

**Acteurs :** Administrateur / Manager / Système (Automatisé)

### Cas d'utilisation : Importation Massive SYDONIA

* **Fonctionnalités :**
* Upload de fichiers Excel SYDONIA.
* **Mapping Intelligent :** Correspondance automatique des colonnes (REF_ENREG, CIF, DATE, SH, etc.).
* **Gestion d'Intégrité :** Utilisation d'une clé composite (`Numéro DCL` + `Code Bureau` + `Année`) pour empêcher les doublons et permettre la mise à jour (Upsert).



### Cas d'utilisation : Analyse de Risque et Ciblage (Consistency Engine)

* **Fonctionnalités :**
* **Détection d'anomalies :** Marquage automatique du statut en "Suspect" si le montant TPS déclaré est égal à 0 alors que le code SH n'est pas dans la liste des exemptions.
* **Scoring de Priorité (1-10) :**
* Calcul basé sur la valeur CIF élevée.
* Bonus de score pour les marchandises sensibles (Alcool, Tabac, etc.).
* Bonus de score pour les dossiers marqués "Suspect".





### Cas d'utilisation : Journalisation de Sécurité (Audit Trail)

* **Fonctionnalités :**
* **Boîtier Noir :** Capture automatique de chaque action utilisateur.
* Tracement des 4W : Who (Utilisateur), What (Action + Valeurs avant/après), When (Timestamp), Where (IP/Bureau).



---

## 3. OPÉRATIONS DE TERRAIN (APPLICATION MOBILE)

**Acteurs :** Agent de Terrain

### Cas d'utilisation : Identification et Recherche de Dossiers

* **Fonctionnalités :**
* **Scanner QR/Barcode :** Lecture directe de la référence `REF_ENREG` sur les documents physiques.
* Recherche textuelle pour retrouver une déclaration dans la base locale.



### Cas d'utilisation : Traitement Prioritaire (Ciblage Intelligent)

* **Fonctionnalités :**
* Affichage de la "To-Do List" triée dynamiquement par `priority_score`.
* Indicateur visuel (badge rouge "URGENT") pour les dossiers ayant un score > 8.



### Cas d'utilisation : Certification de Preuve (Validation)

* **Fonctionnalités :**
* Capture photo obligatoire : Note de Perception et RRJ.
* Géolocalisation forcée lors de l'appui sur "Valider".



### Cas d'utilisation : Sécurisation Géographique (Géofencing)

* **Fonctionnalités :**
* Calcul de distance (Formule Haversine) entre la position de l'agent et le bureau assigné.
* **Blocage :** Interdiction de transmettre si l'agent est à plus de 500m (si activé).
* Tag de certification GPS pour l'audit.



### Cas d'utilisation : Travail en Zone Déconnectée (Offline Mode)

* **Fonctionnalités :**
* **Compression d'image :** Réduction automatique à 800px (max 500 Ko) avant stockage local.
* **File d'attente :** Stockage dans `AsyncStorage` en l'absence de réseau.
* **Auto-Sync :** Envoi automatique en arrière-plan dès détection du réseau via NetInfo.



---

## 4. PILOTAGE ET AIDE À LA DÉCISION (WAR ROOM)

**Acteurs :** Manager / Décideur FPS

### Cas d'utilisation : Monitoring en Temps Réel

* **Fonctionnalités :**
* Dashboard dynamique sous Laravel Livewire.
* KPIs : Total SYDONIA, Total Recouvré TAXTPS, Écart monétaire, Taux de couverture (%).
* **Carte de Chaleur :** Visualisation Leaflet des zones à forte suspicion de fraude.



### Cas d'utilisation : Réconciliation et Traitement des Litiges

* **Fonctionnalités :**
* Interface de comparaison en "Split Screen" : Données importées vs Photo RRJ reçue du terrain.
* Saisie du "Montant Final Validé" après inspection visuelle.
* Obligation de saisir un motif pour toute modification de montant.



### Cas d'utilisation : Analyse du Manque à Gagner

* **Fonctionnalités :**
* Filtre sur les dossiers prioritaires/suspects n'ayant pas fait l'objet d'un contrôle physique.
* Estimation financière des recettes non encore sécurisées.



---

## 5. REPORTING ET CONFORMITÉ LÉGALE

**Acteurs :** Manager / Auditeurs (IGF/Direction)

### Cas d'utilisation : Certification Individuelle

* **Fonctionnalités :**
* Génération de Certificats PDF officiels.
* Inclusion du Hash de sécurité et du QR Code pour vérifier l'authenticité du document hors système.



### Cas d'utilisation : Reporting de Performance

* **Fonctionnalités :**
* Export Excel périodique.
* Statistiques de performance par Agent, par Bureau et par Province.



### Cas d'utilisation : Audit de Transparence

* **Fonctionnalités :**
* Accès "Auditeur" en lecture seule.
* Recherche multicritères dans les journaux d'activité (Audit Trail) pour justifier chaque centime recouvré.

