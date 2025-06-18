<?php
/**
 * Exportar resultados de pruebas en formato JSON
 */

session_start();

header('Content-Type: application/json');
header('Content-Disposition: attachment; filename="stylofitness_test_results_' . date('Y-m-d_H-i-s') . '.json"');

$results = $_SESSION['test_results'] ?? [];

$exportData = [
    'timestamp' => date('Y-m-d H:i:s'),
    'system_info' => [
        'php_version' => PHP_VERSION,
        'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
        'document_root' => $_SERVER['DOCUMENT_ROOT'] ?? '',
        'script_path' => __DIR__
    ],
    'test_summary' => [
        'total_tests' => count($results),
        'successful_tests' => count(array_filter($results, function($r) { return $r['success']; })),
        'failed_tests' => count(array_filter($results, function($r) { return !$r['success']; })),
        'success_rate' => count($results) > 0 ? round((count(array_filter($results, function($r) { return $r['success']; })) / count($results)) * 100, 2) : 0
    ],
    'test_results' => $results
];

echo json_encode($exportData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>