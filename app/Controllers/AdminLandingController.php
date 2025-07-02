<?php

namespace StyleFitness\Controllers;

use StyleFitness\Models\SpecialOffer;
use StyleFitness\Models\WhyChooseUs;
use StyleFitness\Models\Testimonial;
use StyleFitness\Models\LandingPageConfig;
use StyleFitness\Models\GroupClass;
use StyleFitness\Helpers\AppHelper;
use Exception;

/**
 * Controlador Administrativo para Landing Page
 * Gestiona todas las secciones de la página de inicio
 */
class AdminLandingController
{
    private $specialOfferModel;
    private $whyChooseUsModel;
    private $testimonialModel;
    private $landingConfigModel;
    private $groupClassModel;

    public function __construct()
    {
        // Verificar autenticación de administrador
        if (!isset($_SESSION['admin_id'])) {
            header('Location: /admin/login');
            exit;
        }

        $this->specialOfferModel = new SpecialOffer();
        $this->whyChooseUsModel = new WhyChooseUs();
        $this->testimonialModel = new Testimonial();
        $this->landingConfigModel = new LandingPageConfig();
        $this->groupClassModel = new GroupClass();
    }

    // ==========================================
    // OFERTAS ESPECIALES
    // ==========================================

    public function specialOffers()
    {
        require_once __DIR__ . '/../Views/admin/landing/special-offers.php';
    }

    public function getSpecialOffers()
    {
        try {
            $page = $_GET['page'] ?? 1;
            $limit = $_GET['limit'] ?? 10;
            $status = $_GET['status'] ?? '';
            $search = $_GET['search'] ?? '';

            $offers = $this->specialOfferModel->getOffersPaginated($page, $limit, $status, $search);
            $stats = $this->specialOfferModel->getOffersStats();

            $this->jsonResponse([
                'success' => true,
                'data' => $offers,
                'stats' => $stats
            ]);
        } catch (Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Error al obtener ofertas: ' . $e->getMessage()
            ]);
        }
    }

    public function createSpecialOffer()
    {
        try {
            $data = $this->validateOfferData($_POST);
            
            // Manejar subida de imagen
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $data['image_url'] = $this->uploadImage($_FILES['image'], 'offers');
            }

            $offerId = $this->specialOfferModel->createOffer($data);

            $this->jsonResponse([
                'success' => true,
                'message' => 'Oferta creada exitosamente',
                'data' => ['id' => $offerId]
            ]);
        } catch (Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Error al crear oferta: ' . $e->getMessage()
            ]);
        }
    }

    public function updateSpecialOffer()
    {
        try {
            $id = $_POST['id'] ?? null;
            if (!$id) {
                throw new Exception('ID de oferta requerido');
            }

            $data = $this->validateOfferData($_POST);
            
            // Manejar subida de imagen
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $data['image_url'] = $this->uploadImage($_FILES['image'], 'offers');
            }

            $this->specialOfferModel->updateOffer($id, $data);

            $this->jsonResponse([
                'success' => true,
                'message' => 'Oferta actualizada exitosamente'
            ]);
        } catch (Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Error al actualizar oferta: ' . $e->getMessage()
            ]);
        }
    }

    public function deleteSpecialOffer()
    {
        try {
            $id = $_POST['id'] ?? null;
            if (!$id) {
                throw new Exception('ID de oferta requerido');
            }

            $this->specialOfferModel->deleteOffer($id);

            $this->jsonResponse([
                'success' => true,
                'message' => 'Oferta eliminada exitosamente'
            ]);
        } catch (Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Error al eliminar oferta: ' . $e->getMessage()
            ]);
        }
    }

    public function toggleSpecialOfferStatus()
    {
        try {
            $id = $_POST['id'] ?? null;
            if (!$id) {
                throw new Exception('ID de oferta requerido');
            }

            $this->specialOfferModel->toggleStatus($id);

            $this->jsonResponse([
                'success' => true,
                'message' => 'Estado de oferta actualizado'
            ]);
        } catch (Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Error al cambiar estado: ' . $e->getMessage()
            ]);
        }
    }

    // ==========================================
    // POR QUÉ ELEGIRNOS
    // ==========================================

    public function whyChooseUs()
    {
        require_once __DIR__ . '/../Views/admin/landing/why-choose-us.php';
    }

    public function getWhyChooseUsFeatures()
    {
        try {
            $page = $_GET['page'] ?? 1;
            $limit = $_GET['limit'] ?? 10;
            $status = $_GET['status'] ?? '';
            $search = $_GET['search'] ?? '';

            $features = $this->whyChooseUsModel->getFeaturesPaginated($page, $limit, $status, $search);
            $stats = $this->whyChooseUsModel->getFeaturesStats();

            $this->jsonResponse([
                'success' => true,
                'data' => $features,
                'stats' => $stats
            ]);
        } catch (Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Error al obtener características: ' . $e->getMessage()
            ]);
        }
    }

    public function createWhyChooseUsFeature()
    {
        try {
            $data = $this->validateFeatureData($_POST);
            $featureId = $this->whyChooseUsModel->createFeature($data);

            $this->jsonResponse([
                'success' => true,
                'message' => 'Característica creada exitosamente',
                'data' => ['id' => $featureId]
            ]);
        } catch (Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Error al crear característica: ' . $e->getMessage()
            ]);
        }
    }

    public function updateWhyChooseUsFeature()
    {
        try {
            $id = $_POST['id'] ?? null;
            if (!$id) {
                throw new Exception('ID de característica requerido');
            }

            $data = $this->validateFeatureData($_POST);
            $this->whyChooseUsModel->updateFeature($id, $data);

            $this->jsonResponse([
                'success' => true,
                'message' => 'Característica actualizada exitosamente'
            ]);
        } catch (Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Error al actualizar característica: ' . $e->getMessage()
            ]);
        }
    }

    public function deleteWhyChooseUsFeature()
    {
        try {
            $id = $_POST['id'] ?? null;
            if (!$id) {
                throw new Exception('ID de característica requerido');
            }

            $this->whyChooseUsModel->deleteFeature($id);

            $this->jsonResponse([
                'success' => true,
                'message' => 'Característica eliminada exitosamente'
            ]);
        } catch (Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Error al eliminar característica: ' . $e->getMessage()
            ]);
        }
    }

    // ==========================================
    // TESTIMONIOS
    // ==========================================

    public function testimonials()
    {
        require_once __DIR__ . '/../Views/admin/landing/testimonials.php';
    }

    public function getTestimonials()
    {
        try {
            $page = $_GET['page'] ?? 1;
            $limit = $_GET['limit'] ?? 10;
            $status = $_GET['status'] ?? '';
            $featured = $_GET['featured'] ?? '';
            $rating = $_GET['rating'] ?? '';
            $search = $_GET['search'] ?? '';

            $testimonials = $this->testimonialModel->getTestimonialsPaginated(
                $page, $limit, $status, $featured, $rating, $search
            );
            $stats = $this->testimonialModel->getTestimonialsStats();

            $this->jsonResponse([
                'success' => true,
                'data' => $testimonials,
                'stats' => $stats
            ]);
        } catch (Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Error al obtener testimonios: ' . $e->getMessage()
            ]);
        }
    }

    public function createTestimonial()
    {
        try {
            $data = $this->validateTestimonialData($_POST);
            
            // Manejar subida de imagen del cliente
            if (isset($_FILES['client_image']) && $_FILES['client_image']['error'] === UPLOAD_ERR_OK) {
                $data['client_image'] = $this->uploadImage($_FILES['client_image'], 'testimonials');
            }

            $testimonialId = $this->testimonialModel->createTestimonial($data);

            $this->jsonResponse([
                'success' => true,
                'message' => 'Testimonio creado exitosamente',
                'data' => ['id' => $testimonialId]
            ]);
        } catch (Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Error al crear testimonio: ' . $e->getMessage()
            ]);
        }
    }

    public function updateTestimonial()
    {
        try {
            $id = $_POST['id'] ?? null;
            if (!$id) {
                throw new Exception('ID de testimonio requerido');
            }

            $data = $this->validateTestimonialData($_POST);
            
            // Manejar subida de imagen del cliente
            if (isset($_FILES['client_image']) && $_FILES['client_image']['error'] === UPLOAD_ERR_OK) {
                $data['client_image'] = $this->uploadImage($_FILES['client_image'], 'testimonials');
            }

            $this->testimonialModel->updateTestimonial($id, $data);

            $this->jsonResponse([
                'success' => true,
                'message' => 'Testimonio actualizado exitosamente'
            ]);
        } catch (Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Error al actualizar testimonio: ' . $e->getMessage()
            ]);
        }
    }

    public function deleteTestimonial()
    {
        try {
            $id = $_POST['id'] ?? null;
            if (!$id) {
                throw new Exception('ID de testimonio requerido');
            }

            $this->testimonialModel->deleteTestimonial($id);

            $this->jsonResponse([
                'success' => true,
                'message' => 'Testimonio eliminado exitosamente'
            ]);
        } catch (Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Error al eliminar testimonio: ' . $e->getMessage()
            ]);
        }
    }

    // ==========================================
    // CONFIGURACIÓN GENERAL
    // ==========================================

    public function config()
    {
        require_once __DIR__ . '/../Views/admin/landing/config.php';
    }

    public function getLandingConfig()
    {
        try {
            $page = $_GET['page'] ?? 1;
            $limit = $_GET['limit'] ?? 20;
            $section = $_GET['section'] ?? '';
            $status = $_GET['status'] ?? '';
            $search = $_GET['search'] ?? '';

            $configs = $this->landingConfigModel->getConfigsPaginated(
                $page, $limit, $section, $status, $search
            );
            $stats = $this->landingConfigModel->getConfigsStats();

            $this->jsonResponse([
                'success' => true,
                'data' => $configs,
                'stats' => $stats
            ]);
        } catch (Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Error al obtener configuraciones: ' . $e->getMessage()
            ]);
        }
    }

    public function createLandingConfig()
    {
        try {
            $data = $this->validateConfigData($_POST);
            $configId = $this->landingConfigModel->createConfig($data);

            $this->jsonResponse([
                'success' => true,
                'message' => 'Configuración creada exitosamente',
                'data' => ['id' => $configId]
            ]);
        } catch (Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Error al crear configuración: ' . $e->getMessage()
            ]);
        }
    }

    public function updateLandingConfig()
    {
        try {
            $id = $_POST['id'] ?? null;
            if (!$id) {
                throw new Exception('ID de configuración requerido');
            }

            $data = $this->validateConfigData($_POST);
            $this->landingConfigModel->updateConfig($id, $data);

            $this->jsonResponse([
                'success' => true,
                'message' => 'Configuración actualizada exitosamente'
            ]);
        } catch (Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Error al actualizar configuración: ' . $e->getMessage()
            ]);
        }
    }

    public function deleteLandingConfig()
    {
        try {
            $id = $_POST['id'] ?? null;
            if (!$id) {
                throw new Exception('ID de configuración requerido');
            }

            $this->landingConfigModel->deleteConfig($id);

            $this->jsonResponse([
                'success' => true,
                'message' => 'Configuración eliminada exitosamente'
            ]);
        } catch (Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Error al eliminar configuración: ' . $e->getMessage()
            ]);
        }
    }

    public function exportLandingConfig()
    {
        try {
            $configs = $this->landingConfigModel->exportConfigs();
            
            header('Content-Type: application/json');
            header('Content-Disposition: attachment; filename="landing_config_' . date('Y-m-d_H-i-s') . '.json"');
            
            echo json_encode($configs, JSON_PRETTY_PRINT);
            exit;
        } catch (Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Error al exportar configuraciones: ' . $e->getMessage()
            ]);
        }
    }

    // ==========================================
    // MÉTODOS DE VALIDACIÓN
    // ==========================================

    private function validateOfferData($data)
    {
        $required = ['title', 'discount_percentage'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                throw new Exception("Campo requerido: $field");
            }
        }

        return [
            'title' => trim($data['title']),
            'description' => trim($data['description'] ?? ''),
            'discount_percentage' => (float)$data['discount_percentage'],
            'original_price' => !empty($data['original_price']) ? (float)$data['original_price'] : null,
            'discounted_price' => !empty($data['discounted_price']) ? (float)$data['discounted_price'] : null,
            'valid_from' => $data['valid_from'] ?? null,
            'valid_until' => $data['valid_until'] ?? null,
            'terms_conditions' => trim($data['terms_conditions'] ?? ''),
            'is_active' => isset($data['is_active']) ? 1 : 0,
            'display_order' => (int)($data['display_order'] ?? 0)
        ];
    }

    private function validateFeatureData($data)
    {
        $required = ['title'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                throw new Exception("Campo requerido: $field");
            }
        }

        $validated = [
            'title' => trim($data['title']),
            'subtitle' => trim($data['subtitle'] ?? ''),
            'description' => trim($data['description'] ?? ''),
            'icon' => trim($data['icon'] ?? ''),
            'icon_color' => trim($data['icon_color'] ?? '#ff6b35'),
            'background_gradient' => trim($data['background_gradient'] ?? ''),
            'is_active' => isset($data['is_active']) ? 1 : 0,
            'display_order' => (int)($data['display_order'] ?? 0)
        ];

        // Validar JSON para highlights y stats
        if (!empty($data['highlights'])) {
            $highlights = json_decode($data['highlights'], true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Formato JSON inválido para highlights');
            }
            $validated['highlights'] = $data['highlights'];
        }

        if (!empty($data['stats'])) {
            $stats = json_decode($data['stats'], true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Formato JSON inválido para stats');
            }
            $validated['stats'] = $data['stats'];
        }

        return $validated;
    }

    private function validateTestimonialData($data)
    {
        $required = ['client_name', 'content', 'rating'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                throw new Exception("Campo requerido: $field");
            }
        }

        $rating = (int)$data['rating'];
        if ($rating < 1 || $rating > 5) {
            throw new Exception('El rating debe estar entre 1 y 5');
        }

        return [
            'client_name' => trim($data['client_name']),
            'client_position' => trim($data['client_position'] ?? ''),
            'client_company' => trim($data['client_company'] ?? ''),
            'client_location' => trim($data['client_location'] ?? ''),
            'content' => trim($data['content']),
            'rating' => $rating,
            'testimonial_date' => $data['testimonial_date'] ?? null,
            'tags' => trim($data['tags'] ?? ''),
            'is_featured' => isset($data['is_featured']) ? 1 : 0,
            'is_active' => isset($data['is_active']) ? 1 : 0,
            'display_order' => (int)($data['display_order'] ?? 0)
        ];
    }

    private function validateConfigData($data)
    {
        $required = ['section_name', 'config_key', 'config_value', 'value_type'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                throw new Exception("Campo requerido: $field");
            }
        }

        // Validar valor según el tipo
        $value = $data['config_value'];
        $type = $data['value_type'];

        switch ($type) {
            case 'number':
                if (!is_numeric($value)) {
                    throw new Exception('El valor debe ser numérico');
                }
                break;
            case 'boolean':
                if (!in_array(strtolower($value), ['true', 'false', '1', '0'])) {
                    throw new Exception('El valor booleano debe ser true/false o 1/0');
                }
                break;
            case 'json':
                json_decode($value);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new Exception('Formato JSON inválido');
                }
                break;
            case 'url':
                if (!filter_var($value, FILTER_VALIDATE_URL)) {
                    throw new Exception('URL inválida');
                }
                break;
            case 'email':
                if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    throw new Exception('Email inválido');
                }
                break;
        }

        return [
            'section_name' => trim($data['section_name']),
            'config_key' => trim($data['config_key']),
            'config_value' => $value,
            'value_type' => $type,
            'description' => trim($data['description'] ?? ''),
            'category' => trim($data['category'] ?? ''),
            'default_value' => trim($data['default_value'] ?? ''),
            'is_active' => isset($data['is_active']) ? 1 : 0,
            'display_order' => (int)($data['display_order'] ?? 0)
        ];
    }

    // ==========================================
    // MÉTODOS AUXILIARES
    // ==========================================

    private function uploadImage($file, $folder)
    {
        $uploadDir = __DIR__ . '/../../public/uploads/' . $folder . '/';
        
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($file['type'], $allowedTypes)) {
            throw new Exception('Tipo de archivo no permitido');
        }

        if ($file['size'] > 2 * 1024 * 1024) { // 2MB
            throw new Exception('El archivo es demasiado grande (máximo 2MB)');
        }

        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '_' . time() . '.' . $extension;
        $filepath = $uploadDir . $filename;

        if (!move_uploaded_file($file['tmp_name'], $filepath)) {
            throw new Exception('Error al subir el archivo');
        }

        return 'uploads/' . $folder . '/' . $filename;
    }

    private function jsonResponse($data)
    {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}