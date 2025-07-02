<?php

namespace StyleFitness\Models;

use StyleFitness\Config\Database;
use PDO;
use Exception;

/**
 * Modelo para gestionar características "Por qué elegirnos"
 * STYLOFITNESS - Sistema de Gestión de Contenido
 */
class WhyChooseUs
{
    private $db;
    private $table = 'why_choose_us';

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Obtener todas las características activas
     */
    public function getActiveFeatures($limit = null)
    {
        $sql = "SELECT * FROM {$this->table} 
                ORDER BY display_order ASC, created_at DESC";
        
        if ($limit) {
            $sql .= " LIMIT ?";
            return $this->db->fetchAll($sql, [$limit]);
        }
        
        return $this->db->fetchAll($sql);
    }

    /**
     * Obtener característica por ID
     */
    public function getById($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = ?";
        $result = $this->db->fetch($sql, [$id]);
        
        if ($result) {
            // Decodificar JSON fields
            $result['highlights'] = !empty($result['highlights']) ? json_decode($result['highlights'], true) : [];
            $result['stats'] = !empty($result['stats']) ? json_decode($result['stats'], true) : [];
        }
        
        return $result;
    }

    /**
     * Crear nueva característica
     */
    public function create($data)
    {
        $sql = "INSERT INTO {$this->table} 
                (title, subtitle, description, icon, icon_color, 
                background_gradient, highlights, stats, display_order)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $params = [
            $data['title'],
            $data['subtitle'] ?? null,
            $data['description'] ?? null,
            $data['icon'] ?? 'fas fa-star',
            $data['icon_color'] ?? '#ff6b35',
            $data['background_gradient'] ?? null,
            json_encode($data['highlights'] ?? []),
            json_encode($data['stats'] ?? []),
            $data['display_order'] ?? 0
        ];
        
        return $this->db->execute($sql, $params);
    }

    /**
     * Actualizar característica
     */
    public function update($id, $data)
    {
        $sql = "UPDATE {$this->table} SET 
                title = ?, subtitle = ?, description = ?, icon = ?, icon_color = ?, 
                background_gradient = ?, highlights = ?, 
                stats = ?, display_order = ?, updated_at = NOW()
                WHERE id = ?";
        
        $params = [
            $data['title'],
            $data['subtitle'] ?? null,
            $data['description'] ?? null,
            $data['icon'] ?? 'fas fa-star',
            $data['icon_color'] ?? '#ff6b35',
            $data['background_gradient'] ?? null,
            json_encode($data['highlights'] ?? []),
            json_encode($data['stats'] ?? []),
            $data['display_order'] ?? 0,
            $id
        ];
        
        return $this->db->execute($sql, $params);
    }

    /**
     * Eliminar característica
     */
    public function delete($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE id = ?";
        return $this->db->execute($sql, [$id]);
    }

    /**
     * Activar/Desactivar característica
     */
    public function toggleStatus($id)
    {
        // Método mantenido para compatibilidad pero sin funcionalidad
        return true;
    }

    /**
     * Obtener todas las características (para administración)
     */
    public function getAll($page = 1, $limit = 20)
    {
        $offset = ($page - 1) * $limit;
        $sql = "SELECT * FROM {$this->table} 
                ORDER BY display_order ASC, created_at DESC 
                LIMIT ? OFFSET ?";
        
        $results = $this->db->fetchAll($sql, [$limit, $offset]);
        
        // Decodificar JSON fields para cada resultado
        foreach ($results as &$result) {
            $result['highlights'] = !empty($result['highlights']) ? json_decode($result['highlights'], true) : [];
            $result['stats'] = !empty($result['stats']) ? json_decode($result['stats'], true) : [];
        }
        
        return $results;
    }

    /**
     * Contar total de características
     */
    public function getTotalCount()
    {
        $sql = "SELECT COUNT(*) as total FROM {$this->table}";
        $result = $this->db->fetch($sql);
        return $result['total'] ?? 0;
    }

    /**
     * Actualizar orden de visualización
     */
    public function updateDisplayOrder($id, $order)
    {
        $sql = "UPDATE {$this->table} SET display_order = ? WHERE id = ?";
        return $this->db->execute($sql, [$order, $id]);
    }

    /**
     * Obtener características con estadísticas procesadas
     */
    public function getFeaturesWithProcessedStats($limit = null)
    {
        $features = $this->getActiveFeatures($limit);
        
        foreach ($features as &$feature) {
            // Decodificar JSON fields
            $feature['highlights'] = !empty($feature['highlights']) ? json_decode($feature['highlights'], true) : [];
            $feature['stats'] = !empty($feature['stats']) ? json_decode($feature['stats'], true) : [];
            
            // Procesar estadísticas para mostrar números animados
            if (!empty($feature['stats'])) {
                foreach ($feature['stats'] as $key => &$stat) {
                    // Extraer números para animación
                    if (preg_match('/([0-9,]+)/', $stat, $matches)) {
                        $feature['stats_animated'][$key] = [
                            'number' => str_replace(',', '', $matches[1]),
                            'text' => $stat
                        ];
                    }
                }
            }
        }
        
        return $features;
    }

    /**
     * Buscar características por término
     */
    public function search($term, $limit = 10)
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE (title LIKE ? OR subtitle LIKE ? OR description LIKE ?) 
                ORDER BY display_order ASC 
                LIMIT ?";
        
        $searchTerm = "%{$term}%";
        $results = $this->db->fetchAll($sql, [$searchTerm, $searchTerm, $searchTerm, $limit]);
        
        // Decodificar JSON fields
        foreach ($results as &$result) {
            $result['highlights'] = !empty($result['highlights']) ? json_decode($result['highlights'], true) : [];
            $result['stats'] = !empty($result['stats']) ? json_decode($result['stats'], true) : [];
        }
        
        return $results;
    }
}