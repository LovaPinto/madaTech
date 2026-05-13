
CREATE DATABASE IF NOT EXISTS techmada_rh
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE techmada_rh;


CREATE TABLE departements (
  id          INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  nom         VARCHAR(100)    NOT NULL,
  description TEXT,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE types_conge (
  id            INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  libelle       VARCHAR(100)  NOT NULL,
  jours_annuels INT           NOT NULL DEFAULT 0,
  deductible    TINYINT(1)    NOT NULL DEFAULT 1,  -- 1 = oui, 0 = non
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE employes (
  id               INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  nom              VARCHAR(100)  NOT NULL,
  prenom           VARCHAR(100)  NOT NULL,
  email            VARCHAR(191)  NOT NULL,
  password         VARCHAR(255)  NOT NULL,
  role             ENUM('employe','rh','admin') NOT NULL DEFAULT 'employe',
  departement_id   INT UNSIGNED,
  date_embauche    DATE,
  actif            TINYINT(1)    NOT NULL DEFAULT 1,  -- 1 = actif, 0 = désactivé
  PRIMARY KEY (id),
  UNIQUE KEY uq_employes_email (email),
  CONSTRAINT fk_employes_departement
    FOREIGN KEY (departement_id) REFERENCES departements (id)
    ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE soldes (
  id              INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  employe_id      INT UNSIGNED  NOT NULL,
  type_conge_id   INT UNSIGNED  NOT NULL,
  annee           YEAR          NOT NULL,
  jours_attribues INT           NOT NULL DEFAULT 0,
  jours_pris      INT           NOT NULL DEFAULT 0,
  PRIMARY KEY (id),
  UNIQUE KEY uq_soldes_employe_type_annee (employe_id, type_conge_id, annee),
  CONSTRAINT fk_soldes_employe
    FOREIGN KEY (employe_id)    REFERENCES employes    (id) ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT fk_soldes_type_conge
    FOREIGN KEY (type_conge_id) REFERENCES types_conge (id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE conges (
  id               INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  employe_id       INT UNSIGNED  NOT NULL,
  type_conge_id    INT UNSIGNED  NOT NULL,
  date_debut       DATE          NOT NULL,
  date_fin         DATE          NOT NULL,
  nb_jours         INT           NOT NULL DEFAULT 0,
  motif            TEXT,
  statut           ENUM('en_attente','approuvee','refusee','annulee') NOT NULL DEFAULT 'en_attente',
  commentaire_rh   TEXT,
  created_at       DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  traite_par       INT UNSIGNED,                     
  PRIMARY KEY (id),
  CONSTRAINT fk_conges_employe
    FOREIGN KEY (employe_id)    REFERENCES employes    (id) ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT fk_conges_type_conge
    FOREIGN KEY (type_conge_id) REFERENCES types_conge (id) ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT fk_conges_traite_par
    FOREIGN KEY (traite_par)    REFERENCES employes    (id) ON UPDATE CASCADE ON DELETE SET NULL,
  CONSTRAINT chk_dates CHECK (date_fin >= date_debut)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



-- Départements
INSERT INTO departements (nom, description) VALUES
  ('Informatique',  'Développement et infrastructure'),
  ('Ressources Humaines', 'Gestion du personnel'),
  ('Finance',       'Comptabilité et budget');

-- Types de congé
INSERT INTO types_conge (libelle, jours_annuels, deductible) VALUES
  ('Congé annuel',       30, 1),
  ('Congé maladie',      15, 1),
  ('Congé sans solde',    0, 0);

-- Employés  (passwords = password_hash('Password1!', PASSWORD_BCRYPT) — à remplacer en prod)
INSERT INTO employes (nom, prenom, email, password, role, departement_id, date_embauche, actif) VALUES
  ('Admin',   'Sys',    'admin@techmada.mg',   '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin',   2, '2022-01-01', 1),
  ('Rakoto',  'Jean',   'jean@techmada.mg',    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'employe', 1, '2023-03-15', 1),
  ('Rasoa',   'Marie',  'marie@techmada.mg',   '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'rh',      2, '2022-06-01', 1);

-- Soldes initiaux (année en cours)
INSERT INTO soldes (employe_id, type_conge_id, annee, jours_attribues, jours_pris) VALUES
  (2, 1, YEAR(CURDATE()), 30, 0),
  (2, 2, YEAR(CURDATE()), 15, 0),
  (3, 1, YEAR(CURDATE()), 30, 0),
  (3, 2, YEAR(CURDATE()), 15, 0);

-- ============================================================
--  VUE utilitaire : soldes avec restant calculé
-- ============================================================
CREATE OR REPLACE VIEW v_soldes AS
SELECT
  s.id,
  s.employe_id,
  CONCAT(e.prenom, ' ', e.nom) AS employe,
  tc.libelle                   AS type_conge,
  s.annee,
  s.jours_attribues,
  s.jours_pris,
  (s.jours_attribues - s.jours_pris) AS jours_restants
FROM soldes s
JOIN employes    e  ON e.id  = s.employe_id
JOIN types_conge tc ON tc.id = s.type_conge_id;
