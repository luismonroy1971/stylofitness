<?php

require_once __DIR__ . '/../Config/ProgressConfig.php';

/**
 * Helper para el Sistema de Seguimiento de Progreso
 * StyloFitness - Funciones auxiliares para tracking avanzado
 */

class ProgressHelper
{
    /**
     * Formatear valor según tipo de métrica
     */
    public static function formatMetricValue($value, $type, $unit = '')
    {
        switch ($type) {
            case 'percentage':
                return number_format($value, 1) . '%';
            case 'time':
                if ($unit === 'seconds') {
                    $minutes = floor($value / 60);
                    $seconds = $value % 60;
                    return sprintf('%d:%02d', $minutes, $seconds);
                }
                return number_format($value, 1) . ' ' . ($unit ?: 'min');
            case 'distance':
                if ($value >= 1000 && $unit === 'm') {
                    return number_format($value / 1000, 2) . ' km';
                }
                return number_format($value, 1) . ' ' . ($unit ?: 'm');
            case 'weight':
                return number_format($value, 1) . ' ' . ($unit ?: 'kg');
            case 'numeric':
            default:
                return number_format($value, 1) . ($unit ? ' ' . $unit : '');
        }
    }
    
    /**
     * Calcular porcentaje de progreso hacia un objetivo
     */
    public static function calculateGoalProgress($currentValue, $targetValue, $startValue = 0)
    {
        if ($targetValue == $startValue) {
            return 100; // Si no hay diferencia, consideramos completado
        }
        
        $progress = (($currentValue - $startValue) / ($targetValue - $startValue)) * 100;
        return max(0, min(100, $progress)); // Limitar entre 0 y 100
    }
    
    /**
     * Determinar color según porcentaje de progreso
     */
    public static function getProgressColor($percentage)
    {
        if ($percentage >= 90) return 'success';
        if ($percentage >= 70) return 'info';
        if ($percentage >= 50) return 'warning';
        if ($percentage >= 25) return 'orange';
        return 'danger';
    }
    
    /**
     * Calcular tendencia de progreso
     */
    public static function calculateTrend($data, $periods = 3)
    {
        if (count($data) < $periods) {
            return 'stable';
        }
        
        $recent = array_slice($data, -$periods);
        $older = array_slice($data, -($periods * 2), $periods);
        
        $recentAvg = array_sum($recent) / count($recent);
        $olderAvg = array_sum($older) / count($older);
        
        $change = (($recentAvg - $olderAvg) / $olderAvg) * 100;
        
        if ($change > 5) return 'improving';
        if ($change < -5) return 'declining';
        return 'stable';
    }
    
    /**
     * Obtener icono según tendencia
     */
    public static function getTrendIcon($trend)
    {
        switch ($trend) {
            case 'improving':
                return 'fas fa-arrow-up text-success';
            case 'declining':
                return 'fas fa-arrow-down text-danger';
            case 'stable':
            default:
                return 'fas fa-minus text-warning';
        }
    }
    
    /**
     * Calcular consistencia de entrenamiento
     */
    public static function calculateConsistency($workoutDates, $totalDays = 30)
    {
        if (empty($workoutDates)) {
            return 0;
        }
        
        $uniqueDays = count(array_unique(array_map(function($date) {
            return date('Y-m-d', strtotime($date));
        }, $workoutDates)));
        
        return min(100, ($uniqueDays / $totalDays) * 100);
    }
    
    /**
     * Generar recomendaciones basadas en datos
     */
    public static function generateRecommendations($clientData)
    {
        $recommendations = [];
        
        // Verificar consistencia
        if (isset($clientData['consistency']) && $clientData['consistency'] < 50) {
            $recommendations[] = [
                'type' => 'consistency',
                'priority' => 'high',
                'title' => 'Mejorar Consistencia',
                'message' => 'La consistencia de entrenamiento está por debajo del 50%. Considera establecer un horario fijo de entrenamiento.',
                'action' => 'Programar sesiones regulares'
            ];
        }
        
        // Verificar RPE promedio
        if (isset($clientData['avg_rpe']) && $clientData['avg_rpe'] > 8) {
            $recommendations[] = [
                'type' => 'intensity',
                'priority' => 'medium',
                'title' => 'Reducir Intensidad',
                'message' => 'El RPE promedio es muy alto. Considera incluir más días de recuperación.',
                'action' => 'Ajustar intensidad de entrenamientos'
            ];
        }
        
        // Verificar progreso en objetivos
        if (isset($clientData['goal_progress']) && $clientData['goal_progress'] < 25) {
            $recommendations[] = [
                'type' => 'goals',
                'priority' => 'medium',
                'title' => 'Revisar Objetivos',
                'message' => 'El progreso hacia los objetivos es lento. Considera revisar y ajustar las metas.',
                'action' => 'Reevaluar objetivos y estrategia'
            ];
        }
        
        // Verificar variedad de ejercicios
        if (isset($clientData['exercise_variety']) && $clientData['exercise_variety'] < 10) {
            $recommendations[] = [
                'type' => 'variety',
                'priority' => 'low',
                'title' => 'Aumentar Variedad',
                'message' => 'Poca variedad en los ejercicios. Considera incluir nuevos movimientos.',
                'action' => 'Diversificar rutina de ejercicios'
            ];
        }
        
        return $recommendations;
    }
    
    /**
     * Calcular puntuación de riesgo de lesión
     */
    public static function calculateInjuryRisk($workoutData)
    {
        $riskScore = 0;
        $factors = [];
        
        // Factor 1: RPE consistentemente alto
        $highRpeCount = 0;
        foreach ($workoutData as $workout) {
            if (isset($workout['rpe']) && $workout['rpe'] >= 9) {
                $highRpeCount++;
            }
        }
        if ($highRpeCount > count($workoutData) * 0.3) {
            $riskScore += 30;
            $factors[] = 'RPE alto frecuente';
        }
        
        // Factor 2: Incremento rápido de volumen
        if (count($workoutData) >= 4) {
            $recentVolume = 0;
            $previousVolume = 0;
            
            for ($i = count($workoutData) - 2; $i < count($workoutData); $i++) {
                if (isset($workoutData[$i]['total_volume'])) {
                    $recentVolume += $workoutData[$i]['total_volume'];
                }
            }
            
            for ($i = count($workoutData) - 4; $i < count($workoutData) - 2; $i++) {
                if (isset($workoutData[$i]['total_volume'])) {
                    $previousVolume += $workoutData[$i]['total_volume'];
                }
            }
            
            if ($previousVolume > 0 && ($recentVolume / $previousVolume) > 1.3) {
                $riskScore += 25;
                $factors[] = 'Incremento rápido de volumen';
            }
        }
        
        // Factor 3: Falta de días de descanso
        $consecutiveDays = 0;
        $maxConsecutive = 0;
        $lastDate = null;
        
        foreach ($workoutData as $workout) {
            $currentDate = strtotime($workout['workout_date']);
            if ($lastDate && ($currentDate - $lastDate) <= 86400) { // 24 horas
                $consecutiveDays++;
            } else {
                $maxConsecutive = max($maxConsecutive, $consecutiveDays);
                $consecutiveDays = 1;
            }
            $lastDate = $currentDate;
        }
        $maxConsecutive = max($maxConsecutive, $consecutiveDays);
        
        if ($maxConsecutive > 5) {
            $riskScore += 20;
            $factors[] = 'Entrenamientos consecutivos excesivos';
        }
        
        // Factor 4: Desequilibrio muscular (ejercicios repetitivos)
        $exerciseFrequency = [];
        foreach ($workoutData as $workout) {
            if (isset($workout['exercise_name'])) {
                $exerciseFrequency[$workout['exercise_name']] = 
                    ($exerciseFrequency[$workout['exercise_name']] ?? 0) + 1;
            }
        }
        
        $maxFrequency = max($exerciseFrequency);
        $totalExercises = array_sum($exerciseFrequency);
        
        if ($totalExercises > 0 && ($maxFrequency / $totalExercises) > 0.4) {
            $riskScore += 15;
            $factors[] = 'Ejercicios repetitivos';
        }
        
        return [
            'score' => min(100, $riskScore),
            'level' => self::getInjuryRiskLevel($riskScore),
            'factors' => $factors
        ];
    }
    
    /**
     * Obtener nivel de riesgo de lesión
     */
    private static function getInjuryRiskLevel($score)
    {
        if ($score >= 70) return 'high';
        if ($score >= 40) return 'medium';
        if ($score >= 20) return 'low';
        return 'minimal';
    }
    
    /**
     * Formatear duración en formato legible
     */
    public static function formatDuration($seconds)
    {
        if ($seconds < 60) {
            return $seconds . 's';
        } elseif ($seconds < 3600) {
            $minutes = floor($seconds / 60);
            $remainingSeconds = $seconds % 60;
            return $minutes . 'm' . ($remainingSeconds > 0 ? ' ' . $remainingSeconds . 's' : '');
        } else {
            $hours = floor($seconds / 3600);
            $minutes = floor(($seconds % 3600) / 60);
            return $hours . 'h' . ($minutes > 0 ? ' ' . $minutes . 'm' : '');
        }
    }
    
    /**
     * Calcular calorías estimadas por ejercicio
     */
    public static function estimateCalories($exerciseType, $duration, $weight = 70, $intensity = 'medium')
    {
        // MET values para diferentes ejercicios
        $metValues = [
            'cardio' => ['low' => 4, 'medium' => 6, 'high' => 8],
            'strength' => ['low' => 3, 'medium' => 5, 'high' => 7],
            'flexibility' => ['low' => 2, 'medium' => 3, 'high' => 4],
            'sports' => ['low' => 5, 'medium' => 7, 'high' => 9],
            'default' => ['low' => 3, 'medium' => 5, 'high' => 7]
        ];
        
        $met = $metValues[$exerciseType][$intensity] ?? $metValues['default'][$intensity];
        
        // Fórmula: Calorías = MET × peso(kg) × tiempo(horas)
        $hours = $duration / 3600;
        return round($met * $weight * $hours);
    }
    
    /**
     * Generar datos para gráfico de progreso
     */
    public static function generateProgressChartData($data, $period = '30d')
    {
        $periodConfig = ProgressConfig::getChartPeriod($period);
        $days = $periodConfig['days'];
        $format = $periodConfig['format'];
        
        $startDate = date('Y-m-d', strtotime("-{$days} days"));
        $endDate = date('Y-m-d');
        
        $labels = [];
        $datasets = [];
        
        // Generar etiquetas para el período
        $currentDate = $startDate;
        while ($currentDate <= $endDate) {
            $labels[] = date($format, strtotime($currentDate));
            $currentDate = date('Y-m-d', strtotime($currentDate . ' +1 day'));
        }
        
        // Procesar datos por métrica
        $metrics = array_keys($data);
        $colors = array_values(ProgressConfig::CHART_COLORS);
        
        foreach ($metrics as $index => $metric) {
            $metricData = [];
            $currentDate = $startDate;
            
            while ($currentDate <= $endDate) {
                $value = 0;
                foreach ($data[$metric] as $dataPoint) {
                    if (date('Y-m-d', strtotime($dataPoint['date'])) === $currentDate) {
                        $value = $dataPoint['value'];
                        break;
                    }
                }
                $metricData[] = $value;
                $currentDate = date('Y-m-d', strtotime($currentDate . ' +1 day'));
            }
            
            $datasets[] = [
                'label' => ucfirst(str_replace('_', ' ', $metric)),
                'data' => $metricData,
                'borderColor' => $colors[$index % count($colors)],
                'backgroundColor' => $colors[$index % count($colors)] . '20',
                'fill' => false,
                'tension' => 0.4
            ];
        }
        
        return [
            'labels' => $labels,
            'datasets' => $datasets
        ];
    }
    
    /**
     * Validar datos de entrada para métricas
     */
    public static function validateMetricData($value, $type, $unit = '')
    {
        $errors = [];
        
        if (!is_numeric($value)) {
            $errors[] = 'El valor debe ser numérico';
            return $errors;
        }
        
        switch ($type) {
            case 'percentage':
                if ($value < 0 || $value > 100) {
                    $errors[] = 'El porcentaje debe estar entre 0 y 100';
                }
                break;
            case 'time':
                if ($value < 0) {
                    $errors[] = 'El tiempo no puede ser negativo';
                }
                if ($unit === 'seconds' && $value > 86400) {
                    $errors[] = 'El tiempo no puede exceder 24 horas';
                }
                break;
            case 'distance':
                if ($value < 0) {
                    $errors[] = 'La distancia no puede ser negativa';
                }
                break;
            case 'weight':
                if ($value < 0) {
                    $errors[] = 'El peso no puede ser negativo';
                }
                if ($value > 1000) {
                    $errors[] = 'El peso parece excesivo';
                }
                break;
            case 'numeric':
                if ($value < 0) {
                    $errors[] = 'El valor no puede ser negativo';
                }
                break;
        }
        
        return $errors;
    }
    
    /**
     * Generar resumen de actividad semanal
     */
    public static function generateWeeklySummary($workoutData)
    {
        $summary = [
            'total_workouts' => 0,
            'total_duration' => 0,
            'total_calories' => 0,
            'avg_rpe' => 0,
            'most_active_day' => '',
            'workout_distribution' => [],
            'consistency_score' => 0
        ];
        
        if (empty($workoutData)) {
            return $summary;
        }
        
        $dayCount = [];
        $rpeSum = 0;
        $rpeCount = 0;
        
        foreach ($workoutData as $workout) {
            $summary['total_workouts']++;
            $summary['total_duration'] += $workout['duration_seconds'] ?? 0;
            $summary['total_calories'] += $workout['calories_burned'] ?? 0;
            
            if (isset($workout['rpe']) && $workout['rpe'] > 0) {
                $rpeSum += $workout['rpe'];
                $rpeCount++;
            }
            
            $dayOfWeek = date('l', strtotime($workout['workout_date']));
            $dayCount[$dayOfWeek] = ($dayCount[$dayOfWeek] ?? 0) + 1;
        }
        
        $summary['avg_rpe'] = $rpeCount > 0 ? round($rpeSum / $rpeCount, 1) : 0;
        $summary['most_active_day'] = !empty($dayCount) ? array_keys($dayCount, max($dayCount))[0] : '';
        $summary['workout_distribution'] = $dayCount;
        $summary['consistency_score'] = self::calculateConsistency(
            array_column($workoutData, 'workout_date'), 
            7
        );
        
        return $summary;
    }
    
    /**
     * Obtener mensaje motivacional basado en progreso
     */
    public static function getMotivationalMessage($progressData)
    {
        $messages = [
            'excellent' => [
                '¡Excelente trabajo! Tu progreso es excepcional.',
                '¡Increíble! Sigues superando expectativas.',
                '¡Fantástico! Tu dedicación está dando frutos.',
                '¡Impresionante! Eres un ejemplo de constancia.'
            ],
            'good' => [
                '¡Muy bien! Vas por buen camino.',
                '¡Genial! Tu esfuerzo se nota.',
                '¡Sigue así! El progreso es evidente.',
                '¡Bien hecho! Mantén el ritmo.'
            ],
            'average' => [
                'Buen trabajo, pero puedes dar más.',
                'Vas bien, considera aumentar la intensidad.',
                'Progreso constante, ¡sigue adelante!',
                'En el camino correcto, mantén la consistencia.'
            ],
            'needs_improvement' => [
                'Es momento de retomar el ritmo.',
                'Pequeños pasos llevan a grandes logros.',
                'Cada día es una nueva oportunidad.',
                'Tu próximo entrenamiento puede marcar la diferencia.'
            ]
        ];
        
        $category = 'average';
        
        if (isset($progressData['consistency'])) {
            if ($progressData['consistency'] >= 80) {
                $category = 'excellent';
            } elseif ($progressData['consistency'] >= 60) {
                $category = 'good';
            } elseif ($progressData['consistency'] >= 40) {
                $category = 'average';
            } else {
                $category = 'needs_improvement';
            }
        }
        
        $categoryMessages = $messages[$category];
        return $categoryMessages[array_rand($categoryMessages)];
    }
    
    /**
     * Convertir datos para exportación
     */
    public static function prepareDataForExport($data, $format = 'array')
    {
        switch ($format) {
            case 'csv':
                $csv = [];
                if (!empty($data)) {
                    $csv[] = array_keys($data[0]); // Headers
                    foreach ($data as $row) {
                        $csv[] = array_values($row);
                    }
                }
                return $csv;
                
            case 'json':
                return json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                
            case 'xml':
                $xml = new SimpleXMLElement('<data/>');
                foreach ($data as $index => $item) {
                    $record = $xml->addChild('record');
                    foreach ($item as $key => $value) {
                        $record->addChild($key, htmlspecialchars($value));
                    }
                }
                return $xml->asXML();
                
            case 'array':
            default:
                return $data;
        }
    }
}

?>