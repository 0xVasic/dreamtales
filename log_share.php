<?php

header('Content-Type: application/json; charset=utf-8');

$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['error' => 'Nema podataka']);
    exit;
}

$ime = $data['ime'] ?? '';
$vrsta = $data['vrsta'] ?? '';
$ton = $data['ton'] ?? '';
$uzrast = $data['uzrast'] ?? '';
$tema = $data['tema'] ?? '';
$prica = $data['prica'] ?? '';
$rezimeRoditelji = $data['rezimeRoditelji'] ?? '';
$ocena = $data['ocena'] ?? '';

if (!$prica) {
    echo json_encode(['error' => 'Priča je obavezna']);
    exit;
}

$logEntry = [
    'timestamp' => date('Y-m-d H:i:s'),
    'ime' => $ime,
    'vrsta' => $vrsta,
    'ton' => $ton,
    'uzrast' => $uzrast,
    'tema' => $tema,
    'story' => $prica,
    'summaryForParents' => $rezimeRoditelji,
    'rating' => $ocena
];

$logFile = 'logs/shared_stories.log';
$logLine = json_encode($logEntry, JSON_UNESCAPED_UNICODE) . "\n";
file_put_contents($logFile, $logLine, FILE_APPEND | LOCK_EX);

echo json_encode(['success' => true]);
