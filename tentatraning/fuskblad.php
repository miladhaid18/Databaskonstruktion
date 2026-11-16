<!doctype html>
<html lang="sv">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Fuskblad – Tentaträning IT429G</title>
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
        <a class="active" href="fuskblad.php">Fuskblad</a>
        <a href="tentaguide.php">Tentaguide</a>
        <a href="memorering.php">Memorering</a>
    </nav>
</header>

<main class="wrap">
    <section class="hero">
        <span class="eyebrow">Fuskblad · Databas · PHP · .NET MVC</span>
        <h1>Allt du behöver inför salstentamen</h1>
        <p class="lead">
            Kortfattade förklaringar, projektets egna exempel och de vanligaste fällorna.
            Använd sidan som uppslagsverk när du kör träningsprovet.
        </p>
        <div class="hero-actions">
            <a class="btn btn-primary" href="index.php?action=start&mode=exam">Starta träningsprov</a>
            <a class="btn" href="memorering.php">19 mallar med kod</a>
            <a class="btn btn-ghost" href="index.php">Till startsidan</a>
        </div>
    </section>

    <nav class="fusk-nav">
        <a href="#tentan">Tentans upplägg</a>
        <a href="#databas">Databas</a>
        <a href="#php">PHP</a>
        <a href="#mvc">.NET MVC</a>
        <a href="#exempel">Lärarnas exempel</a>
        <a href="#strategi">Tentastrategi</a>
        <a href="#fallor">10 fällor</a>
        <a href="#begrepp">Begrepp</a>
    </nav>

    <section class="fusk-section" id="tentan">
        <h2><span class="section-mark">1</span> Tentans upplägg</h2>
        <div class="grid-2">
            <div class="panel">
                <h3>Format</h3>
                <ul>
                    <li><strong>10 uppgifter</strong> med fem svarsalternativ vardera.</li>
                    <li><strong>Del 1:</strong> 7 uppgifter med exakt ett korrekt alternativ.</li>
                    <li><strong>Del 2:</strong> 3 uppgifter där ett, flera eller alla alternativ kan vara korrekta.</li>
                    <li><strong>Motivering:</strong> 2–4 meningar, cirka 30–50 ord, max 50 ord.</li>
                    <li><strong>Poäng:</strong> 1 p för rätt alternativ + 1 p för korrekt motivering.</li>
                    <li><strong>Viktigt:</strong> minst ett felaktigt alternativ ger 0 p för hela uppgiften, även om motiveringen är bra.</li>
                </ul>
            </div>
            <div class="panel">
                <h3>Betyg</h3>
                <ul>
                    <li><strong>Godkänd:</strong> minst 12 av 20 poäng.</li>
                    <li><strong>Väl godkänd:</strong> minst 16 av 20 poäng.</li>
                    <li><strong>Max:</strong> 20 poäng.</li>
                </ul>
                <p class="muted">Tio frågor × två poäng. Det räcker med sex helt korrekta svar för G och åtta för VG, om motiveringarna är godkända.</p>
            </div>
        </div>
        <div class="callout">
            <strong>Läs frågan noga:</strong> ”Vilket alternativ…” betyder ett svar. ”Vilka alternativ…” betyder att flera kan vara rätt.
            I del 2 ska du bedöma varje alternativ för sig – lägg inte till ett alternativ som bara ”låter rimligt”, för ett enda fel nollställer hela uppgiften.
        </div>
    </section>

    <section class="fusk-section" id="databas">
        <h2><span class="section-mark">2</span> Databas</h2>

        <div class="grid-2">
            <div class="panel">
                <h3>Logisk design – tre steg</h3>
                <ul>
                    <li><strong>1. Relationer:</strong> gå från ER-diagrammet till tabeller, primärnycklar och främmande nycklar.</li>
                    <li><strong>2. Datatyper:</strong> välj CHAR, INTEGER, VARCHAR, DECIMAL, DATETIME utifrån domänen.</li>
                    <li><strong>3. Constraints:</strong> bestäm PK, FK, CHECK, UNIQUE och NOT NULL.</li>
                </ul>
                <p class="muted">Relationerna dokumenterar överföringen från ER till relationsmodell. Den fysiska modellen är SQL-koden.</p>
            </div>
            <div class="panel">
                <h3>Fysisk design – stegen</h3>
                <ul>
                    <li><strong>1. Denormalisering:</strong> merge, kodifiering, horisontell och vertikal.</li>
                    <li><strong>2. Indexering:</strong> snabbare sökning och sortering.</li>
                    <li><strong>3. Rättigheter:</strong> GRANT och REVOKE, minsta rättighets princip.</li>
                    <li><strong>4. Procedurer och triggers:</strong> funktioner, constraints, loggning, materialisering/flytt.</li>
                </ul>
            </div>
        </div>

        <div class="panel">
            <h3>Denormalisering – fyra tekniker</h3>
            <table class="fusk-table">
                <thead>
                <tr><th>Teknik</th><th>Innebörd</th><th>Exempel i projektet</th></tr>
                </thead>
                <tbody>
                <tr>
                    <td>Merge</td>
                    <td>Slå ihop tabeller eller kolumner så att data ligger direkt i huvudtabellen och en join undviks.</td>
                    <td>IncidentNamn ligger direkt i Observation i stället för i en egen Incident-tabell.</td>
                </tr>
                <tr>
                    <td>Kodifiering</td>
                    <td>Byt ut upprepad text mot en kod och en kodtabell (uppslagstabell).</td>
                    <td>PlatsKod mot Plats och GradKod mot GradBeskrivning.</td>
                </tr>
                <tr>
                    <td>Horisontell</td>
                    <td>Lyft ut stora kolumner, ofta text, till en egen tabell med samma primärnyckel.</td>
                    <td>AgentKommentar innehåller Kommentar, som sällan läses tillsammans med Agent.</td>
                </tr>
                <tr>
                    <td>Vertikal</td>
                    <td>Dela upp rader i tabeller efter användningsfrekvens.</td>
                    <td>Observation för aktiva och ARKIVERADObservation för arkiverade observationer.</td>
                </tr>
                </tbody>
            </table>
            <p class="muted" style="margin-top:10px">Ett index är en fysisk optimering men räknas inte som denormalisering. Merge, kodifiering, horisontell och vertikal är de fyra teknikerna.</p>
        </div>

        <div class="panel">
            <h3>Constraints – vad de gör</h3>
            <table class="fusk-table">
                <thead>
                <tr><th>Constraint</th><th>Syfte</th><th>Exempel</th></tr>
                </thead>
                <tbody>
                <tr><td>PRIMARY KEY</td><td>Unikt identifierar varje rad och får inte vara NULL.</td><td>ObsId i Observation, (AgentNamn, AgentNr) i Agent.</td></tr>
                <tr><td>FOREIGN KEY</td><td>Referensintegritet: värdet måste finnas i den refererade tabellen.</td><td>Observation.PlatsKod måste finnas i Plats.</td></tr>
                <tr><td>CHECK</td><td>Domänintegritet: bara värden inom ett intervall eller en regel.</td><td>GradKod 1–4 och Sakerhet 1–114.</td></tr>
                <tr><td>UNIQUE</td><td>Förhindrar dubbletter i en kolumn.</td><td>PlatsNamn får bara förekomma en gång.</td></tr>
                <tr><td>NOT NULL</td><td>Kräver att kolumnen har ett värde.</td><td>PersonId och Namn i Person.</td></tr>
                <tr><td>DEFAULT</td><td>Ger ett standardvärde om inget anges.</td><td>Lon har DEFAULT 13000.</td></tr>
                </tbody>
            </table>
            <p class="muted" style="margin-top:10px">CHECK och FK skyddar databasen även om applikationen har en bugg. De är inte index och gör inte frågor snabbare.</p>
        </div>

        <div class="grid-2">
            <div class="panel">
                <h3>Indexering</h3>
                <ul>
                    <li><strong>OBSDATUM</strong> på Observation(Datum DESC) används för sortering på datum.</li>
                    <li><strong>PERSONNAMN</strong> på Person(Namn ASC) används för sortering på namn.</li>
                    <li>Båda är BTREE, bra för likhet, intervall och ORDER BY.</li>
                    <li>Ett vanligt index gör inte kolumnen unik – det kräver UNIQUE.</li>
                </ul>
            </div>
            <div class="panel">
                <h3>Rättigheter</h3>
                <ul>
                    <li><strong>GRANT</strong> ger rättigheter, <strong>REVOKE</strong> tar bort dem.</li>
                    <li>Appen använder en egen användare i stället för root – minsta rättighets princip.</li>
                    <li><strong>EXECUTE</strong> krävs för att appen ska få anropa procedurerna.</li>
                    <li>Appen får SELECT/INSERT/UPDATE/DELETE på Observation men bara läsa kodtabellerna.</li>
                </ul>
            </div>
        </div>

        <div class="panel">
            <h3>Procedurer och triggers i projektet</h3>
            <table class="fusk-table">
                <thead>
                <tr><th>Objekt</th><th>Typ</th><th>Vad den gör</th></tr>
                </thead>
                <tbody>
                <tr><td>LOGGOBSERVATION</td><td>Trigger, AFTER INSERT</td><td>Loggar varje ny observation i ObservationLog med användare, ObsId och tid.</td></tr>
                <tr><td>CHECKOBSGRAD</td><td>Trigger, BEFORE INSERT</td><td>Stoppar GradKod utanför 1–4 med SIGNAL. Dubbelsäkerhet mot CHECK-constrainten.</td></tr>
                <tr><td>GETAVGSAKERHET</td><td>Procedur</td><td>Returnerar AVG(Sakerhet) från Observation.</td></tr>
                <tr><td>ARKIVERAOBSERVATION</td><td>Procedur</td><td>Tar bort Rapporterat-kopplingar, kopierar raden till ARKIVERADObservation och tar bort originalet.</td></tr>
                </tbody>
            </table>
            <p class="muted" style="margin-top:10px">Trigger = körs automatiskt vid en händelse. Procedur = anropas med CALL och kan ta emot parametrar.</p>
        </div>

        <div class="callout">
            <strong>Typiska varför-svar:</strong> kodifiering minskar redundans och stavfel; merge undviker en join men upprepar data;
            horisontell håller huvudtabellen smal; vertikal håller den aktiva tabellen liten och snabb;
            CHECK ger domänintegritet; FK ger referensintegritet; index snabbar upp sökning/sortering;
            proceduren för arkivering håller ihop flera steg och återanvänds av både PHP och .NET.
        </div>
    </section>

    <section class="fusk-section" id="php">
        <h2><span class="section-mark">3</span> PHP</h2>

        <div class="panel">
            <h3>Flödet i den självrefererande applikationen</h3>
            <p><strong>Formulär (POST, samma fil)</strong> → <strong>PHP högst upp ($_POST)</strong> → <strong>PDO prepare/bind/execute</strong> → <strong>HTML längst ner (foreach/echo)</strong></p>
            <p class="muted">Allt ligger i PHPApplikation.php. Formulärets action pekar tillbaka till samma fil. PHP-blocket körs före HTML:en, så POST hinner hanteras innan sidan skrivs ut.</p>
        </div>

        <div class="grid-2">
            <div class="panel">
                <h3>Formulär och $_POST</h3>
                <ul>
                    <li><code>method="POST"</code> skickar fälten i requestens body.</li>
                    <li>Fältets <code>name</code> blir nyckeln: <code>name="soknamn"</code> läses med <code>$_POST["soknamn"]</code>.</li>
                    <li><code>$_GET</code> används för query-strängen, <code>$_SESSION</code> för sessionsdata.</li>
                    <li>I en självrefererande app pekar <code>action</code> på samma fil.</li>
                </ul>
            </div>
            <div class="panel">
                <h3>Prepared statements</h3>
                <ul>
                    <li>SQL-strängen använder platshållare, till exempel <code>WHERE Namn LIKE ?</code>.</li>
                    <li><code>bindValue(1, "%" . $soknamn . "%")</code> knyter värdet till platshållaren.</li>
                    <li>SQL och data skickas separat – skyddar mot SQL-injektion.</li>
                    <li>Man måste fortfarande validera indata, till exempel att Grad är 1–4.</li>
                </ul>
            </div>
        </div>

        <div class="panel">
            <h3>De tre uppdateringarna i PHP-appen</h3>
            <table class="fusk-table">
                <thead>
                <tr><th>Funktion</th><th>Kod</th><th>Varför</th></tr>
                </thead>
                <tbody>
                <tr>
                    <td>INSERT</td>
                    <td><code>INSERT INTO Observation (...) VALUES (?, ?, ...)</code> med 9 parametrar.</td>
                    <td>Validering först, sedan prepared INSERT. Datum och övriga värden binds säkert.</td>
                </tr>
                <tr>
                    <td>UPDATE</td>
                    <td><code>UPDATE Observation SET Sakerhet = ? WHERE ObsId = ?</code></td>
                    <td>WHERE på primärnyckeln gör att exakt en observation ändras.</td>
                </tr>
                <tr>
                    <td>DELETE / arkivering</td>
                    <td><code>CALL ARKIVERAOBSERVATION(?)</code> följt av <code>closeCursor()</code></td>
                    <td>Proceduren flyttar raden atomärt. closeCursor frigör resultatet så nästa fråga kan köras.</td>
                </tr>
                </tbody>
            </table>
        </div>

        <div class="panel">
            <h3>Komplex tabell – radlayout med $prevObs</h3>
            <ul>
                <li>SQL: <code>LEFT JOIN Rapporterat</code> och <code>LEFT JOIN Person</code> för vittnen, <code>JOIN Plats</code> för platsnamn. Sorterat på ObsId.</li>
                <li><code>$prevObs = "NONE"</code> före loopen.</li>
                <li>När <code>$prevObs != $row["ObsId"]</code> börjar en ny observation: stäng föregående inre tabell och öppna en ny yttre rad.</li>
                <li>Inuti raden skapas en nästlad HTML-tabell med vittnena.</li>
                <li>Efter varje rad uppdateras <code>$prevObs</code>. Efter loopen stängs den sista inre tabellen.</li>
            </ul>
            <p class="muted">Radlayout = en yttre rad per observation och en inre tabell med vittnen bredvid. Jämförelsevariabeln grupperar rätt rader.</p>
        </div>

        <div class="callout">
            <strong>PHP-fällor:</strong> <code>$POST</code> ska vara <code>$_POST</code>. UPDATE utan WHERE uppdaterar alla rader.
            En parameter som binds men inte finns i SQL-strängen gör ingen nytta. <code>execute()</code> behöver inga argument när
            <code>bindParam</code>/<code>bindValue</code> redan har bundit värdena. Glöm inte <code>closeCursor()</code> efter CALL.
        </div>
    </section>

    <section class="fusk-section" id="mvc">
        <h2><span class="section-mark">4</span> .NET MVC</h2>

        <div class="panel">
            <h3>Requestens väg</h3>
            <p><strong>URL</strong> → <strong>Program.cs (routing)</strong> → <strong>Controller</strong> → <strong>Model</strong> → <strong>Databas</strong></p>
            <p><strong>Controller</strong> → <strong>View</strong> (HTML med Razor och ViewBag)</p>
            <p class="muted">Program.cs mappar <code>{controller}/{action}</code>. Controllern tar emot, modellen kör SQL och vyn renderar.</p>
        </div>

        <div class="grid-2">
            <div class="panel">
                <h3>Rollerna</h3>
                <table class="fusk-table">
                    <tbody>
                    <tr><td>Controller</td><td>Tar emot request/formulärdata, anropar modellen, väljer View eller RedirectToAction.</td></tr>
                    <tr><td>Model</td><td>Databaslogik: MySqlConnection, MySqlCommand, DataAdapter, DataTable. Ingen HTML.</td></tr>
                    <tr><td>View</td><td>Renderar HTML med Razor, foreach och ViewBag. Kör ingen SQL.</td></tr>
                    <tr><td>Program.cs</td><td>Routing och applikationens start. UsePathBase, UseStaticFiles, MapControllerRoute.</td></tr>
                    </tbody>
                </table>
            </div>
            <div class="panel">
                <h3>Nyckelbegrepp</h3>
                <ul>
                    <li><strong>ViewBag:</strong> dynamisk behållare från controller till vy. Inte en modellklass.</li>
                    <li><strong>@Url.Action:</strong> bygger en URL till controller/action utifrån routingen. Kör inte actionen.</li>
                    <li><strong>RedirectToAction:</strong> skickar en ny request (PRG). <code>View()</code> renderar direkt.</li>
                    <li><strong>AddWithValue:</strong> parameteriserad SQL, ingen strängkonkatenering.</li>
                    <li><strong>SchoolConnection:</strong> nyckeln i appsettings.json som modellerna läser.</li>
                    <li><strong>Två dropdownar:</strong> Handlaggare har sammansatt PK (AgentNamn, AgentNr).</li>
                </ul>
            </div>
        </div>

        <div class="panel">
            <h3>Kolumnlayouten</h3>
            <ul>
                <li>Yttre rad = en incident. Inre nästlad tabell = incidentens observationer.</li>
                <li><code>prev</code> jämför IncidentId så att en ny yttre rad bara skrivs när incidenten ändras.</li>
                <li>SQL sorterar på <code>IncidentId, ObsId</code> – annars fungerar inte grupperingen.</li>
                <li>Varje observation har en Arkivera-länk via <code>@Html.ActionLink</code> (GET).</li>
            </ul>
            <p class="muted">PHP använder radlayout eftersom en observation har många vittnen. .NET använder kolumnlayout eftersom en incident har många observationer.</p>
        </div>

        <div class="panel">
            <h3>MVC-kedjor – fyra filer per funktion</h3>
            <div class="table-wrap">
                <table class="fusk-table">
                    <thead>
                    <tr><th>Funktion</th><th>View</th><th>Controller</th><th>Model</th><th>SQL</th></tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>Visa alla</td>
                        <td>Observation/Index.cshtml, kolumnlayout</td>
                        <td>Index()</td>
                        <td>GetObservationsWithDetails()</td>
                        <td>JOIN Observation, Plats, GradBeskrivning, Agent</td>
                    </tr>
                    <tr>
                        <td>Filtrera per plats</td>
                        <td>Formulär med platsdropdown</td>
                        <td>SearchObservations(platskod)</td>
                        <td>SearchObservationsByPlats()</td>
                        <td>WHERE PlatsKod = @PLATSKOD</td>
                    </tr>
                    <tr>
                        <td>INSERT</td>
                        <td>Formulär med 8 fält + 2 dropdownar</td>
                        <td>InsertObservation(...)</td>
                        <td>InsertObservation()</td>
                        <td>INSERT med 9 parametrar och NOW()</td>
                    </tr>
                    <tr>
                        <td>UPDATE</td>
                        <td>Dropdown ObsId + säkerhetsfält</td>
                        <td>UpdateSakerhet(obsid, sakerhet)</td>
                        <td>UpdateSakerhet()</td>
                        <td>UPDATE ... WHERE ObsId = @OBSID</td>
                    </tr>
                    <tr>
                        <td>Arkivera</td>
                        <td>ActionLink med obsid</td>
                        <td>ArkiveraObservation(obsid)</td>
                        <td>ArkiveraObservation()</td>
                        <td>CALL ARKIVERAOBSERVATION(@OBSID)</td>
                    </tr>
                    <tr>
                        <td>Sök person</td>
                        <td>Formulär med namn</td>
                        <td>SearchPersons(name)</td>
                        <td>SearchPersons()</td>
                        <td>WHERE Namn LIKE @NAME</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="callout">
            <strong>MVC-fällor:</strong> Modellen är inte en databastabell. ViewBag är inte en modellklass.
            RedirectToAction renderar inte en vy – den gör en ny request. Arkivera är en länk (GET), inte ett formulär.
            Applikationen använder MySQL, inte SQL Server.
        </div>
    </section>

    <section class="fusk-section" id="exempel">
        <h2><span class="section-mark">5</span> Lärarnas exempelfrågor</h2>

        <div class="example">
            <h3>Exempel 1 – hitta felen i PHP-koden</h3>
            <pre class="code"><code>$stmt = $pdo->prepare('UPDATE INVOICEROW SET DATEPAID=:datepaid, COST=:co');
$stmt->bindParam(':datepaid', $POST['paiddate']);
$stmt->bindParam(':co', $POST['cost']);
$stmt->bindParam(':customernumber', $POST['custno']);
$stmt->execute();</code></pre>
            <p class="answer-line">Rätt alternativ: 1, 2 och 3.</p>
            <ul>
                <li><strong>SQL saknar WHERE.</strong> Utan WHERE uppdateras alla fakturarader, inte en specifik rad.</li>
                <li><strong>customernumber saknas i SQL-strängen.</strong> Parametern binds men används aldrig, så rätt rad kan inte identifieras.</li>
                <li><strong>Superglobalen är felstavad.</strong> Det ska vara <code>$_POST</code>, inte <code>$POST</code>.</li>
                <li><strong>FROM saknas – fel.</strong> En UPDATE-sats har ingen FROM-sats.</li>
                <li><strong>execute() saknar parametrar – fel.</strong> När bindParam redan har bundit värdena kan execute() anropas utan argument.</li>
            </ul>
            <p class="muted">I den återgivna koden finns dessutom ett citatteckenfel i SQL-strängen, men det är inte ett av svarsalternativen.</p>
        </div>

        <div class="example">
            <h3>Exempel 2 – lagrade procedurer</h3>
            <p>Vilket påstående om stored procedures är korrekt?</p>
            <p class="answer-line">Rätt alternativ: ”… innebär att man kan skicka in data i parametrar som man kan använda i sin procedur”.</p>
            <ul>
                <li><strong>Fel:</strong> procedurer gör inte att man kan klara sig utan tabeller. Data lagras fortfarande i tabeller.</li>
                <li><strong>Rätt:</strong> en procedur kan ta emot parametrar, till exempel <code>ARKIVERAOBSERVATION(p_ObsId)</code>.</li>
                <li><strong>Fel:</strong> merge-tekniken kräver ingen procedur för att man ska kunna ställa frågor.</li>
                <li><strong>Fel:</strong> man behöver ingen include-fil i databasen för att använda en procedur.</li>
                <li><strong>Fel:</strong> man anropar en procedur med <code>CALL</code>. <code>EXECUTE</code> är en rättighet, inte ett måste i slutet av proceduren.</li>
            </ul>
        </div>
    </section>

    <section class="fusk-section" id="strategi">
        <h2><span class="section-mark">6</span> Tentastrategi</h2>
        <div class="grid-2">
            <div class="panel">
                <h3>Så angriper du en fråga</h3>
                <ul>
                    <li>Läs om det står <strong>vilket</strong> eller <strong>vilka</strong>.</li>
                    <li>I del 1: välj exakt ett alternativ.</li>
                    <li>I del 2: bedöm varje alternativ för sig. Ett felaktigt val nollställer frågan.</li>
                    <li>Om du är osäker: uteslut alternativ som blandar begrepp, till exempel CHECK och index.</li>
                    <li>Motivera med kursens begrepp, inte bara med en omskrivning av alternativet.</li>
                    <li>Håll dig under 50 ord och kontrollera att du svarat på <em>varför</em>.</li>
                </ul>
            </div>
            <div class="panel">
                <h3>Motiveringsmall</h3>
                <p class="quote">Rätt alternativ är X eftersom … Det innebär att … Därför …</p>
                <p>Exempel på en FK-fråga:</p>
                <p class="quote quote-example">Rätt alternativ är A eftersom en FOREIGN KEY kräver att värdet finns i den refererade tabellen. Det ger referensintegritet, så en observation inte kan peka på en okänd plats. Därför stoppar databasen inserten.</p>
                <p class="meta">Cirka 40 ord, tre meningar – inom gränsen.</p>
            </div>
        </div>
        <div class="callout">
            <strong>Tidsplan:</strong> 10 frågor på ungefär 50–60 minuter. Lägg mer tid på del 2, där du måste bedöma varje alternativ.
            Om du fastnar: gå vidare och kom tillbaka. Ett tomt svar ger 0 poäng, men ett felaktigt val i del 2 kan också ge 0 – välj bara alternativ du kan motivera.
        </div>
    </section>

    <section class="fusk-section" id="fallor">
        <h2><span class="section-mark">7</span> 10 fällor att undvika</h2>
        <div class="panel">
            <ol>
                <li><strong>Horisontell vs vertikal:</strong> horisontell = stora kolumner lyfts ut; vertikal = rader delas upp efter användning.</li>
                <li><strong>Merge vs kodifiering:</strong> merge slår ihop tabeller; kodifiering byter text mot kod och kodtabell.</li>
                <li><strong>Index är inte unikhet:</strong> ett vanligt index gör inte kolumnen unik. Det kräver UNIQUE.</li>
                <li><strong>FK är inte CHECK:</strong> FK = referensintegritet, CHECK = domänintegritet.</li>
                <li><strong>Trigger anropas inte manuellt:</strong> den körs automatiskt vid en händelse. Procedurer anropas med CALL.</li>
                <li><strong>Procedure kräver inte execute() i slutet:</strong> anropet görs med CALL. EXECUTE är en behörighet.</li>
                <li><strong>Prepared statements ersätter inte validering:</strong> de skyddar mot SQL-injektion, men ogiltiga värden måste fortfarande stoppas.</li>
                <li><strong>ViewBag är ingen modellklass:</strong> det är en dynamisk behållare från controller till vy.</li>
                <li><strong>RedirectToAction renderar inte en vy:</strong> den skickar en ny request (PRG). View() renderar direkt.</li>
                <li><strong>Välj inte ”alla som låter rätt” i del 2:</strong> ett enda felaktigt alternativ ger 0 poäng för hela uppgiften.</li>
            </ol>
        </div>
        <div class="panel">
            <h3>Fler snabba sanningar</h3>
            <ul>
                <li>Modellen är C#-klassen som kör SQL – inte en databastabell.</li>
                <li>PHP-appen är självrefererande, inte MVC med separata lager.</li>
                <li>.NET-appen använder MySQL (MySql.Data), inte SQL Server.</li>
                <li>Arkivering i .NET är en ActionLink (GET), inte ett formulär.</li>
                <li>Handlaggare har sammansatt PK, därför två dropdownar.</li>
                <li>Kolumnlayout: yttre rad per incident, inre tabell med observationer.</li>
                <li>Radlayout: yttre rad per observation, inre tabell med vittnen.</li>
            </ul>
        </div>
    </section>

    <section class="fusk-section" id="begrepp">
        <h2><span class="section-mark">8</span> Begrepp på en rad</h2>
        <div class="panel">
            <table class="fusk-table">
                <tbody>
                <tr><td>Domänintegritet</td><td>Regler för vilka värden som får sparas, till exempel CHECK och UNIQUE.</td></tr>
                <tr><td>Referensintegritet</td><td>Främmande nycklar måste peka på rader som finns.</td></tr>
                <tr><td>Redundans</td><td>Samma data lagras på flera ställen. Kodifiering och merge kan minska eller öka den.</td></tr>
                <tr><td>Atomär</td><td>Allt eller inget. En procedur kan hålla ihop flera steg.</td></tr>
                <tr><td>Parameteriserad SQL</td><td>SQL och värden skickas separat med platshållare.</td></tr>
                <tr><td>Routing</td><td>URL mappas till controller och action i Program.cs.</td></tr>
                <tr><td>PRG</td><td>Post-Redirect-Get: efter POST redirectar man till GET så att F5 inte upprepar.</td></tr>
                <tr><td>Nästlad tabell</td><td>En HTML-tabell inuti en cell i en annan HTML-tabell.</td></tr>
                <tr><td>Sammansatt nyckel</td><td>En nyckel som består av flera kolumner, till exempel (AgentNamn, AgentNr).</td></tr>
                <tr><td>Denormalisering</td><td>Medveten avvikelse från normaliserad struktur för enkelhet eller prestanda.</td></tr>
                <tr><td>Kodtabell</td><td>Uppslagstabell som kopplar en kod till en beskrivning.</td></tr>
                <tr><td>ViewBag</td><td>Dynamisk behållare som skickar data från controller till vy.</td></tr>
                </tbody>
            </table>
        </div>
        <div class="actions">
            <a class="btn btn-primary" href="index.php?action=start&mode=exam">Testa dig själv nu</a>
            <a class="btn" href="index.php">Till startsidan</a>
        </div>
    </section>
</main>

<footer class="footer">
    <p>Fuskbladet är sammanställt från kursens delmoment och lärarnas exempelfrågor. Använd träningsprovet för att öva på formatet.</p>
</footer>
</body>
</html>
