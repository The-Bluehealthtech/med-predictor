<?php

// Test script pour l'API des patients cliniques
$url = 'http://localhost:8000/api/clinical/patients';

// Test sans filtres
echo "=== Test API Patients Cliniques ===\n";
echo "URL: $url\n\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json',
    'Content-Type: application/json'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: $httpCode\n";
echo "Response:\n";
echo $response . "\n\n";

// Test avec filtres
$urlWithFilters = $url . '?status=confirmed&type=consultation';
echo "=== Test avec filtres ===\n";
echo "URL: $urlWithFilters\n\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $urlWithFilters);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json',
    'Content-Type: application/json'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: $httpCode\n";
echo "Response:\n";
echo $response . "\n";

?>
