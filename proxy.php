
<?php
// === CONFIGURATION ===
$targetUrl = "https://autre-site.com/api/receive"; // URL cible
$secretToken = "MON_TOKEN_SECRET"; // Token pour sécuriser l'accès
$logFile = __DIR__ . "/log.txt"; // Fichier de log

// === SECURITE : Vérifier le token ===
if (!isset($_GET['token']) || $_GET['token'] !== $secretToken) {
    http_response_code(403);
    exit("Accès refusé : token invalide");
}

// === Vérifier que la requête est POST ===
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit("Méthode non autorisée");
}

// === Récupérer le JSON brut envoyé par Kizeo ===
$data = file_get_contents('php://input');

// === Log des données reçues ===
file_put_contents($logFile, date('Y-m-d H:i:s') . " - " . $data . PHP_EOL, FILE_APPEND);

// === Transférer les données vers l'URL cible ===
$ch = curl_init($targetUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Content-Length: ' . strlen($data)
]);

$response = curl_exec($ch);
$error = curl_error($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// === Réponse ===
if ($error) {
    http_response_code(500);
    echo "Erreur lors du transfert : " . $error;
} else {
    http_response_code($httpCode);
    echo "Transfert réussi. Réponse de la cible :<br><pre>" . htmlspecialchars($response) . "</pre>";
}
?>
