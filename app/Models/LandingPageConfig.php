<?php

namespace StyleFitness\Models;

use StyleFitness\Config\Database;
use PDO;
use Exception;

/**
 * Modelo para gestionar configuración de la landing page
 * STYLOFITNESS - Sistema de Gestión de Contenido
 */
class LandingPageConfig
{
    private $db;
    private $table = 'landing_page_config';

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Obtener configuración por sección
     */
    public function getBySection($section)
    {
        $sql = "SELECT * FROM {$this->table} WHERE section_name = ? AND is_enabled = 1";
        $result = $this->db->fetch($sql, [$section]);
        
        if ($result && !empty($result['settings'])) {
            $result['settings'] = json_decode($result['settings'], true);
        }
        
        return $result;
    }

    /**
     * Obtener toda la configuración activa
     */
    public function getAllActiveConfig()
    {
        $sql = "SELECT * FROM {$this->table} WHERE is_enabled = 1 ORDER BY section_name ASC";
        $results = $this->db->fetchAll($sql);
        
        $config = [];
        foreach ($results as $row) {
            $config[$row['section_name']] = [
                'id' => $row['id'],
                'section_name' => $row['section_name'],
                'settings' => !empty($row['settings']) ? json_decode($row['settings'], true) : [],
                'display_order' => $row['display_order'],
                'updated_at' => $row['updated_at']
            ];
        }
        
        return $config;
    }

    /**
     * Crear nueva configuración
     */
    public function create($data)
    {
        $sql = "INSERT INTO {$this->table} 
                (section_name, settings, display_order, is_enabled) 
                VALUES (?, ?, ?, ?)";
        
        $params = [
            $data['section_name'],
            json_encode($data['settings'] ?? []),
            $data['display_order'] ?? 0,
            $data['is_enabled'] ?? 1
        ];
        
        return $this->db->execute($sql, $params);
    }

    /**
     * Actualizar configuración
     */
    public function update($id, $data)
    {
        $sql = "UPDATE {$this->table} SET 
                section_name = ?, settings = ?, 
                display_order = ?, is_enabled = ?, updated_at = NOW() 
                WHERE id = ?";
        
        $params = [
            $data['section_name'],
            json_encode($data['settings'] ?? []),
            $data['display_order'] ?? 0,
            $data['is_enabled'] ?? 1,
            $id
        ];
        
        return $this->db->execute($sql, $params);
    }

    /**
     * Actualizar solo los datos de configuración
     */
    public function updateConfigData($section, $settings)
    {
        $sql = "UPDATE {$this->table} SET settings = ?, updated_at = NOW() WHERE section_name = ?";
        return $this->db->execute($sql, [json_encode($settings), $section]);
    }

    /**
     * Eliminar configuración
     */
    public function delete($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE id = ?";
        return $this->db->execute($sql, [$id]);
    }

    /**
     * Activar/Desactivar configuración
     */
    public function toggleStatus($id)
    {
        $sql = "UPDATE {$this->table} SET is_enabled = NOT is_enabled WHERE id = ?";
        return $this->db->execute($sql, [$id]);
    }

    /**
     * Obtener configuración del hero/banner principal
     */
    public function getHeroConfig()
    {
        return $this->getBySection('hero') ?? [
            'title' => 'STYLOFITNESS',
            'subtitle' => 'Transforma tu vida con nosotros',
            'description' => 'La revolución fitness que transformará tu vida por completo',
            'settings' => [
                'background_image' => '/images/hero-bg.jpg',
                'cta_text' => 'Comenzar Ahora',
                'cta_link' => '/store',
                'show_stats' => true
            ]
        ];
    }

    /**
     * Obtener configuración de estadísticas
     */
    public function getStatsConfig()
    {
        return $this->getBySection('stats');
    }

    /**
     * Obtener configuración de características
     */
    public function getFeaturesConfig()
    {
        return $this->getBySection('features') ?? [
            'title' => '¿Por qué elegir STYLOFITNESS?',
            'subtitle' => 'Descubre lo que nos hace únicos',
            'settings' => [
                'layout' => 'grid',
                'columns' => 3,
                'show_icons' => true,
                'background_color' => '#f8f9fa'
            ]
        ];
    }

    /**
     * Obtener configuración de call-to-action
     */
    public function getCTAConfig()
    {
        return $this->getBySection('cta');
    }

    /**
     * Obtener configuración de footer
     */
    public function getFooterConfig()
    {
        return $this->getBySection('footer');
    }

    /**
     * Obtener todas las configuraciones (para administración)
     */
    public function getAll($page = 1, $limit = 20)
    {
        $offset = ($page - 1) * $limit;
        $sql = "SELECT * FROM {$this->table} 
                ORDER BY display_order ASC, section_name ASC 
                LIMIT ? OFFSET ?";
        
        $results = $this->db->fetchAll($sql, [$limit, $offset]);
        
        // Procesar datos JSON
        foreach ($results as &$config) {
            $config['settings'] = !empty($config['settings']) ? json_decode($config['settings'], true) : [];
        }
        
        return $results;
    }

    /**
     * Contar total de configuraciones
     */
    public function getTotalCount()
    {
        $sql = "SELECT COUNT(*) as total FROM {$this->table}";
        $result = $this->db->fetch($sql);
        return $result['total'] ?? 0;
    }

    /**
     * Buscar configuraciones
     */
    public function search($term, $limit = 10)
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE section_name LIKE ? 
                ORDER BY display_order ASC 
                LIMIT ?";
        
        $searchTerm = "%{$term}%";
        $results = $this->db->fetchAll($sql, [$searchTerm, $limit]);
        
        // Procesar datos JSON
        foreach ($results as &$config) {
            $config['settings'] = !empty($config['settings']) ? json_decode($config['settings'], true) : [];
        }
        
        return $results;
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
     * Obtener configuraciones por estado
     */
    public function getByStatus($is_enabled = true)
    {
        $sql = "SELECT * FROM {$this->table} WHERE is_enabled = ? ORDER BY display_order ASC";
        $results = $this->db->fetchAll($sql, [$is_enabled ? 1 : 0]);
        
        // Procesar datos JSON
        foreach ($results as &$config) {
            $config['settings'] = !empty($config['settings']) ? json_decode($config['settings'], true) : [];
        }
        
        return $results;
    }

    /**
     * Verificar si existe una sección
     */
    public function sectionExists($section)
    {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE section_name = ?";
        $result = $this->db->fetch($sql, [$section]);
        return ($result['count'] ?? 0) > 0;
    }

    /**
     * Obtener configuración por ID
     */
    public function getById($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = ?";
        $result = $this->db->fetch($sql, [$id]);
        
        if ($result && !empty($result['settings'])) {
            $result['settings'] = json_decode($result['settings'], true);
        }
        
        return $result;
    }

    /**
     * Obtener estadísticas de configuración
     */
    public function getStats()
    {
        $sql = "SELECT 
                COUNT(*) as total_configs,
                COUNT(CASE WHEN is_enabled = 1 THEN 1 END) as active_configs,
                COUNT(DISTINCT section_name) as unique_sections,
                MAX(updated_at) as last_updated
                FROM {$this->table}";
        
        return $this->db->fetch($sql);
    }

    /**
     * Duplicar configuración
     */
    public function duplicate($id, $new_section = null)
    {
        $original = $this->getById($id);
        if (!$original) {
            return false;
        }
        
        $data = [
            'section_name' => $new_section ?? $original['section_name'] . '_copy',
            'settings' => $original['settings'],
            'display_order' => $original['display_order'],
            'is_enabled' => 0 // Crear como inactiva por defecto
        ];
        
        return $this->create($data);
    }

    /**
     * Exportar configuración
     */
    public function exportConfig($section = null)
    {
        if ($section) {
            $sql = "SELECT * FROM {$this->table} WHERE section_name = ?";
            $results = $this->db->fetchAll($sql, [$section]);
        } else {
            $sql = "SELECT * FROM {$this->table} ORDER BY section_name ASC";
            $results = $this->db->fetchAll($sql);
        }
        
        // Procesar datos JSON
        foreach ($results as &$config) {
            $config['settings'] = !empty($config['settings']) ? json_decode($config['settings'], true) : [];
        }
        
        return $results;
    }

}