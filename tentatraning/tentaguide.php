<!doctype html>
<html lang="sv">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tentaguide - Tentatraning IT429G</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header class="topbar">
    <a class="brand" href="index.php">
        <span class="brand-mark">DB</span>
        <span>Tentatraning</span>
        <span class="brand-sub">IT429G</span>
    </a>
    <nav class="main-nav">
        <a href="index.php">Start</a>
        <a href="fuskblad.php">Fuskblad</a>
        <a class="active" href="tentaguide.php">Tentaguide</a>
        <a href="memorering.php">Memorering</a>
    </nav>
</header>

<main class="wrap">
    <section class="hero">
        <span class="eyebrow">Tentaguide - IT429G</span>
        <h1>Helheten innan detaljerna</h1>
        <p class="lead">
            En guide byggd på de gamla tentorna och dina tre delmoment. Här lär du dig metoden att
            läsa ett nytt case, hitta entiteter, regler och operationer och koppla dem till databas,
            PHP och MVC. Du ska inte memorera ditt projekt - du ska förstå mönstret.
        </p>
        <div class="hero-actions">
            <a class="btn btn-primary" href="index.php?action=start&mode=exam">Starta blandat prov</a>
            <a class="btn" href="memorering.php">19 mallar med kod</a>
            <a class="btn btn-ghost" href="fuskblad.php">Fuskbladet</a>
        </div>
    </section>

    <div class="callout">
        <strong>Har du bara 2 dagar?</strong>
        Följ <a href="#plan">2-dagarsplanen</a>. Den är byggd för att förstå helheten först och
        sedan träna på själva provformatet. Läs inte allt - gör, förklara och rätta.
    </div>

    <nav class="fusk-nav">
        <a href="#format">Nya formatet</a>
        <a href="#gamla">Gamla tentor</a>
        <a href="#case">Case-metod</a>
        <a href="#roda">Röda tråden</a>
        <a href="#databas">Databas</a>
        <a href="#php">PHP</a>
        <a href="#mvc">MVC</a>
        <a href="#motivering">Motivering</a>
        <a href="#ovning">10 övningsfrågor</a>
        <a href="#plan">2-dagarsplan</a>
    </nav>

    <section class="fusk-section" id="format">
        <h2><span class="section-mark">1</span> Nya tentans format</h2>
        <div class="grid-2">
            <div class="panel">
                <h3>Fråga 1-7: ett rätt svar</h3>
                <ul>
                    <li>Varje fråga har fem alternativ.</li>
                    <li>Exakt ett alternativ är korrekt.</li>
                    <li>Du ska välja rätt alternativ och motivera i 2-4 meningar.</li>
                    <li>Motiveringen ska vara högst 50 ord.</li>
                </ul>
                <h3>Fråga 8-10: flera rätta svar</h3>
                <ul>
                    <li>Ett, flera eller alla alternativ kan vara korrekta.</li>
                    <li>Bedöm varje alternativ för sig.</li>
                    <li>Minst ett felaktigt alternativ ger 0 poäng för hela uppgiften.</li>
                    <li>Även flervalsfrågorna kräver en kort motivering.</li>
                </ul>
            </div>
            <div class="panel">
                <h3>Poäng och betyg</h3>
                <ul>
                    <li><strong>2 poäng per fråga:</strong> 1 p för rätt val och 1 p för godkänd motivering.</li>
                    <li><strong>Max 20 poäng.</strong></li>
                    <li><strong>G:</strong> minst 12 poäng.</li>
                    <li><strong>VG:</strong> minst 16 poäng.</li>
                </ul>
                <h3>Tidsplan</h3>
                <ul>
                    <li>Läs frågan noga och markera nyckelordet: ett eller flera svar.</li>
                    <li>Svara på alla frågor innan du finslipar motiveringarna.</li>
                    <li>Lämna 1-2 minuter per motivering och kontrollera flervalen extra.</li>
                </ul>
            </div>
        </div>
        <div class="callout">
            <strong>Nya formatet testar samma kunskap som de gamla tentorna.</strong>
            Skillnaden är att du måste välja rätt alternativ och förklara varför - inte skriva en lång utredning.
            Tänk: begrepp + förklaring + koppling till case eller kod.
        </div>
    </section>

    <section class="fusk-section" id="gamla">
        <h2><span class="section-mark">2</span> Vad de gamla tentorna frågade om</h2>
        <p>
            Båda gamla tentorna använder caset <strong>Tv-spelssamlarna</strong> och är uppdelade i
            Databasimplementation, PHP och MVC. Uppgifterna är öppna och breda. I nya tentan blir
            samma teman kortare frågor med svarsalternativ.
            <strong>Det viktiga är inte att memorera caset</strong> - det är att känna igen mönstret
            med entiteter, relationer, regler och operationer. Nya tentan kan använda en helt annan text.
        </p>
        <div class="guide-table-wrap">
            <table class="guide-table">
                <thead>
                    <tr>
                        <th>Tema från gamla tentor</th>
                        <th>Det de faktiskt frågade om</th>
                        <th>Så kan det bli i nya formatet</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Merge, vertikal och horisontell split</td>
                        <td>Vad teknikerna innebär, varför de kan optimera och hur nycklar påverkas.</td>
                        <td>Vilken teknik passar caset? Vad händer med PK/FK? Vilket exempel är rätt?</td>
                    </tr>
                    <tr>
                        <td>Kodifiering</td>
                        <td>Vad kodifiering är och hur den kan tillämpas på caset.</td>
                        <td>Varför byta ut upprepad text mot en kodtabell? Vilken tabell ska skapas?</td>
                    </tr>
                    <tr>
                        <td>Procedurer, triggers och SQL-injection</td>
                        <td>Skillnaden mellan procedur och trigger, egna exempel och skydd mot SQL-injection.</td>
                        <td>Vilket påstående är rätt? Varför är prepared statements ett skydd?</td>
                    </tr>
                    <tr>
                        <td>PDO och prepared statements</td>
                        <td>Fördelar med att binda input och vad som händer om man inte gör det.</td>
                        <td>Vad gör <code>bindValue</code>? Vad skyddar parametrar mot?</td>
                    </tr>
                    <tr>
                        <td>GET, POST, sessions och hidden inputs</td>
                        <td>Skicka data mellan sidor, single-page applikation och form-response.</td>
                        <td>Vilket sätt passar bäst? Vad är skillnaden mellan synligt, dolt och server-side state?</td>
                    </tr>
                    <tr>
                        <td>MVC-flödet och kodläsning</td>
                        <td>Formulär till databas till vy, controller/model, <code>LIKE</code> och <code>AddWithValue</code>.</td>
                        <td>Vilken del ansvarar för vad? Vad gör koden? Vad blir resultatet?</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <p class="muted">
            Källor:
            <a href="../Databaskonstruktion/gamla%20tentor/tenta%201.pdf" target="_blank" rel="noopener">Tenta 1</a>
            och
            <a href="../Databaskonstruktion/gamla%20tentor/tenta%202.pdf" target="_blank" rel="noopener">Tenta 2</a>.
        </p>
    </section>

    <section class="fusk-section" id="case">
        <h2><span class="section-mark">3</span> Så läser du en lång case-text</h2>
        <p>
            Frågorna kommer troligen inte att handla om just ditt projekt. De kommer att utgå från
            en längre text om ett annat system. Därför ska du inte memorera ditt case - du ska lära
            dig metoden att bryta ner vilket case som helst.
        </p>
        <div class="grid-2">
            <div class="panel">
                <h3>Fem steg</h3>
                <ol>
                    <li><strong>Läs helheten först:</strong> Vad är systemet? Vem använder det? Vilken data och vilka handlingar finns?</li>
                    <li><strong>Markera entiteter:</strong> substantiv som blir tabeller, till exempel Bok, Medlem och Lån.</li>
                    <li><strong>Markera relationer och regler:</strong> verb och uttryck som "har", "kan ha flera", "måste vara unik" och "får max".</li>
                    <li><strong>Markera operationer:</strong> sök, registrera, uppdatera, arkivera, logga och visa.</li>
                    <li><strong>Koppla till lager:</strong> struktur och regler till databasen, flödet till PHP/MVC, presentationen till vyn.</li>
                </ol>
            </div>
            <div class="panel">
                <h3>Exempel: ett bibliotek</h3>
                <p>
                    Läs texten och plocka ut det viktiga, inte alla detaljer:
                </p>
                <ul>
                    <li><strong>Entiteter:</strong> Bok, Författare, Medlem, Lån.</li>
                    <li><strong>Relationer:</strong> en författare har flera böcker; en medlem har flera lån; en bok kan lånas flera gånger.</li>
                    <li><strong>Regler:</strong> ISBN är unikt; en medlem får max fem aktiva lån; ett lån måste ha datum.</li>
                    <li><strong>Operationer:</strong> sök bok på titel, låna bok, lämna bok, lista lån.</li>
                </ul>
            </div>
        </div>
        <div class="guide-table-wrap">
            <table class="guide-table">
                <thead>
                    <tr>
                        <th>Texten säger</th>
                        <th>Vad du gör</th>
                        <th>Exempel</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>"Det finns böcker och medlemmar"</td>
                        <td>Entiteter blir tabeller</td>
                        <td><code>Bok</code>, <code>Medlem</code></td>
                    </tr>
                    <tr>
                        <td>"En bok kan ha flera författare"</td>
                        <td>M:N blir en kopplingstabell</td>
                        <td><code>BokForfattare</code> med två FK</td>
                    </tr>
                    <tr>
                        <td>"ISBN är unikt"</td>
                        <td>Regel blir en constraint</td>
                        <td><code>UNIQUE</code> på ISBN</td>
                    </tr>
                    <tr>
                        <td>"En medlem får max fem lån"</td>
                        <td>Regel blir CHECK, trigger eller applikationslogik</td>
                        <td>Kontroll före insert</td>
                    </tr>
                    <tr>
                        <td>"Sök på titel"</td>
                        <td>Operation blir en parameteriserad fråga</td>
                        <td><code>WHERE Titel LIKE ?</code></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="callout">
            <strong>Motivera med tre delar:</strong> 1) vilket begrepp det gäller,
            2) varför det fungerar så, 3) ett exempel från texten. Då blir svaret både korrekt och
            förankrat i caset - även om caset är helt nytt för dig.
        </div>
    </section>

    <section class="fusk-section" id="roda">
        <h2><span class="section-mark">4</span> Röda tråden: samma case i tre lager</h2>
        <p>
            Tänk alltid i den här ordningen: <strong>krav -> databas -> applikation -> presentation</strong>.
            Samma data och samma regler går igen i alla tre delmomenten.
        </p>
        <div class="flow">
            <div class="flow-step">
                <strong>1. Krav</strong>
                <span>Vad ska systemet göra? Vilka entiteter, regler och frågor finns?</span>
            </div>
            <div class="flow-step">
                <strong>2. Databas</strong>
                <span>ER -> tabeller -> PK/FK -> constraints -> index -> procedurer och triggers.</span>
            </div>
            <div class="flow-step">
                <strong>3. Applikation</strong>
                <span>PHP med PDO eller MVC med controller, model och view.</span>
            </div>
            <div class="flow-step">
                <strong>4. Presentation</strong>
                <span>HTML-tabell, nästlad tabell, dropdown och återkoppling till användaren.</span>
            </div>
        </div>
        <div class="grid-2">
            <div class="panel">
                <h3>Databasen är sanningen</h3>
                <p>
                    Databasen äger reglerna. PK identifierar rader, FK skyddar relationer och
                    CHECK/UNIQUE/NOT NULL hindrar fel data. Applikationen får inte vara enda skyddet.
                </p>
            </div>
            <div class="panel">
                <h3>Applikationen är vägen</h3>
                <p>
                    PHP och MVC gör samma sak: tar emot input, kör SQL och visar resultat.
                    PHP gör det i en fil med procedurellt flöde. MVC delar upp ansvaret i tre delar.
                </p>
            </div>
        </div>
        <div class="callout">
            <strong>Fastnar du?</strong> Fråga dig vilket lager frågan gäller.
            "Vilken datatyp?" är databas. "Vad gör <code>bindValue</code>?" är PHP.
            "Vem anropar modellen?" är MVC.
        </div>
    </section>

    <section class="fusk-section" id="databas">
        <h2><span class="section-mark">5</span> Databas - från ER till SQL</h2>
        <div class="grid-2">
            <div class="panel">
                <h3>Logisk design</h3>
                <ul>
                    <li><strong>Entiteter och relationer:</strong> 1:1 och 1:N löses med FK. M:N kräver en kopplingstabell.</li>
                    <li><strong>Nycklar:</strong> PK identifierar, FK refererar. Sammansatt PK kräver att alla delar finns i FK.</li>
                    <li><strong>Datatyper:</strong> CHAR fast längd, VARCHAR variabel text, INT heltal, DECIMAL exakta decimaltal, DATETIME datum och tid.</li>
                    <li><strong>Constraints:</strong> PRIMARY KEY, FOREIGN KEY, CHECK, UNIQUE och NOT NULL.</li>
                </ul>
                <p class="muted">
                    Ditt projekt är bara ett exempel. På tentan kan tabellerna heta något annat.
                    Leta efter samma mönster: entiteter, relationer, regler och operationer.
                </p>
            </div>
            <div class="panel">
                <h3>Fysisk design</h3>
                <ul>
                    <li><strong>Merge:</strong> slå ihop tabeller när de alltid används tillsammans.</li>
                    <li><strong>Kodifiering:</strong> ersätt upprepad text med en kodtabell.</li>
                    <li><strong>Horisontell split:</strong> flytta stora kolumner till en egen tabell.</li>
                    <li><strong>Vertikal split:</strong> dela upp rader efter användning, till exempel aktiva och arkiverade.</li>
                    <li><strong>Index:</strong> snabbare sökning och sortering, till exempel BTREE på datum eller namn.</li>
                    <li><strong>Rättigheter:</strong> GRANT/REVOKE enligt minsta rättighets princip.</li>
                    <li><strong>Procedurer och triggers:</strong> procedur anropas med CALL, trigger körs automatiskt vid en händelse.</li>
                </ul>
            </div>
        </div>
        <div class="callout">
            <strong>Kursens terminologi:</strong> horisontell split = dela kolumner, vertikal split = dela rader.
            Det är omvänt mot en del standardlitteratur - håll koll på vad kursen menar.
        </div>
        <div class="grid-2">
            <div class="panel">
                <h3>Samma mönster i ditt projekt</h3>
                <ul>
                    <li>En procedur som räknar medelvärde (<code>GETAVGSAKERHET</code>).</li>
                    <li>En procedur som flyttar en rad atomärt till arkivtabellen (<code>ARKIVERAOBSERVATION</code>).</li>
                    <li>En trigger som loggar nya rader (<code>LOGGOBSERVATION</code>).</li>
                    <li>En trigger som kontrollerar ett värde före insert (<code>CHECKOBSGRAD</code>).</li>
                    <li>Index som snabbar upp sortering på datum och namn (<code>OBSDATUM</code>, <code>PERSONNAMN</code>).</li>
                </ul>
                <p class="muted">Tentan kan använda andra namn - det är principen som testas.</p>
            </div>
            <div class="panel">
                <h3>Typisk tentafälla</h3>
                <p>
                    Ett index gör sökning snabbare men gör inte kolumnen unik och ersätter inte en constraint.
                    En trigger är inte samma sak som en procedur. En FK skyddar referenser men skapar inte
                    automatiskt ett index i alla databaser.
                </p>
            </div>
        </div>
    </section>

    <section class="fusk-section" id="php">
        <h2><span class="section-mark">6</span> PHP - en sida, ett flöde</h2>
        <p>
            En typisk PHP-app (som din) är självrefererande: formuläret postar tillbaka till samma fil.
            Därför måste PHP-blocket läsa <code>$_POST</code> och köra SQL innan HTML:en skrivs ut.
            Samma princip gäller oavsett vad tabellerna heter.
        </p>
        <pre class="guide-code"><code>&lt;?php
$pdo = new PDO($dsn, $user, $pass);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo-&gt;prepare('INSERT INTO DinTabell (...) VALUES (?, ?, ?)');
    $stmt-&gt;execute([$obsid, $datum, $sakerhet]);
}
?&gt;

&lt;form method="post" action="PHPApplikation.php"&gt;
    ...
&lt;/form&gt;</code></pre>
        <div class="grid-2">
            <div class="panel">
                <h3>Det viktigaste</h3>
                <ul>
                    <li><strong>PDO:</strong> <code>prepare</code>, <code>bindValue</code> och <code>execute</code>.</li>
                    <li><strong>Skydd:</strong> parametrar skiljer data från SQL-kod och minskar risken för SQL-injection.</li>
                    <li><strong>Dropdown:</strong> fylls från databasen så att användaren bara kan välja giltiga värden.</li>
                    <li><strong>Nästlad tabell:</strong> yttre rad per huvudpost, inre tabell med detaljer.</li>
                    <li><strong><code>$prevObs</code>:</strong> jämför föregående ObsId och avgör när en ny yttre rad ska börjas.</li>
                </ul>
            </div>
            <div class="panel">
                <h3>GET, POST, session och hidden</h3>
                <ul>
                    <li><strong>GET:</strong> data syns i URL:en, bra för sökningar och bokmärken.</li>
                    <li><strong>POST:</strong> data skickas i request-body, bättre för ändringar och känsligare data.</li>
                    <li><strong>Session:</strong> state lagras på servern och kopplas till användaren via en cookie.</li>
                    <li><strong>Hidden input:</strong> skickas med formuläret men är inte hemlig - användaren kan ändra värdet.</li>
                    <li><strong>Single-page:</strong> formulär och output i samma fil. <strong>Form-response:</strong> formulär och svar på olika sidor.</li>
                </ul>
            </div>
        </div>
        <div class="callout">
            <strong>Kom ihåg:</strong> prepared statements skyddar mot SQL-injection, men de ersätter inte
            validering av datatyper, behörigheter eller affärsregler.
        </div>
    </section>

    <section class="fusk-section" id="mvc">
        <h2><span class="section-mark">7</span> MVC - samma flöde, tydligare ansvar</h2>
        <div class="flow">
            <div class="flow-step">
                <strong>View</strong>
                <span>Formulär med <code>Url.Action</code> postar till en action.</span>
            </div>
            <div class="flow-step">
                <strong>Controller</strong>
                <span>Tar emot request, anropar model och väljer view eller redirect.</span>
            </div>
            <div class="flow-step">
                <strong>Model</strong>
                <span>Kör SQL med parametrar och returnerar DataTable eller påverkar databasen.</span>
            </div>
            <div class="flow-step">
                <strong>View</strong>
                <span>Renderar HTML med Razor, ViewBag, loopar och nästlade tabeller.</span>
            </div>
        </div>
        <div class="grid-2">
            <div class="panel">
                <h3>Byggstenar</h3>
                <ul>
                    <li><strong>Routing:</strong> URL -> controller/action.</li>
                    <li><strong>Controller:</strong> dirigenten som tar emot request och returnerar view.</li>
                    <li><strong>Model:</strong> databaslogiken och SQL-frågorna.</li>
                    <li><strong>View:</strong> HTML/Razor som användaren ser.</li>
                    <li><strong>ViewBag:</strong> skickar data från controller till view.</li>
                    <li><strong>DataTable:</strong> vanligt resultat från MySQL-frågor i en MVC-app.</li>
                </ul>
            </div>
            <div class="panel">
                <h3>Viktiga mönster</h3>
                <ul>
                    <li><strong><code>Url.Action</code>:</strong> bygger rätt URL till en action.</li>
                    <li><strong><code>ActionLink</code>:</strong> skapar en länk, till exempel "Arkivera".</li>
                    <li><strong><code>RedirectToAction</code>:</strong> PRG - undvik att F5 skickar formuläret igen.</li>
                    <li><strong><code>AddWithValue</code>:</strong> binder parametrar i C#.</li>
                    <li><strong>Flera controllers:</strong> en controller per ansvarsområde, till exempel personer och observationer.</li>
                    <li><strong>Kolumnlayout:</strong> yttre rad per huvudpost, inre tabell med detaljer.</li>
                </ul>
            </div>
        </div>
        <pre class="guide-code"><code>public IActionResult Insert(...)
{
    _model.Insert(...);
    return RedirectToAction("Index");
}</code></pre>
        <div class="callout">
            <strong>Jämför PHP och MVC:</strong> PHP gör allt i en fil. MVC delar upp samma flöde i
            View -> Controller -> Model -> View. Databasen och SQL:en är fortfarande kärnan.
        </div>
    </section>

    <section class="fusk-section" id="motivering">
        <h2><span class="section-mark">8</span> Motiveringar som ger poäng</h2>
        <div class="grid-2">
            <div class="panel">
                <h3>Enkelval: fråga 1-7</h3>
                <p class="motivation-template">
                    Jag väljer <strong>X</strong> eftersom ... Det innebär att ... I caset passar det
                    eftersom ... Därför är X det bästa alternativet.
                </p>
                <p>
                    Exempel: "Jag väljer DECIMAL(10,2) eftersom priset är ett exakt decimaltal.
                    Datatypen bevarar två decimaler och undviker avrundningsfel. I caset passar den
                    för belopp som ska jämföras och beräknas."
                </p>
            </div>
            <div class="panel">
                <h3>Flerval: fråga 8-10</h3>
                <p class="motivation-template">
                    <strong>X</strong> är rätt eftersom ... <strong>Y</strong> är också rätt eftersom ...
                    <strong>Z</strong> är fel eftersom ... Därför väljer jag X och Y.
                </p>
                <p>
                    Exempel: "PRIMARY KEY och FOREIGN KEY är rätt eftersom de identifierar rader och
                    skyddar referenser. CHECK och UNIQUE är också rätt eftersom de begränsar värden och
                    dubbletter. SELECT är fel eftersom det är en fråga, inte en constraint."
                </p>
            </div>
        </div>
        <div class="panel">
            <h3>Fyra snabba regler</h3>
            <ul>
                <li>Skriv 2-4 meningar och högst 50 ord.</li>
                <li>Nämn begreppet, förklara varför och koppla till case eller kod.</li>
                <li>Skriv inte bara "det är rätt" - visa att du förstår.</li>
                <li>I flerval: förklara både varför rätt är rätt och varför fel är fel.</li>
            </ul>
        </div>
    </section>

    <section class="fusk-section" id="ovning">
        <h2><span class="section-mark">9</span> 10 övningsfrågor i nya formatet</h2>
        <p>Gör frågorna först utan att öppna svaret. Skriv dina motiveringar med egna ord. I appen finns nu 46 interaktiva frågor med slumpade svarsalternativ – kör ett fullt prov när du vill träna tempot. Markera frågor du redan kan med "Jag kan den" så hoppas de över i nästa prov.</p>

        <div class="guide-q">
            <p><strong>1.</strong> Vilken teknik innebär att stora textkolumner flyttas till en egen tabell?</p>
            <ol type="A">
                <li>Merge</li>
                <li>Kodifiering</li>
                <li>Horisontell split</li>
                <li>Vertikal split</li>
                <li>Index</li>
            </ol>
            <details>
                <summary>Visa svar och motivering</summary>
                <p><strong>C. Horisontell split.</strong> Horisontell split delar tabellen efter kolumner och flyttar stora textfält till en egen tabell. Det minskar mängden data som läses vid vanliga frågor. Vertikal split delar i stället upp rader, till exempel aktiva och arkiverade.</p>
            </details>
        </div>

        <div class="guide-q">
            <p><strong>2.</strong> Vad skiljer en trigger från en lagrad procedur?</p>
            <ol type="A">
                <li>En trigger anropas alltid med CALL.</li>
                <li>En trigger körs automatiskt vid en händelse, medan en procedur anropas med CALL.</li>
                <li>En procedur kan inte ta parametrar.</li>
                <li>En trigger kan bara användas vid SELECT.</li>
                <li>En procedur körs bara vid INSERT.</li>
            </ol>
            <details>
                <summary>Visa svar och motivering</summary>
                <p><strong>B.</strong> En trigger aktiveras automatiskt av en händelse, till exempel AFTER INSERT. En procedur är ett namngivet block som anropas uttryckligen med CALL. Därför är triggern händelsestyrd och proceduren anropsstyrd.</p>
            </details>
        </div>

        <div class="guide-q">
            <p><strong>3.</strong> Varför använder man prepared statements med PDO?</p>
            <ol type="A">
                <li>De gör HTML-tabellen snyggare.</li>
                <li>De binder användarinput som parametrar och minskar risken för SQL-injection.</li>
                <li>De gör kolumnen unik.</li>
                <li>De ersätter foreign keys.</li>
                <li>De skapar automatiskt index.</li>
            </ol>
            <details>
                <summary>Visa svar och motivering</summary>
                <p><strong>B.</strong> Prepared statements skickar SQL och data separat. Användarinput blir parametrar i stället för en del av SQL-koden. Det minskar risken för SQL-injection och gör typningen säkrare.</p>
            </details>
        </div>

        <div class="guide-q">
            <p><strong>4.</strong> I en självrefererande PHP-sida, vad måste göras innan HTML skrivs ut?</p>
            <ol type="A">
                <li>Inget - HTML kan skrivas först.</li>
                <li>Läsa <code>$_POST</code> och köra SQL.</li>
                <li>Stänga sessionen.</li>
                <li>Skapa ett index.</li>
                <li>Byta databas.</li>
            </ol>
            <details>
                <summary>Visa svar och motivering</summary>
                <p><strong>B.</strong> Formuläret postar tillbaka till samma fil. Därför måste PHP-blocket läsa <code>$_POST</code> och köra SQL innan HTML:en skickas. Annars hinner inte resultatet med i sidan.</p>
            </details>
        </div>

        <div class="guide-q">
            <p><strong>5.</strong> Vilken del av MVC tar emot request, anropar modellen och returnerar en view?</p>
            <ol type="A">
                <li>Model</li>
                <li>View</li>
                <li>Controller</li>
                <li>Razor</li>
                <li>SQL</li>
            </ol>
            <details>
                <summary>Visa svar och motivering</summary>
                <p><strong>C. Controller.</strong> Controllern tar emot requesten och koordinerar arbetet. Den anropar modellen för data och returnerar sedan en view. Modellen kör SQL och vyn renderar HTML.</p>
            </details>
        </div>

        <div class="guide-q">
            <p><strong>6.</strong> Vad är syftet med <code>RedirectToAction</code> efter en insert i MVC?</p>
            <ol type="A">
                <li>Skapa en ny tabell.</li>
                <li>Implementera PRG så att F5 inte skickar formuläret igen.</li>
                <li>Radera en rad.</li>
                <li>Skapa ett index.</li>
                <li>Byta databas.</li>
            </ol>
            <details>
                <summary>Visa svar och motivering</summary>
                <p><strong>B.</strong> RedirectToAction skickar användaren till en ny GET-request efter att ändringen sparats. Det kallas PRG, Post-Redirect-Get. Då upprepas inte inserten om användaren trycker F5.</p>
            </details>
        </div>

        <div class="guide-q">
            <p><strong>7.</strong> Vad gör <code>LIKE @NAME</code> när parametern får <code>%</code> före och efter värdet?</p>
            <ol type="A">
                <li>Kräver exakt matchning.</li>
                <li>Söker efter en delsträng.</li>
                <li>Sorterar resultatet.</li>
                <li>Skapar en primärnyckel.</li>
                <li>Tar bort raden.</li>
            </ol>
            <details>
                <summary>Visa svar och motivering</summary>
                <p><strong>B.</strong> Procenttecknet är ett jokertecken i LIKE. Med <code>%namn%</code> matchas alla värden som innehåller delsträngen. Utan procenttecken krävs exakt matchning.</p>
            </details>
        </div>

        <div class="guide-q">
            <p><strong>8.</strong> Vilka av följande är constraints?</p>
            <ol type="A">
                <li>PRIMARY KEY</li>
                <li>FOREIGN KEY</li>
                <li>CHECK</li>
                <li>UNIQUE</li>
                <li>SELECT</li>
            </ol>
            <details>
                <summary>Visa svar och motivering</summary>
                <p><strong>A, B, C och D.</strong> PRIMARY KEY identifierar rader, FOREIGN KEY skyddar referenser, CHECK begränsar värden och UNIQUE hindrar dubbletter. SELECT är en fråga och inte en constraint.</p>
            </details>
        </div>

        <div class="guide-q">
            <p><strong>9.</strong> Vilka påståenden om GET, POST och sessioner är korrekta?</p>
            <ol type="A">
                <li>GET skickar data som syns i URL:en.</li>
                <li>POST skickar data i request-body.</li>
                <li>En session kan lagra state på servern.</li>
                <li>Ett hidden input-fält är alltid hemligt för användaren.</li>
                <li>GET är alltid säkrare än POST.</li>
            </ol>
            <details>
                <summary>Visa svar och motivering</summary>
                <p><strong>A, B och C.</strong> GET-data syns i URL:en och POST-data skickas i bodyn. Sessioner lagrar state på servern. Hidden inputs är inte hemliga eftersom användaren kan se och ändra dem. GET är inte automatiskt säkrare än POST.</p>
            </details>
        </div>

        <div class="guide-q">
            <p><strong>10.</strong> Vilka påståenden om MVC är korrekta?</p>
            <ol type="A">
                <li>Controllern tar emot requesten.</li>
                <li>Modellen kan köra SQL mot databasen.</li>
                <li>Vyn renderar HTML med Razor.</li>
                <li>ViewBag kan skicka data från controller till view.</li>
                <li>Modellen ansvarar för att rendera HTML.</li>
            </ol>
            <details>
                <summary>Visa svar och motivering</summary>
                <p><strong>A, B, C och D.</strong> Controllern tar emot requesten, modellen kör SQL och vyn renderar HTML. ViewBag används för att skicka data från controller till view. Det är vyn, inte modellen, som ansvarar för HTML.</p>
            </details>
        </div>
    </section>

    <section class="fusk-section" id="plan">
        <h2><span class="section-mark">10</span> 2 dagar - så lär du dig helheten</h2>
        <p>
            Målet är inte att läsa allt eller memorera ditt projekt. Målet är att kunna läsa en ny
            case-text, plocka ut <strong>entiteter, regler och operationer</strong> och koppla dem till
            <strong>krav -> databas -> applikation -> HTML</strong>. Dag 1 bygger du helheten.
            Dag 2 tränar du på formatet.
        </p>
        <div class="grid-2">
            <div class="panel">
                <h3>Dag 1 - Förstå kedjan</h3>
                <ul>
                    <li><strong>08:30-09:15:</strong> Läs "Nya formatet", "Case-metoden" och "Röda tråden". Ta en kort text och markera entiteter, regler och operationer. Rita kedjan på papper.</li>
                    <li><strong>09:00-11:00:</strong> Databas: läs översikten, gå igenom de 8 memoreringskorten och kör databasträning (10 frågor).</li>
                    <li><strong>11:15-12:45:</strong> PHP: läs översikten, gå igenom de 5 korten och kör PHP-träning (10 frågor).</li>
                    <li><strong>13:30-15:00:</strong> MVC: läs översikten, gå igenom de 6 korten och kör MVC-träning (10 frågor).</li>
                    <li><strong>15:15-16:15:</strong> Rätta alla fel. Skriv en egen motivering till varje fel med begrepp + varför + exempel.</li>
                    <li><strong>16:15-16:30:</strong> Stäng appen. Förklara högt: "Ett case blir tabeller, som PHP eller MVC läser och visar som HTML."</li>
                </ul>
                <p class="muted">Dag 1 är klar när du kan rita flödet och förklara varje del med egna ord.</p>
            </div>
            <div class="panel">
                <h3>Dag 2 - Träna på formatet</h3>
                <ul>
                    <li><strong>08:30-09:15:</strong> Läs motiveringsmallarna. Gör de 10 övningsfrågorna utan att öppna svaren. Gör en mini-ER på 10 minuter från en kort case-text.</li>
                    <li><strong>09:00-10:30:</strong> Gör ett fullt blandat prov under tid. Skriv 2-4 meningar till varje fråga.</li>
                    <li><strong>10:45-12:00:</strong> Rätta provet. För varje fel: hitta begreppet i guiden och skriv om motiveringen.</li>
                    <li><strong>13:00-14:30:</strong> Kör "Repetera fel" och träna på din svagaste kategori.</li>
                    <li><strong>14:30-15:30:</strong> Aktiv recall: skriv 20 begrepp på papper, förklara dem högt och koda ett litet PDO- eller MVC-exempel.</li>
                    <li><strong>15:30-16:00:</strong> Läs checklistan. Repetera bara det du är osäker på. Sov.</li>
                </ul>
                <p class="muted">Dag 2 är klar när du har minst 12/20 på ett fullt prov och kan förklara dina fel.</p>
            </div>
        </div>
        <div class="callout">
            <strong>Tre regler som gör störst skillnad:</strong>
            1) Läs inte passivt - förklara högt och skriv egna motiveringar.
            2) Varje fel ska bli en kort förklaring med begrepp, orsak och exempel.
            3) Träna på metoden, inte caset: samma mönster går igen i databas, PHP och MVC - även när texten byts ut.
        </div>
        <div class="panel">
            <h3>Minsta version om du har lite tid</h3>
            <ul>
                <li><strong>Dag 1:</strong> 45 min databas + 45 min PHP + 30 min MVC. Kör 10 frågor per del och rätta direkt.</li>
                <li><strong>Dag 2:</strong> Gör ett fullt prov, rätta alla fel, läs motiveringsmallarna och checklistan.</li>
                <li><strong>Allra minst:</strong> Läs "Röda tråden", kör ett blandat prov och skriv en motivering till varje fråga.</li>
            </ul>
        </div>
    </section>

    <section class="fusk-section" id="checklista">
        <h2><span class="section-mark">11</span> Checklista före tentan</h2>
        <div class="grid-2">
            <div class="panel">
                <h3>Databas</h3>
                <ul class="guide-check">
                    <li>Jag kan läsa en ny case-text och plocka ut entiteter, regler och operationer.</li>
                    <li>Jag kan förklara PK, FK, CHECK, UNIQUE och NOT NULL.</li>
                    <li>Jag kan skilja på merge, kodifiering, horisontell och vertikal split.</li>
                    <li>Jag kan förklara varför ett index inte är en constraint.</li>
                    <li>Jag kan skilja på procedur och trigger.</li>
                    <li>Jag kan förklara hur prepared statements minskar SQL-injection.</li>
                </ul>
            </div>
            <div class="panel">
                <h3>PHP och MVC</h3>
                <ul class="guide-check">
                    <li>Jag kan förklara varför PHP måste köras före HTML i en självrefererande sida.</li>
                    <li>Jag kan förklara GET, POST, session och hidden input.</li>
                    <li>Jag kan följa flödet View -> Controller -> Model -> View.</li>
                    <li>Jag kan förklara ViewBag, DataTable, Url.Action och RedirectToAction.</li>
                    <li>Jag kan skriva en motivering på 2-4 meningar med begrepp och exempel.</li>
                </ul>
            </div>
        </div>
        <div class="hero-actions">
            <a class="btn btn-primary" href="index.php?action=start&mode=exam">Kör ett fullt prov</a>
            <a class="btn" href="memorering.php">Repetera mallarna</a>
            <a class="btn btn-ghost" href="fuskblad.php">Till fuskbladet</a>
        </div>
    </section>
</main>

<footer class="site-footer">
    <p>Tentaguide för IT429G - byggd på gamla tentor och de tre delmomenten.</p>
</footer>
</body>
</html>
