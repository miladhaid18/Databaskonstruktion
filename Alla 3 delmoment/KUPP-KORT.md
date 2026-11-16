# Kupp-kort Muntlig Redovisning — IT429G HT26

## 1. DATABASE (databaskonstruktion-assignment-1-startup/database.sql)

| Krav | Fil:Rad | Vad den gör (en mening) |
|------|---------|--------------------------|
| Logisk design – relationer | database.sql:9–107 | 10 tabeller med PK/FK: Agent, AgentKommentar, Handlaggare, Plats, GradBeskrivning, Observation, ARKIVERADObservation, Person, Rapporterat, ObservationLog |
| Logisk design – datatyper | database.sql:9–107 | CHAR, INTEGER, VARCHAR, DATETIME, DECIMAL(5,2) valda per kolumn enligt domän |
| Logisk design – constraints | database.sql:9–107 | PK, FK, CHECK (lön 12–25k, agentnr 1–99, grad 1–4, säkerhet 1–114), NOT NULL, DEFAULT, UNIQUE |
| Denormalisering – Merge | database.sql:59 | IncidentNamn ligger i Observation (undviker join mot Incident-tabell) |
| Denormalisering – Kodifiering | database.sql:39–50, 56, 60 | PlatsKod→Plats, GradKod→GradBeskrivning (kodtabeller för dropdownar) |
| Denormalisering – Horisontell | database.sql:22–29 | AgentKommentar utflyttad (stor textkolumn separat) |
| Denormalisering – Vertikal | database.sql:73–92 | ARKIVERADObservation separat tabell för arkiverade rader |
| Indexering (2 st) | database.sql:173–177 | OBSDATUM (Datum DESC), PERSONNAMN (Namn ASC) — BTREE med kommentarer |
| Rättigheter (≥3 GRANT) | database.sql:183–214 | 11 GRANT: EXECUTE, SELECT på kodtabeller/arkiv/logg, SELECT/INSERT/UPDATE/DELETE på Observation |
| Trigger 1 – Loggning | database.sql:221–228 | LOGGOBSERVATION AFTER INSERT → loggar 'INS', USER(), NEW.ObsId, NOW() till ObservationLog |
| Trigger 2 – Validering | database.sql:231–238 | CHECKOBSGRAD BEFORE INSERT → SIGNAL om GradKod ej 1–4 (dubbelsäkerhet) |
| Procedur 1 – Medelvärde | database.sql:241–248 | GETAVGSAKERHET() returnerar AVG(Sakerhet) från Observation |
| Procedur 2 – Arkivering | database.sql:251–259 | ARKIVERAOBSERVATION(p_ObsId): DELETE Rapporterat → INSERT ARKIVERAD → DELETE Observation (atomärt) |

---

## 2. PHP (databaskonstruktion-assignment-2-startup/PHPApplikation.php)

| Krav | Rad | Vad den gör |
|------|-----|-------------|
| Enkel tabell (utan join) | 115–156 | Person-tabell med sökning på namn + dropdown-filter Kodnamn (inkl. "Saknar kodnamn") |
| Komplex tabell (join + nästlad) | 168–214 | **Radlayout**: Observation + vittnen via Rapporterat+Person — $prevObs skapar inre tabell per ObsId |
| Sökning / Dropdown / Länkning | 122–137, 221–247 | 4 dropdownar: Kodnamn-filter, Grad, Plats, Handlaggare (sammansatt nyckel AgentNamn\|AgentNr) + sökfält |
| INSERT | 48–76, 216–249 | Registrera ny observation — validering + prepared INSERT med 9 parametrar |
| DELETE (via procedur) | 78–94, 251–263 | Arkivera observation — CALL ARKIVERAOBSERVATION(?) med bindValue |
| UPDATE | 96–111, 265–279 | Uppdatera Sakerhet på vald observation — prepared UPDATE |
| Lagrad procedur i vy | 158–166 | CALL GETAVGSAKERHET() visar medelsäkerhet |

---

## 3. .NET MVC (databaskonstruktion-assignment-3-startup/)

### Controllers
| Fil | Metod | Rad | Syfte |
|-----|-------|-----|-------|
| Controllers/ObservationController.cs | Index() | 18–24 | Hämtar alla observationer + platser + handläggare → View |
| Controllers/ObservationController.cs | SearchObservations(platskod) | 26–30 | Filtrerar observationer per plats → SearchObservations.cshtml |
| Controllers/ObservationController.cs | InsertObservation(...) | 32–36 | Tar 8 parametrar, anropar modell, redirect Index |
| Controllers/ObservationController.cs | UpdateSakerhet(obsid, sakerhet) | 38–42 | Uppdaterar säkerhet via modell, redirect Index |
| Controllers/ObservationController.cs | ArkiveraObservation(obsid) | 44–48 | Anropar procedur via modell, redirect Index |
| Controllers/PersonController.cs | Index() | 18–23 | Hämtar alla personer + medelsäkerhet → View |
| Controllers/PersonController.cs | SearchPersons(name) | 25–29 | Sökning på namn → SearchPersons.cshtml |

### Models
| Fil | Metod | Rad | SQL / Syfte |
|-----|-------|-----|-------------|
| Models/ObservationModel.cs | GetObservationsWithDetails() | 17–27 | JOIN Observation, Plats, GradBeskrivning, Agent ORDER BY IncidentId, ObsId |
| Models/ObservationModel.cs | GetAllPlats() | 29–39 | SELECT * FROM Plats |
| Models/ObservationModel.cs | GetAllHandlaggare() | 41–51 | JOIN Agent+Handlaggare för dropdownar (sammansatt nyckel) |
| Models/ObservationModel.cs | InsertObservation() | 53–69 | INSERT parametriserad (ObsId, NOW(), Grad, Sakerhet, IncidentId, IncidentNamn, PlatsKod, RegNamn, RegNr) |
| Models/ObservationModel.cs | UpdateSakerhet() | 71–81 | UPDATE Observation SET Sakerhet=@SAKERHET WHERE ObsId=@OBSID |
| Models/ObservationModel.cs | ArkiveraObservation() | 83–91 | CALL ARKIVERAOBSERVATION(@OBSID) |
| Models/ObservationModel.cs | SearchObservationsByPlats() | 93–104 | JOIN Observation+Plats WHERE PlatsKod=@PLATSKOD |
| Models/PersonModel.cs | GetAllPersons() | 17–27 | SELECT * FROM Person |
| Models/PersonModel.cs | GetAvgSakerhet() | 29–39 | CALL GETAVGSAKERHET() |
| Models/PersonModel.cs | SearchPersons(name) | 41–52 | SELECT * FROM Person WHERE Namn LIKE @NAME (@NAME = "%" + name + "%") |

### Views
| Fil | Rad | Vad den visar |
|-----|-----|---------------|
| Views/Observation/Index.cshtml | 5–17 | Dropdown filtrera per plats → POST SearchObservations |
| Views/Observation/Index.cshtml | 26–55 | **Kolumnlayout**: yttre loop IncidentId → inre loop observationer → nästlad tabell i kolumn 3 |
| Views/Observation/Index.cshtml | 58–93 | Insert-form: fält + 2 dropdownar handläggare (regnamn + regnr = sammansatt PK) |
| Views/Observation/Index.cshtml | 96–109 | Update-form: dropdown ObsId + säkerhetsfält |
| Views/Observation/Index.cshtml | 46 | Arkivera-länk: @Html.ActionLink("Arkivera", "ArkiveraObservation", new { obsid }) |
| Views/Person/Index.cshtml | 4–11 | Sökformulär name → POST SearchPersons |
| Views/Person/Index.cshtml | 13–30 | Enkel tabell Person (auto-header från Columns) |
| Views/Person/Index.cshtml | 33–50 | Medelsäkerhet via procedur (auto-header) |

### Config
| Fil | Rad | Innehåll |
|-----|-----|----------|
| appsettings.json | 9 | SchoolConnection = anslutningssträng (används i båda modeller) |

---

## 4. MVC-KEDJOR (Fysisk layout — 4 filer per funktion)

| Funktion | View | Controller | Model | SQL |
|----------|------|------------|-------|-----|
| Visa alla (kolumnlayout) | Views/Observation/Index.cshtml:26–55 | ObservationController.Index:18–24 | ObservationModel.GetObservationsWithDetails:17–27 | JOIN 4 tabeller |
| Filtrera per plats | Views/Observation/Index.cshtml:5–17 | ObservationController.SearchObservations:26–30 | ObservationModel.SearchObservationsByPlats:93–104 | WHERE PlatsKod=@PLATSKOD |
| Insert observation | Views/Observation/Index.cshtml:58–93 | ObservationController.InsertObservation:32–36 | ObservationModel.InsertObservation:53–69 | INSERT 9 parametrar |
| Update säkerhet | Views/Observation/Index.cshtml:96–109 | ObservationController.UpdateSakerhet:38–42 | ObservationModel.UpdateSakerhet:71–81 | UPDATE Sakerhet WHERE ObsId |
| Arkivera (procedur) | Views/Observation/Index.cshtml:46 | ObservationController.ArkiveraObservation:44–48 | ObservationModel.ArkiveraObservation:83–91 | CALL ARKIVERAOBSERVATION |
| Sök person | Views/Person/Index.cshtml:4–11 | PersonController.SearchPersons:25–29 | PersonModel.SearchPersons:41–52 | LIKE @NAME (parametriserad) |

> Alla SQL-anrop: `Parameters.AddWithValue` — **ingen strängkonkatenering**.

---

## 5. PERSONLIGA FRÅGOR (vanliga)

| Fråga | Svar (peka på fil:rad) |
|-------|------------------------|
| Varför SchoolConnection? | Models/*Model.cs:14 — skolans miljövariabel, undviker kollision med ConnectionString |
| Varför 2 dropdownar handläggare? | Views/Observation/Index.cshtml:74–91 — PK i Handlaggare är sammansatt (AgentNamn, AgentNr) |
| Varför radlayout PHP, kolumnlayout .NET? | PHP: 1 obs → N vittner (bredvid). .NET: 1 incident → N obs (i kolumn) — kravspec |
| Vad gör CHECKOBSGRAD? | database.sql:231–238 — BEFORE INSERT, SIGNAL om GradKod ej 1–4 (dubbelsäkerhet) |
| Varför ARKIVERAOBSERVATION som procedur? | database.sql:251–259 — atomärt: DELETE Rapporterat → INSERT ARKIVERAD → DELETE Observation |
| Vad händer om ObservationLog saknas? | Trigger LOGGOBSERVATION kraschar — tabellen finns i database.sql:110–117 |

---

## 6. GIT-HITTA-SNABBT

| Om läraren säger... | Du bläddrar till... |
|---------------------|---------------------|
| "Visa logisk design" | database.sql:9–107 |
| "Visa denormalisering" | database.sql:22–29 (horisontell), 39–50 (kodifiering), 59 (merge), 73–92 (vertikal) |
| "Visa index" | database.sql:173–177 |
| "Visa grants" | database.sql:183–214 |
| "Visa triggers" | database.sql:221–239 |
| "Visa procedurer" | database.sql:241–259 |
| "Visa komplex tabell PHP" | PHPApplikation.php:168–214 |
| "Visa INSERT PHP" | PHPApplikation.php:48–76, 216–249 |
| "Visa DELETE/UPDATE PHP" | PHPApplikation.php:78–111, 251–279 |
| "Visa kolumnlayout .NET" | Views/Observation/Index.cshtml:26–55 |
| "Visa MVC-kedja Insert" | View→Controller→Model→SQL (tabell ovan) |
| "Visa parameter-säkerhet" | Models/*Model.cs: alla AddWithValue-rader |

---

**Tips**: Öppna filen på git.webug.se, scrolla till radnummer, läs 3–5 rader högt, säga meningen. Max 5 min/fråga.