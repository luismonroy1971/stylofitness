<?php

namespace StyleFitness\Models;

use StyleFitness\Config\Database;
use PDO;
use Exception;

/**
 * Modelo para gestionar testimonios
 * STYLOFITNESS - Sistema de Gestión de Contenido
 */
class Testimonial
{
    private $db;
    private $table = 'testimonials';

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Obtener testimonios activos
     */
    public function getActiveTestimonials($limit = null, $featured_only = false)
    {
        $sql = "SELECT * FROM {$this->table} WHERE is_active = 1";
        
        if ($featured_only) {
            $sql .= " AND is_featured = 1";
        }
        
        $sql .= " ORDER BY display_order ASC, created_at DESC";
        
        if ($limit) {
            $sql .= " LIMIT ?";
            return $this->db->fetchAll($sql, [$limit]);
        }
        
        return $this->db->fetchAll($sql);
    }

    /**
     * Obtener testimonios destacados para la landing
     */
    public function getFeaturedTestimonials($limit = 4)
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE is_active = 1 AND is_featured = 1 
                ORDER BY display_order ASC, created_at DESC 
                LIMIT ?";
        
        $results = $this->db->fetchAll($sql, [$limit]);
        
        // Procesar datos adicionales
        foreach ($results as &$testimonial) {
            $testimonial['social_proof'] = !empty($testimonial['social_proof']) ? json_decode($testimonial['social_proof'], true) : [];
            $testimonial['stars'] = $this->generateStarsArray($testimonial['rating']);
        }
        
        return $results;
    }

    /**
     * Obtener testimonial por ID
     */
    public function getById($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = ?";
        $result = $this->db->fetch($sql, [$id]);
        
        if ($result) {
            $result['social_proof'] = !empty($result['social_proof']) ? json_decode($result['social_proof'], true) : [];
            $result['stars'] = $this->generateStarsArray($result['rating']);
        }
        
        return $result;
    }

    /**
     * Crear nuevo testimonial
     */
    public function create($data)
    {
        $sql = "INSERT INTO {$this->table} 
                (name, role, company, image, testimonial_text, rating, 
                 location, date_given, is_featured, is_active, display_order, social_proof) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $params = [
            $data['name'],
            $data['role'] ?? null,
            $data['company'] ?? null,
            $data['image'] ?? null,
            $data['testimonial_text'],
            $data['rating'] ?? 5,
            $data['location'] ?? null,
            $data['date_given'] ?? date('Y-m-d'),
            $data['is_featured'] ?? 0,
            $data['is_active'] ?? 1,
            $data['display_order'] ?? 0,
            json_encode($data['social_proof'] ?? [])
        ];
        
        return $this->db->execute($sql, $params);
    }

    /**
     * Actualizar testimonial
     */
    public function update($id, $data)
    {
        $sql = "UPDATE {$this->table} SET 
                name = ?, role = ?, company = ?, image = ?, testimonial_text = ?, 
                rating = ?, location = ?, date_given = ?, is_featured = ?, 
                is_active = ?, display_order = ?, social_proof = ?, updated_at = NOW() 
                WHERE id = ?";
        
        $params = [
            $data['name'],
            $data['role'] ?? null,
            $data['company'] ?? null,
            $data['image'] ?? null,
            $data['testimonial_text'],
            $data['rating'] ?? 5,
            $data['location'] ?? null,
            $data['date_given'] ?? date('Y-m-d'),
            $data['is_featured'] ?? 0,
            $data['is_active'] ?? 1,
            $data['display_order'] ?? 0,
            json_encode($data['social_proof'] ?? []),
            $id
        ];
        
        return $this->db->execute($sql, $params);
    }

    /**
     * Eliminar testimonial
     */
    public function delete($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE id = ?";
        return $this->db->execute($sql, [$id]);
    }

    /**
     * Activar/Desactivar testimonial
     */
    public function toggleStatus($id)
    {
        $sql = "UPDATE {$this->table} SET is_active = NOT is_active WHERE id = ?";
        return $this->db->execute($sql, [$id]);
    }

    /**
     * Marcar/Desmarcar como destacado
     */
    public function toggleFeatured($id)
    {
        $sql = "UPDATE {$this->table} SET is_featured = NOT is_featured WHERE id = ?";
        return $this->db->execute($sql, [$id]);
    }

    /**
     * Obtener todos los testimonials (para administración)
     */
    public function getAll($page = 1, $limit = 20)
    {
        $offset = ($page - 1) * $limit;
        $sql = "SELECT * FROM {$this->table} 
                ORDER BY is_featured DESC, display_order ASC, created_at DESC 
                LIMIT ? OFFSET ?";
        
        $results = $this->db->fetchAll($sql, [$limit, $offset]);
        
        // Procesar datos adicionales
        foreach ($results as &$testimonial) {
            $testimonial['social_proof'] = !empty($testimonial['social_proof']) ? json_decode($testimonial['social_proof'], true) : [];
            $testimonial['stars'] = $this->generateStarsArray($testimonial['rating']);
        }
        
        return $results;
    }

    /**
     * Contar total de testimonials
     */
    public function getTotalCount()
    {
        $sql = "SELECT COUNT(*) as total FROM {$this->table}";
        $result = $this->db->fetch($sql);
        return $result['total'] ?? 0;
    }

    /**
     * Obtener estadísticas de testimonials
     */
    public function getStats()
    {
        $sql = "SELECT 
                COUNT(*) as total,
                COUNT(CASE WHEN is_active = 1 THEN 1 END) as active,
                COUNT(CASE WHEN is_featured = 1 THEN 1 END) as featured,
                AVG(rating) as average_rating,
                COUNT(CASE WHEN rating = 5 THEN 1 END) as five_stars
                FROM {$this->table}";
        
        return $this->db->fetch($sql);
    }

    /**
     * Buscar testimonials
     */
    public function search($term, $limit = 10)
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE (name LIKE ? OR role LIKE ? OR testimonial_text LIKE ? OR location LIKE ?) 
                AND is_active = 1 
                ORDER BY is_featured DESC, display_order ASC 
                LIMIT ?";
        
        $searchTerm = "%{$term}%";
        $results = $this->db->fetchAll($sql, [$searchTerm, $searchTerm, $searchTerm, $searchTerm, $limit]);
        
        // Procesar datos adicionales
        foreach ($results as &$testimonial) {
            $testimonial['social_proof'] = !empty($testimonial['social_proof']) ? json_decode($testimonial['social_proof'], true) : [];
            $testimonial['stars'] = $this->generateStarsArray($testimonial['rating']);
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
     * Obtener testimonials por rating
     */
    public function getByRating($rating, $limit = null)
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE rating = ? AND is_active = 1 
                ORDER BY display_order ASC, created_at DESC";
        
        if ($limit) {
            $sql .= " LIMIT ?";
            return $this->db->fetchAll($sql, [$rating, $limit]);
        }
        
        return $this->db->fetchAll($sql, [$rating]);
    }

    /**
     * Generar array de estrellas para mostrar rating
     */
    private function generateStarsArray($rating)
    {
        $stars = [];
        for ($i = 1; $i <= 5; $i++) {
            $stars[] = $i <= $rating ? 'filled' : 'empty';
        }
        return $stars;
    }

    /**
     * Obtener testimonials recientes
     */
    public function getRecent($days = 30, $limit = 5)
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE is_active = 1 
                AND created_at >= DATE_SUB(NOW(), INTERVAL ? DAY) 
                ORDER BY created_at DESC 
                LIMIT ?";
        
        return $this->db->fetchAll($sql, [$days, $limit]);
    }
}