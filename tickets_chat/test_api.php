<?php
// Test script - verifica si la API funciona
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'xampp_project/config/database.php';

echo "=== TEST API ===\n\n";

// 1. Probar conexión a BD
try {
    $db = getDB();
    echo "✓ Conexión a BD exitosa\n";
} catch (Exception $e) {
    echo "✗ Error de conexión: " . $e->getMessage() . "\n";
    exit;
}

// 2. Listar usuarios
try {
    $stmt = $db->prepare("SELECT id, name FROM users WHERE active = 1");
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "✓ " . count($users) . " usuarios activos encontrados:\n";
    foreach ($users as $user) {
        $initials = '';
        $words = explode(' ', $user['name']);
        foreach ($words as $word) {
            if (!empty($word)) {
                $initials .= strtoupper($word[0]);
            }
        }
        $initials = substr($initials, 0, 2);
        echo "  - {$user['name']} ({$initials})\n";
    }
} catch (Exception $e) {
    echo "✗ Error listando usuarios: " . $e->getMessage() . "\n";
}

// 3. Test login con primeras iniciales
if (!empty($users)) {
    $testUser = $users[0];
    $words = explode(' ', $testUser['name']);
    $testInitials = '';
    foreach ($words as $word) {
        if (!empty($word)) {
            $testInitials .= strtoupper($word[0]);
        }
    }
    $testInitials = substr($testInitials, 0, 2);
    
    echo "\n✓ Usuario de prueba: {$testUser['name']} ({$testInitials})\n";
}

echo "\n=== FIN TEST ===\n";
?>
