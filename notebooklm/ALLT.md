# IT429G Databaskonstruktion – ALLT

Detta dokument sammanfattar kursens kärna för repetition och NotebookLM. Det kompletterar PDF:erna i `Databaskonstruktion/studieplan/`.

## Tentaformat

- **10 frågor**, totalt **20 p**.
- Fråga **1–7**: ett rätt alternativ av fem + motivering.
- Fråga **8–10**: flera rätta alternativ + motivering.
- **1 p** för rätt alternativ + **1 p** för motivering.
- Minst ett fel i alternativen ger **0 p** på den frågan.
- **G = 12 p**, **VG = 16 p**.
- Motivering: 2–4 meningar eller 30–50 ord.
- Svara tydligt: "Rätt svar: X. Motivering: ...".

## 1. Databasmodellering

### Entiteter och relationer

- En **entitet** blir oftast en tabell.
- En **relation** blir en foreign key eller en kopplingstabell.
- Kardinalitet:
  - **1:1** – en rad motsvarar högst en rad i den andra tabellen.
  - **1:N** – en rad kan motsvara många rader.
  - **M:N** – kräver en kopplingstabell med foreign keys till båda tabellerna.

### Nycklar

- **Primärnyckel (PK)**: unikt identifierar varje rad, får inte vara NULL.
- **Kandidatnyckel**: alternativ unik nyckel.
- **Foreign key (FK)**: refererar till PK i en annan tabell och skapar referentiell integritet.
- **Surrogatnyckel**: konstgjord nyckel, t.ex. `PersonId`.
- **Naturlig nyckel**: verklig uppgift, t.ex. personnummer.

### Normalisering

- **1NF**: atomära värden, inga upprepade grupper.
- **2NF**: 1NF + inga partiella beroenden av en sammansatt nyckel.
- **3NF**: 2NF + inga transitiva beroenden.
- Normalisering minskar redundans och uppdateringsproblem, men kan kräva fler joins.

### Vertikal och horisontell split

- **Vertikal split**: delar **kolumner** i flera tabeller med samma PK, oftast 1:1.
  - Exempel: `Person` och `PersonKontakt`.
- **Horisontell split**: delar **rader** i flera tabeller med samma kolumner.
  - Exempel: `Observation2024` och `Observation2025`, eller arkiv per region.
- **Vanlig fälla**: vertikal = kolumner, horisontell = rader. Inte tvärtom.

## 2. SQL

### DDL

```sql
CREATE TABLE Person (
    PersonId INT PRIMARY KEY,
    Namn VARCHAR(100) NOT NULL,
    Kodnamn VARCHAR(50),
    Personnummer CHAR(12) NOT NULL,
    CONSTRAINT chk_personnummer CHECK (Personnummer REGEXP '^[0-9]{12}$')
);
```

- `CHAR(12)` = exakt 12 tecken.
- `VARCHAR(12)` = upp till 12 tecken.
- `INT(12)` är visningsbredd, inte ett krav på 12 siffror.
- Använd `CHECK` för format och `NOT NULL` för obligatoriska värden.

### Foreign key

```sql
CREATE TABLE Observation (
    ObsId INT PRIMARY KEY,
    PlatsKod INT NOT NULL,
    FOREIGN KEY (PlatsKod) REFERENCES Plats(PlatsKod)
);
```

- `ON DELETE CASCADE` tar bort barnrader automatiskt.
- `ON DELETE RESTRICT` stoppar borttagning om barnrader finns.
- `ON UPDATE CASCADE` uppdaterar FK när PK ändras.

### DML

```sql
SELECT Namn, Kodnamn
FROM Person
WHERE Namn LIKE '%Anna%'
ORDER BY Namn ASC;

INSERT INTO Person (PersonId, Namn, Kodnamn)
VALUES (1, 'Anna', 'A1');

UPDATE Person
SET Kodnamn = 'A2'
WHERE PersonId = 1;

DELETE FROM Person
WHERE PersonId = 1;
```

- `UPDATE` och `DELETE` måste nästan alltid ha `WHERE`.
- `LIKE` används för mönster; `=` för exakt matchning.
- `IS NULL` / `IS NOT NULL` används för NULL, inte `= NULL`.

### Joins

- `INNER JOIN`: endast rader som matchar i båda tabellerna.
- `LEFT JOIN`: alla rader från vänster tabell + matchande rader från höger.
- `RIGHT JOIN`: alla rader från höger tabell + matchande rader från vänster.
- `FULL OUTER JOIN`: finns inte direkt i MySQL; använd `UNION`.
- `CROSS JOIN`: alla kombinationer.

```sql
SELECT p.Namn, o.IncidentNamn
FROM Person p
LEFT JOIN Rapporterat r ON p.PersonId = r.PersonId
LEFT JOIN Observation o ON r.ObsId = o.ObsId;
```

### Aggregering

```sql
SELECT PlatsKod, COUNT(*) AS Antal
FROM Observation
GROUP BY PlatsKod
HAVING COUNT(*) > 2;
```

- `WHERE` filtrerar rader före gruppering.
- `HAVING` filtrerar grupper efter `GROUP BY`.
- `COUNT(*)` räknar alla rader.
- `COUNT(kolumn)` ignorerar NULL.
- `SUM`, `AVG`, `MIN`, `MAX` ignorerar NULL.

### Subqueries

```sql
SELECT Namn
FROM Person
WHERE PersonId IN (
    SELECT PersonId FROM Rapporterat WHERE ObsId = 10
);
```

- `IN`, `EXISTS`, `NOT EXISTS` och skalära subqueries är vanliga.
- `EXISTS` kan vara effektivt när man bara vill veta om en rad finns.

### Vyer

```sql
CREATE VIEW PersonOversikt AS
SELECT p.PersonId, p.Namn, COUNT(r.ObsId) AS AntalObservationer
FROM Person p
LEFT JOIN Rapporterat r ON p.PersonId = r.PersonId
GROUP BY p.PersonId, p.Namn;
```

- En vy är en sparad fråga, inte en egen tabell med data.
- Vyer kan förenkla rapporter och begränsa vilka kolumner som exponeras.

### Procedurer och funktioner

```sql
DELIMITER //
CREATE PROCEDURE GETAVGSAKERHET()
BEGIN
    SELECT AVG(Sakerhet) AS MedelSakerhet FROM Observation;
END //
DELIMITER ;

CALL GETAVGSAKERHET();
```

- Procedurer anropas med `CALL`.
- Parametrar kan vara `IN`, `OUT` eller `INOUT`.
- Procedurer kan innehålla transaktioner och flera SQL-satser.

### Triggers

```sql
CREATE TRIGGER logg_observation
AFTER INSERT ON Observation
FOR EACH ROW
INSERT INTO ObservationLog (ObsId, Handelse)
VALUES (NEW.ObsId, 'INSERT');
```

- Triggers körs automatiskt vid `INSERT`, `UPDATE` eller `DELETE`.
- `BEFORE` kan ändra `NEW`; `AFTER` används ofta för loggning.
- Triggers är **inte** ett skydd mot SQL-injection.
- Triggers kan ge oväntade bieffekter och är svårare att felsöka.

### Transaktioner

```sql
START TRANSACTION;
UPDATE Konto SET Saldo = Saldo - 100 WHERE KontoId = 1;
UPDATE Konto SET Saldo = Saldo + 100 WHERE KontoId = 2;
COMMIT;
```

- `COMMIT` sparar; `ROLLBACK` ångrar.
- ACID: Atomicity, Consistency, Isolation, Durability.
- Använd transaktioner när flera ändringar måste lyckas tillsammans.

### Index

- Index snabbar upp `SELECT`, `JOIN` och `ORDER BY`.
- Index gör `INSERT`, `UPDATE` och `DELETE` långsammare.
- Indexera kolumner som ofta används i `WHERE` och join-villkor.
- Primärnyckel och unika constraints skapar index automatiskt.

### Behörigheter

```sql
CREATE USER 'app'@'localhost' IDENTIFIED BY '<REDACTED>';
GRANT SELECT, INSERT ON databas.Observation TO 'app'@'localhost';
REVOKE DELETE ON databas.Observation FROM 'app'@'localhost';
```

- Följ **least privilege**: ge bara de rättigheter som behövs.
- App-användaren ska inte vara `root`.
- `GRANT` och `REVOKE` är viktiga tentafrågor.

## 3. C# / ADO.NET / MVC

### Anslutning

```csharp
using MySql.Data.MySqlClient;

string connectionString =
    "Server=localhost;Database=databas;User ID=app;Password=<REDACTED>;";

using var conn = new MySqlConnection(connectionString);
conn.Open();
```

- Connection strings ska komma från konfiguration eller miljövariabler, inte hårdkodas.
- I ASP.NET Core: `appsettings.json`, `IConfiguration` eller environment variables.

### Parameterized queries

```csharp
using var cmd = new MySqlCommand(
    "SELECT * FROM Person WHERE Namn LIKE @NAME;", conn);
cmd.Parameters.AddWithValue("@NAME", "%" + name + "%");

using var reader = cmd.ExecuteReader();
while (reader.Read())
{
    Console.WriteLine(reader["Namn"]);
}
```

- Använd alltid parametrar för användarinmatning.
- `@NAME` måste matcha parameternamnet exakt. `@NAM` ger fel.
- `LIKE` används för mönster; `=` för exakt matchning.
- `AddWithValue` är enkelt men kan ge typproblem; `Add` med explicit typ är mer robust.

### DataSet och DataTable

```csharp
using var adapter = new MySqlDataAdapter("SELECT * FROM Person;", conn);
var ds = new DataSet();
adapter.Fill(ds, "result");
DataTable table = ds.Tables["result"];
```

- `DataSet` kan innehålla flera tabeller.
- `DataTable` är en tabell i minnet.
- `MySqlDataReader` är snabbare för stora resultat och strömning.

### MVC-flöde

1. Användaren skickar en request.
2. Routing väljer controller och action.
3. Controller anropar model/service.
4. Model läser/skriver i databasen.
5. Controller skickar data till view.
6. View renderar HTML till användaren.

- Controller ska inte innehålla SQL direkt; lägg databaslogik i model/repository.
- View ska inte innehålla affärslogik.

### GET och POST

- `GET`: läser data, parametrar i URL, ska vara idempotent.
- `POST`: skapar/ändrar data, parametrar i request body.
- Använd `[HttpPost]` för ändringar.
- Använd `[ValidateAntiForgeryToken]` i ASP.NET Core MVC.
- Validera alltid på servern; klientvalidering är bara extra.

### Felhantering

```csharp
try
{
    conn.Open();
    // ...
}
catch (MySqlException ex)
{
    // Logga internt, visa generellt fel för användaren.
}
```

- Exponera inte stack traces eller connection strings för användaren.
- Logga fel med tillräcklig kontext.

### Vanliga C#-fällor

- `@NAME` vs `@NAM`.
- `=` vs `LIKE`.
- Glömd `WHERE` i `UPDATE`/`DELETE`.
- `$POST` vs `$_POST` (PHP) eller felaktig parameterbindning.
- Glömd `conn.Open()`.
- Glömd `using`/`Dispose` för anslutningar och readers.
- Hårdkodade lösenord i källkoden.

## 4. PHP / PDO

### Anslutning

```php
$pdo = new PDO(
    'mysql:host=localhost;dbname=databas;charset=utf8',
    'app',
    '<REDACTED>'
);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
```

### Prepared statements

```php
$stmt = $pdo->prepare("SELECT * FROM Person WHERE Namn LIKE ?");
$stmt->execute(["%$namn%"]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
```

- Använd `?` eller namngivna parametrar (`:namn`).
- `bindValue`/`bindParam` kan användas för explicit typ.
- `FETCH_ASSOC` ger associativa arrayer.
- `FETCH_NUM` ger numeriska index.
- `FETCH_BOTH` ger båda.

### Formulär

```php
$namn = isset($_POST['namn']) ? trim($_POST['namn']) : '';
```

- `$_POST` används för POST-data.
- `$_GET` används för query string.
- `isset`, `empty`, `trim` och `filter_input` är vanliga skydd.
- Escape:a output med `htmlspecialchars($value, ENT_QUOTES, 'UTF-8')`.

### Sessions och lösenord

```php
session_start();
$_SESSION['user_id'] = $id;
session_regenerate_id(true);

$hash = password_hash($password, PASSWORD_DEFAULT);
if (password_verify($password, $hash)) { /* inloggad */ }
```

- Spara aldrig lösenord i klartext.
- Använd `password_hash` och `password_verify`.
- Regenerera session-ID vid inloggning.

### Vanliga PHP-fällor

- `$POST` i stället för `$_POST`.
- `=` i stället för `==`/`===` i jämförelser.
- Glömd `WHERE` i `UPDATE`/`DELETE`.
- SQL-strängar byggs med concatenation i stället för parametrar.
- Glömd `charset=utf8` i DSN.
- Fel fetch-läge (`FETCH_NUM` vs `FETCH_ASSOC`).

## 5. Säkerhet

### SQL-injection

- Exempel på farlig input: `' OR '1'='1' --`.
- Exempel på farlig kod: `"SELECT * FROM Person WHERE Namn = '$namn'"`.
- Skydd:
  1. Prepared statements/parameterized queries.
  2. Least privilege för databasanvändaren.
  3. Validering och typkontroll.
  4. Begränsad felinformation.
- **Trigger är inte ett skydd mot SQL-injection.**

### XSS och CSRF

- XSS: escape:a all output; använd `htmlspecialchars`.
- CSRF: använd anti-CSRF-token och `[ValidateAntiForgeryToken]` i MVC.
- Sätt cookies med `HttpOnly`, `Secure` och `SameSite`.

### Hemligheter

- Lösenord och connection strings ska inte ligga i Git.
- Använd miljövariabler eller secret managers.
- Om en hemlighet publicerats: rotera den och rensa historiken.

## 6. Diagnostest och rättelser

Resultat: **11/20**.

| Fråga | Ditt svar | Rätt svar | Poäng | Kommentar |
|---|---|---|---|---|
| 1 | b | b | 2 | Primärnyckel. |
| 2 | a | d | 0 | Vertikal = kolumner, horisontell = rader. |
| 3 | c | c | 1 | `CHAR(12)` + `CHECK` + `NOT NULL`. |
| 4 | a | a | 2 | Foreign key. |
| 5 | e | e | 1 | GET vs POST. |
| 6 | b | b | 2 | `FETCH_NUM`. |
| 7 | c | c | 2 | MVC-flöde. |
| 8 | b,c,e | a,b,c | 0 | Prepared statements + least privilege; trigger är inte skydd. |
| 9 | a,c,e | a,b | 0 | `@NAME` vs `@NAM`; `=` vs `LIKE`. |
| 10 | a,b,c | a,b,c | 1 | `UPDATE` utan `WHERE`, `$POST` vs `$_POST`, ej bunden parameter. |

### Prioriterade rättelser

1. **Vertikal/horisontell split** – blanda inte ihop kolumner och rader.
2. **C#-kodfel** – `@NAME` vs `@NAM`, `=` vs `LIKE`, parametrar.
3. **SQL-injection** – prepared statements och least privilege, inte triggers.
4. **Motiveringar** – skriv 2–4 meningar med kursens begrepp.
5. **Datatyper** – `CHAR(12)` är längd, `INT(12)` är visningsbredd.

## 7. Studieplan 15 november–18 december 2026

Varje pass: 30 minuter, aktiv återkallelse, facit sist.

| Period | Fokus |
|---|---|
| 15–21 nov | SQL-grunder, vertikal/horisontell split, joins. |
| 22–28 nov | DDL/DML, nycklar, normalisering, ER. |
| 29 nov–5 dec | Vyer, procedurer, triggers, transaktioner, index. |
| 6–12 dec | C#/ADO.NET, MVC, PHP/PDO, säkerhet. |
| 13–17 dec | Gamla tentor, felanalys, snabbrepetition. |
| 18 dec | Tenta. |

### Dagens pass

- Dag 1 (15 nov): vertikal vs horisontell split.
- Dag 2 (16 nov): repetition av split + joins.
- Fortsätt enligt veckotemat ovan.

## 8. Vanliga tentafällor

- Vertikal = kolumner, horisontell = rader.
- `@NAME` vs `@NAM`.
- `=` vs `LIKE`.
- `CHAR(12)` vs `INT(12)`.
- `WHERE` vs `HAVING`.
- `COUNT(*)` vs `COUNT(kolumn)`.
- `IS NULL` vs `= NULL`.
- `LEFT JOIN` vs `INNER JOIN`.
- `UPDATE`/`DELETE` utan `WHERE`.
- `$_POST` vs `$POST`.
- `FETCH_NUM` vs `FETCH_ASSOC`.
- Trigger är inte SQL-injection-skydd.
- `GRANT`/`REVOKE` och least privilege.
- `COMMIT`/`ROLLBACK` och transaktioner.
- `PRIMARY KEY` vs `UNIQUE`.
- `DELETE` vs `TRUNCATE` vs `DROP`.
- `VARCHAR` vs `CHAR`.
- `DISTINCT` och dubbletter.
- NULL i aggregate-funktioner.
- `INNER JOIN` vs `LEFT JOIN` och antal rader.

## 9. Motiveringsteknik

- Börja med svaret: "Rätt svar: X."
- Förklara varför: "Eftersom ...".
- Nämn konsekvensen: "Det gör att ...".
- Om flera rätt: lista alla och förklara sambandet.
- Undvik fluff; använd kursens begrepp.

Exempel:

> Rätt svar: d. Vertikal split delar tabellen på kolumner, medan horisontell split delar den på rader. I en vertikal split behålls samma primärnyckel i båda tabellerna, medan en horisontell split har samma kolumner men olika rader.

## 10. Filindex

- `Databaskonstruktion/studieplan/` – mjukstart, diagnostest och split-PDF.
- `Databaskonstruktion/gamla tentor/` – gamla tentor.
- `Databaskonstruktion/lärarens kodexemplar/` – MVC-exempel.
- `databaskonstruktion-assignment-1-startup/` – SQL och ER.
- `PHP databaskonstruktion-assignment-2-startup/` – PHP/PDO.
- `databaskonstruktion-assignment-3-startup/` – ASP.NET Core MVC.
- `tentatraning/` – repetitionsmaterial.
- `Alla 3 delmoment/` – samlade delmoment.
