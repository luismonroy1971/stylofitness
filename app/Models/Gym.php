<?php

namespace StyleFitness\Models;

use StyleFitness\Config\Database;
use Exception;
use PDO;

/**
 * Modelo de Gimnasios - STYLOFITNESS
 * Gestión de información de gimnasios
 */
class Gym
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Crear un nuevo gimnasio
     */
    public function create($data)
    {
        $sql = 'INSERT INTO gyms (
            name, address, phone, email, logo, theme_colors, 
            settings, operating_hours, social_media, is_active, created_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())';

        return $this->db->insert($sql, [
            $data['name'],
            $data['address'] ?? null,
            $data['phone'] ?? null,
            $data['email'] ?? null,
            $data['logo'] ?? null,
            json_encode($data['theme_colors'] ?? []),
            json_encode($data['settings'] ?? []),
            json_encode($data['operating_hours'] ?? []),
            json_encode($data['social_media'] ?? []),
            $data['is_active'] ?? 1
        ]);
    }

    /**
     * Obtener gimnasio por ID
     */
    public function getById($id)
    {
        $sql = 'SELECT * FROM gyms WHERE id = ?';
        $gym = $this->db->fetch($sql, [$id]);
        
        if ($gym) {
            $gym = $this->formatGymData($gym);
        }
        
        return $gym;
    }

    /**
     * Obtener todos los gimnasios activos
     */
    public function getActive()
    {
        $sql = 'SELECT * FROM gyms WHERE is_active = 1 ORDER BY name ASC';
        $gyms = $this->db->fetchAll($sql);
        
        return array_map([$this, 'formatGymData'], $gyms);
    }

    /**
     * Obtener el primer gimnasio activo (para sitios con un solo gimnasio)
     */
    public function getDefault()
    {
        $sql = 'SELECT * FROM gyms WHERE is_active = 1 ORDER BY id ASC LIMIT 1';
        $gym = $this->db->fetch($sql);
        
        if ($gym) {
            $gym = $this->formatGymData($gym);
        }
        
        return $gym;
    }

    /**
     * Actualizar información del gimnasio
     */
    public function update($id, $data)
    {
        $sql = 'UPDATE gyms SET 
            name = ?, address = ?, phone = ?, email = ?, logo = ?,
            theme_colors = ?, settings = ?, operating_hours = ?, 
            social_media = ?, is_active = ?, updated_at = NOW()
            WHERE id = ?';

        return $this->db->query($sql, [
            $data['name'],
            $data['address'] ?? null,
            $data['phone'] ?? null,
            $data['email'] ?? null,
            $data['logo'] ?? null,
            json_encode($data['theme_colors'] ?? []),
            json_encode($data['settings'] ?? []),
            json_encode($data['operating_hours'] ?? []),
            json_encode($data['social_media'] ?? []),
            $data['is_active'] ?? 1,
            $id
        ]);
    }

    /**
     * Eliminar gimnasio
     */
    public function delete($id)
    {
        $sql = 'DELETE FROM gyms WHERE id = ?';
        return $this->db->query($sql, [$id]);
    }

    /**
     * Cambiar estado activo/inactivo
     */
    public function toggleStatus($id)
    {
        $sql = 'UPDATE gyms SET is_active = NOT is_active, updated_at = NOW() WHERE id = ?';
        return $this->db->query($sql, [$id]);
    }

    /**
     * Obtener configuración del gimnasio
     */
    public function getSettings($gymId = null)
    {
        if ($gymId) {
            $gym = $this->getById($gymId);
        } else {
            $gym = $this->getDefault();
        }
        
        return $gym ? $gym['settings'] : [];
    }

    /**
     * Actualizar configuración del gimnasio
     */
    public function updateSettings($gymId, $settings)
    {
        $sql = 'UPDATE gyms SET settings = ?, updated_at = NOW() WHERE id = ?';
        return $this->db->query($sql, [json_encode($settings), $gymId]);
    }

    /**
     * Formatear datos del gimnasio
     */
    private function formatGymData($gym)
    {
        if (!$gym) return null;
        
        // Decodificar campos JSON
        $gym['theme_colors'] = !empty($gym['theme_colors']) ? json_decode($gym['theme_colors'], true) : [];
        $gym['settings'] = !empty($gym['settings']) ? json_decode($gym['settings'], true) : [];
        $gym['operating_hours'] = !empty($gym['operating_hours']) ? json_decode($gym['operating_hours'], true) : [];
        $gym['social_media'] = !empty($gym['social_media']) ? json_decode($gym['social_media'], true) : [];
        
        // Convertir is_active a boolean
        $gym['is_active'] = (bool) $gym['is_active'];
        
        return $gym;
    }

    /**
     * Verificar si existe al menos un gimnasio
     */
    public function hasGyms()
    {
        $sql = 'SELECT COUNT(*) as count FROM gyms WHERE is_active = 1';
        $result = $this->db->fetch($sql);
        return $result['count'] > 0;
    }

    /**
     * Obtener estadísticas básicas
     */
    public function getStats()
    {
        $sql = 'SELECT 
            COUNT(*) as total,
            SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active,
            SUM(CASE WHEN is_active = 0 THEN 1 ELSE 0 END) as inactive
            FROM gyms';
        
        return $this->db->fetch($sql);
    }

    /**
     * Alias para getActive() - compatibilidad con LandingController
     */
    public function getActiveGyms()
    {
        return $this->getActive();
    }
}