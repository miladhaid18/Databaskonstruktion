DROP DATABASE IF EXISTS a25milha;
CREATE DATABASE a25milha;
USE a25milha;

CREATE TABLE Agent(
AgentNamn CHAR(1) NOT NULL,
AgentNr INTEGER NOT NULL,
Fornamn VARCHAR(50) NOT NULL,
Efternamn VARCHAR(50) NOT NULL,
Lon INTEGER DEFAULT 13000,
Kommentar VARCHAR(1024),
PRIMARY KEY (AgentNamn, AgentNr)
-- Restriktion: Lonen maste ligga mellan 12000 och 25000 kr for handlaggare
CHECK (Lon >= 12000 AND Lon <= 25000),
-- Restriktion: Agentnumret maste vara mellan 1 och 99
CHECK (AgentNr > 0 AND AgentNr <= 99)
) ENGINE=INNODB;

CREATE TABLE Handlaggare(
AgentNamn CHAR(1) NOT NULL,
AgentNr INTEGER NOT NULL,
PRIMARY KEY (AgentNamn, AgentNr),
FOREIGN KEY (AgentNamn, AgentNr) REFERENCES Agent(Age)
) ENGINE=INNODB;

CREATE TABLE Incident(
IncidentId INTEGER NOT NULL,
Namn VARCHAR(100) UNIQUE NOT NULL,
Plats VARCHAR(100) NOT NULL,
RapportorNamn CHAR(1) NOT NULL,
RapportorNr INTEGER NOT NULL,
PRIMARY KEY (IncidentId),
FOREIGN KEY (RapportorNamn, RapportorNr) REFERENCES Handlaggare(AgentNamn, AgentNr)
) ENGINE=INNODB;

CREATE TABLE Observation(
ObsId INTEGER NOT NULL,
Datum DATETIME NOT NULL,
Grad INTEGER NOT NULL,
Sakerhet DECIMAL(5,2) NOT NULL,
IncidentId INTEGER NOT NULL,
RegistreradAvNamn CHAR(1) NOT NULL,
RegistreradAvNr INTEGER NOT NULL,
PRIMARY KEY (ObsId),
-- Restriktion: Narkontaktsgraden maste vara ett heltal fran 1 till 4
CHECK (Grad >= 1 AND Grad <= 4),
-- Restriktion: Sakerheten i procent maste vara mellan 1.00 och 114.00
CHECK (Sakerhet >= 1.00 AND Sakerhet <= 114.00),
FOREIGN KEY (IncidentId) REFERENCES Incident(IncidentId),
FOREIGN KEY (RegistreradAvNamn, RegistreradAvNr) REFERENCES Handlaggare(AgentNamn, AgentNr)
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

-- 1. Testdata for Agent (Leif L 1 ar en okopplad agent som inte ar handlaggare)
INSERT INTO Agent(AgentNamn, AgentNr, Fornamn, Efternamn, Lon, Kommentar) VALUES
('J', 2, 'John', 'Doe', 14000, 'Att dolja sanningen for att skydda manskligheten.'),
('K', 5, 'Karl', 'Svensson', 16000, 'Sanningen finns dar ute, men inte for alla.'),
('A', 8, 'Anna', 'Andersson', 13000, NULL),
('L', 1, 'Leif', 'Larsson', 20000, 'En faltagent som inte arbetar som handlaggare.');

-- 2. Testdata for Handlaggare
INSERT INTO Handlaggare(AgentNamn, AgentNr) VALUES
('J', 2),
('K', 5),
('A', 8);

-- 3. Testdata for Incident
INSERT INTO Incident(IncidentId, Namn, Plats, RapportorNamn, RapportorNr) VALUES
(101, 'Roswell-siktelsen', 'Roswell, New Mexico', 'J', 2),
(102, 'Vastgota-ljusen', 'Skovde, Vastra Gotaland', 'K', 5),
(103, 'Stockholms-ufot', 'Stockholm, Sodermalm', 'K', 5);

-- 4. Testdata for Observation
INSERT INTO Observation(ObsId, Datum, Grad, Sakerhet, IncidentId, RegistreradAvNamn, RegistreradAvNr) VALUES
(501, '2026-09-01 22:30:00', 3, 85.50, 101, 'J', 2),
(502, '2026-09-02 01:15:00', 1, 45.00, 102, 'K', 5),
(503, '2026-09-03 14:00:00', 4, 114.00, 103, 'A', 8);

-- 5. Testdata for Person (Olof Palme ar en okopplad person som inte gjort observationer)
INSERT INTO Person(PersonId, Namn, Kodnamn) VALUES
('19850512-1234', 'Erik Johansson', 'Vittnet'),
('19900824-5678', 'Maria Larsson', 'Astronomen'),
('19781103-9012', 'Olof Palme', 'Skattebetalaren'),
('ANON-999999-1', 'Anonym uppgiftslamnare', NULL);

-- 6. Testdata for Rapporterat
INSERT INTO Rapporterat(PersonId, ObsId) VALUES
('19850512-1234', 501),
('19900824-5678', 502),
('19850512-1234', 503);


-- =================================================================
-- GILTIG TESTDATA
-- =================================================================

INSERT INTO Agent(AgentNamn, AgentNr, Fornamn, Efternamn, Lon, Kommentar) VALUES
('J', 2, 'John', 'Doe', 14000, 'Att dolja sanningen for att skydda manskligheten.'),
('K', 5, 'Karl', 'Svensson', 16000, 'Sanningen finns dar ute, men inte for alla.'),
('A', 8, 'Anna', 'Andersson', 13000, NULL),
('L', 1, 'Leif', 'Larsson', 20000, 'En faltagent som inte arbetar som handlaggare.');

INSERT INTO Handlaggare(AgentNamn, AgentNr) VALUES
('J', 2),
('K', 5),
('A', 8);

INSERT INTO Incident(IncidentId, Namn, Plats, RapportorNamn, RapportorNr) VALUES
(101, 'Roswell-siktelsen', 'Roswell, New Mexico', 'J', 2),
(102, 'Vastgota-ljusen', 'Skovde, Vastra Gotaland', 'K', 5),
(103, 'Stockholms-ufot', 'Stockholm, Sodermalm', 'K', 5);

INSERT INTO Observation(ObsId, Datum, Grad, Sakerhet, IncidentId, RegistreradAvNamn, RegistreradAvNr) VALUES
(501, '2026-09-01 22:30:00', 3, 85.50, 101, 'J', 2),
(502, '2026-09-02 01:15:00', 1, 45.00, 102, 'K', 5),
(503, '2026-09-03 14:00:00', 4, 114.00, 103, 'A', 8);

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
-- TEST AV CONSTRAINTS (DESSA SKA GE FELMEDDELANDE I MYSQL)
-- =================================================================

-- Test 1: Lon under 12000 kr (Ska misslyckas pga CHECK Lon)
-- INSERT INTO Agent(AgentNamn, AgentNr, Fornamn, Efternamn, Lon) VALUES ('X', 10, 'Test', 'Testsson', 10000);

-- Test 2: AgentNr lika med 0 (Ska misslyckas pga CHECK AgentNr)
-- INSERT INTO Agent(AgentNamn, AgentNr, Fornamn, Efternamn, Lon) VALUES ('Y', 0, 'Test', 'Testsson', 15000);

-- Test 3: Duplicerat incidentnamn (Ska misslyckas pga UNIQUE Namn)
-- INSERT INTO Incident(IncidentId, Namn, Plats, RapportorNamn, RapportorNr) VALUES (104, 'Roswell-siktelsen', 'Kiruna', 'J', 2);

-- Test 4: Grad over 4 (Ska misslyckas pga CHECK Grad)
-- INSERT INTO Observation(ObsId, Datum, Grad, Sakerhe