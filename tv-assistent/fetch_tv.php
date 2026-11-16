<?php

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
header('Access-Control-Allow-Origin: *');

date_default_timezone_set('Europe/Stockholm');

const API_BASE = 'https://web-api.tv.nu';
const CACHE_TTL = 600;
const CURl_TIMEOUT = 12;
const DAY_START_HOUR = 6;

$CHANNELS = [
    'svt1' => ['name' => 'SVT1', 'aliases' => ['svt1', 'svt 1', 'svt ett', 'ettan', 'kanal 1']],
    'svt2' => ['name' => 'SVT2', 'aliases' => ['svt2', 'svt 2', 'svt två', 'tvåan', 'kanal 2']],
    'tv3' => ['name' => 'TV3', 'aliases' => ['tv3', 'tv 3', 'trean', 'kanal 3']],
    'tv4' => ['name' => 'TV4', 'aliases' => ['tv4', 'tv 4', 'fyran', 'kanal 4']],
    'kanal-5' => ['name' => 'Kanal 5', 'aliases' => ['kanal5', 'kanal 5', 'femman', 'tv5', 'tv 5']],
    'tv6' => ['name' => 'TV6', 'aliases' => ['tv6', 'tv 6', 'sexan', 'kanal 6']],
    'sjuan' => ['name' => 'Sjuan', 'aliases' => ['sjuan', 'kanal 7', 'kanal7', 'tv7', 'tv 7']],
    'tv8' => ['name' => 'TV8', 'aliases' => ['tv8', 'tv 8', 'åttan', 'kanal 8']],
    'kanal-9' => ['name' => 'Kanal 9', 'aliases' => ['kanal9', 'kanal 9', 'nian', 'tv9', 'tv 9']],
    'tv10' => ['name' => 'TV10', 'aliases' => ['tv10', 'tv 10', 'tian', 'kanal 10']],
    'kanal-11' => ['name' => 'Kanal 11', 'aliases' => ['kanal11', 'kanal 11', 'elvan', 'tv11', 'tv 11']],
    'tv12' => ['name' => 'TV12', 'aliases' => ['tv12', 'tv 12', 'tolvan', 'kanal 12']],
    'viaplay-sport' => ['name' => 'Viaplay Sport', 'aliases' => ['viaplay sport', 'viaplaysport', 'viaplay', 'viaplay sport 1']],
    'eurosport-1' => ['name' => 'Eurosport 1', 'aliases' => ['eurosport 1', 'eurosport1', 'eurosport']],
    'kunskapskanalen' => ['name' => 'Kunskapskanalen', 'aliases' => ['kunskapskanalen', 'kunskaps kanalen']],
    'svt24' => ['name' => 'SVT24', 'aliases' => ['svt24', 'svt 24', 'svt tjugofyra']],
    'svt-barn' => ['name' => 'SVT Barn', 'aliases' => ['svt barn', 'svtbarn', 'barnkanalen', 'barn kanalen']],
    'tlc' => ['name' => 'TLC', 'aliases' => ['tlc']],
    'koket' => ['name' => 'Köket', 'aliases' => ['köket', 'koket', 'kökskanalen']],
];

$MONTHS = [
    1 => 'januari', 'februari', 'mars', 'april', 'maj', 'juni',
    'juli', 'augusti', 'september', 'oktober', 'november', 'december',
];

function respond(array $payload, int $status = 200): void
{
    http_response_code($status);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function cacheDir(): string
{
    $dir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'tv_assistent_cache';
    if (!is_dir($dir)) {
        @mkdir($dir, 0777, true);
    }
    return $dir;
}

function curlOptions(string $url): array
{
    return [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_CONNECTTIMEOUT => 5,
        CURLOPT_TIMEOUT => 10,
        CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0 Safari/537.36 TV-Assistent/2.0',
        CURLOPT_ENCODING => '',
        CURLOPT_HTTPHEADER => [
            'Accept: application/json',
            'Accept-Language: sv-SE,sv;q=0.9,en;q=0.5',
        ],
    ];
}

function httpGetJson(string $url): ?array
{
    for ($attempt = 0; $attempt < 2; $attempt += 1) {
        $ch = curl_init();
        curl_setopt_array($ch, curlOptions($url));
        $body = curl_exec($ch);
        $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if (is_string($body) && $status === 200) {
            $data = json_decode($body, true);
            if (!is_array($data) || (int) ($data['meta']['status'] ?? 0) !== 200) {
                return null;
            }

            return $data;
        }

        if ($status !== 0 && $status < 500) {
            return null;
        }

        if ($attempt === 0) {
            usleep(300000);
        }
    }

    return null;
}

function parseScheduleResponse(string $body, string $slug): ?array
{
    $data = json_decode($body, true);
    if (!is_array($data) || (int) ($data['meta']['status'] ?? 0) !== 200 || !isset($data['data'])) {
        return null;
    }

    $channel = $data['data'];
    return [
        'slug' => $slug,
        'name' => (string) ($channel['name'] ?? $slug),
        'logo' => $channel['themedLogo']['light']['url'] ?? null,
        'broadcasts' => is_array($channel['broadcasts'] ?? null) ? $channel['broadcasts'] : [],
    ];
}

function scheduleCacheFile(string $slug, string $date): string
{
    return cacheDir() . DIRECTORY_SEPARATOR . 'sched_' . $slug . '_' . $date . '.json';
}

function readScheduleCache(string $slug, string $date): ?array
{
    $file = scheduleCacheFile($slug, $date);
    if (!is_file($file) || (time() - filemtime($file)) > CACHE_TTL) {
        return null;
    }

    $cached = json_decode((string) file_get_contents($file), true);
    if (!is_array($cached) || !isset($cached['broadcasts'])) {
        return null;
    }

    return $cached;
}

function writeScheduleCache(string $slug, string $date, array $payload): void
{
    $file = scheduleCacheFile($slug, $date);
    @file_put_contents($file, json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), LOCK_EX);

    if (random_int(1, 40) === 1) {
        foreach (glob(cacheDir() . DIRECTORY_SEPARATOR . 'sched_*.json') ?: [] as $old) {
            if ((time() - filemtime($old)) > 86400) {
                @unlink($old);
            }
        }
    }
}

function getSchedule(string $slug, string $date): ?array
{
    $cached = readScheduleCache($slug, $date);
    if ($cached !== null) {
        return $cached;
    }

    $url = API_BASE . '/channel/' . rawurlencode($slug) . '/schedule?date=' . rawurlencode($date);
    $data = httpGetJson($url);
    if ($data === null) {
        return null;
    }

    $body = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    $payload = parseScheduleResponse($body, $slug);
    if ($payload === null) {
        return null;
    }

    writeScheduleCache($slug, $date, $payload);
    return $payload;
}

function getSchedules(array $slugs, string $date): array
{
    $schedules = [];
    $missing = [];

    foreach ($slugs as $slug) {
        $cached = readScheduleCache($slug, $date);
        if ($cached !== null) {
            $schedules[$slug] = $cached;
        } else {
            $missing[] = $slug;
        }
    }

    if ($missing === []) {
        return $schedules;
    }

    $multi = curl_multi_init();
    $handles = [];
    $urls = [];

    foreach ($missing as $slug) {
        $url = API_BASE . '/channel/' . rawurlencode($slug) . '/schedule?date=' . rawurlencode($date);
        $urls[$slug] = $url;
        $ch = curl_init();
        curl_setopt_array($ch, curlOptions($url));
        curl_multi_add_handle($multi, $ch);
        $handles[$slug] = $ch;
    }

    $started = microtime(true);
    do {
        $status = curl_multi_exec($multi, $running);
        if ($running > 0) {
            curl_multi_select($multi, 1.0);
        }
        if ((microtime(true) - $started) > CURl_TIMEOUT) {
            break;
        }
    } while ($running > 0 && $status === CURLM_OK);

    foreach ($handles as $slug => $ch) {
        $body = curl_multi_getcontent($ch);
        $httpStatus = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($httpStatus === 200 && is_string($body) && $body !== '') {
            $payload = parseScheduleResponse($body, $slug);
            if ($payload !== null) {
                $schedules[$slug] = $payload;
                writeScheduleCache($slug, $date, $payload);
            }
        }

        curl_multi_remove_handle($multi, $ch);
        curl_close($ch);
    }

    curl_multi_close($multi);
    return $schedules;
}

function validDate(string $date): bool
{
    if (!preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $date, $m)) {
        return false;
    }
    return checkdate((int) $m[2], (int) $m[3], (int) $m[1]);
}

function normalizeChannelKey(string $value): string
{
    $value = mb_strtolower(trim($value), 'UTF-8');
    $value = str_replace(['-', '_'], ' ', $value);
    return trim(preg_replace('/\s+/', ' ', $value) ?? $value);
}

function channelFromToken(string $token, array $channels): ?string
{
    $key = normalizeChannelKey($token);
    if ($key === '') {
        return null;
    }

    if (isset($channels[$key])) {
        return $key;
    }

    foreach ($channels as $slug => $info) {
        foreach ($info['aliases'] as $alias) {
            if (normalizeChannelKey($alias) === $key) {
                return $slug;
            }
        }
    }

    return null;
}

function resolveChannels(?string $raw, array $channels): array
{
    if ($raw === null || trim($raw) === '') {
        return array_keys($channels);
    }

    $resolved = [];
    foreach (explode(',', $raw) as $token) {
        $slug = channelFromToken($token, $channels);
        if ($slug !== null && !in_array($slug, $resolved, true)) {
            $resolved[] = $slug;
        }
    }

    return $resolved;
}

function flattenSchedule(array $schedule): array
{
    $items = [];

    foreach ($schedule['broadcasts'] as $broadcast) {
        $wrapper = $broadcast['broadcast'] ?? null;
        if (!is_array($wrapper) || !isset($wrapper['startTime'], $wrapper['endTime'])) {
            continue;
        }

        $title = trim((string) ($broadcast['title'] ?? ''));
        $intermission = !empty($broadcast['isIntermission']) || $title === 'Sändningsuppehåll';

        $items[] = [
            'channel' => (string) $schedule['name'],
            'channelSlug' => (string) $schedule['slug'],
            'logo' => $schedule['logo'],
            'title' => $title !== '' ? $title : ($intermission ? 'Sändningsuppehåll' : 'Okänt program'),
            'description' => trim((string) ($broadcast['description'] ?? '')),
            'startTime' => (int) $wrapper['startTime'],
            'endTime' => (int) $wrapper['endTime'],
            'isLive' => !empty($broadcast['isLive']),
            'isRerun' => !empty($broadcast['isRerun']),
            'isIntermission' => $intermission,
            'genre' => (string) ($broadcast['genres'][0]['name'] ?? ''),
            'year' => $broadcast['year'] ?? null,
        ];
    }

    return $items;
}

function resultFromItem(array $item): array
{
    $start = intdiv($item['startTime'], 1000);
    $end = intdiv($item['endTime'], 1000);

    return [
        'channel' => $item['channel'],
        'channelSlug' => $item['channelSlug'],
        'logo' => $item['logo'],
        'title' => $item['title'],
        'description' => $item['description'],
        'date' => date('Y-m-d', $start),
        'start' => date('H:i', $start),
        'end' => date('H:i', $end),
        'startTime' => $item['startTime'],
        'endTime' => $item['endTime'],
        'isLive' => $item['isLive'],
        'isRerun' => $item['isRerun'],
        'isIntermission' => $item['isIntermission'],
        'genre' => $item['genre'],
        'year' => $item['year'],
    ];
}

function findBroadcastAt(array $items, int $timestampMs): ?array
{
    foreach ($items as $item) {
        if ($timestampMs >= $item['startTime'] && $timestampMs < $item['endTime']) {
            return $item;
        }
    }
    return null;
}

function findNextBroadcast(array $items, int $timestampMs): ?array
{
    $best = null;
    foreach ($items as $item) {
        if ($item['startTime'] >= $timestampMs && ($best === null || $item['startTime'] < $best['startTime'])) {
            $best = $item;
        }
    }
    return $best;
}

function dayLabel(string $date, string $today): string
{
    $diff = (int) ((strtotime($date . ' 12:00:00') - strtotime($today . ' 12:00:00')) / 86400);

    if ($diff === 0) {
        return 'Idag';
    }
    if ($diff === 1) {
        return 'Imorgon';
    }
    if ($diff === -1) {
        return 'Igår';
    }

    global $MONTHS;
    $month = (int) date('n', strtotime($date));
    return 'Den ' . (int) date('j', strtotime($date)) . ' ' . $MONTHS[$month];
}

function dayWordForTimestamp(int $timestampMs, string $today): string
{
    $date = date('Y-m-d', intdiv($timestampMs, 1000));
    $diff = (int) ((strtotime($date . ' 12:00:00') - strtotime($today . ' 12:00:00')) / 86400);

    if ($diff === 0) {
        return 'idag';
    }
    if ($diff === 1) {
        return 'imorgon';
    }
    if ($diff === -1) {
        return 'igår';
    }

    global $MONTHS;
    return 'den ' . (int) date('j', strtotime($date)) . ' ' . $MONTHS[(int) date('n', strtotime($date))];
}

function normalizeSearchText(string $value): string
{
    $value = mb_strtolower($value, 'UTF-8');
    $value = strtr($value, [
        'å' => 'a', 'ä' => 'a', 'ö' => 'o', 'é' => 'e', 'è' => 'e',
        'ü' => 'y', 'á' => 'a', 'à' => 'a', 'ø' => 'o', 'æ' => 'a',
    ]);
    $value = preg_replace('/[^\p{L}\p{N}\s]+/u', ' ', $value) ?? $value;
    return trim(preg_replace('/\s+/', ' ', $value) ?? $value);
}

function titleScore(string $title, string $query): int
{
    $titleNorm = normalizeSearchText($title);
    $queryNorm = normalizeSearchText($query);

    if ($titleNorm === '' || $queryNorm === '') {
        return 0;
    }
    if ($titleNorm === $queryNorm) {
        return 100;
    }
    if (str_starts_with($titleNorm, $queryNorm)) {
        return 85;
    }
    if (str_contains($titleNorm, $queryNorm)) {
        return 75;
    }
    if (str_contains($queryNorm, $titleNorm)) {
        return 65;
    }

    $queryWords = array_filter(explode(' ', $queryNorm));
    $titleWords = array_filter(explode(' ', $titleNorm));
    $common = array_intersect($queryWords, $titleWords);

    if ($common !== []) {
        return (int) (30 + 20 * count($common) / max(1, count($queryWords)));
    }

    return 0;
}

function searchPrograms(string $query, array $onlyChannels, string $fromDate, string $today): array
{
    $url = API_BASE . '/search?query=' . rawurlencode($query);
    $data = httpGetJson($url);

    if ($data === null || !is_array($data['data'] ?? null)) {
        return ['results' => [], 'bestTitle' => null];
    }

    $fromTimestamp = strtotime($fromDate . ' 00:00:00') * 1000;
    $now = (int) round(microtime(true) * 1000);
    $minimum = max($fromTimestamp, $now - 3600 * 1000);
    $items = [];
    $bestTitle = null;
    $bestScore = 0;

    foreach ($data['data'] as $entry) {
        $main = $entry['mainItem'] ?? [];
        $title = (string) ($main['title'] ?? '');
        if ($title === '') {
            continue;
        }

        $score = titleScore($title, $query);
        if ($score < 55) {
            continue;
        }

        if ($score > $bestScore) {
            $bestScore = $score;
            $bestTitle = $title;
        }

        foreach (($entry['extraItems'] ?? []) as $extra) {
            if (!isset($extra['startTime'], $extra['endTime'])) {
                continue;
            }
            if ((int) $extra['startTime'] < $minimum) {
                continue;
            }

            $channel = $extra['channel'] ?? [];
            $slug = (string) ($channel['slug'] ?? '');
            if ($onlyChannels !== [] && !in_array($slug, $onlyChannels, true)) {
                continue;
            }

            $items[] = [
                'channel' => (string) ($channel['name'] ?? ''),
                'channelSlug' => $slug,
                'logo' => $channel['themedLogo']['light']['url'] ?? null,
                'title' => (string) ($extra['title'] ?? $title),
                'description' => (string) ($main['description'] ?? ''),
                'startTime' => (int) $extra['startTime'],
                'endTime' => (int) $extra['endTime'],
                'isLive' => false,
                'isRerun' => false,
                'isIntermission' => false,
                'genre' => '',
                'year' => $main['year'] ?? null,
            ];
        }
    }

    $unique = [];
    $seen = [];
    foreach ($items as $item) {
        $key = $item['channelSlug'] . '|' . $item['startTime'] . '|' . $item['title'];
        if (isset($seen[$key])) {
            continue;
        }
        $seen[$key] = true;
        $unique[] = $item;
    }

    usort($unique, static fn(array $a, array $b): int => $a['startTime'] <=> $b['startTime']);

    return ['results' => array_slice($unique, 0, 15), 'bestTitle' => $bestTitle];
}

function formatChannelList(array $channels): string
{
    $names = array_map(static fn(array $info): string => $info['name'], array_values($channels));
    $last = array_pop($names);
    return implode(', ', $names) . ' och ' . $last;
}

$action = strtolower(trim((string) ($_GET['action'] ?? '')));
$programQuery = trim((string) ($_GET['program'] ?? ''));
if ($action === '') {
    $action = $programQuery !== '' ? 'search' : 'schedule';
}

if ($action === 'channels') {
    respond([
        'success' => true,
        'action' => 'channels',
        'message' => 'Jag kan kolla ' . formatChannelList($CHANNELS) . '.',
        'channels' => array_map(
            static fn(string $slug, array $info): array => ['slug' => $slug, 'name' => $info['name']],
            array_keys($CHANNELS),
            array_values($CHANNELS)
        ),
    ]);
}

$alreadyResolved = resolveChannels(isset($_GET['channel']) ? (string) $_GET['channel'] : null, $CHANNELS);
$allChannelsRequested = !isset($_GET['channel']) || trim((string) $_GET['channel']) === '';

if (!$allChannelsRequested && $alreadyResolved === []) {
    respond([
        'success' => false,
        'action' => $action,
        'message' => 'Jag kunde inte hitta den kanalen. Säg till exempel SVT1, TV4 eller Kanal 5.',
        'results' => [],
    ]);
}

$today = date('Y-m-d');
$requestedDate = isset($_GET['date']) ? trim((string) $_GET['date']) : $today;
if (!validDate($requestedDate)) {
    $requestedDate = $today;
}

if ($action === 'search') {
    if ($programQuery === '') {
        respond([
            'success' => false,
            'action' => 'search',
            'message' => 'Vilket program vill du att jag söker efter?',
            'results' => [],
        ]);
    }

    $channelsForSearch = $allChannelsRequested ? [] : $alreadyResolved;
    $search = searchPrograms($programQuery, $channelsForSearch, $requestedDate, $today);
    $rawResults = $search['results'];

    if ($rawResults === []) {
        $message = $search['bestTitle'] !== null
            ? 'Jag hittade programmet ' . $search['bestTitle'] . ', men hittar inga kommande sändningar just nu.'
            : 'Jag hittade tyvärr inget program som matchar ' . $programQuery . '. Prova att säga programnamnet igen.';

        respond([
            'success' => false,
            'action' => 'search',
            'message' => $message,
            'results' => [],
        ]);
    }

    $results = array_map('resultFromItem', $rawResults);
    $first = $results[0];
    $message = $first['title'] . ' sänds på ' . $first['channel'] . ' '
        . dayWordForTimestamp($first['startTime'], $today) . ' klockan ' . $first['start'] . '.';

    if (count($results) > 1) {
        $parts = [];
        foreach (array_slice($results, 1, 3) as $result) {
            $parts[] = $result['channel'] . ' ' . dayWordForTimestamp($result['startTime'], $today) . ' klockan ' . $result['start'];
        }
        $message .= ' Jag hittade också ' . implode(', ', $parts) . '.';
    }

    respond([
        'success' => true,
        'action' => 'search',
        'date' => $requestedDate,
        'query' => $programQuery,
        'message' => $message,
        'results' => $results,
    ]);
}

$timeRaw = trim((string) ($_GET['time'] ?? ''));
$time = '';
$timeIsNow = false;

if ($timeRaw === 'now' || $timeRaw === 'nu') {
    $timeIsNow = true;
} elseif (preg_match('/^(\d{1,2}):(\d{2})$/', $timeRaw, $m)) {
    $hour = (int) $m[1];
    $minute = (int) $m[2];
    if ($hour >= 0 && $hour <= 23 && $minute >= 0 && $minute <= 59) {
        $time = sprintf('%02d:%02d', $hour, $minute);
    }
}

$explicitTime = $time !== '';
if (!$explicitTime && !$timeIsNow && !$allChannelsRequested) {
    $time = '';
}

$scheduleDate = $requestedDate;
if ($timeIsNow) {
    if ((int) date('G') < DAY_START_HOUR) {
        $scheduleDate = date('Y-m-d', strtotime($requestedDate . ' -1 day'));
    }
    $queryTimestamp = (int) round(microtime(true) * 1000);
} elseif ($explicitTime) {
    $hour = (int) substr($time, 0, 2);
    if ($hour < DAY_START_HOUR) {
        $scheduleDate = date('Y-m-d', strtotime($requestedDate . ' -1 day'));
    }
    $queryTimestamp = strtotime($requestedDate . ' ' . $time . ':00') * 1000;
} else {
    $queryTimestamp = 0;
}

if (!$explicitTime && !$timeIsNow) {
    if (!$allChannelsRequested) {
        $slugs = $alreadyResolved;
    } else {
        $slugs = array_keys($CHANNELS);
    }

    $schedules = getSchedules($slugs, $scheduleDate);
    if ($schedules === []) {
        respond([
            'success' => false,
            'action' => 'schedule',
            'message' => 'Jag kunde inte nå tv.nu just nu. Försök igen om en liten stund.',
            'results' => [],
        ], 502);
    }

    $results = [];
    foreach ($slugs as $slug) {
        if (!isset($schedules[$slug])) {
            continue;
        }

        $items = flattenSchedule($schedules[$slug]);
        $from = strtotime($requestedDate . ' 18:00:00') * 1000;
        $to = strtotime($requestedDate . ' 23:59:59') * 1000;
        $evening = array_values(array_filter($items, static function (array $item) use ($from, $to): bool {
            return !$item['isIntermission'] && $item['startTime'] >= $from && $item['startTime'] <= $to;
        }));

        if ($evening === []) {
            $evening = array_values(array_filter($items, static fn(array $item): bool => !$item['isIntermission']));
        }

        foreach (array_slice($evening, 0, 8) as $item) {
            $results[] = resultFromItem($item);
        }
    }

    if ($results === []) {
        respond([
            'success' => false,
            'action' => 'schedule',
            'message' => 'Jag hittade inga program på ' . dayLabel($requestedDate, $today) . '.',
            'results' => [],
        ]);
    }

    $channelName = $allChannelsRequested ? 'de största kanalerna' : $results[0]['channel'];
    $parts = [];
    foreach (array_slice($results, 0, 5) as $result) {
        $parts[] = $result['start'] . ' ' . $result['title'];
    }
    $message = $allChannelsRequested
        ? 'Här är ett urval ' . strtolower(dayLabel($requestedDate, $today)) . ': ' . implode(', ', $parts) . '.'
        : dayLabel($requestedDate, $today) . ' på ' . $channelName . ': ' . implode(', ', $parts) . '.';

    if (count($results) > 5) {
        $message .= ' Med flera.';
    }

    respond([
        'success' => true,
        'action' => 'schedule',
        'date' => $requestedDate,
        'scheduleDate' => $scheduleDate,
        'time' => null,
        'message' => $message,
        'results' => $results,
    ]);
}

if ($timeIsNow) {
    $time = date('H:i');
}

$slugs = $alreadyResolved;
$schedules = getSchedules($slugs, $scheduleDate);

if ($schedules === []) {
    respond([
        'success' => false,
        'action' => 'schedule',
        'message' => 'Jag kunde inte nå tv.nu just nu. Försök igen om en liten stund.',
        'results' => [],
    ], 502);
}

$found = [];
$nextByChannel = [];
$timeLabel = $timeIsNow ? 'Just nu' : dayLabel($requestedDate, $today) . ' klockan ' . $time;

foreach ($slugs as $slug) {
    if (!isset($schedules[$slug])) {
        continue;
    }

    $items = flattenSchedule($schedules[$slug]);
    $current = findBroadcastAt($items, $queryTimestamp);

    if ($current !== null && !$current['isIntermission']) {
        $found[$slug] = $current;
        continue;
    }

    $next = findNextBroadcast($items, $queryTimestamp);
    if ($next !== null) {
        $nextByChannel[$slug] = $next;
    }
}

$results = [];
foreach ($found as $item) {
    $results[] = resultFromItem($item);
}

if ($results === []) {
    $parts = [];
    foreach (array_slice($nextByChannel, 0, 3, true) as $item) {
        $result = resultFromItem($item);
        $parts[] = $result['channel'] . ' klockan ' . $result['start'] . ' ' . $result['title'];
    }

    $message = $parts === []
        ? 'Jag hittade inget program ' . strtolower($timeLabel) . '.'
        : 'Det är inget program ' . strtolower($timeLabel) . '. Nästa: ' . implode(', ', $parts) . '.';

    respond([
        'success' => false,
        'action' => 'schedule',
        'date' => $requestedDate,
        'scheduleDate' => $scheduleDate,
        'time' => $time,
        'message' => $message,
        'results' => [],
    ]);
}

if (count($results) === 1) {
    $result = $results[0];
    $startedEarlier = $result['start'] !== $time && !$timeIsNow;

    if ($timeIsNow) {
        $message = 'Just nu på ' . $result['channel'] . ' går ' . $result['title'] . '.';
    } elseif ($startedEarlier) {
        $message = $timeLabel . ' på ' . $result['channel'] . ' pågår ' . $result['title']
            . ', som startade ' . $result['start'] . '.';
    } else {
        $message = $timeLabel . ' på ' . $result['channel'] . ' går ' . $result['title'] . '.';
    }

    if ($result['isLive']) {
        $message .= ' Det är en direktsändning.';
    }
} else {
    $parts = [];
    foreach (array_slice($results, 0, 7) as $result) {
        $parts[] = $result['channel'] . ' – ' . $result['title'];
    }
    $speechPrefix = $timeIsNow ? 'Just nu går: ' : $timeLabel . ' går: ';
    $message = $speechPrefix . implode(', ', $parts) . '.';
    if (count($results) > 7) {
        $message .= ' Med flera.';
    }
}

respond([
    'success' => true,
    'action' => 'schedule',
    'date' => $requestedDate,
    'scheduleDate' => $scheduleDate,
    'time' => $time,
    'message' => $message,
    'results' => $results,
]);
