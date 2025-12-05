
<?php
// Afficher uniquement ce qui est reçu, sans transfert
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo "Aucune donnée reçue.";
    exit;
}

$data = file_get_contents('php://input');
$json = json_decode($data, true);

header('Content-Type: text/html; charset=utf-8');
echo "<h1>Données reçues :</h1><pre>";
print_r($json);
echo "</pre>";
