-- Départements
CREATE TABLE departements (
  id          INTEGER PRIMARY KEY AUTOINCREMENT,
  nom         TEXT NOT NULL,
  description TEXT
);

-- Types de congé
CREATE TABLE types_conge (
  id            INTEGER PRIMARY KEY AUTOINCREMENT,
  libelle       TEXT NOT NULL,
  jours_annuels INTEGER NOT NULL DEFAULT 0,
  deductible    INTEGER NOT NULL DEFAULT 1  -- 1 = oui, 0 = non
);

-- Employés
CREATE TABLE employes (
  id               INTEGER PRIMARY KEY AUTOINCREMENT,
  nom              TEXT NOT NULL,
  prenom           TEXT NOT NULL,
  email            TEXT NOT NULL UNIQUE,
  password         TEXT NOT NULL,
  role             TEXT NOT NULL DEFAULT 'employe'
                   CHECK (role IN ('employe','rh','admin')),
  departement_id   INTEGER,
  date_embauche    DATE,
  actif            INTEGER NOT NULL DEFAULT 1,  -- 1 = actif, 0 = désactivé
  FOREIGN KEY (departement_id) REFERENCES departements (id)
    ON DELETE SET NULL
);

-- Soldes
CREATE TABLE soldes (
  id              INTEGER PRIMARY KEY AUTOINCREMENT,
  employe_id      INTEGER NOT NULL,
  type_conge_id   INTEGER NOT NULL,
  annee           INTEGER NOT NULL,
  jours_attribues INTEGER NOT NULL DEFAULT 0,
  jours_pris      INTEGER NOT NULL DEFAULT 0,
  UNIQUE (employe_id, type_conge_id, annee),
  FOREIGN KEY (employe_id)    REFERENCES employes    (id) ON DELETE CASCADE,
  FOREIGN KEY (type_conge_id) REFERENCES types_conge (id) ON DELETE CASCADE
);

-- Congés
CREATE TABLE conges (
  id               INTEGER PRIMARY KEY AUTOINCREMENT,
  employe_id       INTEGER NOT NULL,
  type_conge_id    INTEGER NOT NULL,
  date_debut       DATE NOT NULL,
  date_fin         DATE NOT NULL,
  nb_jours         INTEGER NOT NULL DEFAULT 0,
  motif            TEXT,
  statut           TEXT NOT NULL DEFAULT 'en_attente'
                   CHECK (statut IN ('en_attente','approuvee','refusee','annulee')),
  commentaire_rh   TEXT,
  created_at       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  traite_par       INTEGER,
  FOREIGN KEY (employe_id)    REFERENCES employes    (id) ON DELETE CASCADE,
  FOREIGN KEY (type_conge_id) REFERENCES types_conge (id) ON DELETE RESTRICT,
  FOREIGN KEY (traite_par)    REFERENCES employes    (id) ON DELETE SET NULL,
  CHECK (date_fin >= date_debut)
);

-- Données initiales
INSERT INTO departements (nom, description) VALUES
  ('Informatique',  'Développement et infrastructure'),
  ('Ressources Humaines', 'Gestion du personnel'),
  ('Finance',       'Comptabilité et budget');

INSERT INTO types_conge (libelle, jours_annuels, deductible) VALUES
  ('Congé annuel',       30, 1),
  ('Congé maladie',      15, 1),
  ('Congé sans solde',    0, 0);

INSERT INTO employes (nom, prenom, email, password, role, departement_id, date_embauche, actif) VALUES
  ('Admin',   'Sys',    'admin@techmada.mg',   '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin',   2, '2022-01-01', 1), 
  ('Rakoto',  'Jean',   'jean@techmada.mg',    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'employe', 1, '2023-03-15', 1),
  ('Rasoa',   'Marie',  'marie@techmada.mg',   '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'rh',      2, '2022-06-01', 1);

-- Soldes initiaux (année en cours)
INSERT INTO soldes (employe_id, type_conge_id, annee, jours_attribues, jours_pris) VALUES
  (2, 1, strftime('%Y','now'), 30, 0),
  (2, 2, strftime('%Y','now'), 15, 0),
  (3, 1, strftime('%Y','now'), 30, 0),
  (3, 2, strftime('%Y','now'), 15, 0);

-- Vue utilitaire
CREATE VIEW v_soldes AS
SELECT
  s.id,
  s.employe_id,
  e.prenom || ' ' || e.nom AS employe,
  tc.libelle               AS type_conge,
  s.annee,
  s.jours_attribues,
  s.jours_pris,
  (s.jours_attribues - s.jours_pris) AS jours_restants
FROM soldes s
JOIN employes    e  ON e.id  = s.employe_id
JOIN types_conge tc ON tc.id = s.type_conge_id;
