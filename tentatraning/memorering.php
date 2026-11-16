<?php

$delar = [
    'Databas' => [
        [
            'titel' => 'Konceptuell design och ER',
            'mall' => 'Konceptuell design är steget från krav till en begreppslig modell: vilka entiteter som finns, vilka relationer de har och vilka kardinaliteter och arv som gäller. ER-diagrammet dokumenterar detta innan tabeller och datatyper bestäms. Antaganden som görs ska dokumenteras.',
            'nyckelord' => ['entitet', 'relation', 'kardinalitet', 'arv', 'antaganden'],
        ],
        [
            'titel' => 'Logisk design – relationer',
            'mall' => 'I logisk design översätts ER-modellen till relationer: tabeller, primärnycklar och främmande nycklar. 1:1 och 1:N löses med FK, medan M:N löses med en kopplingstabell. Sammansatta nycklar kräver att alla delar finns med i FK.',
            'nyckelord' => ['PK', 'FK', '1:N', 'M:N', 'kopplingstabell'],
        ],
        [
            'titel' => 'Logisk design – datatyper',
            'mall' => 'Datatypen väljs efter vilken typ av värde kolumnen ska lagra och vilka regler som gäller. CHAR passar fast längd, VARCHAR variabel text, INTEGER heltal, DECIMAL exakta decimaltal och DATETIME datum med tid. Fel datatyp kan ge onödigt utrymme eller felaktig avrundning.',
            'nyckelord' => ['CHAR', 'VARCHAR', 'INTEGER', 'DECIMAL', 'DATETIME'],
        ],
        [
            'titel' => 'Logisk design – constraints',
            'mall' => 'Constraints är regler som databasen själv upprätthåller: PRIMARY KEY, FOREIGN KEY, CHECK, UNIQUE och NOT NULL. De skyddar datan även om applikationen har en bugg. CHECK styr värden, FK styr referenser och UNIQUE hindrar dubbletter.',
            'nyckelord' => ['PK', 'FK', 'CHECK', 'UNIQUE', 'NOT NULL'],
        ],
        [
            'titel' => 'Fysisk design – denormalisering',
            'mall' => 'Denormalisering ändrar den logiska modellen för att förbättra prestanda eller förenkla applikationen. Merge slår ihop tabeller, kodifiering byter ut upprepad text mot en kodtabell, horisontell lyfter ut stora textkolumner och vertikal delar upp rader efter användning. Man måste kunna motivera varje teknik och vilket problem den löser.',
            'nyckelord' => ['merge', 'kodifiering', 'horisontell', 'vertikal', 'prestanda'],
        ],
        [
            'titel' => 'Fysisk design – indexering',
            'mall' => 'Ett index är en sorterad struktur som gör sökning och sortering snabbare, till exempel BTREE på Datum eller Namn. Indexet gör däremot inte kolumnen unik och ersätter inte en constraint. Välj index utifrån vanliga queries och kontrollera att de verkligen används.',
            'nyckelord' => ['index', 'BTREE', 'sökning', 'sortering', 'constraint'],
        ],
        [
            'titel' => 'Fysisk design – rättigheter',
            'mall' => 'Rättigheter styr vad en användare eller applikation får göra i databasen. Man ger bara nödvändiga rättigheter enligt minsta rättighets princip, till exempel SELECT på kodtabeller och EXECUTE för procedurer. Appen ska inte använda root.',
            'nyckelord' => ['GRANT', 'REVOKE', 'minsta rättighet', 'EXECUTE', 'app-användare'],
        ],
        [
            'titel' => 'Fysisk design – procedurer och triggers',
            'mall' => 'En trigger körs automatiskt vid en händelse, medan en procedur anropas med CALL. I projektet används loggning, constraint-kontroll, medelvärde och atomär flytt till arkiv. Fördelen är att logiken ligger i databasen och kan återanvändas av både PHP och .NET.',
            'nyckelord' => ['trigger', 'procedur', 'CALL', 'loggning', 'atomär'],
        ],
    ],
    'PHP' => [
        [
            'titel' => 'Struktur och flöde',
            'mall' => 'PHP-appen är självrefererande: formuläret postar tillbaka till samma fil, och PHP-blocket högst upp läser $_POST och kör SQL innan HTML:en skrivs ut. Allt ligger i PHPApplikation.php, till skillnad från MVC där ansvaret delas upp. Därför måste POST hanteras före HTML-utskriften.',
            'nyckelord' => ['självrefererande', '$_POST', 'PDO', 'PHP före HTML', 'en fil'],
        ],
        [
            'titel' => 'Enkla tabeller',
            'mall' => 'En enkel tabell hämtar data från en tabell utan join och skriver ut resultatet i en HTML-tabell med foreach. Data kan hämtas med SQL eller med en lagrad procedur, till exempel GETAVGSAKERHET för medelsäkerhet. Rubriker och rader byggs från resultatet.',
            'nyckelord' => ['SELECT', 'HTML-tabell', 'foreach', 'procedur', 'medelsäkerhet'],
        ],
        [
            'titel' => 'Komplexa tabeller',
            'mall' => 'En komplex tabell hämtar data från flera tabeller med JOIN och visar en nästlad HTML-tabell. I PHP-appen används radlayout: en yttre rad per observation och en inre tabell med vittnen. Jämförelsevariabeln $prevObs avgör när en ny yttre rad ska börjas.',
            'nyckelord' => ['JOIN', 'nästlad tabell', 'radlayout', '$prevObs', 'vittnen'],
        ],
        [
            'titel' => 'Sökning, länkning och dropdown',
            'mall' => 'Sökning och filtrering sker genom att ett formulär postar ett värde som PHP använder i en parameteriserad SQL-fråga. Dropdownar fylls från databasen så att användaren bara kan välja giltiga värden, och sammansatta nycklar kräver flera fält. Resultatet visas i en filtrerad tabell.',
            'nyckelord' => ['$_POST', 'LIKE', 'dropdown', 'parameter', 'sammansatt nyckel'],
        ],
        [
            'titel' => 'Insert, update, delete och procedur',
            'mall' => 'PHP-appen gör INSERT, UPDATE och arkivering via procedur, och alla värden skickas som parametrar med prepare, bindValue och execute. Det skyddar mot SQL-injektion och gör typningen säker. UPDATE måste ha WHERE så att bara rätt rad ändras.',
            'nyckelord' => ['INSERT', 'UPDATE', 'DELETE', 'prepared statements', 'WHERE'],
        ],
    ],
    'MVC' => [
        [
            'titel' => 'Struktur och routing',
            'mall' => 'En request går från URL till Program.cs och routingen, vidare till en controller som anropar en model och returnerar en view. Controllern tar emot formulärdata, modellen kör SQL och vyn renderar HTML med Razor. ViewBag används för att skicka data från controller till view.',
            'nyckelord' => ['routing', 'controller', 'model', 'view', 'ViewBag'],
        ],
        [
            'titel' => 'Enkla tabeller',
            'mall' => 'En enkel tabell i MVC hämtar data från en tabell utan join och lägger resultatet i en DataTable via ViewBag. Vyn loopar över Columns och Rows och bygger en HTML-tabell med automatiska rubriker. Proceduren GETAVGSAKERHET kan användas för medelsäkerhet.',
            'nyckelord' => ['DataTable', 'ViewBag', 'Columns', 'Rows', 'procedur'],
        ],
        [
            'titel' => 'Komplexa tabeller',
            'mall' => 'Den komplexa tabellen använder JOIN mellan Observation, Plats, GradBeskrivning och Agent. I .NET-appen används kolumnlayout: en yttre rad per incident och en inre tabell med observationer. Variabeln prev gör att en ny yttre rad bara skapas när IncidentId ändras.',
            'nyckelord' => ['JOIN', 'kolumnlayout', 'nästlad tabell', 'prev', 'IncidentId'],
        ],
        [
            'titel' => 'Sökning, länkning och dropdown',
            'mall' => 'Formuläret använder @Url.Action för att posta till rätt controller-action, som anropar modellen och skickar resultatet till en sökvy. Dropdownar fylls från ViewBag och byggs med foreach över databasens rader. ActionLink används för länkar som till exempel Arkivera.',
            'nyckelord' => ['Url.Action', 'controller', 'ViewBag', 'dropdown', 'ActionLink'],
        ],
        [
            'titel' => 'Insert, update, delete och procedur',
            'mall' => 'INSERT, UPDATE och arkivering följer MVC-kedjan View → Controller → Model → SQL. Modellen använder parametrar med AddWithValue, och controllern gör RedirectToAction efter lyckad ändring så att F5 inte skickar formuläret igen. Arkivering sker via CALL ARKIVERAOBSERVATION.',
            'nyckelord' => ['MVC-kedja', 'AddWithValue', 'RedirectToAction', 'PRG', 'CALL'],
        ],
        [
            'titel' => 'Designval och motivering',
            'mall' => 'Designvalen ska motiveras utifrån kraven: SchoolConnection undviker namnkonflikt, två dropdownar behövs för sammansatt PK och kolumnlayout passar en incident med många observationer. Proceduren väljs för att flytten ska bli atomär, och parametriserad SQL väljs för att undvika SQL-injektion. Man ska kunna jämföra PHP:s radlayout med MVC:s kolumnlayout.',
            'nyckelord' => ['SchoolConnection', 'sammansatt PK', 'kolumnlayout', 'atomär', 'SQL-injektion'],
        ],
    ],
];

$koder = [
    'Konceptuell design och ER' => <<<'SQL'
CREATE TABLE Forfattare (
    ForfattareId INT PRIMARY KEY,
    Namn VARCHAR(80) NOT NULL
);

CREATE TABLE Bok (
    BokId INT PRIMARY KEY,
    Titel VARCHAR(120) NOT NULL,
    ForfattareId INT NOT NULL,
    FOREIGN KEY (ForfattareId)
        REFERENCES Forfattare(ForfattareId)
);
SQL,
    'Logisk design – relationer' => <<<'SQL'
CREATE TABLE Student (
    StudentId INT PRIMARY KEY,
    Namn VARCHAR(80) NOT NULL
);

CREATE TABLE Kurs (
    KursId INT PRIMARY KEY,
    Namn VARCHAR(80) NOT NULL
);

CREATE TABLE StudentKurs (
    StudentId INT NOT NULL,
    KursId INT NOT NULL,
    PRIMARY KEY (StudentId, KursId),
    FOREIGN KEY (StudentId) REFERENCES Student(StudentId),
    FOREIGN KEY (KursId) REFERENCES Kurs(KursId)
);
SQL,
    'Logisk design – datatyper' => <<<'SQL'
CREATE TABLE Produkt (
    ProduktId INT PRIMARY KEY,
    Namn VARCHAR(100) NOT NULL,
    Pris DECIMAL(10,2) NOT NULL,
    Skapad DATETIME NOT NULL,
    Aktiv CHAR(1) NOT NULL
);
SQL,
    'Logisk design – constraints' => <<<'SQL'
CREATE TABLE Observation (
    ObsId INT PRIMARY KEY,
    Sakerhet DECIMAL(5,2) NOT NULL
        CHECK (Sakerhet BETWEEN 1 AND 114),
    Epost VARCHAR(120) UNIQUE,
    PlatsKod INT NOT NULL,
    FOREIGN KEY (PlatsKod)
        REFERENCES Plats(PlatsKod)
);
SQL,
    'Fysisk design – denormalisering' => <<<'SQL'
CREATE TABLE Observation (
    ObsId INT PRIMARY KEY,
    IncidentId INT NOT NULL,
    IncidentNamn VARCHAR(100) NOT NULL,
    PlatsNamn VARCHAR(100) NOT NULL,
    Sakerhet DECIMAL(5,2) NOT NULL
);
SQL,
    'Fysisk design – indexering' => <<<'SQL'
CREATE INDEX IX_Observation_Datum
    ON Observation (Datum);

SELECT ObsId, Datum
FROM Observation
WHERE Datum >= '2026-01-01';
SQL,
    'Fysisk design – rättigheter' => <<<'SQL'
GRANT SELECT ON db.Kodtabell TO 'app'@'localhost';
GRANT EXECUTE ON PROCEDURE db.ArkiveraObservation
    TO 'app'@'localhost';
REVOKE DELETE ON db.Observation FROM 'app'@'localhost';
SQL,
    'Fysisk design – procedurer och triggers' => <<<'SQL'
CREATE PROCEDURE ArkiveraObservation(IN p_ObsId INT)
BEGIN
    START TRANSACTION;
    INSERT INTO Arkiv
        SELECT * FROM Observation WHERE ObsId = p_ObsId;
    DELETE FROM Observation WHERE ObsId = p_ObsId;
    COMMIT;
END;
SQL,
    'Struktur och flöde' => <<<'PHP'
<?php
$pdo = new PDO($dsn, $user, $pass);
$namn = trim($_POST['namn'] ?? '');
if ($namn !== '') {
    $stmt = $pdo->prepare(
        'INSERT INTO Person (Namn) VALUES (?)'
    );
    $stmt->execute([$namn]);
}
?>
<form method="post">
    <input name="namn">
    <button>Spara</button>
</form>
PHP,
    'Enkla tabeller (PHP)' => <<<'PHP'
<?php
$rader = $pdo->query(
    'SELECT PersonId, Namn FROM Person ORDER BY Namn'
);
?>
<table>
    <tr><th>PersonId</th><th>Namn</th></tr>
    <?php foreach ($rader as $rad): ?>
        <tr>
            <td><?= htmlspecialchars($rad['PersonId']) ?></td>
            <td><?= htmlspecialchars($rad['Namn']) ?></td>
        </tr>
    <?php endforeach; ?>
</table>
PHP,
    'Komplexa tabeller (PHP)' => <<<'PHP'
<?php
$sql = 'SELECT o.ObsId, o.IncidentNamn, p.Namn
        FROM Observation o
        LEFT JOIN Rapporterat r ON r.ObsId = o.ObsId
        LEFT JOIN Person p ON p.PersonId = r.PersonId
        ORDER BY o.ObsId';
$prevObs = null;
foreach ($pdo->query($sql) as $row) {
    if ($prevObs !== $row['ObsId']) {
        if ($prevObs !== null) echo '</table></td></tr>';
        echo '<tr><td>' . $row['IncidentNamn'] . '</td><td><table>';
    }
    if ($row['Namn'] !== null) {
        echo '<tr><td>' . $row['Namn'] . '</td></tr>';
    }
    $prevObs = $row['ObsId'];
}
?>
PHP,
    'Sökning, länkning och dropdown (PHP)' => <<<'PHP'
<?php
$sok = trim($_POST['sok'] ?? '');
$stmt = $pdo->prepare(
    'SELECT PersonId, Namn FROM Person WHERE Namn LIKE ?'
);
$stmt->execute(['%' . $sok . '%']);
$personer = $stmt->fetchAll();
$platser = $pdo->query(
    'SELECT PlatsKod, PlatsNamn FROM Plats'
);
?>
<form method="post">
    <input name="sok" value="<?= htmlspecialchars($sok) ?>">
    <select name="plats">
        <?php foreach ($platser as $p): ?>
            <option value="<?= $p['PlatsKod'] ?>">
                <?= $p['PlatsNamn'] ?>
            </option>
        <?php endforeach; ?>
    </select>
</form>
PHP,
    'Insert, update, delete och procedur (PHP)' => <<<'PHP'
<?php
$stmt = $pdo->prepare(
    'UPDATE Observation SET Sakerhet = ? WHERE ObsId = ?'
);
$stmt->bindValue(1, $sakerhet);
$stmt->bindValue(2, $obsid);
$stmt->execute();

$ark = $pdo->prepare('CALL ARKIVERAOBSERVATION(?)');
$ark->bindValue(1, $obsid);
$ark->execute();
$ark->closeCursor();
PHP,
    'Struktur och routing' => <<<'CS'
public class ObservationController : Controller
{
    private readonly ObservationModel _model;

    public ObservationController(IConfiguration config)
    {
        _model = new ObservationModel(config);
    }

    public IActionResult Index()
    {
        ViewBag.ObsTable = _model.GetObservationsWithDetails();
        return View();
    }
}
CS,
    'Enkla tabeller (MVC)' => <<<'RAZOR'
<table>
    <tr>
        @foreach (var col in ViewBag.AllPlats.Columns)
        {
            <th>@col.ColumnName</th>
        }
    </tr>
    @foreach (var row in ViewBag.AllPlats.Rows)
    {
        <tr>
            @foreach (var col in ViewBag.AllPlats.Columns)
            {
                <td>@row[col.ColumnName]</td>
            }
        </tr>
    }
</table>
RAZOR,
    'Komplexa tabeller (MVC)' => <<<'RAZOR'
@{ string prev = "None"; }
@foreach (var obs in ViewBag.ObsTable.Rows)
{
    if (prev != obs["IncidentId"].ToString())
    {
        <tr>
            <td>@obs["IncidentNamn"]</td>
            <td>
                <table>
                    @foreach (var inner in ViewBag.ObsTable.Rows)
                    {
                        if (inner["IncidentId"].ToString()
                            == obs["IncidentId"].ToString())
                        {
                            <tr><td>@inner["ObsId"]</td></tr>
                        }
                    }
                </table>
            </td>
        </tr>
    }
    prev = obs["IncidentId"].ToString();
}
RAZOR,
    'Sökning, länkning och dropdown (MVC)' => <<<'RAZOR'
<form method="post"
      action="@Url.Action("SearchObservations", "Observation")">
    <select name="platskod">
        @foreach (var row in ViewBag.AllPlats.Rows)
        {
            <option value="@row["PlatsKod"]">
                @row["PlatsNamn"]
            </option>
        }
    </select>
    <input type="submit" value="Sök">
</form>

@Html.ActionLink("Arkivera", "ArkiveraObservation",
    "Observation", new { obsid = 1 }, null)
RAZOR,
    'Insert, update, delete och procedur (MVC)' => <<<'CS'
// Controller
public IActionResult UpdateSakerhet(string obsid, string sakerhet)
{
    _model.UpdateSakerhet(obsid, sakerhet);
    return RedirectToAction("Index");
}

// Model
using var cmd = new MySqlCommand(
    "UPDATE Observation SET Sakerhet = @S WHERE ObsId = @O",
    dbcon
);
cmd.Parameters.AddWithValue("@S", sakerhet);
cmd.Parameters.AddWithValue("@O", obsid);
cmd.ExecuteNonQuery();
CS,
    'Designval och motivering' => <<<'RAZOR'
<select name="regnamn">
    @foreach (var h in ViewBag.AllHandlaggare.Rows)
    {
        <option value="@h["AgentNamn"]">@h["AgentNamn"]</option>
    }
</select>

<select name="regnr">
    @foreach (var h in ViewBag.AllHandlaggare.Rows)
    {
        <option value="@h["AgentNr"]">@h["AgentNr"]</option>
    }
</select>
RAZOR,
];

function e(?string $text): string
{
    return htmlspecialchars((string) $text, ENT_QUOTES, 'UTF-8');
}

$antal = 0;
foreach ($delar as $kort) {
    $antal += count($kort);
}
$antalKoder = count($koder);
?>
<!doctype html>
<html lang="sv">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Memorering – Tentaträning IT429G</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header class="topbar">
    <a class="brand" href="index.php">
        <span class="brand-mark">DB</span>
        <span>Tentaträning</span>
        <span class="brand-sub">IT429G</span>
    </a>
    <nav class="main-nav">
        <a href="index.php">Start</a>
        <a href="fuskblad.php">Fuskblad</a>
        <a href="tentaguide.php">Tentaguide</a>
        <a class="active" href="memorering.php">Memorering</a>
    </nav>
</header>

<main class="wrap">
    <section class="hero">
        <span class="eyebrow">Memorering · <?= $antal ?> mallar + <?= $antalKoder ?> kodbitar</span>
        <h1><?= $antal ?> mallar med kodbitar</h1>
        <p class="lead">
            Läs mallen, studera kodbiten som gör samma sak och dölj sedan båda. Återge
            först vad koden gör och varför, sedan mallen. Repetera efter 1 dag, 3 dagar
            och 1 vecka – mallarna är studiehjälp, inte fullständiga tentsvar.
        </p>
        <div class="hero-actions">
            <button type="button" class="btn btn-primary" data-mem-all="hide">Dölj alla mallar</button>
            <button type="button" class="btn" data-mem-all="show">Visa alla mallar</button>
            <button type="button" class="btn btn-ghost" data-mem-print>Skriv ut</button>
        </div>
    </section>

    <section class="panel mem-guide">
        <h2>Så använder du sidan</h2>
        <ol class="mem-steps">
            <li>Läs mallen och studera kodbiten tills du ser vilken rad som gör det viktigaste.</li>
            <li>Dölj mallen och förklara högt vad koden gör – inte bara vad den heter.</li>
            <li>Visa igen och rätta både formuleringen och din kodförklaring.</li>
            <li>Repetera efter 1 dag, 3 dagar och 1 vecka – inte bara samma dag.</li>
        </ol>
    </section>

    <section class="panel mem-guide">
        <h2>Så läser du en kodbit</h2>
        <ol class="mem-steps">
            <li>Bestäm språk och syfte: SQL, PHP eller MVC.</li>
            <li>Följ dataflödet: indata → bearbetning → utdata.</li>
            <li>Hitta nyckelraden: JOIN, WHERE, GROUP BY, prepare, RedirectToAction.</li>
            <li>Skriv motiveringen: ”Koden gör X eftersom Y” och koppla den till ämnet.</li>
        </ol>
    </section>

    <nav class="fusk-nav">
        <a href="#databas">Databas (8)</a>
        <a href="#php">PHP (5)</a>
        <a href="#mvc">.NET MVC (6)</a>
    </nav>

    <?php $nummer = 0; ?>
    <?php $sektion = 0; ?>
    <?php foreach ($delar as $del => $kort): ?>
        <?php $sektion++; ?>
        <section class="fusk-section" id="<?= e(mb_strtolower($del)) ?>">
            <h2>
                <span class="section-mark"><?= $sektion ?></span>
                <?= e($del === 'MVC' ? '.NET MVC' : $del) ?>
                <span class="muted">(<?= count($kort) ?>)</span>
            </h2>
            <div class="mem-grid">
                <?php foreach ($kort as $mall): ?>
                    <?php $nummer++; ?>
                    <article class="mem-card" data-mem-card>
                        <div class="mem-head">
                            <span class="mem-num"><?= $nummer ?></span>
                            <h3><?= e($mall['titel']) ?></h3>
                        </div>
                        <div class="mem-body">
                            <p><?= e($mall['mall']) ?></p>
                            <?php $kod = $koder[$mall['titel'] . ' (' . $del . ')'] ?? $koder[$mall['titel']] ?? null; ?>
                            <?php if ($kod !== null): ?>
                                <span class="mem-code-label">Kodbit</span>
                                <pre class="mem-code"><code><?= e($kod) ?></code></pre>
                            <?php endif; ?>
                            <div class="mem-keywords" aria-label="Nyckelord">
                                <?php foreach ($mall['nyckelord'] as $ord): ?>
                                    <span class="mem-keyword"><?= e($ord) ?></span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <button type="button" class="btn btn-small mem-toggle" data-mem-toggle aria-expanded="true">Dölj mall</button>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endforeach; ?>

    <section class="panel mem-note">
        <h2>Kom ihåg</h2>
        <p class="muted">
            Mallarna är korta för att vara lätta att repetera, inte fullständiga tentsvar.
            Kodbitarna visar samma sak i praktiken. På tentan måste du koppla både mallen
            och koden till just den frågan och de alternativ som finns. Ett felaktigt
            alternativ i del 2 ger 0 poäng för hela uppgiften.
        </p>
        <div class="hero-actions">
            <a class="btn btn-primary" href="index.php?action=start&mode=exam">Testa dig själv</a>
            <a class="btn" href="fuskblad.php">Till fuskbladet</a>
        </div>
    </section>
</main>

<footer class="footer">
    <p>Memorering för IT429G Databaskonstruktion. Byggd från kursens tre delmoment.</p>
</footer>
<script src="app.js"></script>
</body>
</html>

