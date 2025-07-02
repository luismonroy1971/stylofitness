<?php

namespace StyleFitness\Models;

use StyleFitness\Config\Database;
use PDO;
use Exception;

/**
 * Modelo para gestionar ofertas especiales
 * STYLOFITNESS - Sistema de Gestión de Contenido
 */
class SpecialOffer
{
    private $db;
    private $table = 'special_offers';

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Obtener todas las ofertas activas
     */
    public function getActiveOffers($limit = null)
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE is_active = 1 
                AND (start_date IS NULL OR start_date <= NOW()) 
                AND (end_date IS NULL OR end_date >= NOW()) 
                ORDER BY display_order ASC, created_at DESC";
        
        if ($limit) {
            $sql .= " LIMIT ?";
            return $this->db->fetchAll($sql, [$limit]);
        }
        
        return $this->db->fetchAll($sql);
    }

    /**
     * Obtener oferta por ID
     */
    public function getById($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = ?";
        return $this->db->fetch($sql, [$id]);
    }

    /**
     * Crear nueva oferta
     */
    public function create($data)
    {
        $sql = "INSERT INTO {$this->table} 
                (title, subtitle, description, discount_percentage, discount_amount, 
                 image, background_color, text_color, button_text, button_link, 
                 start_date, end_date, is_active, display_order) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $params = [
            $data['title'],
            $data['subtitle'] ?? null,
            $data['description'] ?? null,
            $data['discount_percentage'] ?? null,
            $data['discount_amount'] ?? null,
            $data['image'] ?? null,
            $data['background_color'] ?? '#ff6b35',
            $data['text_color'] ?? '#ffffff',
            $data['button_text'] ?? 'Ver Oferta',
            $data['button_link'] ?? null,
            $data['start_date'] ?? null,
            $data['end_date'] ?? null,
            $data['is_active'] ?? 1,
            $data['display_order'] ?? 0
        ];
        
        return $this->db->execute($sql, $params);
    }

    /**
     * Actualizar oferta
     */
    public function update($id, $data)
    {
        $sql = "UPDATE {$this->table} SET 
                title = ?, subtitle = ?, description = ?, 
                discount_percentage = ?, discount_amount = ?, image = ?, 
                background_color = ?, text_color = ?, button_text = ?, 
                button_link = ?, start_date = ?, end_date = ?, 
                is_active = ?, display_order = ?, updated_at = NOW() 
                WHERE id = ?";
        
        $params = [
            $data['title'],
            $data['subtitle'] ?? null,
            $data['description'] ?? null,
            $data['discount_percentage'] ?? null,
            $data['discount_amount'] ?? null,
            $data['image'] ?? null,
            $data['background_color'] ?? '#ff6b35',
            $data['text_color'] ?? '#ffffff',
            $data['button_text'] ?? 'Ver Oferta',
            $data['button_link'] ?? null,
            $data['start_date'] ?? null,
            $data['end_date'] ?? null,
            $data['is_active'] ?? 1,
            $data['display_order'] ?? 0,
            $id
        ];
        
        return $this->db->execute($sql, $params);
    }

    /**
     * Eliminar oferta
     */
    public function delete($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE id = ?";
        return $this->db->execute($sql, [$id]);
    }

    /**
     * Activar/Desactivar oferta
     */
    public function toggleStatus($id)
    {
        $sql = "UPDATE {$this->table} SET is_active = NOT is_active WHERE id = ?";
        return $this->db->execute($sql, [$id]);
    }

    /**
     * Obtener todas las ofertas (para administración)
     */
    public function getAll($page = 1, $limit = 20)
    {
        $offset = ($page - 1) * $limit;
        $sql = "SELECT * FROM {$this->table} 
                ORDER BY display_order ASC, created_at DESC 
                LIMIT ? OFFSET ?";
        
        return $this->db->fetchAll($sql, [$limit, $offset]);
    }

    /**
     * Contar total de ofertas
     */
    public function getTotalCount()
    {
        $sql = "SELECT COUNT(*) as total FROM {$this->table}";
        $result = $this->db->fetch($sql);
        return $result['total'] ?? 0;
    }

    /**
     * Verificar si una oferta está vigente
     */
    public function isOfferValid($id)
    {
        $sql = "SELECT COUNT(*) as valid FROM {$this->table} 
                WHERE id = ? AND is_active = 1 
                AND (start_date IS NULL OR start_date <= NOW()) 
                AND (end_date IS NULL OR end_date >= NOW())";
        
        $result = $this->db->fetch($sql, [$id]);
        return $result['valid'] > 0;
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
     * Obtener ofertas próximas a vencer
     */
    public function getExpiringOffers($days = 7)
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE is_active = 1 
                AND end_date IS NOT NULL 
                AND end_date BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL ? DAY) 
                ORDER BY end_date ASC";
        
        return $this->db->fetchAll($sql, [$days]);
    }

    /**
     * Obtener ofertas destacadas
     */
    public function getFeaturedOffers($limit = 3)
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE is_active = 1 
                AND (start_date IS NULL OR start_date <= NOW()) 
                AND (end_date IS NULL OR end_date >= NOW()) 
                AND display_order > 0
                ORDER BY display_order ASC, created_at DESC
                LIMIT ?";
        
        return $this->db->fetchAll($sql, [$limit]);
    }
}