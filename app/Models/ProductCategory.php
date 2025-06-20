<?php
/**
 * Modelo de Categorías de Productos - STYLOFITNESS
 * Gestión de categorías y subcategorías de productos
 */

class ProductCategory {
    
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function create($data) {
        $sql = "INSERT INTO product_categories (
            name, slug, description, parent_id, image_url, banner_image,
            is_active, sort_order, meta_title, meta_description, created_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        
        return $this->db->insert($sql, [
            $data['name'],
            $data['slug'] ?? $this->generateSlug($data['name']),
            $data['description'] ?? '',
            $data['parent_id'] ?? null,
            $data['image_url'] ?? null,
            $data['banner_image'] ?? null,
            $data['is_active'] ?? true,
            $data['sort_order'] ?? 0,
            $data['meta_title'] ?? null,
            $data['meta_description'] ?? null
        ]);
    }
    
    public function findById($id) {
        $category = $this->db->fetch(
            "SELECT pc.*, parent.name as parent_name
             FROM product_categories pc
             LEFT JOIN product_categories parent ON pc.parent_id = parent.id
             WHERE pc.id = ?",
            [$id]
        );
        
        if ($category) {
            $category['children'] = $this->getChildren($id);
            $category['product_count'] = $this->getProductCount($id);
        }
        
        return $category;
    }
    
    public function findBySlug($slug) {
        $category = $this->db->fetch(
            "SELECT pc.*, parent.name as parent_name, parent.slug as parent_slug
             FROM product_categories pc
             LEFT JOIN product_categories parent ON pc.parent_id = parent.id
             WHERE pc.slug = ? AND pc.is_active = 1",
            [$slug]
        );
        
        if ($category) {
            $category['children'] = $this->getChildren($category['id']);
            $category['product_count'] = $this->getProductCount($category['id']);
            $category['breadcrumb'] = $this->getBreadcrumb($category['id']);
        }
        
        return $category;
    }
    
    public function update($id, $data) {
        $fields = [];
        $values = [];
        
        $allowedFields = [
            'name', 'slug', 'description', 'parent_id', 'image_url', 
            'banner_image', 'is_active', 'sort_order', 'meta_title', 'meta_description'
        ];
        
        foreach ($data as $key => $value) {
            if (in_array($key, $allowedFields)) {
                $fields[] = "{$key} = ?";
                $values[] = $value;
            }
        }
        
        if (empty($fields)) {
            return false;
        }
        
        $values[] = $id;
        
        $sql = "UPDATE product_categories SET " . implode(', ', $fields) . " WHERE id = ?";
        
        return $this->db->query($sql, $values);
    }
    
    public function getCategories($parentId = null, $includeInactive = false) {
        $where = [];
        $params = [];
        
        if ($parentId === null) {
            $where[] = "parent_id IS NULL";
        } else {
            $where[] = "parent_id = ?";
            $params[] = $parentId;
        }
        
        if (!$includeInactive) {
            $where[] = "pc.is_active = 1";
        }
        
        $sql = "SELECT pc.*, 
                COUNT(p.id) as product_count,
                (SELECT COUNT(*) FROM product_categories sub WHERE sub.parent_id = pc.id AND sub.is_active = 1) as subcategory_count
                FROM product_categories pc
                LEFT JOIN products p ON pc.id = p.category_id AND p.is_active = 1
                WHERE " . implode(' AND ', $where) . "
                GROUP BY pc.id
                ORDER BY pc.sort_order ASC, pc.name ASC";
        
        $categories = $this->db->fetchAll($sql, $params);
        
        // Obtener subcategorías para cada categoría
        foreach ($categories as &$category) {
            $category['children'] = $this->getCategories($category['id'], $includeInactive);
        }
        
        return $categories;
    }
    
    public function getAllCategories($includeInactive = false) {
        $where = $includeInactive ? "1=1" : "pc.is_active = 1";
        
        $sql = "SELECT pc.*, parent.name as parent_name,
                COUNT(p.id) as product_count
                FROM product_categories pc
                LEFT JOIN product_categories parent ON pc.parent_id = parent.id
                LEFT JOIN products p ON pc.id = p.category_id AND p.is_active = 1
                WHERE {$where}
                GROUP BY pc.id
                ORDER BY pc.sort_order ASC, pc.name ASC";
        
        return $this->db->fetchAll($sql);
    }
    
    public function getMainCategories() {
        return $this->getCategories(null, false);
    }
    
    public function getChildren($parentId) {
        return $this->db->fetchAll(
            "SELECT pc.*, COUNT(p.id) as product_count
             FROM product_categories pc
             LEFT JOIN products p ON pc.id = p.category_id AND p.is_active = 1
             WHERE pc.parent_id = ? AND pc.is_active = 1
             GROUP BY pc.id
             ORDER BY pc.sort_order ASC, pc.name ASC",
            [$parentId]
        );
    }
    
    public function getProductCount($categoryId, $includeSubcategories = true) {
        if (!$includeSubcategories) {
            return $this->db->count(
                "SELECT COUNT(*) FROM products WHERE category_id = ? AND products.is_active = 1",
                [$categoryId]
            );
        }
        
        // Incluir productos de subcategorías
        $categoryIds = $this->getAllCategoryIds($categoryId);
        $placeholders = str_repeat('?,', count($categoryIds) - 1) . '?';
        
        return $this->db->count(
            "SELECT COUNT(*) FROM products WHERE category_id IN ({$placeholders}) AND products.is_active = 1",
            $categoryIds
        );
    }
    
    public function getBreadcrumb($categoryId) {
        $breadcrumb = [];
        $category = $this->findById($categoryId);
        
        while ($category) {
            array_unshift($breadcrumb, [
                'id' => $category['id'],
                'name' => $category['name'],
                'slug' => $category['slug']
            ]);
            
            if ($category['parent_id']) {
                $category = $this->findById($category['parent_id']);
            } else {
                $category = null;
            }
        }
        
        return $breadcrumb;
    }
    
    public function getCategoryTree() {
        return $this->buildTree($this->getAllCategories(true));
    }
    
    public function getCategoryPath($categoryId) {
        $path = [];
        $category = $this->findById($categoryId);
        
        while ($category) {
            array_unshift($path, $category['name']);
            
            if ($category['parent_id']) {
                $category = $this->findById($category['parent_id']);
            } else {
                $category = null;
            }
        }
        
        return implode(' > ', $path);
    }
    
    public function getPopularCategories($limit = 6) {
        return $this->db->fetchAll(
            "SELECT pc.*, COUNT(p.id) as product_count
             FROM product_categories pc
             JOIN products p ON pc.id = p.category_id
             WHERE pc.is_active = 1 AND p.is_active = 1
             GROUP BY pc.id
             HAVING product_count > 0
             ORDER BY product_count DESC
             LIMIT ?",
            [$limit]
        );
    }
    
    public function getFeaturedCategories() {
        return $this->db->fetchAll(
            "SELECT pc.*, COUNT(p.id) as product_count
             FROM product_categories pc
             LEFT JOIN products p ON pc.id = p.category_id AND p.is_active = 1
             WHERE pc.is_active = 1 AND pc.image_url IS NOT NULL
             GROUP BY pc.id
             ORDER BY pc.sort_order ASC
             LIMIT 8"
        );
    }
    
    public function searchCategories($query) {
        $searchTerm = '%' . $query . '%';
        
        return $this->db->fetchAll(
            "SELECT pc.*, COUNT(p.id) as product_count
             FROM product_categories pc
             LEFT JOIN products p ON pc.id = p.category_id AND p.is_active = 1
             WHERE pc.is_active = 1 AND (pc.name LIKE ? OR pc.description LIKE ?)
             GROUP BY pc.id
             ORDER BY pc.name ASC",
            [$searchTerm, $searchTerm]
        );
    }
    
    public function reorderCategories($categoryIds) {
        $order = 1;
        foreach ($categoryIds as $categoryId) {
            $this->update($categoryId, ['sort_order' => $order]);
            $order++;
        }
        return true;
    }
    
    public function moveCategory($categoryId, $newParentId) {
        // Verificar que no se está moviendo a sí mismo o a un descendiente
        if ($this->isDescendant($categoryId, $newParentId)) {
            return false;
        }
        
        return $this->update($categoryId, ['parent_id' => $newParentId]);
    }
    
    public function validateCategory($data) {
        $errors = [];
        
        if (empty($data['name'])) {
            $errors['name'] = 'El nombre es obligatorio';
        }
        
        if (empty($data['slug'])) {
            $data['slug'] = $this->generateSlug($data['name']);
        } elseif ($this->slugExists($data['slug'], $data['id'] ?? null)) {
            $errors['slug'] = 'Este slug ya está en uso';
        }
        
        if (!empty($data['parent_id'])) {
            $parent = $this->findById($data['parent_id']);
            if (!$parent) {
                $errors['parent_id'] = 'La categoría padre no existe';
            } elseif (isset($data['id']) && $this->isDescendant($data['parent_id'], $data['id'])) {
                $errors['parent_id'] = 'No se puede establecer un descendiente como padre';
            }
        }
        
        return $errors;
    }
    
    public function delete($id) {
        $category = $this->findById($id);
        if (!$category) {
            return false;
        }
        
        // Verificar si tiene productos
        $productCount = $this->getProductCount($id, false);
        if ($productCount > 0) {
            return false; // No eliminar categorías con productos
        }
        
        // Verificar si tiene subcategorías
        $children = $this->getChildren($id);
        if (!empty($children)) {
            return false; // No eliminar categorías con subcategorías
        }
        
        return $this->db->query("DELETE FROM product_categories WHERE id = ?", [$id]);
    }
    
    public function deactivate($id) {
        return $this->update($id, ['is_active' => false]);
    }
    
    private function getAllCategoryIds($categoryId) {
        $ids = [$categoryId];
        $children = $this->getChildren($categoryId);
        
        foreach ($children as $child) {
            $ids = array_merge($ids, $this->getAllCategoryIds($child['id']));
        }
        
        return $ids;
    }
    
    private function buildTree($categories, $parentId = null) {
        $tree = [];
        
        foreach ($categories as $category) {
            if ($category['parent_id'] == $parentId) {
                $category['children'] = $this->buildTree($categories, $category['id']);
                $tree[] = $category;
            }
        }
        
        return $tree;
    }
    
    private function isDescendant($ancestorId, $descendantId) {
        if ($ancestorId === $descendantId) {
            return true;
        }
        
        $children = $this->getChildren($ancestorId);
        
        foreach ($children as $child) {
            if ($this->isDescendant($child['id'], $descendantId)) {
                return true;
            }
        }
        
        return false;
    }
    
    private function generateSlug($name) {
        $slug = strtolower(trim($name));
        $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
        $slug = preg_replace('/-+/', '-', $slug);
        $slug = trim($slug, '-');
        
        // Verificar unicidad
        $baseSlug = $slug;
        $counter = 1;
        
        while ($this->slugExists($slug)) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }
        
        return $slug;
    }
    
    private function slugExists($slug, $excludeId = null) {
        $sql = "SELECT COUNT(*) FROM product_categories WHERE slug = ?";
        $params = [$slug];
        
        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }
        
        return $this->db->count($sql, $params) > 0;
    }
    
    /**
     * Obtiene el slug de una categoría por su ID
     * @param int $id ID de la categoría
     * @return string Slug de la categoría o cadena vacía si no existe
     */
    public function getSlugById($id) {
        $category = $this->db->fetch(
            "SELECT slug FROM product_categories WHERE id = ?",
            [$id]
        );
        
        return $category ? $category['slug'] : '';
    }
}
