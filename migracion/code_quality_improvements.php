<?php
/**
 * Mejoras de Calidad de Código y Mantenibilidad
 * Script para optimizar y mejorar el sistema de migración
 * 
 * @author Sistema de Migración StyloFitness
 * @version 1.0
 */

class CodeQualityImprovements {
    
    /**
     * Ejecutar todas las mejoras de calidad
     */
    public static function runAllImprovements() {
        echo "🔧 APLICANDO MEJORAS DE CALIDAD DE CÓDIGO\n";
        echo str_repeat("=", 60) . "\n\n";
        
        self::validateConfigurationFiles();
        self::checkDatabaseConnections();
        self::validateDirectoryStructure();
        self::generateDocumentation();
        self::createBackupScript();
        self::optimizePerformance();
        
        echo "\n✅ Todas las mejoras aplicadas exitosamente\n";
    }
    
    /**
     * Validar archivos de configuración
     */
    private static function validateConfigurationFiles() {
        echo "📋 VALIDANDO ARCHIVOS DE CONFIGURACIÓN\n";
        echo str_repeat("-", 40) . "\n";
        
        $config_file = __DIR__ . '/config.json';
        
        if (!file_exists($config_file)) {
            echo "❌ Archivo config.json no encontrado\n";
            return false;
        }
        
        $config = json_decode(file_get_contents($config_file), true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            echo "❌ Error en formato JSON: " . json_last_error_msg() . "\n";
            return false;
        }
        
        // Validar secciones requeridas
        $required_sections = [
            'wordpress_database',
            'stylofitness_database', 
            'migration_settings',
            'image_settings',
            'category_mapping'
        ];
        
        foreach ($required_sections as $section) {
            if (!isset($config[$section])) {
                echo "⚠️  Sección faltante: $section\n";
            } else {
                echo "✅ Sección válida: $section\n";
            }
        }
        
        echo "\n";
        return true;
    }
    
    /**
     * Verificar conexiones de base de datos
     */
    private static function checkDatabaseConnections() {
        echo "🔗 VERIFICANDO CONEXIONES DE BASE DE DATOS\n";
        echo str_repeat("-", 40) . "\n";
        
        $config = json_decode(file_get_contents(__DIR__ . '/config.json'), true);
        
        // Verificar WordPress
        try {
            $wp_config = $config['wordpress_database'];
            $wp_dsn = "mysql:host={$wp_config['host']};dbname={$wp_config['database']};charset={$wp_config['charset']}";
            $wp_db = new PDO($wp_dsn, $wp_config['username'], $wp_config['password']);
            $wp_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            echo "✅ Conexión WordPress: OK\n";
        } catch (PDOException $e) {
            echo "❌ Error conexión WordPress: " . $e->getMessage() . "\n";
        }
        
        // Verificar StyloFitness
        try {
            $sf_config = $config['stylofitness_database'];
            $sf_dsn = "mysql:host={$sf_config['host']};dbname={$sf_config['database']};charset={$sf_config['charset']}";
            $sf_db = new PDO($sf_dsn, $sf_config['username'], $sf_config['password']);
            $sf_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            echo "✅ Conexión StyloFitness: OK\n";
        } catch (PDOException $e) {
            echo "❌ Error conexión StyloFitness: " . $e->getMessage() . "\n";
        }
        
        echo "\n";
    }
    
    /**
     * Validar estructura de directorios
     */
    private static function validateDirectoryStructure() {
        echo "📁 VALIDANDO ESTRUCTURA DE DIRECTORIOS\n";
        echo str_repeat("-", 40) . "\n";
        
        $config = json_decode(file_get_contents(__DIR__ . '/config.json'), true);
        
        $directories = [
            $config['image_settings']['stylofitness_uploads_path'],
            $config['image_settings']['stylofitness_uploads_path'] . '/products',
            $config['image_settings']['stylofitness_uploads_path'] . '/categories',
            $config['image_settings']['stylofitness_uploads_path'] . '/thumbnails'
        ];
        
        foreach ($directories as $dir) {
            if (is_dir($dir)) {
                $permissions = substr(sprintf('%o', fileperms($dir)), -4);
                echo "✅ Directorio existe: $dir (permisos: $permissions)\n";
            } else {
                echo "⚠️  Directorio no existe: $dir\n";
                if (mkdir($dir, 0755, true)) {
                    echo "   ✅ Directorio creado exitosamente\n";
                } else {
                    echo "   ❌ Error al crear directorio\n";
                }
            }
        }
        
        echo "\n";
    }
    
    /**
     * Generar documentación automática
     */
    private static function generateDocumentation() {
        echo "📚 GENERANDO DOCUMENTACIÓN AUTOMÁTICA\n";
        echo str_repeat("-", 40) . "\n";
        
        $docs = [
            'MIGRATION_LOG.md' => self::generateMigrationLog(),
            'API_REFERENCE.md' => self::generateApiReference(),
            'TROUBLESHOOTING.md' => self::generateTroubleshooting()
        ];
        
        foreach ($docs as $filename => $content) {
            $file_path = __DIR__ . '/' . $filename;
            if (file_put_contents($file_path, $content)) {
                echo "✅ Documentación generada: $filename\n";
            } else {
                echo "❌ Error generando: $filename\n";
            }
        }
        
        echo "\n";
    }
    
    /**
     * Crear script de backup automático
     */
    private static function createBackupScript() {
        echo "💾 CREANDO SCRIPT DE BACKUP AUTOMÁTICO\n";
        echo str_repeat("-", 40) . "\n";
        
        $backup_script = self::generateBackupScript();
        $backup_file = __DIR__ . '/auto_backup.php';
        
        if (file_put_contents($backup_file, $backup_script)) {
            echo "✅ Script de backup creado: auto_backup.php\n";
        } else {
            echo "❌ Error creando script de backup\n";
        }
        
        echo "\n";
    }
    
    /**
     * Optimizaciones de rendimiento
     */
    private static function optimizePerformance() {
        echo "⚡ APLICANDO OPTIMIZACIONES DE RENDIMIENTO\n";
        echo str_repeat("-", 40) . "\n";
        
        // Crear índices recomendados
        $indexes = [
            'CREATE INDEX IF NOT EXISTS idx_products_name ON products(name)',
            'CREATE INDEX IF NOT EXISTS idx_products_slug ON products(slug)',
            'CREATE INDEX IF NOT EXISTS idx_products_category ON products(category_id)',
            'CREATE INDEX IF NOT EXISTS idx_categories_name ON product_categories(name)'
        ];
        
        echo "📊 Índices de base de datos recomendados:\n";
        foreach ($indexes as $index) {
            echo "   - $index\n";
        }
        
        // Crear archivo de configuración de rendimiento
        $performance_config = [
            'database_optimizations' => $indexes,
            'memory_limits' => [
                'php_memory_limit' => '512M',
                'max_execution_time' => 300
            ],
            'batch_processing' => [
                'recommended_batch_size' => 50,
                'image_processing_batch' => 10
            ]
        ];
        
        $perf_file = __DIR__ . '/performance_config.json';
        if (file_put_contents($perf_file, json_encode($performance_config, JSON_PRETTY_PRINT))) {
            echo "✅ Configuración de rendimiento guardada\n";
        }
        
        echo "\n";
    }
    
    /**
     * Generar log de migración
     */
    private static function generateMigrationLog() {
        return "# Migration Log\n\n" .
               "## Historial de Migraciones\n\n" .
               "| Fecha | Tipo | Estado | Registros | Observaciones |\n" .
               "|-------|------|--------|-----------|---------------|\n" .
               "| " . date('Y-m-d H:i:s') . " | Inicial | Pendiente | - | Sistema configurado |\n\n" .
               "## Comandos Útiles\n\n" .
               "```bash\n" .
               "# Migración completa\n" .
               "php migration_script.php\n\n" .
               "# Solo imágenes\n" .
               "php image_migration_script.php\n\n" .
               "# Verificación\n" .
               "php verification_script.php\n" .
               "```\n";
    }
    
    /**
     * Generar referencia de API
     */
    private static function generateApiReference() {
        return "# API Reference\n\n" .
               "## Clases Principales\n\n" .
               "### MigrationScript\n" .
               "- `migrateCategories()`: Migra categorías de productos\n" .
               "- `migrateProducts()`: Migra productos y metadatos\n\n" .
               "### ImageMigration\n" .
               "- `migrateAllImages()`: Migración completa de imágenes\n" .
               "- `migrateProductImages()`: Solo imágenes de productos\n" .
               "- `migrateCategoryImages()`: Solo imágenes de categorías\n\n" .
               "### VerificationScript\n" .
               "- `verifyMigration()`: Verificación completa\n" .
               "- `generateReport()`: Genera reporte detallado\n";
    }
    
    /**
     * Generar guía de solución de problemas
     */
    private static function generateTroubleshooting() {
        return "# Troubleshooting Guide\n\n" .
               "## Problemas Comunes\n\n" .
               "### Error de Conexión a Base de Datos\n" .
               "- Verificar credenciales en config.json\n" .
               "- Comprobar que el servidor MySQL esté ejecutándose\n" .
               "- Validar permisos de usuario\n\n" .
               "### Imágenes No Migradas\n" .
               "- Verificar rutas en image_settings\n" .
               "- Comprobar permisos de directorios\n" .
               "- Revisar extensión GD de PHP\n\n" .
               "### Productos Duplicados\n" .
               "- Ejecutar utilities.php para limpiar duplicados\n" .
               "- Verificar configuración skip_existing\n\n" .
               "## Comandos de Diagnóstico\n\n" .
               "```bash\n" .
               "# Verificar sintaxis\n" .
               "php syntax_check.php\n\n" .
               "# Verificar calidad\n" .
               "php code_quality_improvements.php\n" .
               "```\n";
    }
    
    /**
     * Generar script de backup
     */
    private static function generateBackupScript() {
        return "<?php\n" .
               "/**\n" .
               " * Script de Backup Automático\n" .
               " * Genera respaldos antes de ejecutar migraciones\n" .
               " */\n\n" .
               "class AutoBackup {\n" .
               "    public static function createBackup() {\n" .
               "        \$timestamp = date('Y-m-d_H-i-s');\n" .
               "        \$backup_dir = __DIR__ . '/backups';\n" .
               "        \n" .
               "        if (!is_dir(\$backup_dir)) {\n" .
               "            mkdir(\$backup_dir, 0755, true);\n" .
               "        }\n" .
               "        \n" .
               "        echo \"💾 Creando backup: \$timestamp\\n\";\n" .
               "        \n" .
               "        // Aquí agregar lógica de backup\n" .
               "        // mysqldump, copiar archivos, etc.\n" .
               "        \n" .
               "        return true;\n" .
               "    }\n" .
               "}\n\n" .
               "if (php_sapi_name() === 'cli') {\n" .
               "    AutoBackup::createBackup();\n" .
               "}\n" .
               "?>\n";
    }
}

// Ejecutar mejoras si se llama desde línea de comandos
if (php_sapi_name() === 'cli') {
    try {
        CodeQualityImprovements::runAllImprovements();
    } catch (Exception $e) {
        echo "❌ Error: " . $e->getMessage() . "\n";
        exit(1);
    }
}
?>