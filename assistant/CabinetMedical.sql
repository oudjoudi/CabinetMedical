CREATE TABLE IF NOT EXISTS USAGERS
(
  id            INT(11)      NOT NULL AUTO_INCREMENT,
  id_referent   BIGINT(4)    NOT NULL,
  civilite      VARCHAR(5)   NULL,
  nom           VARCHAR(255) NULL,
  prenom        VARCHAR(255) NULL,
  adresse       TEXT         NULL,
  dateNaissance DATE         NULL,
  lieuNaissance VARCHAR(255) NULL,
  numSecu       VARCHAR(128) NULL
  ,
  PRIMARY KEY (ID)
);

# -----------------------------------------------------------------------------
#       INDEX DE LA TABLE USAGERS
# -----------------------------------------------------------------------------


CREATE INDEX I_FK_USAGERS_MEDECINS
ON USAGERS (id_referent ASC);

# -----------------------------------------------------------------------------
#       TABLE : MEDECINS
# -----------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS MEDECINS
(
  id       INT(11)      NOT NULL AUTO_INCREMENT,
  civilite VARCHAR(5)   NULL,
  nom      VARCHAR(255) NULL,
  prenom   VARCHAR(255) NULL
  ,
  PRIMARY KEY (ID)
);

# -----------------------------------------------------------------------------
#       TABLE : CONSULTATION
# -----------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS CONSULTATION
(
  id            INT(11)        NOT NULL AUTO_INCREMENT,
  usager        INT(11)        NOT NULL,
  medecin       INT(11)        NOT NULL,
  dateConsult   DATE           NULL,
  heureConsult  TIME           NULL,
  dureeConsult  INT(11)        NULL DEFAULT 30
  ,
  PRIMARY KEY (ID)
);

# -----------------------------------------------------------------------------
#       INDEX DE LA TABLE CONSULTATION
# -----------------------------------------------------------------------------


CREATE INDEX I_FK_CONSULTATION_USAGERS
ON CONSULTATION (usager ASC);

CREATE INDEX I_FK_CONSULTATION_MEDECINS
ON CONSULTATION (medecin ASC);


# -----------------------------------------------------------------------------
#       CREATION DES REFERENCES DE TABLE
# -----------------------------------------------------------------------------


ALTER TABLE USAGERS
ADD FOREIGN KEY FK_USAGERS_MEDECINS (ID_REFERENT)
REFERENCES MEDECINS (ID);


ALTER TABLE CONSULTATION
ADD FOREIGN KEY FK_CONSULTATION_USAGERS (USAGER)
REFERENCES USAGERS (ID);


ALTER TABLE CONSULTATION
ADD FOREIGN KEY FK_CONSULTATION_MEDECINS (MEDECIN)
REFERENCES MEDECINS (ID);


# -----------------------------------------------------------------------------
#       EXEMPLES POUR LE CABINET MEDICAL
# -----------------------------------------------------------------------------
INSERT INTO `MEDECINS` (`id`, `civilite`, `nom`, `prenom`) VALUES
  (1, 'Dr', 'DOUSSEAUD', 'Julien'),
  (2, 'Dr', 'TAMALOU', 'Momo');

INSERT INTO `USAGERS` (`id`, `id_referent`, `civilite`, `nom`, `prenom`, `adresse`, `dateNaissance`, `lieuNaissance`, `numSecu`) VALUES
  (1, 1, 'Mr', 'RODRIGUEZ', 'Guillaume', '12 rue de roses', '1995-05-18', 'Toulouse', '1943155564978'),
  (2, 1, 'Mlle', 'DURAND', 'Jennifer', '2 avenue de l''impasse', '1990-01-09', 'Toulouse', '290090314514'),
  (3, 1, 'Mme', 'PORTMAN', 'Natalie', '5 rue d''Hollywood', '1980-10-20', 'Paris', '24518415414586'),
  (4, 2, 'Mr', 'DUPONT', 'Lucas', '69 chemin de la porte', '2000-09-01', 'Muret', '148548425465'),
  (5, 2, 'Mme', 'SHINRA', 'Cissnei', '47 boulevard de la republique', '1992-05-30', 'Toulouse', '247548425484');

INSERT INTO `CONSULTATION` (`id`, `usager`, `medecin`, `dateConsult`, `heureConsult`, `dureeConsult`) VALUES
  (1, 1, 1, '2015-01-01', '14:40:00', 45),
  (2, 2, 1, '2014-12-31', '13:00:00', 15),
  (3, 1, 2, '2014-12-06', '12:00:00', 50);