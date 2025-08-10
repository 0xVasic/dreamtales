<?php
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$prompt = $input['prompt'] ?? '';

if (!$prompt) {
    echo json_encode(['error' => 'Molimo unesite prompt.']);
    exit;
}

$apiKey = ' '; // API ključ za Google Gemini

$url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent";

$data = [
    "contents" => [
        [
            "parts" => [
                ["text" => $prompt]
            ]
        ]
    ],
    "generationConfig" => [
        "thinkingConfig" => [
            "thinkingBudget" => 0
        ]
    ]
];

$options = [
    'http' => [
        'header'  => "Content-Type: application/json\r\n" .
                     "x-goog-api-key: {$apiKey}\r\n",
        'method'  => 'POST',
        'content' => json_encode($data),
        'ignore_errors' => true
    ]
];

$context  = stream_context_create($options);
$result = file_get_contents($url, false, $context);

if ($result === FALSE) {
    echo json_encode(['error' => 'Greška pri pozivu API-ja']);
    exit;
}

echo $result;
?>
