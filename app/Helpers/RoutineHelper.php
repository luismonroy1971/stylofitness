<?php
/**
 * RoutineHelper - STYLOFITNESS
 * Helper específico para funcionalidades de rutinas
 */

class RoutineHelper {
    
    /**
     * Formatear objetivo de rutina
     */
    public static function formatObjective($objective) {
        $objectives = [
            'weight_loss' => 'Pérdida de Peso',
            'muscle_gain' => 'Ganancia Muscular',
            'strength' => 'Fuerza',
            'endurance' => 'Resistencia',
            'flexibility' => 'Flexibilidad'
        ];
        
        return $objectives[$objective] ?? ucfirst(str_replace('_', ' ', $objective));
    }
    
    /**
     * Formatear nivel de dificultad
     */
    public static function formatDifficulty($difficulty) {
        $difficulties = [
            'beginner' => 'Principiante',
            'intermediate' => 'Intermedio',
            'advanced' => 'Avanzado'
        ];
        
        return $difficulties[$difficulty] ?? ucfirst($difficulty);
    }
    
    /**
     * Obtener clase CSS para objetivo
     */
    public static function getObjectiveClass($objective) {
        $classes = [
            'weight_loss' => 'badge-danger',
            'muscle_gain' => 'badge-success',
            'strength' => 'badge-warning',
            'endurance' => 'badge-info',
            'flexibility' => 'badge-secondary'
        ];
        
        return $classes[$objective] ?? 'badge-primary';
    }
    
    /**
     * Obtener clase CSS para dificultad
     */
    public static function getDifficultyClass($difficulty) {
        $classes = [
            'beginner' => 'badge-success',
            'intermediate' => 'badge-warning',
            'advanced' => 'badge-danger'
        ];
        
        return $classes[$difficulty] ?? 'badge-secondary';
    }
    
    /**
     * Calcular progreso de rutina
     */
    public static function calculateProgress($routine, $workoutLogs) {
        if (empty($routine) || empty($workoutLogs)) {
            return [
                'completion_percentage' => 0,
                'exercises_completed' => 0,
                'total_exercises' => 0,
                'days_trained' => 0,
                'consistency_score' => 0
            ];
        }
        
        $totalExercises = count($routine['exercises'] ?? []);
        $completedExercises = count($workoutLogs);
        $completionPercentage = $totalExercises > 0 ? ($completedExercises / $totalExercises) * 100 : 0;
        
        // Calcular días únicos de entrenamiento
        $trainedDays = [];
        foreach ($workoutLogs as $log) {
            $day = date('Y-m-d', strtotime($log['completed_at']));
            $trainedDays[$day] = true;
        }
        $daysTrained = count($trainedDays);
        
        // Calcular score de consistencia (últimos 30 días)
        $thirtyDaysAgo = date('Y-m-d', strtotime('-30 days'));
        $recentLogs = array_filter($workoutLogs, function($log) use ($thirtyDaysAgo) {
            return $log['completed_at'] >= $thirtyDaysAgo;
        });
        
        $expectedSessions = $routine['sessions_per_week'] * 4; // 4 semanas aproximadas
        $actualSessions = count($recentLogs);
        $consistencyScore = min(100, ($actualSessions / max(1, $expectedSessions)) * 100);
        
        return [
            'completion_percentage' => round($completionPercentage, 1),
            'exercises_completed' => $completedExercises,
            'total_exercises' => $totalExercises,
            'days_trained' => $daysTrained,
            'consistency_score' => round($consistencyScore, 1),
            'recent_sessions' => $actualSessions,
            'expected_sessions' => $expectedSessions
        ];
    }
    
    /**
     * Generar recomendaciones para rutina
     */
    public static function generateRecommendations($routine, $userStats = []) {
        $recommendations = [];
        $objective = $routine['objective'];
        
        // Recomendaciones basadas en objetivo
        switch ($objective) {
            case 'weight_loss':
                if (!self::hasCardioExercises($routine)) {
                    $recommendations[] = [
                        'type' => 'cardio',
                        'title' => 'Agregar ejercicios cardiovasculares',
                        'description' => 'Para maximizar la quema de calorías, considera agregar más ejercicios de cardio',
                        'priority' => 'high'
                    ];
                }
                break;
                
            case 'muscle_gain':
                if (self::hasExcessiveCardio($routine)) {
                    $recommendations[] = [
                        'type' => 'strength',
                        'title' => 'Reducir cardio',
                        'description' => 'Para ganancia muscular, enfócate más en ejercicios de fuerza que en cardio',
                        'priority' => 'medium'
                    ];
                }
                break;
                
            case 'strength':
                if (!self::hasCompoundExercises($routine)) {
                    $recommendations[] = [
                        'type' => 'compound',
                        'title' => 'Incluir ejercicios compuestos',
                        'description' => 'Los ejercicios compuestos son esenciales para el desarrollo de fuerza',
                        'priority' => 'high'
                    ];
                }
                break;
        }
        
        // Recomendaciones basadas en balance muscular
        $muscleBalance = self::analyzeMuscleBalance($routine);
        if (!empty($muscleBalance['underworked'])) {
            $recommendations[] = [
                'type' => 'balance',
                'title' => 'Mejorar balance muscular',
                'description' => 'Grupos musculares poco trabajados: ' . implode(', ', $muscleBalance['underworked']),
                'priority' => 'medium'
            ];
        }
        
        // Recomendaciones de productos
        $productRecommendations = self::getProductRecommendations($objective);
        if (!empty($productRecommendations)) {
            $recommendations[] = [
                'type' => 'products',
                'title' => 'Suplementos recomendados',
                'description' => 'Productos que pueden complementar tu rutina',
                'priority' => 'low',
                'products' => $productRecommendations
            ];
        }
        
        return $recommendations;
    }
    
    /**
     * Verificar si la rutina tiene ejercicios de cardio
     */
    private static function hasCardioExercises($routine) {
        if (empty($routine['exercises'])) return false;
        
        foreach ($routine['exercises'] as $exercise) {
            if (isset($exercise['category_name']) && 
                stripos($exercise['category_name'], 'cardio') !== false) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Verificar si la rutina tiene exceso de cardio
     */
    private static function hasExcessiveCardio($routine) {
        if (empty($routine['exercises'])) return false;
        
        $totalExercises = count($routine['exercises']);
        $cardioExercises = 0;
        
        foreach ($routine['exercises'] as $exercise) {
            if (isset($exercise['category_name']) && 
                stripos($exercise['category_name'], 'cardio') !== false) {
                $cardioExercises++;
            }
        }
        
        return ($cardioExercises / $totalExercises) > 0.4; // Más del 40% cardio
    }
    
    /**
     * Verificar si la rutina tiene ejercicios compuestos
     */
    private static function hasCompoundExercises($routine) {
        if (empty($routine['exercises'])) return false;
        
        $compoundExercises = [
            'sentadilla', 'peso muerto', 'press banca', 'press militar',
            'dominadas', 'remo', 'fondos'
        ];
        
        foreach ($routine['exercises'] as $exercise) {
            $exerciseName = strtolower($exercise['exercise_name'] ?? '');
            foreach ($compoundExercises as $compound) {
                if (stripos($exerciseName, $compound) !== false) {
                    return true;
                }
            }
        }
        
        return false;
    }
    
    /**
     * Analizar balance muscular de la rutina
     */
    private static function analyzeMuscleBalance($routine) {
        $muscleGroups = [
            'pectorales' => 0,
            'dorsales' => 0,
            'deltoides' => 0,
            'biceps' => 0,
            'triceps' => 0,
            'cuadriceps' => 0,
            'isquiotibiales' => 0,
            'gluteos' => 0,
            'core' => 0
        ];
        
        if (!empty($routine['exercises'])) {
            foreach ($routine['exercises'] as $exercise) {
                if (!empty($exercise['muscle_groups'])) {
                    foreach ($exercise['muscle_groups'] as $muscle) {
                        $muscle = strtolower($muscle);
                        if (isset($muscleGroups[$muscle])) {
                            $muscleGroups[$muscle]++;
                        }
                    }
                }
            }
        }
        
        $total = array_sum($muscleGroups);
        $average = $total / count($muscleGroups);
        $threshold = $average * 0.5; // 50% del promedio
        
        $underworked = [];
        foreach ($muscleGroups as $muscle => $count) {
            if ($count < $threshold && $count < 2) {
                $underworked[] = ucfirst($muscle);
            }
        }
        
        return [
            'distribution' => $muscleGroups,
            'underworked' => $underworked,
            'balance_score' => self::calculateBalanceScore($muscleGroups)
        ];
    }
    
    /**
     * Calcular puntuación de balance
     */
    private static function calculateBalanceScore($muscleGroups) {
        $values = array_values($muscleGroups);
        $mean = array_sum($values) / count($values);
        
        if ($mean == 0) return 0;
        
        $variance = 0;
        foreach ($values as $value) {
            $variance += pow($value - $mean, 2);
        }
        $variance /= count($values);
        
        $standardDeviation = sqrt($variance);
        $coefficientOfVariation = $standardDeviation / $mean;
        
        // Convertir a puntuación del 0-100 (menor variación = mejor puntuación)
        return max(0, 100 - ($coefficientOfVariation * 100));
    }
    
    /**
     * Obtener recomendaciones de productos
     */
    private static function getProductRecommendations($objective) {
        $recommendations = [
            'weight_loss' => [
                'categories' => ['quemadores', 'l-carnitina', 'te-verde'],
                'message' => 'Suplementos para potenciar la quema de grasa'
            ],
            'muscle_gain' => [
                'categories' => ['proteinas', 'creatina', 'ganadores-peso'],
                'message' => 'Suplementos para maximizar el crecimiento muscular'
            ],
            'strength' => [
                'categories' => ['creatina', 'pre-entrenos', 'proteinas'],
                'message' => 'Suplementos para aumentar fuerza y potencia'
            ],
            'endurance' => [
                'categories' => ['bcaa', 'bebidas-deportivas', 'beta-alanina'],
                'message' => 'Suplementos para mejorar resistencia'
            ]
        ];
        
        return $recommendations[$objective] ?? [];
    }
    
    /**
     * Validar estructura de rutina
     */
    public static function validateRoutineStructure($routineData) {
        $errors = [];
        
        // Validar información básica
        if (empty($routineData['name'])) {
            $errors['name'] = 'El nombre de la rutina es obligatorio';
        }
        
        if (empty($routineData['objective'])) {
            $errors['objective'] = 'El objetivo es obligatorio';
        }
        
        if (empty($routineData['difficulty_level'])) {
            $errors['difficulty_level'] = 'El nivel de dificultad es obligatorio';
        }
        
        // Validar duración
        $weeks = intval($routineData['duration_weeks'] ?? 0);
        if ($weeks < 1 || $weeks > 52) {
            $errors['duration_weeks'] = 'La duración debe estar entre 1 y 52 semanas';
        }
        
        // Validar sesiones por semana
        $sessions = intval($routineData['sessions_per_week'] ?? 0);
        if ($sessions < 1 || $sessions > 7) {
            $errors['sessions_per_week'] = 'Las sesiones por semana deben estar entre 1 y 7';
        }
        
        // Validar ejercicios
        if (empty($routineData['exercises'])) {
            $errors['exercises'] = 'La rutina debe tener al menos un ejercicio';
        } else {
            $exerciseErrors = self::validateExercises($routineData['exercises']);
            if (!empty($exerciseErrors)) {
                $errors['exercises'] = $exerciseErrors;
            }
        }
        
        return $errors;
    }
    
    /**
     * Validar ejercicios de rutina
     */
    private static function validateExercises($exercises) {
        $errors = [];
        
        foreach ($exercises as $dayNumber => $dayExercises) {
            if (empty($dayExercises)) {
                continue;
            }
            
            foreach ($dayExercises as $order => $exercise) {
                $exerciseKey = "day_{$dayNumber}_exercise_{$order}";
                
                if (empty($exercise['exercise_id'])) {
                    $errors[$exerciseKey][] = 'ID de ejercicio requerido';
                }
                
                $sets = intval($exercise['sets'] ?? 0);
                if ($sets < 1 || $sets > 10) {
                    $errors[$exerciseKey][] = 'Las series deben estar entre 1 y 10';
                }
                
                if (empty($exercise['reps'])) {
                    $errors[$exerciseKey][] = 'Las repeticiones son obligatorias';
                }
                
                $rest = intval($exercise['rest_seconds'] ?? 0);
                if ($rest < 15 || $rest > 600) {
                    $errors[$exerciseKey][] = 'El descanso debe estar entre 15 y 600 segundos';
                }
            }
        }
        
        return $errors;
    }
    
    /**
     * Generar resumen de rutina
     */
    public static function generateRoutineSummary($routine, $exercises = []) {
        $summary = [
            'total_exercises' => 0,
            'total_sets' => 0,
            'estimated_duration' => 0,
            'muscle_groups' => [],
            'equipment_needed' => [],
            'difficulty_distribution' => [
                'beginner' => 0,
                'intermediate' => 0,
                'advanced' => 0
            ],
            'exercise_types' => [],
            'days_count' => 0
        ];
        
        if (!empty($exercises)) {
            $days = [];
            
            foreach ($exercises as $exercise) {
                $summary['total_exercises']++;
                $summary['total_sets'] += intval($exercise['sets'] ?? 3);
                
                // Duración estimada (3 minutos por serie + descansos)
                $exerciseDuration = (intval($exercise['sets'] ?? 3) * 45) + 
                                  ((intval($exercise['sets'] ?? 3) - 1) * intval($exercise['rest_seconds'] ?? 60));
                $summary['estimated_duration'] += $exerciseDuration;
                
                // Grupos musculares
                if (!empty($exercise['muscle_groups'])) {
                    foreach ($exercise['muscle_groups'] as $muscle) {
                        $summary['muscle_groups'][$muscle] = ($summary['muscle_groups'][$muscle] ?? 0) + 1;
                    }
                }
                
                // Equipo necesario
                if (!empty($exercise['equipment_needed'])) {
                    $equipment = explode(',', $exercise['equipment_needed']);
                    foreach ($equipment as $eq) {
                        $eq = trim($eq);
                        $summary['equipment_needed'][$eq] = ($summary['equipment_needed'][$eq] ?? 0) + 1;
                    }
                }
                
                // Distribución de dificultad
                $difficulty = $exercise['difficulty_level'] ?? 'intermediate';
                $summary['difficulty_distribution'][$difficulty]++;
                
                // Días únicos
                $day = $exercise['day_number'] ?? 1;
                $days[$day] = true;
            }
            
            $summary['days_count'] = count($days);
            $summary['estimated_duration'] = round($summary['estimated_duration'] / 60); // Convertir a minutos
        }
        
        return $summary;
    }
    
    /**
     * Comparar rutinas
     */
    public static function compareRoutines($routine1, $routine2) {
        $comparison = [
            'difficulty' => self::compareDifficulty($routine1, $routine2),
            'duration' => self::compareDuration($routine1, $routine2),
            'intensity' => self::compareIntensity($routine1, $routine2),
            'muscle_coverage' => self::compareMuscleGroups($routine1, $routine2),
            'similarity_score' => 0
        ];
        
        // Calcular puntuación de similitud
        $factors = ['difficulty', 'duration', 'intensity', 'muscle_coverage'];
        $totalScore = 0;
        
        foreach ($factors as $factor) {
            $totalScore += $comparison[$factor]['score'] ?? 0;
        }
        
        $comparison['similarity_score'] = $totalScore / count($factors);
        
        return $comparison;
    }
    
    /**
     * Comparar dificultad entre rutinas
     */
    private static function compareDifficulty($routine1, $routine2) {
        $difficultyScores = ['beginner' => 1, 'intermediate' => 2, 'advanced' => 3];
        
        $score1 = $difficultyScores[$routine1['difficulty_level']] ?? 2;
        $score2 = $difficultyScores[$routine2['difficulty_level']] ?? 2;
        
        $difference = abs($score1 - $score2);
        $similarity = max(0, 100 - ($difference * 33.33)); // 0-100 scale
        
        return [
            'routine1' => $routine1['difficulty_level'],
            'routine2' => $routine2['difficulty_level'],
            'difference' => $difference,
            'score' => $similarity
        ];
    }
    
    /**
     * Comparar duración entre rutinas
     */
    private static function compareDuration($routine1, $routine2) {
        $duration1 = intval($routine1['estimated_duration_minutes'] ?? 60);
        $duration2 = intval($routine2['estimated_duration_minutes'] ?? 60);
        
        $difference = abs($duration1 - $duration2);
        $maxDuration = max($duration1, $duration2);
        
        $similarity = $maxDuration > 0 ? max(0, 100 - (($difference / $maxDuration) * 100)) : 100;
        
        return [
            'routine1' => $duration1,
            'routine2' => $duration2,
            'difference' => $difference,
            'score' => $similarity
        ];
    }
    
    /**
     * Comparar intensidad entre rutinas
     */
    private static function compareIntensity($routine1, $routine2) {
        // Calcular intensidad basada en sesiones por semana y duración
        $intensity1 = (intval($routine1['sessions_per_week'] ?? 3) * 
                      intval($routine1['estimated_duration_minutes'] ?? 60)) / 7;
        $intensity2 = (intval($routine2['sessions_per_week'] ?? 3) * 
                      intval($routine2['estimated_duration_minutes'] ?? 60)) / 7;
        
        $difference = abs($intensity1 - $intensity2);
        $maxIntensity = max($intensity1, $intensity2);
        
        $similarity = $maxIntensity > 0 ? max(0, 100 - (($difference / $maxIntensity) * 100)) : 100;
        
        return [
            'routine1' => round($intensity1, 2),
            'routine2' => round($intensity2, 2),
            'difference' => round($difference, 2),
            'score' => $similarity
        ];
    }
    
    /**
     * Comparar cobertura de grupos musculares
     */
    private static function compareMuscleGroups($routine1, $routine2) {
        // Simplificado - en una implementación real analizaría los ejercicios
        $muscles1 = self::extractMuscleGroups($routine1);
        $muscles2 = self::extractMuscleGroups($routine2);
        
        $intersection = array_intersect($muscles1, $muscles2);
        $union = array_unique(array_merge($muscles1, $muscles2));
        
        $similarity = count($union) > 0 ? (count($intersection) / count($union)) * 100 : 100;
        
        return [
            'routine1' => $muscles1,
            'routine2' => $muscles2,
            'common' => $intersection,
            'score' => $similarity
        ];
    }
    
    /**
     * Extraer grupos musculares de rutina
     */
    private static function extractMuscleGroups($routine) {
        // Mapeo simplificado basado en objetivo
        $muscleMapping = [
            'weight_loss' => ['cardio', 'cuerpo_completo'],
            'muscle_gain' => ['pectorales', 'dorsales', 'piernas', 'brazos'],
            'strength' => ['pectorales', 'dorsales', 'piernas', 'core'],
            'endurance' => ['cardio', 'core', 'piernas'],
            'flexibility' => ['todo_el_cuerpo', 'core']
        ];
        
        return $muscleMapping[$routine['objective']] ?? ['general'];
    }
    
    /**
     * Generar código QR para rutina
     */
    public static function generateRoutineQR($routineId, $size = 200) {
        $url = getAppConfig('app_url') . '/routines/view/' . $routineId;
        $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size={$size}x{$size}&data=" . urlencode($url);
        
        return $qrUrl;
    }
    
    /**
     * Exportar rutina a diferentes formatos
     */
    public static function exportRoutine($routine, $exercises, $format = 'array') {
        switch ($format) {
            case 'json':
                return json_encode([
                    'routine' => $routine,
                    'exercises' => $exercises,
                    'exported_at' => date('Y-m-d H:i:s')
                ], JSON_PRETTY_PRINT);
                
            case 'csv':
                return self::exportRoutineToCSV($routine, $exercises);
                
            case 'xml':
                return AppHelper::arrayToXml([
                    'routine' => $routine,
                    'exercises' => $exercises
                ], 'routine_export');
                
            default:
                return [
                    'routine' => $routine,
                    'exercises' => $exercises,
                    'exported_at' => date('Y-m-d H:i:s')
                ];
        }
    }
    
    /**
     * Exportar rutina a CSV
     */
    private static function exportRoutineToCSV($routine, $exercises) {
        $csv = "Rutina: " . $routine['name'] . "\n";
        $csv .= "Objetivo: " . self::formatObjective($routine['objective']) . "\n";
        $csv .= "Dificultad: " . self::formatDifficulty($routine['difficulty_level']) . "\n\n";
        
        $csv .= "Día,Orden,Ejercicio,Series,Repeticiones,Peso,Descanso,Notas\n";
        
        foreach ($exercises as $exercise) {
            $csv .= implode(',', [
                $exercise['day_number'],
                $exercise['order_index'],
                '"' . $exercise['exercise_name'] . '"',
                $exercise['sets'],
                '"' . $exercise['reps'] . '"',
                '"' . ($exercise['weight'] ?? '') . '"',
                $exercise['rest_seconds'],
                '"' . ($exercise['notes'] ?? '') . '"'
            ]) . "\n";
        }
        
        return $csv;
    }
}
?>