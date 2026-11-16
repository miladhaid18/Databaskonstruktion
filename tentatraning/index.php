<?php

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$fragor = require __DIR__ . '/fragor.php';

const KANDA_COOKIE = 'tentatraning_kanda';

function e(?string $text): string
{
    return htmlspecialchars((string) $text, ENT_QUOTES, 'UTF-8');
}

function kanda_ids(array $fragor): array
{
    $raw = $_COOKIE[KANDA_COOKIE] ?? '';
    if (!is_string($raw) || $raw === '') {
        return [];
    }
    $giltiga = array_column($fragor, 'id');
    $ids = array_map('trim', explode(',', $raw));
    return array_values(array_intersect(array_unique($ids), $giltiga));
}

function spara_kanda(array $ids): void
{
    $ids = array_values(array_unique(array_filter($ids, 'is_string')));
    if ($ids === []) {
        setcookie(KANDA_COOKIE, '', time() - 3600, '/');
        unset($_COOKIE[KANDA_COOKIE]);
        return;
    }

    $varde = implode(',', $ids);
    setcookie(KANDA_COOKIE, $varde, [
        'expires' => time() + 31536000,
        'path' => '/',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    $_COOKIE[KANDA_COOKIE] = $varde;
}

function ordrakning(string $text): int
{
    $text = trim($text);
    if ($text === '') {
        return 0;
    }
    $ord = preg_split('/\s+/u', $text, -1, PREG_SPLIT_NO_EMPTY);
    return is_array($ord) ? count($ord) : 0;
}

function meningsrakning(string $text): int
{
    $text = trim($text);
    if ($text === '') {
        return 0;
    }
    $antal = preg_match_all('/[.!?]+(?=\s|$)/u', $text);
    return max(1, (int) $antal);
}

function hitta_nyckelord(array $fraga, string $text): array
{
    $text = mb_strtolower($text, 'UTF-8');
    $funna = [];
    foreach ($fraga['nyckelord'] as $grupp) {
        foreach ($grupp['varianter'] as $variant) {
            if (mb_strpos($text, mb_strtolower($variant, 'UTF-8')) !== false) {
                $funna[] = $grupp['namn'];
                break;
            }
        }
    }
    return $funna;
}

function utvardera(array $fraga, array $valda, string $motivering): array
{
    $ratt = [];
    foreach ($fraga['alternativ'] as $index => $alternativ) {
        if (!empty($alternativ['ratt'])) {
            $ratt[] = $index;
        }
    }

    sort($valda);
    sort($ratt);

    $valRatt = $valda === $ratt;
    $ord = ordrakning($motivering);
    $meningar = meningsrakning($motivering);
    $funna = hitta_nyckelord($fraga, $motivering);
    $saknade = [];
    foreach ($fraga['nyckelord'] as $grupp) {
        if (!in_array($grupp['namn'], $funna, true)) {
            $saknade[] = $grupp['namn'];
        }
    }

    $motRatt = $ord >= 8 && $ord <= 50 && count($funna) >= $fraga['min_nyckelord'];
    $poang = $valRatt ? (1 + ($motRatt ? 1 : 0)) : 0;

    return [
        'valda' => $valda,
        'ratt' => $ratt,
        'val_ratt' => $valRatt,
        'motivering' => $motivering,
        'ord' => $ord,
        'meningar' => $meningar,
        'funna' => $funna,
        'saknade' => $saknade,
        'mot_ratt' => $motRatt,
        'poang' => $poang,
    ];
}

function fraga_fran_id(array $fragor, string $id): ?array
{
    foreach ($fragor as $fraga) {
        if ($fraga['id'] === $id) {
            return $fraga;
        }
    }
    return null;
}

function skapa_ordning(array $fragor, array $ids): array
{
    $ordning = [];
    foreach ($ids as $id) {
        $fraga = fraga_fran_id($fragor, $id);
        if ($fraga === null) {
            continue;
        }
        $indexar = range(0, count($fraga['alternativ']) - 1);
        shuffle($indexar);
        $ordning[$id] = $indexar;
    }
    return $ordning;
}

function aktuell_fraga(array $quiz, array $fragor): ?array
{
    $id = $quiz['ids'][$quiz['index']] ?? null;
    return $id !== null ? fraga_fran_id($fragor, $id) : null;
}

function forsta_obesvarade(array $quiz): ?int
{
    foreach ($quiz['ids'] as $index => $id) {
        if (!isset($quiz['svar'][$id])) {
            return $index;
        }
    }
    return null;
}

function bygg_prov(array $fragor, string $mode): array
{
    $kanda = kanda_ids($fragor);
    $pool = array_values(array_filter($fragor, fn ($q) => !in_array($q['id'], $kanda, true)));

    $perKategori = ['Databas' => [], 'PHP' => [], 'MVC' => []];
    foreach ($pool as $fraga) {
        $perKategori[$fraga['kategori']][] = $fraga;
    }

    $valda = [];
    if (in_array($mode, ['Databas', 'PHP', 'MVC'], true)) {
        $kategorifragor = $perKategori[$mode];
        $singlar = array_values(array_filter($kategorifragor, fn ($q) => $q['del'] === 1));
        $multi = array_values(array_filter($kategorifragor, fn ($q) => $q['del'] === 2));
        shuffle($singlar);
        shuffle($multi);
        $valda = array_merge(array_slice($singlar, 0, 7), array_slice($multi, 0, 3));
        $titel = 'Träning – ' . $mode;
    } else {
        $singlar = [];
        $multi = [];
        foreach ($perKategori as $kategori => $kategorifragor) {
            $singlar[$kategori] = array_values(array_filter($kategorifragor, fn ($q) => $q['del'] === 1));
            $multi[$kategori] = array_values(array_filter($kategorifragor, fn ($q) => $q['del'] === 2));
            shuffle($singlar[$kategori]);
            shuffle($multi[$kategori]);
        }

        foreach (['Databas', 'PHP', 'MVC'] as $kategori) {
            $valda = array_merge($valda, array_slice($singlar[$kategori], 0, 2));
        }

        $rester = [];
        foreach (['Databas', 'PHP', 'MVC'] as $kategori) {
            $rester = array_merge($rester, array_slice($singlar[$kategori], 2));
        }
        shuffle($rester);
        $behov = 7 - count($valda);
        if ($behov > 0) {
            $valda = array_merge($valda, array_slice($rester, 0, $behov));
        }

        foreach (['Databas', 'PHP', 'MVC'] as $kategori) {
            if (isset($multi[$kategori][0])) {
                $valda[] = $multi[$kategori][0];
            }
        }

        $multiRester = [];
        foreach (['Databas', 'PHP', 'MVC'] as $kategori) {
            $multiRester = array_merge($multiRester, array_slice($multi[$kategori], 1));
        }
        shuffle($multiRester);
        $multiAntal = count(array_filter($valda, fn ($q) => $q['del'] === 2));
        $behov = 3 - $multiAntal;
        if ($behov > 0) {
            $valda = array_merge($valda, array_slice($multiRester, 0, $behov));
        }

        $titel = count($valda) < 10 ? 'Öva kvarvarande' : 'Salstentamen';
    }

    if ($valda === []) {
        return [];
    }

    $singelVal = array_values(array_filter($valda, fn ($q) => $q['del'] === 1));
    $multiVal = array_values(array_filter($valda, fn ($q) => $q['del'] === 2));
    shuffle($singelVal);
    shuffle($multiVal);
    $valda = array_merge($singelVal, $multiVal);

    $ids = array_map(fn ($q) => $q['id'], $valda);
    if (count($ids) < 10) {
        $titel .= ' · ' . count($ids) . ' frågor';
    }

    return [
        'mode' => $mode,
        'titel' => $titel,
        'ids' => $ids,
        'ordning' => skapa_ordning($fragor, $ids),
        'index' => 0,
        'svar' => [],
        'skapad' => time(),
    ];
}

function betyg_info(int $poang): array
{
    if ($poang >= 16) {
        return ['namn' => 'Väl godkänd', 'klass' => 'vg', 'kort' => 'VG'];
    }
    if ($poang >= 12) {
        return ['namn' => 'Godkänd', 'klass' => 'g', 'kort' => 'G'];
    }
    return ['namn' => 'Inte godkänd', 'klass' => 'ig', 'kort' => 'IG'];
}

function summering(array $quiz, array $fragor): array
{
    $perKategori = [
        'Databas' => ['poang' => 0, 'max' => 0],
        'PHP' => ['poang' => 0, 'max' => 0],
        'MVC' => ['poang' => 0, 'max' => 0],
    ];
    $poang = 0;
    $besvarade = 0;

    foreach ($quiz['ids'] as $id) {
        $fraga = fraga_fran_id($fragor, $id);
        if ($fraga === null) {
            continue;
        }
        $perKategori[$fraga['kategori']]['max'] += 2;
        if (isset($quiz['svar'][$id])) {
            $svar = $quiz['svar'][$id];
            $poang += $svar['poang'];
            $perKategori[$fraga['kategori']]['poang'] += $svar['poang'];
            $besvarade++;
        }
    }

    return [
        'poang' => $poang,
        'max' => count($quiz['ids']) * 2,
        'besvarade' => $besvarade,
        'perKategori' => $perKategori,
        'betyg' => betyg_info($poang),
    ];
}

function spara_historik(array $summering, array $quiz, string $betygNamn): void
{
    if (!isset($_SESSION['historik']) || !is_array($_SESSION['historik'])) {
        $_SESSION['historik'] = [];
    }

    array_unshift($_SESSION['historik'], [
        'datum' => date('Y-m-d H:i'),
        'titel' => $quiz['titel'],
        'poang' => $summering['poang'],
        'max' => $summering['max'],
        'betyg' => $betygNamn,
    ]);

    $_SESSION['historik'] = array_slice($_SESSION['historik'], 0, 8);
}

function flash(string $typ, string $text): void
{
    $_SESSION['flash'] = ['typ' => $typ, 'text' => $text];
}

function ta_flash(): ?array
{
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $flash;
}

function bokstaver(array $indexar): string
{
    return implode(', ', array_map(fn ($i) => chr(65 + (int) $i), $indexar));
}

function ordning_for_fraga(array $quiz, array $fraga): array
{
    $ordning = $quiz['ordning'][$fraga['id']] ?? null;
    if (is_array($ordning) && count($ordning) === count($fraga['alternativ'])) {
        return array_values($ordning);
    }
    return range(0, count($fraga['alternativ']) - 1);
}

function bokstaver_i_ordning(array $indexar, array $ordning): string
{
    $positioner = [];
    foreach ($indexar as $index) {
        $position = array_search((int) $index, $ordning, true);
        if ($position !== false) {
            $positioner[] = $position;
        }
    }
    sort($positioner);
    return implode(', ', array_map(fn ($p) => chr(65 + (int) $p), $positioner));
}

function alternativ_feedback(array $fraga, array $svar, ?array $ordning = null): string
{
    $ordning = $ordning ?? range(0, count($fraga['alternativ']) - 1);
    $html = '<ul class="option-list feedback-list">';
    foreach ($ordning as $position => $index) {
        if (!isset($fraga['alternativ'][$index])) {
            continue;
        }
        $alternativ = $fraga['alternativ'][$index];
        $ratt = !empty($alternativ['ratt']);
        $vald = in_array($index, $svar['valda'], true);
        $klass = $ratt ? 'opt-correct' : 'opt-wrong';
        if ($vald) {
            $klass .= $ratt ? ' opt-picked-correct' : ' opt-picked-wrong';
        }

        $html .= '<li class="' . $klass . '">';
        $html .= '<div class="opt-head">';
        $html .= '<span class="option-letter">' . chr(65 + (int) $position) . '</span>';
        $html .= '<span class="opt-text">' . e($alternativ['text']) . '</span>';
        $html .= '<span class="pill ' . ($ratt ? 'pill-ok' : 'pill-bad') . '">' . ($ratt ? 'Rätt svar' : 'Fel') . '</span>';
        if ($vald) {
            $html .= '<span class="pill pill-you">Ditt val</span>';
        }
        $html .= '</div>';
        $html .= '<p class="opt-expl">' . e($alternativ['forklaring']) . '</p>';
        $html .= '</li>';
    }
    return $html . '</ul>';
}

function sidhuvud(string $titel, string $aktiv = ''): void
{
    $nav = [
        ['id' => 'hem', 'text' => 'Start', 'url' => 'index.php'],
        ['id' => 'fuskblad', 'text' => 'Fuskblad', 'url' => 'fuskblad.php'],
        ['id' => 'tentaguide', 'text' => 'Tentaguide', 'url' => 'tentaguide.php'],
        ['id' => 'memorering', 'text' => 'Memorering', 'url' => 'memorering.php'],
    ];
    ?>
<!doctype html>
<html lang="sv">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($titel) ?> – Tentaträning IT429G</title>
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
        <?php foreach ($nav as $lank): ?>
            <a class="<?= $aktiv === $lank['id'] ? 'active' : '' ?>" href="<?= e($lank['url']) ?>"><?= e($lank['text']) ?></a>
        <?php endforeach; ?>
    </nav>
</header>
<main class="wrap">
<?php
    $flash = ta_flash();
    if ($flash !== null) {
        $klass = $flash['typ'] === 'ok' ? 'flash-ok' : 'flash-fel';
        echo '<div class="flash ' . $klass . '">' . e($flash['text']) . '</div>';
    }
}

function sidfot(): void
{
    ?>
</main>
<footer class="footer">
    <p>Träningsapp byggd från kursens delmoment i databas, PHP och .NET MVC. Nyckelorden i motiveringsbedömningen är en studiehjälp – jämför alltid med exempelmotiveringen.</p>
</footer>
<script src="app.js"></script>
</body>
</html>
<?php
}

$action = $_GET['action'] ?? 'home';

if ($action === 'reset_kanda') {
    spara_kanda([]);
    flash('ok', 'Alla frågor visas igen.');
    header('Location: index.php');
    exit;
}

if ($action === 'known') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: index.php');
        exit;
    }

    $qid = (string) ($_POST['qid'] ?? '');
    $fraga = fraga_fran_id($fragor, $qid);
    if ($fraga === null) {
        flash('fel', 'Frågan kunde inte hittas.');
        header('Location: index.php');
        exit;
    }

    $kanda = kanda_ids($fragor);
    if (!in_array($qid, $kanda, true)) {
        $kanda[] = $qid;
        spara_kanda($kanda);
    }

    $quiz = $_SESSION['quiz'] ?? null;
    if ($quiz !== null) {
        $position = array_search($qid, $quiz['ids'], true);
        if ($position !== false) {
            array_splice($_SESSION['quiz']['ids'], $position, 1);
            unset($_SESSION['quiz']['ordning'][$qid], $_SESSION['quiz']['svar'][$qid], $_SESSION['utkast'][$qid]);
            if ($position < $_SESSION['quiz']['index']) {
                $_SESSION['quiz']['index']--;
            }
        }
    }

    unset($_SESSION['feedback_id']);
    flash('ok', 'Frågan är markerad som känd och visas inte igen i nya prov.');

    if ($quiz === null || $_SESSION['quiz']['ids'] === []) {
        unset($_SESSION['quiz']);
        header('Location: index.php');
        exit;
    }

    header('Location: index.php?action=quiz');
    exit;
}

if ($action === 'start') {
    $mode = $_GET['mode'] ?? 'exam';
    if (!in_array($mode, ['exam', 'Databas', 'PHP', 'MVC'], true)) {
        $mode = 'exam';
    }
    $quiz = bygg_prov($fragor, $mode);
    if ($quiz === []) {
        flash('ok', 'Alla frågor är markerade som kända. Återställ kända för att öva igen.');
        header('Location: index.php');
        exit;
    }
    $_SESSION['quiz'] = $quiz;
    unset($_SESSION['feedback_id'], $_SESSION['utkast']);
    header('Location: index.php?action=quiz');
    exit;
}

if ($action === 'avbryt') {
    unset($_SESSION['quiz'], $_SESSION['feedback_id'], $_SESSION['utkast']);
    flash('ok', 'Provet avbröts.');
    header('Location: index.php');
    exit;
}

if ($action === 'answer') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: index.php?action=quiz');
        exit;
    }

    $quiz = $_SESSION['quiz'] ?? null;
    if ($quiz === null) {
        flash('fel', 'Det finns inget pågående prov.');
        header('Location: index.php');
        exit;
    }

    $fraga = aktuell_fraga($quiz, $fragor);
    if ($fraga === null) {
        header('Location: index.php?action=results');
        exit;
    }

    if (isset($quiz['svar'][$fraga['id']])) {
        $_SESSION['feedback_id'] = $fraga['id'];
        header('Location: index.php?action=feedback');
        exit;
    }

    if (($_POST['qid'] ?? '') !== $fraga['id']) {
        flash('fel', 'Formuläret var inaktuellt. Försök igen.');
        header('Location: index.php?action=quiz');
        exit;
    }

    $valda = $_POST['svar'] ?? [];
    $valda = is_array($valda) ? $valda : [$valda];
    $valda = array_values(array_unique(array_map('intval', $valda)));
    $valda = array_values(array_filter($valda, fn ($i) => $i >= 0 && $i < count($fraga['alternativ'])));
    $motivering = trim((string) ($_POST['motivering'] ?? ''));

    if ($valda === []) {
        $_SESSION['utkast'][$fraga['id']] = ['valda' => $valda, 'motivering' => $motivering];
        flash('fel', 'Välj minst ett svarsalternativ innan du svarar.');
        header('Location: index.php?action=quiz');
        exit;
    }

    if (ordrakning($motivering) < 3) {
        $_SESSION['utkast'][$fraga['id']] = ['valda' => $valda, 'motivering' => $motivering];
        flash('fel', 'Skriv en kort motivering med 2–4 meningar (max 50 ord).');
        header('Location: index.php?action=quiz');
        exit;
    }

    $_SESSION['quiz']['svar'][$fraga['id']] = utvardera($fraga, $valda, $motivering);
    unset($_SESSION['utkast'][$fraga['id']]);
    $_SESSION['feedback_id'] = $fraga['id'];
    header('Location: index.php?action=feedback');
    exit;
}

if ($action === 'feedback') {
    $quiz = $_SESSION['quiz'] ?? null;
    $id = $_SESSION['feedback_id'] ?? null;
    $fraga = $id !== null ? fraga_fran_id($fragor, $id) : null;
    if ($quiz === null || $fraga === null || !isset($quiz['svar'][$id])) {
        header('Location: index.php?action=quiz');
        exit;
    }

    $svar = $quiz['svar'][$id];
    $ordning = ordning_for_fraga($quiz, $fraga);
    $nummer = array_search($id, $quiz['ids'], true);
    $nummer = $nummer === false ? 1 : (int) $nummer + 1;
    $antal = count($quiz['ids']);
    $poang = $svar['poang'];
    $klass = $poang === 2 ? 'good' : ($poang === 1 ? 'mid' : 'bad');
    $rubrik = $poang === 2 ? 'Full pott!' : ($poang === 1 ? 'Halvt rätt' : 'Inte rätt den här gången');

    sidhuvud('Förklaring', 'hem');
    ?>
<section class="feedback-head <?= $klass ?>">
    <div>
        <span class="eyebrow">Fråga <?= $nummer ?> av <?= $antal ?> · <?= e($fraga['kategori']) ?></span>
        <h1><?= e($rubrik) ?></h1>
        <p>
            <?php if (!$svar['val_ratt']): ?>
                Du valde minst ett felaktigt alternativ. Enligt tentans regel ger hela uppgiften 0 poäng, även om motiveringen skulle vara bra.
            <?php elseif ($svar['mot_ratt']): ?>
                Rätt alternativ och en godkänd motivering. Precis så vill läraren se svaret.
            <?php else: ?>
                Rätt alternativ ger 1 poäng. Komplettera motiveringen med de viktigaste begreppen för att få det andra poänget.
            <?php endif; ?>
        </p>
    </div>
    <div class="points"><strong><?= $poang ?></strong><span>av 2 p</span></div>
</section>

<article class="panel question-panel">
    <h2><?= e($fraga['fraga']) ?></h2>
    <?php if (!empty($fraga['kod'])): ?>
        <pre class="code"><code><?= e($fraga['kod']) ?></code></pre>
    <?php endif; ?>

    <div class="answer-summary">
        <div>
            <span class="summary-label">Ditt svar</span>
            <strong><?= $svar['valda'] === [] ? 'Inget' : e(bokstaver_i_ordning($svar['valda'], $ordning)) ?></strong>
        </div>
        <div>
            <span class="summary-label">Rätt svar</span>
            <strong class="text-good"><?= e(bokstaver_i_ordning($svar['ratt'], $ordning)) ?></strong>
        </div>
    </div>

    <?= alternativ_feedback($fraga, $svar, $ordning) ?>
</article>

<section class="panel">
    <h2>Motivering</h2>
    <div class="mot-grid">
        <div>
            <h3>Din motivering</h3>
            <p class="quote"><?= nl2br(e($svar['motivering'])) ?></p>
            <p class="meta">
                <?= $svar['ord'] ?> ord · <?= $svar['meningar'] ?> mening<?= $svar['meningar'] === 1 ? '' : 'ar' ?> ·
                <span class="<?= $svar['mot_ratt'] ? 'text-good' : 'text-bad' ?>"><?= $svar['mot_ratt'] ? 'Godkänd' : 'Inte godkänd' ?></span>
            </p>
            <ul class="mot-feedback">
                <?php if ($svar['ord'] > 50): ?>
                    <li>Du skrev <?= $svar['ord'] ?> ord. Tentan har max 50 ord – korta ner.</li>
                <?php endif; ?>
                <?php if ($svar['ord'] < 8): ?>
                    <li>Motiveringen är för kort. Skriv en hel förklaring, inte bara ett nyckelord.</li>
                <?php endif; ?>
                <?php if ($svar['saknade'] !== []): ?>
                    <li>Försök få med: <strong><?= e(implode(', ', $svar['saknade'])) ?></strong>.</li>
                <?php endif; ?>
                <?php if ($svar['mot_ratt']): ?>
                    <li>Bra – du fick med de viktigaste begreppen och höll dig inom ordgränsen.</li>
                <?php endif; ?>
            </ul>
        </div>
        <div>
            <h3>Exempelmotivering</h3>
            <p class="quote quote-example"><?= e($fraga['motivering']) ?></p>
        </div>
    </div>
</section>

<div class="actions">
    <a class="btn btn-primary" href="index.php?action=next">
        <?= $nummer < $antal ? 'Nästa fråga' : 'Visa resultat' ?>
    </a>
    <form method="post" action="index.php?action=known" class="inline-form">
        <input type="hidden" name="qid" value="<?= e($fraga['id']) ?>">
        <button type="submit" class="btn btn-ghost">Jag kan den – visa inte igen</button>
    </form>
</div>
    <?php
    sidfot();
    exit;
}

if ($action === 'next') {
    $quiz = $_SESSION['quiz'] ?? null;
    if ($quiz === null) {
        header('Location: index.php');
        exit;
    }

    $fraga = aktuell_fraga($quiz, $fragor);
    if ($fraga === null || !isset($quiz['svar'][$fraga['id']])) {
        header('Location: index.php?action=quiz');
        exit;
    }

    unset($_SESSION['feedback_id']);
    $_SESSION['quiz']['index']++;
    if ($_SESSION['quiz']['index'] >= count($quiz['ids'])) {
        header('Location: index.php?action=results');
    } else {
        header('Location: index.php?action=quiz');
    }
    exit;
}

if ($action === 'restart') {
    $quiz = $_SESSION['quiz'] ?? null;
    if ($quiz === null) {
        header('Location: index.php');
        exit;
    }
    $_SESSION['quiz']['index'] = 0;
    $_SESSION['quiz']['svar'] = [];
    unset($_SESSION['quiz']['historik_klar'], $_SESSION['feedback_id'], $_SESSION['utkast']);
    header('Location: index.php?action=quiz');
    exit;
}

if ($action === 'retry_wrong') {
    $quiz = $_SESSION['quiz'] ?? null;
    if ($quiz === null) {
        flash('fel', 'Det finns inget resultat att repetera.');
        header('Location: index.php');
        exit;
    }

    $kanda = kanda_ids($fragor);
    $ids = [];
    foreach ($quiz['ids'] as $id) {
        if (($quiz['svar'][$id]['poang'] ?? 0) < 2 && !in_array($id, $kanda, true)) {
            $ids[] = $id;
        }
    }

    if ($ids === []) {
        flash('ok', 'Det finns inga fel kvar att repetera – de är antingen godkända eller markerade som kända.');
        header('Location: index.php?action=results');
        exit;
    }

    $_SESSION['quiz'] = [
        'mode' => 'retry',
        'titel' => 'Repetera fel',
        'ids' => $ids,
        'ordning' => skapa_ordning($fragor, $ids),
        'index' => 0,
        'svar' => [],
        'skapad' => time(),
    ];
    unset($_SESSION['feedback_id'], $_SESSION['utkast']);
    header('Location: index.php?action=quiz');
    exit;
}

if ($action === 'results') {
    $quiz = $_SESSION['quiz'] ?? null;
    if ($quiz === null) {
        header('Location: index.php');
        exit;
    }

    $summering = summering($quiz, $fragor);
    if ($summering['besvarade'] < count($quiz['ids'])) {
        $forsta = forsta_obesvarade($quiz);
        if ($forsta !== null) {
            $_SESSION['quiz']['index'] = $forsta;
        }
        header('Location: index.php?action=quiz');
        exit;
    }

    $arOvningsprov = $quiz['mode'] !== 'retry' && count($quiz['ids']) < 10;
    $betygNamn = $quiz['mode'] === 'retry' ? 'Repetition' : ($arOvningsprov ? 'Övningsprov' : $summering['betyg']['namn']);
    if (empty($_SESSION['quiz']['historik_klar'])) {
        spara_historik($summering, $quiz, $betygNamn);
        $_SESSION['quiz']['historik_klar'] = true;
    }

    $quiz = $_SESSION['quiz'];
    $summering = summering($quiz, $fragor);
    $visaBetyg = $quiz['mode'] !== 'retry' && !$arOvningsprov;
    $procent = $summering['max'] > 0 ? (int) round($summering['poang'] / $summering['max'] * 100) : 0;
    $ringFarg = $arOvningsprov ? '#58a6ff' : ($summering['betyg']['klass'] === 'vg' ? '#3fb950' : ($summering['betyg']['klass'] === 'g' ? '#58a6ff' : '#f85149'));
    $antalFel = 0;
    foreach ($quiz['ids'] as $id) {
        if (($quiz['svar'][$id]['poang'] ?? 0) < 2) {
            $antalFel++;
        }
    }

    sidhuvud('Resultat', 'hem');
    ?>
<section class="result-hero">
    <div class="score-ring" style="--pct: <?= $procent ?>%; --ring: <?= $ringFarg ?>;">
        <div>
            <strong><?= $summering['poang'] ?></strong>
            <span>av <?= $summering['max'] ?> p</span>
        </div>
    </div>
    <div>
        <span class="eyebrow"><?= e($quiz['titel']) ?></span>
        <h1>
            <?php if ($quiz['mode'] === 'retry'): ?>
                Repetition klar
            <?php elseif ($arOvningsprov): ?>
                Övningsprov klart
            <?php else: ?>
                <?= e($summering['betyg']['namn']) ?>
            <?php endif; ?>
        </h1>
        <p>
            <?php if ($visaBetyg && $summering['betyg']['klass'] === 'vg'): ?>
                Stark insats. Du har grepp om både databasen, PHP och MVC.
            <?php elseif ($visaBetyg && $summering['betyg']['klass'] === 'g'): ?>
                Godkänt. Repetera frågorna du missade för att sikta på VG.
            <?php elseif ($visaBetyg): ?>
                Inte godkänt än. Läs fuskbladet och kör om – särskilt frågorna där du tappade poäng.
            <?php elseif ($arOvningsprov): ?>
                Du har övat på <?= count($quiz['ids']) ?> kvarvarande frågor. Markera fler som kända när du är säker.
            <?php else: ?>
                Du har repeterat <?= count($quiz['ids']) ?> frågor. Kör gärna ett nytt blandat prov när du känner dig redo.
            <?php endif; ?>
        </p>
        <?php if ($visaBetyg): ?>
            <p class="grade-scale">G: 12 p · VG: 16 p · Max: 20 p</p>
        <?php endif; ?>
    </div>
</section>

<section class="grid-2">
    <div class="panel">
        <h2>Poäng per del</h2>
        <table class="table">
            <thead>
            <tr><th>Del</th><th>Poäng</th><th>Max</th></tr>
            </thead>
            <tbody>
            <?php foreach ($summering['perKategori'] as $kategori => $rad): ?>
                <?php if ($rad['max'] === 0) { continue; } ?>
                <tr>
                    <td><?= e($kategori) ?></td>
                    <td><?= $rad['poang'] ?></td>
                    <td><?= $rad['max'] ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="panel">
        <h2>Nästa steg</h2>
        <p class="muted">Du har <?= $antalFel ?> fråga<?= $antalFel === 1 ? '' : 'or' ?> kvar att finslipa.</p>
        <div class="stack">
            <?php if ($antalFel > 0): ?>
                <a class="btn btn-primary" href="index.php?action=retry_wrong">Repetera fel (<?= $antalFel ?>)</a>
            <?php endif; ?>
            <a class="btn" href="index.php?action=restart">Gör om samma prov</a>
            <a class="btn" href="index.php?action=start&mode=exam">Nytt blandat prov</a>
            <a class="btn btn-ghost" href="index.php">Till startsidan</a>
        </div>
    </div>
</section>

<section class="panel">
    <h2>Alla frågor</h2>
    <div class="review-list">
        <?php foreach ($quiz['ids'] as $index => $id): ?>
            <?php
            $fraga = fraga_fran_id($fragor, $id);
            $svar = $quiz['svar'][$id] ?? null;
            if ($fraga === null || $svar === null) {
                continue;
            }
            $p = $svar['poang'];
            $ordning = ordning_for_fraga($quiz, $fraga);
            $pillKlass = $p === 2 ? 'pill-ok' : ($p === 1 ? 'pill-mid' : 'pill-bad');
            ?>
            <details class="review">
                <summary>
                    <span class="review-num"><?= $index + 1 ?></span>
                    <span class="review-q"><?= e($fraga['fraga']) ?></span>
                    <span class="pill <?= $pillKlass ?>"><?= $p ?> / 2 p</span>
                </summary>
                <div class="review-body">
                    <div class="answer-summary">
                        <div>
                            <span class="summary-label">Ditt svar</span>
                            <strong><?= e(bokstaver_i_ordning($svar['valda'], $ordning)) ?></strong>
                        </div>
                        <div>
                            <span class="summary-label">Rätt svar</span>
                            <strong class="text-good"><?= e(bokstaver_i_ordning($svar['ratt'], $ordning)) ?></strong>
                        </div>
                    </div>
                    <?= alternativ_feedback($fraga, $svar, $ordning) ?>
                    <div class="mot-grid">
                        <div>
                            <h3>Din motivering</h3>
                            <p class="quote"><?= nl2br(e($svar['motivering'])) ?></p>
                            <p class="meta"><?= $svar['ord'] ?> ord · <?= $svar['meningar'] ?> mening<?= $svar['meningar'] === 1 ? '' : 'ar' ?> · <?= $svar['mot_ratt'] ? 'Godkänd' : 'Inte godkänd' ?></p>
                        </div>
                        <div>
                            <h3>Exempelmotivering</h3>
                            <p class="quote quote-example"><?= e($fraga['motivering']) ?></p>
                        </div>
                    </div>
                </div>
            </details>
        <?php endforeach; ?>
    </div>
</section>
    <?php
    sidfot();
    exit;
}

if ($action === 'quiz') {
    $quiz = $_SESSION['quiz'] ?? null;
    if ($quiz === null) {
        flash('fel', 'Starta ett prov först.');
        header('Location: index.php');
        exit;
    }

    if ($quiz['index'] >= count($quiz['ids'])) {
        $forsta = forsta_obesvarade($quiz);
        if ($forsta !== null) {
            $_SESSION['quiz']['index'] = $forsta;
            header('Location: index.php?action=quiz');
        } else {
            header('Location: index.php?action=results');
        }
        exit;
    }

    $fraga = aktuell_fraga($quiz, $fragor);
    if ($fraga === null) {
        flash('fel', 'Frågan kunde inte hittas.');
        header('Location: index.php');
        exit;
    }

    if (isset($quiz['svar'][$fraga['id']])) {
        $_SESSION['feedback_id'] = $fraga['id'];
        header('Location: index.php?action=feedback');
        exit;
    }

    $nummer = $quiz['index'] + 1;
    $antal = count($quiz['ids']);
    $procent = (int) round(($nummer - 1) / $antal * 100);
    $utkast = $_SESSION['utkast'][$fraga['id']] ?? ['valda' => [], 'motivering' => ''];

    sidhuvud('Fråga ' . $nummer, 'hem');
    ?>
<section class="quiz-head">
    <div>
        <a class="back" href="index.php?action=avbryt">Avbryt prov</a>
        <h1><?= e($quiz['titel']) ?></h1>
    </div>
    <div class="quiz-count">Fråga <strong><?= $nummer ?></strong> / <?= $antal ?></div>
</section>

<div class="progress" aria-hidden="true"><span style="width: <?= $procent ?>%"></span></div>

<div class="badges">
    <span class="badge badge-kat"><?= e($fraga['kategori']) ?></span>
    <span class="badge <?= $fraga['del'] === 1 ? 'badge-del1' : 'badge-del2' ?>">
        <?= $fraga['del'] === 1 ? 'Del 1 · ett rätt svar' : 'Del 2 · ett eller flera rätta svar' ?>
    </span>
</div>

<article class="panel question-panel">
    <h2><?= e($fraga['fraga']) ?></h2>
    <?php if (!empty($fraga['kod'])): ?>
        <pre class="code"><code><?= e($fraga['kod']) ?></code></pre>
    <?php endif; ?>

    <form method="post" action="index.php?action=answer" class="quiz-form">
        <input type="hidden" name="qid" value="<?= e($fraga['id']) ?>">
        <div class="option-list">
            <?php $ordning = ordning_for_fraga($quiz, $fraga); ?>
            <?php foreach ($ordning as $position => $index): ?>
                <?php $alternativ = $fraga['alternativ'][$index]; ?>
                <?php $vald = in_array($index, $utkast['valda'], true); ?>
                <label class="option <?= $vald ? 'selected' : '' ?>">
                    <input
                        type="<?= $fraga['del'] === 1 ? 'radio' : 'checkbox' ?>"
                        name="svar[]"
                        value="<?= $index ?>"
                        <?= $vald ? 'checked' : '' ?>
                        <?= $fraga['del'] === 1 ? 'required' : '' ?>
                    >
                    <span class="option-letter"><?= chr(65 + (int) $position) ?></span>
                    <span class="option-text"><?= e($alternativ['text']) ?></span>
                </label>
            <?php endforeach; ?>
        </div>

        <div class="motivation-block">
            <label for="motivering">Din motivering <span class="muted">(2–4 meningar, max 50 ord)</span></label>
            <textarea id="motivering" name="motivering" rows="5" maxlength="700" placeholder="Förklara varför du valde just detta svar. Använd kursens begrepp."><?= e($utkast['motivering']) ?></textarea>
            <div class="mot-meta">
                <span id="ordraknare">0 ord</span>
                <span>Max 50 ord</span>
            </div>
        </div>

        <div class="actions">
            <button type="submit" class="btn btn-primary btn-wide">Svara och se förklaring</button>
            <button type="submit" class="btn btn-ghost" formaction="index.php?action=known" formnovalidate>Jag kan den – visa inte igen</button>
        </div>
    </form>
</article>
    <?php
    sidfot();
    exit;
}

$quiz = $_SESSION['quiz'] ?? null;
$pagar = $quiz !== null && count($quiz['svar']) < count($quiz['ids']);
$historik = $_SESSION['historik'] ?? [];
$kanda = kanda_ids($fragor);
$kvar = count($fragor) - count($kanda);

sidhuvud('Plugga inför salstentamen', 'hem');
?>
<section class="hero">
    <span class="eyebrow">IT429G HT26 · Databaskonstruktion</span>
    <h1>Plugga smart inför salstentamen</h1>
    <p class="lead">
        46 tentastil-liknande frågor i databas, PHP och .NET MVC. Välj svar, skriv en kort motivering
        och få en förklaring till varje alternativ – även när du svarar fel. Frågor och svarsalternativ blandas.
        Markera frågor du redan kan – då hoppas de över i nästa prov.
    </p>
    <div class="hero-actions">
        <a class="btn btn-primary" href="index.php?action=start&mode=exam">Starta blandat prov</a>
        <a class="btn" href="fuskblad.php">Öppna fuskbladet</a>
        <a class="btn btn-ghost" href="memorering.php">19 mallar med kod</a>
        <a class="btn btn-ghost" href="tentaguide.php#plan">2-dagarsplan</a>
    </div>
</section>

<div class="callout">
    <strong>Bara 2 dagar kvar?</strong>
    <a href="tentaguide.php#plan">Följ 2-dagarsplanen</a> - förstå helheten, träna på formatet
    och rätta dina fel. Det är den snabbaste vägen till godkänt.
</div>

<section class="known-status">
    <div>
        <strong><?= count($kanda) ?> kända</strong>
        <span class="muted"> · <?= $kvar ?> kvar att öva på</span>
        <p class="meta">Kryssa "Jag kan den" i provet så hoppas frågan över nästa gång.</p>
    </div>
    <?php if ($kanda !== []): ?>
        <a class="btn btn-small" href="index.php?action=reset_kanda" onclick="return confirm('Visa alla frågor igen?')">Återställ kända</a>
    <?php endif; ?>
</section>

<section class="stat-grid">
    <div class="stat"><strong>10</strong><span>frågor per prov</span></div>
    <div class="stat"><strong>2 p</strong><span>per fråga</span></div>
    <div class="stat"><strong>20 p</strong><span>maxpoäng</span></div>
    <div class="stat"><strong>12 / 16</strong><span>G / VG</span></div>
</section>

<?php if ($pagar): ?>
    <div class="notice">
        <div>
            <strong>Pågående prov:</strong> <?= e($quiz['titel']) ?> – fråga <?= count($quiz['svar']) + 1 ?> av <?= count($quiz['ids']) ?>.
        </div>
        <a class="btn btn-small" href="index.php?action=quiz">Fortsätt</a>
    </div>
<?php endif; ?>

<section class="mode-grid">
    <a class="mode-card primary" href="index.php?action=start&mode=exam">
        <span class="mode-tag">Prov</span>
            <h2>Salstentamen</h2>
            <p>7 enkelval och 3 flerval, blandat från alla tre delar. Svarsalternativen slumpas och kända frågor hoppas över.</p>
        <span class="mode-cta">Starta prov</span>
    </a>
    <a class="mode-card" href="index.php?action=start&mode=Databas">
        <span class="mode-tag">Del 1</span>
        <h2>Databas</h2>
        <p>7 enkelval + 3 flerval, bara databas: design, constraints, index och procedurer.</p>
        <span class="mode-cta">Träna databas</span>
    </a>
    <a class="mode-card" href="index.php?action=start&mode=PHP">
        <span class="mode-tag">Del 2</span>
        <h2>PHP</h2>
        <p>7 enkelval + 3 flerval, bara PHP: formulär, PDO, tabeller och CRUD.</p>
        <span class="mode-cta">Träna PHP</span>
    </a>
    <a class="mode-card" href="index.php?action=start&mode=MVC">
        <span class="mode-tag">Del 3</span>
        <h2>.NET MVC</h2>
        <p>7 enkelval + 3 flerval, bara MVC: controller, model, view och routing.</p>
        <span class="mode-cta">Träna MVC</span>
    </a>
</section>

<section class="grid-2">
    <div class="panel">
        <h2>Så funkar tentan</h2>
        <ul class="checklist">
            <li><strong>Del 1:</strong> 7 frågor med exakt ett rätt svar.</li>
            <li><strong>Del 2:</strong> 3 frågor där ett, flera eller alla alternativ kan vara rätt.</li>
            <li><strong>Motivering:</strong> 2–4 meningar, max 50 ord.</li>
            <li><strong>Poäng:</strong> 1 p för rätt alternativ + 1 p för korrekt motivering.</li>
            <li><strong>Viktigt:</strong> minst ett felaktigt alternativ ger 0 p för hela frågan.</li>
            <li><strong>Betyg:</strong> G vid 12 p, VG vid 16 p. Max är 20 p.</li>
        </ul>
    </div>
    <div class="panel">
        <h2>Så använder du appen</h2>
        <p>Börja med ett blandat prov för att se var du står. Träna sedan på delen du missade och avsluta med att repetera felen.</p>
        <p class="muted">Efter varje svar får du en förklaring till alla fem alternativ, en exempelmotivering och en lista över begrepp du bör få med.</p>
        <a class="btn" href="fuskblad.php">Läs fuskbladet först</a>
    </div>
</section>

<?php if ($historik !== []): ?>
    <section class="panel">
        <h2>Senaste resultat</h2>
        <div class="table-wrap">
            <table class="table">
                <thead>
                <tr><th>Datum</th><th>Prov</th><th>Poäng</th><th>Betyg</th></tr>
                </thead>
                <tbody>
                <?php foreach ($historik as $rad): ?>
                    <tr>
                        <td><?= e($rad['datum']) ?></td>
                        <td><?= e($rad['titel']) ?></td>
                        <td><?= (int) $rad['poang'] ?> / <?= (int) $rad['max'] ?></td>
                        <td><?= e($rad['betyg']) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
<?php endif; ?>
<?php
sidfot();
