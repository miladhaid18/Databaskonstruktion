DROP DATABASE IF EXISTS a25milha;
CREATE DATABASE a25milha;
USE a25milha;

/*-----------------------------------------------------------
VECKA 2 - PUNKT 9, 10 & 11: DATABAS MED DENORMALISERING OCH INDEXERING
------------------------------------------------------------*/

CREATE TABLE Agent(
  AgentNamn CHAR(1) NOT NULL,
  AgentNr INTEGER NOT NULL,
  Fornamn VARCHAR(50) NOT NULL,
  Efternamn VARCHAR(50) NOT NULL,
  Lon INTEGER DEFAULT 13000,
  PRIMARY KEY (AgentNamn, AgentNr),
  /* Restriktion: Lonen maste ligga mellan 12000 och 25000 kr for handlaggare */
  CHECK (Lon >= 12000 AND Lon <= 25000),
  /* Restriktion: Agentnumret maste vara mellan 1 och 99 */
  CHECK (AgentNr > 0 AND AgentNr <= 99)
) ENGINE=INNODB;

/* HORISONTELL DENORMALISERING: Utlyft av stor textkolumn (Kommentar) */
CREATE TABLE AgentKommentar(
  AgentNamn CHAR(1) NOT NULL,
  AgentNr INTEGER NOT NULL,
  Kommentar VARCHAR(1024) NOT NULL,
  PRIMARY KEY (AgentNamn, AgentNr),
  FOREIGN KEY (AgentNamn, AgentNr) REFERENCES Agent(AgentNamn, AgentNr)
) ENGINE=INNODB;

CREATE TABLE Handlaggare(
  AgentNamn CHAR(1) NOT NULL,
  AgentNr INTEGER NOT NULL,
  PRIMARY KEY (AgentNamn, AgentNr),
  FOREIGN KEY (AgentNamn, AgentNr) REFERENCES Agent(AgentNamn, AgentNr)
) ENGINE=INNODB;

/* KODTABELL 1: Uppslagstabell för platser (Kodifiering 1) */
CREATE TABLE Plats(
  PlatsKod INTEGER NOT NULL,
  PlatsNamn VARCHAR(100) NOT NULL UNIQUE,
  PRIMARY KEY (PlatsKod)
) ENGINE=INNODB;

/* KODTABELL 2: Uppslagstabell för närkontaktsgrader (Kodifiering 2) */
CREATE TABLE GradBeskrivning(
  GradKod INTEGER NOT NULL,
  Beskrivning VARCHAR(100) NOT NULL,
  PRIMARY KEY (GradKod)
) ENGINE=INNODB;

/* DENORMALISERAD TABELL 1 (AKTIVA OBSERVATIONER - MERGE + KODIFIERING 1 & 2) */
CREATE TABLE Observation(
  ObsId INTEGER NOT NULL,
  Datum DATETIME NOT NULL,
  GradKod INTEGER NOT NULL,
  Sakerhet DECIMAL(5,2) NOT NULL,
  IncidentId INTEGER NOT NULL,
  IncidentNamn VARCHAR(100) NOT NULL,
  PlatsKod INTEGER NOT NULL,
  RegistreradAvNamn CHAR(1) NOT NULL,
  RegistreradAvNr INTEGER NOT NULL,
  PRIMARY KEY (ObsId),
  /* Restriktion: Narkontaktsgraden maste vara ett heltal fran 1 till 4 */
  CHECK (GradKod >= 1 AND GradKod <= 4),
  /* Restriktion: Sakerheten i procent maste vara mellan 1.00 och 114.00 */
  CHECK (Sakerhet >= 1.00 AND Sakerhet <= 114.00),
  FOREIGN KEY (RegistreradAvNamn, RegistreradAvNr) REFERENCES Handlaggare(AgentNamn, AgentNr),
  FOREIGN KEY (PlatsKod) REFERENCES Plats(PlatsKod),
  FOREIGN KEY (GradKod) REFERENCES GradBeskrivning(GradKod)
) ENGINE=INNODB;

/* VERTIKAL DENORMALISERING: Tabell för arkiverade/mörklagda observationer */
CREATE TABLE ARKIVERADObservation(
  ObsId INTEGER NOT NULL,
  Datum DATETIME NOT NULL,
  GradKod INTEGER NOT NULL,
  Sakerhet DECIMAL(5,2) NOT NULL,
  IncidentId INTEGER NOT NULL,
  IncidentNamn VARCHAR(100) NOT NULL,
  PlatsKod INTEGER NOT NULL,
  RegistreradAvNamn CHAR(1) NOT NULL,
  RegistreradAvNr INTEGER NOT NULL,
  PRIMARY KEY (ObsId),
  /* Restriktion: Narkontaktsgraden maste vara ett heltal fran 1 till 4 */
  CHECK (GradKod >= 1 AND GradKod <= 4),
  /* Restriktion: Sakerheten i procent maste vara mellan 1.00 AND 114.00 */
  CHECK (Sakerhet >= 1.00 AND Sakerhet <= 114.00),
  FOREIGN KEY (RegistreradAvNamn, RegistreradAvNr) REFERENCES Handlaggare(AgentNamn, AgentNr),
  FOREIGN KEY (PlatsKod) REFERENCES Plats(PlatsKod),
  FOREIGN KEY (GradKod) REFERENCES GradBeskrivning(GradKod)
) ENGINE=INNODB;

CREATE TABLE Person(
  PersonId CHAR(13) NOT NULL,
  Namn VARCHAR(100) NOT NULL,
  Kodnamn VARCHAR(50),
  PRIMARY KEY (PersonId)
) ENGINE=INNODB;

CREATE TABLE Rapporterat(
  PersonId CHAR(13) NOT NULL,
  ObsId INTEGER NOT NULL,
  PRIMARY KEY (PersonId, ObsId),
  FOREIGN KEY (PersonId) REFERENCES Person(PersonId),
  FOREIGN KEY (ObsId) REFERENCES Observation(ObsId)
) ENGINE=INNODB;

/* LOGGTABELL: lagrar loggraden fran triggern LOGGOBSERVATION */
CREATE TABLE ObservationLog(
  LogId INTEGER NOT NULL AUTO_INCREMENT,
  Operation VARCHAR(10) NOT NULL,
  Anvandare VARCHAR(100) NOT NULL,
  ObsId INTEGER NOT NULL,
  Tidpunkt DATETIME NOT NULL,
  PRIMARY KEY (LogId)
) ENGINE=INNODB;

-- =================================================================
-- PUNKT 10: INSERTS FÖR DENORMALISERING (TESTDATA)
-- =================================================================

INSERT INTO Agent(AgentNamn, AgentNr, Fornamn, Efternamn, Lon) VALUES
('J', 2, 'John', 'Doe', 14000),
('K', 5, 'Karl', 'Svensson', 16000),
('A', 8, 'Anna', 'Andersson', 13000),
('L', 1, 'Leif', 'Larsson', 20000);

INSERT INTO AgentKommentar(AgentNamn, AgentNr, Kommentar) VALUES
('J', 2, 'Att dolja sanningen for att skydda manskligheten.'),
('K', 5, 'Sanningen finns dar ute, men inte for alla.'),
('L', 1, 'En faltagent som inte arbetar som handlaggare.');

INSERT INTO Handlaggare(AgentNamn, AgentNr) VALUES
('J', 2),
('K', 5),
('A', 8);

INSERT INTO Plats(PlatsKod, PlatsNamn) VALUES
(1, 'Roswell, New Mexico'),
(2, 'Skovde, Vastra Gotaland'),
(3, 'Stockholm, Sodermalm');

INSERT INTO GradBeskrivning(GradKod, Beskrivning) VALUES
(1, 'Enkel iakttagelse pa avstand'),
(2, 'Fysiska spar eller avvikelser'),
(3, 'Visuell narkontakt'),
(4, 'Extrem narkontakt / Enlevering');

INSERT INTO Observation(ObsId, Datum, GradKod, Sakerhet, IncidentId, IncidentNamn, PlatsKod, RegistreradAvNamn, RegistreradAvNr) VALUES
(501, '2026-09-01 22:30:00', 3, 85.50, 101, 'Roswell-siktelsen', 1, 'J', 2),
(502, '2026-09-02 01:15:00', 1, 45.00, 102, 'Vastgota-ljusen', 2, 'K', 5),
(503, '2026-09-03 14:00:00', 4, 114.00, 103, 'Stockholms-ufot', 3, 'A', 8);

INSERT INTO ARKIVERADObservation(ObsId, Datum, GradKod, Sakerhet, IncidentId, IncidentNamn, PlatsKod, RegistreradAvNamn, RegistreradAvNr) VALUES
(509, '2020-05-10 03:00:00', 2, 99.00, 100, 'Morklagt Norrlandsljus', 2, 'J', 2);

INSERT INTO Person(PersonId, Namn, Kodnamn) VALUES
('19850512-1234', 'Erik Johansson', 'Vittnet'),
('19900824-5678', 'Maria Larsson', 'Astronomen'),
('19781103-9012', 'Olof Palme', 'Skattebetalaren'),
('ANON-999999-1', 'Anonym uppgiftslamnare', NULL);

INSERT INTO Rapporterat(PersonId, ObsId) VALUES
('19850512-1234', 501),
('19900824-5678', 502),
('19850512-1234', 503);

-- =================================================================
-- PUNKT 11: INDEXERING (MED MINST 2 OLIKA INDEX MED ENRADIGA KOMMENTARER)
-- =================================================================

-- INDEX FOR QUERY SELECT * FROM Observation ORDER BY Datum DESC;
CREATE INDEX OBSDATUM ON Observation(Datum DESC) USING BTREE;

-- INDEX FOR QUERY SELECT * FROM Person ORDER BY Namn ASC;
CREATE INDEX PERSONNAMN ON Person(Namn ASC) USING BTREE;

-- ================================================================= 
-- PUNKT 12: RÄTTIGHETER (MINST 3 OLIKA GRANT / REVOKE KOMMANDON)
-- =================================================================

-- Skapa en användare för handläggarnas applikation
CREATE USER IF NOT EXISTS 'handlaggare_app'@'localhost' IDENTIFIED BY '<REDACTED>';

-- Ger rätt att köra procedurer i databasen
GRANT EXECUTE ON a25milha.* TO 'handlaggare_app'@'localhost';

-- Ger läsrättighet till kodtabellen för grader
GRANT SELECT ON a25milha.GradBeskrivning TO 'handlaggare_app'@'localhost';

-- Ger läsrättighet till arkiverade observationer
GRANT SELECT ON a25milha.ARKIVERADObservation TO 'handlaggare_app'@'localhost';

-- Ger läsrättighet till handläggarregistret
GRANT SELECT ON a25milha.Handlaggare TO 'handlaggare_app'@'localhost';

-- Ger läsrättighet till kodtabellen för platser
GRANT SELECT ON a25milha.Plats TO 'handlaggare_app'@'localhost';

-- Ger läs- och raderingsrätt till kopplingstabellen rapporterat
GRANT SELECT, DELETE ON a25milha.Rapporterat TO 'handlaggare_app'@'localhost';

-- Ger läsrättighet till vittnesregistret
GRANT SELECT ON a25milha.Person TO 'handlaggare_app'@'localhost';

-- Ger läsrättighet till loggtabellen
GRANT SELECT ON a25milha.ObservationLog TO 'handlaggare_app'@'localhost';

-- Ger läsrättighet till agentregistret
GRANT SELECT ON a25milha.Agent TO 'handlaggare_app'@'localhost';

-- Ger fulla rättigheter på aktiva observationer (sök, registrera, ändra, arkivera)
GRANT SELECT, INSERT, UPDATE, DELETE ON a25milha.Observation TO 'handlaggare_app'@'localhost';

-- =================================================================
-- PUNKT 13: PROCEDURER OCH TRIGGERS (2 TRIGGERS + 2 PROCEDURER)
-- =================================================================

-- Trigger för loggning av nya observationer i loggtabell
DELIMITER //
CREATE TRIGGER LOGGOBSERVATION AFTER INSERT ON Observation
FOR EACH ROW BEGIN
  INSERT INTO ObservationLog(Operation, Anvandare, ObsId, Tidpunkt)
  VALUES ('INS', USER(), NEW.ObsId, NOW());
END;
//
DELIMITER ;

-- Trigger för kontroll av narkontaktsgrad vid registrering av observation
DELIMITER //
CREATE TRIGGER CHECKOBSGRAD BEFORE INSERT ON Observation
FOR EACH ROW BEGIN
  IF (NEW.GradKod < 1 OR NEW.GradKod > 4) THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Narkontaktsgraden maste vara mellan 1 och 4.';
  END IF;
END;
//
DELIMITER ;

-- Procedur för beräkning av medelsäkerhet för registrerade observationer
DELIMITER //
CREATE PROCEDURE GETAVGSAKERHET()
BEGIN
  SELECT AVG(Sakerhet) AS MedelSakerhet FROM Observation;
END;
//
DELIMITER ;

-- Procedur för arkivering av en observation till arkivtabellen
DELIMITER //
CREATE PROCEDURE ARKIVERAOBSERVATION(IN p_ObsId INTEGER)
BEGIN
  DELETE FROM Rapporterat WHERE ObsId = p_ObsId;
  INSERT INTO ARKIVERADObservation
  SELECT * FROM Observation WHERE ObsId = p_ObsId;
  DELETE FROM Observation WHERE ObsId = p_ObsId;
END;
//
DELIMITER ;