<?php

$logFile = 'logs/shared_stories.log';
$stories = [];

if (file_exists($logFile)) {
    $lines = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $entry = json_decode($line, true);
        if ($entry) {
            $stories[] = $entry;
        }
    }
}

$stories = array_reverse($stories);
usort($stories, function($a, $b) {
    $ratingA = isset($a['rating']) && is_numeric($a['rating']) ? (int)$a['rating'] : 0;
    $ratingB = isset($b['rating']) && is_numeric($b['rating']) ? (int)$b['rating'] : 0;
    return $ratingB <=> $ratingA;  // Opadajuće
});
// PAGINACIJA
$perPage = 5;
$totalStories = count($stories);
$totalPages = ceil($totalStories / $perPage);
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
if ($page > $totalPages) $page = $totalPages;

$offset = ($page - 1) * $perPage;
$storiesPage = array_slice($stories, $offset, $perPage);

function formatBold($text) { 
    return preg_replace('/\*\*(.+?)\*\*/u', '<strong>$1</strong>', htmlspecialchars($text));
}

?>
<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>DreamTales - Podeljene priče</title>
    <link href="https://fonts.googleapis.com/css2?family=Comfortaa&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="styles/share.css" />
</head>
<body>
    <h1>Podeljene priče sa DreamTales</h1>
    
    <div class="back-link-container">
        <a href="index.html" class="back-link" aria-label="Nazad na početnu stranicu">&larr; Nazad na početnu</a>
    </div>

    <?php if ($totalStories === 0): ?>
        <p class="no-stories">Još uvek nema podeljenih priča.</p>
    <?php else: ?>
        <?php foreach ($storiesPage as $s): ?>
            <article class="story" role="article" aria-label="Priča za dete <?php echo htmlspecialchars($s['ime'] ?? ''); ?>">
                <div class="story-header">Ime deteta: <?php echo htmlspecialchars($s['ime'] ?? ''); ?></div>
                <div class="story-meta" aria-label="Detalji priče">
                    <div>Vrsta: <strong><?php echo htmlspecialchars($s['vrsta'] ?? ''); ?></strong></div>
                    <div>Ton: <strong><?php echo htmlspecialchars($s['ton'] ?? ''); ?></strong></div>
                    <div>Uzrast: <strong><?php echo htmlspecialchars($s['uzrast'] ?? ''); ?></strong></div>
                    <div>Tema: <strong><?php echo htmlspecialchars($s['tema'] ?? ''); ?></strong></div>
                    <div>Deljeno: <strong><?php echo htmlspecialchars($s['timestamp'] ?? ''); ?></strong></div>
                </div>
                <section class="story-text" aria-label="Tekst priče">
                    <h3>Priča</h3>
                    <?php echo formatBold($s['story'] ?? ''); ?>
                </section>
                <section class="story-summary" aria-label="Poruka za roditelje">
                    <h3>Poruka za roditelje</h3>
                    <?php echo formatBold($s['summaryForParents'] ?? ''); ?>
                </section>
                <div class="story-rating" aria-label="Ocena priče">
                    Ocena: <?php echo htmlspecialchars($s['rating'] ?? 'Nije ocenjena'); ?>
                </div>
            </article>
        <?php endforeach; ?>

        <?php if ($totalPages > 1): ?>
            <nav class="pagination" aria-label="Navigacija paginacije">
                <?php if ($page > 1): ?>
                    <a href="?page=1" aria-label="Prva strana">&laquo;</a>
                    <a href="?page=<?php echo $page - 1; ?>" aria-label="Prethodna strana">&lsaquo;</a>
                <?php endif; ?>

                <?php
                $startPage = max(1, $page - 2);
                $endPage = min($totalPages, $page + 2);
                for ($p = $startPage; $p <= $endPage; $p++): 
                ?>
                    <?php if ($p == $page): ?>
                        <span class="current-page" aria-current="page"><?php echo $p; ?></span>
                    <?php else: ?>
                        <a href="?page=<?php echo $p; ?>"><?php echo $p; ?></a>
                    <?php endif; ?>
                <?php endfor; ?>

                <?php if ($page < $totalPages): ?>
                    <a href="?page=<?php echo $page + 1; ?>" aria-label="Sledeća strana">&rsaquo;</a>
                    <a href="?page=<?php echo $totalPages; ?>" aria-label="Poslednja strana">&raquo;</a>
                <?php endif; ?>
            </nav>
        <?php endif; ?>
    <?php endif; ?>
</body>
</html>
