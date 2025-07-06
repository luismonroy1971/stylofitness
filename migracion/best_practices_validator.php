<?php
/**
 * Validador de Mejores Prácticas
 * Verifica que el sistema de migración siga las mejores prácticas de desarrollo
 * 
 * @author Sistema de Migración StyloFitness
 * @version 1.0
 */

class BestPracticesValidator {
    
    private $issues = [];
    private $recommendations = [];
    private $score = 0;
    private $max_score = 0;
    
    /**
     * Ejecutar validación completa
     */
    public function runValidation() {
        echo "🔍 VALIDADOR DE MEJORES PRÁCTICAS\n";
        echo str_repeat("=", 60) . "\n\n";
        
        $this->validateCodeStructure();
        $this->validateSecurity();
        $this->validatePerformance();
        $this->validateMaintainability();
        $this->validateDocumentation();
        $this->validateErrorHandling();
        
        $this->generateFinalReport();
    }
    
    /**
     * Validar estructura del código
     */
    private function validateCodeStructure() {
        echo "🏗️  VALIDANDO ESTRUCTURA DEL CÓDIGO\n";
        echo str_repeat("-", 40) . "\n";
        
        $files = [
            'migration_script.php' => 'Script principal de migración',
            'image_migration_script.php' => 'Script de migración de imágenes',
            'verification_script.php' => 'Script de verificación',
            'utilities.php' => 'Utilidades de mantenimiento',
            'config.json' => 'Archivo de configuración'
        ];
        
        foreach ($files as $file => $description) {
            if (file_exists(__DIR__ . '/' . $file)) {
                echo "✅ $file - $description\n";
                $this->addScore(1);
            } else {
                echo "❌ $file - FALTANTE\n";
                $this->addIssue("Archivo faltante: $file");
            }
        }
        
        // Verificar separación de responsabilidades
        $this->validateSeparationOfConcerns();
        
        echo "\n";
    }
    
    /**
     * Validar aspectos de seguridad
     */
    private function validateSecurity() {
        echo "🔒 VALIDANDO SEGURIDAD\n";
        echo str_repeat("-", 40) . "\n";
        
        // Verificar uso de prepared statements
        $files_to_check = ['migration_script.php', 'image_migration_script.php', 'verification_script.php'];
        
        foreach ($files_to_check as $file) {
            if (file_exists(__DIR__ . '/' . $file)) {
                $content = file_get_contents(__DIR__ . '/' . $file);
                
                // Verificar prepared statements
                if (strpos($content, '->prepare(') !== false) {
                    echo "✅ $file - Usa prepared statements\n";
                    $this->addScore(1);
                } else {
                    echo "⚠️  $file - No usa prepared statements\n";
                    $this->addRecommendation("Usar prepared statements en $file");
                }
                
                // Verificar manejo de errores PDO
                if (strpos($content, 'PDO::ERRMODE_EXCEPTION') !== false) {
                    echo "✅ $file - Manejo de errores PDO configurado\n";
                    $this->addScore(1);
                } else {
                    echo "⚠️  $file - Configurar manejo de errores PDO\n";
                    $this->addRecommendation("Configurar PDO::ERRMODE_EXCEPTION en $file");
                }
                
                // Verificar validación de entrada
                if (strpos($content, 'filter_var') !== false || strpos($content, 'is_numeric') !== false) {
                    echo "✅ $file - Incluye validación de entrada\n";
                    $this->addScore(1);
                } else {
                    echo "⚠️  $file - Agregar más validación de entrada\n";
                    $this->addRecommendation("Agregar validación de entrada en $file");
                }
            }
        }
        
        // Verificar configuración segura
        if (file_exists(__DIR__ . '/config.json')) {
            $config = json_decode(file_get_contents(__DIR__ . '/config.json'), true);
            
            // Verificar que no hay credenciales hardcodeadas
            if (isset($config['wordpress_database']['password']) && 
                $config['wordpress_database']['password'] !== 'your_password') {
                echo "⚠️  Credenciales configuradas en config.json\n";
                $this->addRecommendation("Considerar usar variables de entorno para credenciales");
            }
        }
        
        echo "\n";
    }
    
    /**
     * Validar rendimiento
     */
    private function validatePerformance() {
        echo "⚡ VALIDANDO RENDIMIENTO\n";
        echo str_repeat("-", 40) . "\n";
        
        // Verificar configuración de batch processing
        if (file_exists(__DIR__ . '/config.json')) {
            $config = json_decode(file_get_contents(__DIR__ . '/config.json'), true);
            
            if (isset($config['migration_settings']['batch_size'])) {
                $batch_size = $config['migration_settings']['batch_size'];
                if ($batch_size > 0 && $batch_size <= 100) {
                    echo "✅ Batch size configurado apropiadamente: $batch_size\n";
                    $this->addScore(1);
                } else {
                    echo "⚠️  Batch size puede ser optimizado: $batch_size\n";
                    $this->addRecommendation("Ajustar batch_size entre 50-100 para mejor rendimiento");
                }
            }
        }
        
        // Verificar uso de transacciones
        $files_to_check = ['migration_script.php', 'utilities.php'];
        
        foreach ($files_to_check as $file) {
            if (file_exists(__DIR__ . '/' . $file)) {
                $content = file_get_contents(__DIR__ . '/' . $file);
                
                if (strpos($content, 'beginTransaction') !== false) {
                    echo "✅ $file - Usa transacciones\n";
                    $this->addScore(1);
                } else {
                    echo "⚠️  $file - Considerar uso de transacciones\n";
                    $this->addRecommendation("Implementar transacciones en $file");
                }
            }
        }
        
        // Verificar optimizaciones de memoria
        echo "📊 Recomendaciones de memoria:\n";
        echo "   - Configurar memory_limit a 512M o superior\n";
        echo "   - Usar unset() para liberar variables grandes\n";
        echo "   - Procesar imágenes en lotes pequeños\n";
        
        echo "\n";
    }
    
    /**
     * Validar mantenibilidad
     */
    private function validateMaintainability() {
        echo "🔧 VALIDANDO MANTENIBILIDAD\n";
        echo str_repeat("-", 40) . "\n";
        
        // Verificar documentación en código
        $files_to_check = ['migration_script.php', 'image_migration_script.php'];
        
        foreach ($files_to_check as $file) {
            if (file_exists(__DIR__ . '/' . $file)) {
                $content = file_get_contents(__DIR__ . '/' . $file);
                
                // Contar comentarios de documentación
                $doc_comments = substr_count($content, '/**');
                $total_functions = substr_count($content, 'function ');
                
                if ($doc_comments >= $total_functions * 0.8) {
                    echo "✅ $file - Bien documentado ($doc_comments/$total_functions funciones)\n";
                    $this->addScore(1);
                } else {
                    echo "⚠️  $file - Necesita más documentación ($doc_comments/$total_functions funciones)\n";
                    $this->addRecommendation("Agregar documentación PHPDoc en $file");
                }
            }
        }
        
        // Verificar logging
        $logging_found = false;
        foreach ($files_to_check as $file) {
            if (file_exists(__DIR__ . '/' . $file)) {
                $content = file_get_contents(__DIR__ . '/' . $file);
                if (strpos($content, 'echo') !== false || strpos($content, 'error_log') !== false) {
                    $logging_found = true;
                    break;
                }
            }
        }
        
        if ($logging_found) {
            echo "✅ Sistema de logging implementado\n";
            $this->addScore(1);
        } else {
            echo "⚠️  Implementar sistema de logging\n";
            $this->addRecommendation("Agregar logging detallado para debugging");
        }
        
        echo "\n";
    }
    
    /**
     * Validar documentación
     */
    private function validateDocumentation() {
        echo "📚 VALIDANDO DOCUMENTACIÓN\n";
        echo str_repeat("-", 40) . "\n";
        
        $docs = [
            'README.md' => 'Documentación principal',
            'MIGRATION_LOG.md' => 'Log de migraciones',
            'API_REFERENCE.md' => 'Referencia de API',
            'TROUBLESHOOTING.md' => 'Guía de solución de problemas'
        ];
        
        foreach ($docs as $file => $description) {
            if (file_exists(__DIR__ . '/' . $file)) {
                echo "✅ $file - $description\n";
                $this->addScore(1);
            } else {
                echo "⚠️  $file - $description (recomendado)\n";
                $this->addRecommendation("Crear $file");
            }
        }
        
        echo "\n";
    }
    
    /**
     * Validar manejo de errores
     */
    private function validateErrorHandling() {
        echo "🚨 VALIDANDO MANEJO DE ERRORES\n";
        echo str_repeat("-", 40) . "\n";
        
        $files_to_check = ['migration_script.php', 'image_migration_script.php'];
        
        foreach ($files_to_check as $file) {
            if (file_exists(__DIR__ . '/' . $file)) {
                $content = file_get_contents(__DIR__ . '/' . $file);
                
                // Verificar try-catch
                if (strpos($content, 'try {') !== false && strpos($content, 'catch') !== false) {
                    echo "✅ $file - Implementa try-catch\n";
                    $this->addScore(1);
                } else {
                    echo "⚠️  $file - Agregar manejo de excepciones\n";
                    $this->addRecommendation("Implementar try-catch en $file");
                }
                
                // Verificar validación de archivos
                if (strpos($content, 'file_exists') !== false) {
                    echo "✅ $file - Valida existencia de archivos\n";
                    $this->addScore(1);
                } else {
                    echo "⚠️  $file - Agregar validación de archivos\n";
                    $this->addRecommendation("Validar existencia de archivos en $file");
                }
            }
        }
        
        echo "\n";
    }
    
    /**
     * Validar separación de responsabilidades
     */
    private function validateSeparationOfConcerns() {
        // Verificar que cada script tiene una responsabilidad clara
        $scripts = [
            'migration_script.php' => ['class MigrationScript', 'migración de datos'],
            'image_migration_script.php' => ['class ImageMigration', 'migración de imágenes'],
            'verification_script.php' => ['class VerificationScript', 'verificación'],
            'utilities.php' => ['class Utilities', 'utilidades']
        ];
        
        foreach ($scripts as $file => $info) {
            if (file_exists(__DIR__ . '/' . $file)) {
                $content = file_get_contents(__DIR__ . '/' . $file);
                if (strpos($content, $info[0]) !== false) {
                    echo "✅ $file - Responsabilidad clara: {$info[1]}\n";
                    $this->addScore(1);
                } else {
                    echo "⚠️  $file - Verificar estructura de clase\n";
                }
            }
        }
    }
    
    /**
     * Generar reporte final
     */
    private function generateFinalReport() {
        echo "📊 REPORTE FINAL DE CALIDAD\n";
        echo str_repeat("=", 60) . "\n";
        
        $percentage = $this->max_score > 0 ? round(($this->score / $this->max_score) * 100, 2) : 0;
        
        echo "🎯 Puntuación: {$this->score}/{$this->max_score} ($percentage%)\n\n";
        
        // Clasificar calidad
        if ($percentage >= 90) {
            echo "🏆 EXCELENTE - Código de alta calidad\n";
        } elseif ($percentage >= 75) {
            echo "✅ BUENO - Código de buena calidad\n";
        } elseif ($percentage >= 60) {
            echo "⚠️  ACEPTABLE - Necesita algunas mejoras\n";
        } else {
            echo "❌ NECESITA MEJORAS - Requiere atención\n";
        }
        
        // Mostrar problemas encontrados
        if (!empty($this->issues)) {
            echo "\n🚨 PROBLEMAS ENCONTRADOS:\n";
            foreach ($this->issues as $issue) {
                echo "   - $issue\n";
            }
        }
        
        // Mostrar recomendaciones
        if (!empty($this->recommendations)) {
            echo "\n💡 RECOMENDACIONES:\n";
            foreach ($this->recommendations as $recommendation) {
                echo "   - $recommendation\n";
            }
        }
        
        // Generar archivo de reporte
        $report = [
            'timestamp' => date('Y-m-d H:i:s'),
            'score' => $this->score,
            'max_score' => $this->max_score,
            'percentage' => $percentage,
            'issues' => $this->issues,
            'recommendations' => $this->recommendations
        ];
        
        $report_file = __DIR__ . '/quality_report_' . date('Y-m-d_H-i-s') . '.json';
        file_put_contents($report_file, json_encode($report, JSON_PRETTY_PRINT));
        
        echo "\n📄 Reporte detallado guardado en: $report_file\n";
    }
    
    /**
     * Agregar puntuación
     */
    private function addScore($points) {
        $this->score += $points;
        $this->max_score += $points;
    }
    
    /**
     * Agregar problema
     */
    private function addIssue($issue) {
        $this->issues[] = $issue;
        $this->max_score += 1; // Problema = punto perdido
    }
    
    /**
     * Agregar recomendación
     */
    private function addRecommendation($recommendation) {
        $this->recommendations[] = $recommendation;
    }
}

// Ejecutar validación si se llama desde línea de comandos
if (php_sapi_name() === 'cli') {
    try {
        $validator = new BestPracticesValidator();
        $validator->runValidation();
    } catch (Exception $e) {
        echo "❌ Error durante la validación: " . $e->getMessage() . "\n";
        exit(1);
    }
}
?>