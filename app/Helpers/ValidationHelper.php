<?php

namespace StyleFitness\Helpers;

use StyleFitness\Config\Database;
use DateTime;

/**
 * Helper de Validación - STYLOFITNESS
 * Funciones auxiliares para validación de datos
 */

class ValidationHelper
{
    /**
     * Validar email
     */
    public static function validateEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Validar teléfono
     */
    public static function validatePhone(string $phone): int|false
    {
        $pattern = '/^[+]?[\d\s\-\(\)]{8,15}$/';
        return preg_match($pattern, $phone);
    }

    /**
     * Validar contraseña fuerte
     */
    public static function validateStrongPassword(string $password): bool
    {
        if (strlen($password) < 8) {
            return false;
        }

        // Al menos una mayúscula, una minúscula y un número
        if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/', $password)) {
            return false;
        }

        return true;
    }

    /**
     * Validar URL
     */
    public static function validateUrl(string $url): bool
    {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }

    /**
     * Validar fecha
     */
    public static function validateDate(string $date, string $format = 'Y-m-d'): bool
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }

    /**
     * Validar rango numérico
     */
    public static function validateNumericRange(float|int $value, float|int $min, float|int $max): bool
    {
        return is_numeric($value) && $value >= $min && $value <= $max;
    }

    /**
     * Validar SKU de producto
     */
    public static function validateSKU(string $sku): int|false
    {
        $pattern = '/^[A-Z0-9]{3,20}$/';
        return preg_match($pattern, strtoupper($sku));
    }

    /**
     * Validar slug
     */
    public static function validateSlug(string $slug): int|false
    {
        $pattern = '/^[a-z0-9]+(?:-[a-z0-9]+)*$/';
        return preg_match($pattern, $slug);
    }

    /**
     * Sanitizar entrada HTML
     */
    public static function sanitizeHtml(string $html, string $allowedTags = '<p><br><strong><em><ul><ol><li>'): string
    {
        return strip_tags($html, $allowedTags);
    }

    /**
     * Validar archivo de imagen
     */
    public static function validateImageFile(array $file): array
    {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $maxSize = 5 * 1024 * 1024; // 5MB

        if (!in_array($file['type'], $allowedTypes)) {
            return ['error' => 'Tipo de archivo no permitido'];
        }

        if ($file['size'] > $maxSize) {
            return ['error' => 'El archivo es demasiado grande (máximo 5MB)'];
        }

        return ['valid' => true];
    }

    /**
     * Validar archivo de video
     */
    public static function validateVideoFile(array $file): array
    {
        $allowedTypes = ['video/mp4', 'video/quicktime', 'video/x-msvideo'];
        $maxSize = 50 * 1024 * 1024; // 50MB

        if (!in_array($file['type'], $allowedTypes)) {
            return ['error' => 'Tipo de archivo no permitido'];
        }

        if ($file['size'] > $maxSize) {
            return ['error' => 'El archivo es demasiado grande (máximo 50MB)'];
        }

        return ['valid' => true];
    }

    /**
     * Validar datos de tarjeta de crédito (básico)
     */
    public static function validateCreditCard(string $number): bool
    {
        // Eliminar espacios y guiones
        $number = preg_replace('/[\s\-]/', '', $number);

        // Verificar que solo contenga números
        if (!ctype_digit($number)) {
            return false;
        }

        // Verificar longitud
        $length = strlen($number);
        if ($length < 13 || $length > 19) {
            return false;
        }

        // Algoritmo de Luhn
        $sum = 0;
        $alternate = false;

        for ($i = $length - 1; $i >= 0; $i--) {
            $digit = (int)$number[$i];

            if ($alternate) {
                $digit *= 2;
                if ($digit > 9) {
                    $digit = ($digit % 10) + 1;
                }
            }

            $sum += $digit;
            $alternate = !$alternate;
        }

        return ($sum % 10) === 0;
    }

    /**
     * Validar código postal peruano
     */
    public static function validatePeruvianPostalCode(string $code): int|false
    {
        $pattern = '/^[0-9]{5}$/';
        return preg_match($pattern, $code);
    }

    /**
     * Validar RUC peruano
     */
    public static function validatePeruvianRUC(string $ruc): bool
    {
        if (strlen($ruc) !== 11 || !ctype_digit($ruc)) {
            return false;
        }

        $factor = [5, 4, 3, 2, 7, 6, 5, 4, 3, 2];
        $sum = 0;

        for ($i = 0; $i < 10; $i++) {
            $sum += (int)$ruc[$i] * $factor[$i];
        }

        $remainder = $sum % 11;
        $checkDigit = $remainder < 2 ? $remainder : 11 - $remainder;

        return (int)$ruc[10] === $checkDigit;
    }

    /**
     * Validar DNI peruano
     */
    public static function validatePeruvianDNI(string $dni): bool
    {
        return strlen($dni) === 8 && ctype_digit($dni);
    }

    /**
     * Validar horario (formato HH:MM)
     */
    public static function validateTime(string $time): int|false
    {
        $pattern = '/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/';
        return preg_match($pattern, $time);
    }

    /**
     * Validar JSON
     */
    public static function validateJson(string $json): bool
    {
        json_decode($json);
        return json_last_error() === JSON_ERROR_NONE;
    }

    /**
     * Generar reglas de validación para formularios
     */
    public static function getValidationRules(string $formType): array
    {
        $rules = [
            'user_registration' => [
                'first_name' => ['required', 'string', 'max:100'],
                'last_name' => ['required', 'string', 'max:100'],
                'email' => ['required', 'email', 'unique:users'],
                'password' => ['required', 'min:8', 'strong_password'],
                'phone' => ['optional', 'phone'],
            ],
            'product' => [
                'name' => ['required', 'string', 'max:255'],
                'sku' => ['required', 'sku', 'unique:products'],
                'price' => ['required', 'numeric', 'min:0'],
                'stock_quantity' => ['required', 'integer', 'min:0'],
                'weight' => ['optional', 'numeric', 'min:0'],
            ],
            'routine' => [
                'name' => ['required', 'string', 'max:255'],
                'objective' => ['required', 'in:weight_loss,muscle_gain,strength,endurance,flexibility'],
                'difficulty_level' => ['required', 'in:beginner,intermediate,advanced'],
                'duration_weeks' => ['required', 'integer', 'min:1', 'max:52'],
                'sessions_per_week' => ['required', 'integer', 'min:1', 'max:7'],
            ],
        ];

        return $rules[$formType] ?? [];
    }

    /**
     * Ejecutar validación con reglas
     */
    public static function validate(array $data, array $rules): array
    {
        $errors = [];

        foreach ($rules as $field => $fieldRules) {
            $value = $data[$field] ?? null;

            foreach ($fieldRules as $rule) {
                $ruleParts = explode(':', $rule);
                $ruleName = $ruleParts[0];
                $ruleParam = $ruleParts[1] ?? null;

                $error = self::validateRule($field, $value, $ruleName, $ruleParam, $data);
                if ($error) {
                    $errors[$field] = $error;
                    break; // Solo mostrar el primer error por campo
                }
            }
        }

        return $errors;
    }

    private static function validateRule(string $field, mixed $value, string $rule, ?string $param, array $allData): ?string
    {
        switch ($rule) {
            case 'required':
                if (empty($value)) {
                    return "El campo {$field} es obligatorio";
                }
                break;

            case 'optional':
                return null; // Siempre válido

            case 'string':
                if (!is_string($value)) {
                    return "El campo {$field} debe ser texto";
                }
                break;

            case 'integer':
                if (!filter_var($value, FILTER_VALIDATE_INT)) {
                    return "El campo {$field} debe ser un número entero";
                }
                break;

            case 'numeric':
                if (!is_numeric($value)) {
                    return "El campo {$field} debe ser numérico";
                }
                break;

            case 'email':
                if (!self::validateEmail($value)) {
                    return "El campo {$field} debe ser un email válido";
                }
                break;

            case 'phone':
                if (!empty($value) && !self::validatePhone($value)) {
                    return "El campo {$field} debe ser un teléfono válido";
                }
                break;

            case 'strong_password':
                if (!self::validateStrongPassword($value)) {
                    return 'La contraseña debe tener al menos 8 caracteres, una mayúscula, una minúscula y un número';
                }
                break;

            case 'sku':
                if (!self::validateSKU($value)) {
                    return 'El SKU debe contener solo letras y números (3-20 caracteres)';
                }
                break;

            case 'max':
                if (strlen($value) > (int)$param) {
                    return "El campo {$field} no puede tener más de {$param} caracteres";
                }
                break;

            case 'min':
                if (is_numeric($value) && $value < (float)$param) {
                    return "El campo {$field} debe ser mayor o igual a {$param}";
                } elseif (is_string($value) && strlen($value) < (int)$param) {
                    return "El campo {$field} debe tener al menos {$param} caracteres";
                }
                break;

            case 'in':
                $allowedValues = explode(',', $param);
                if (!in_array($value, $allowedValues)) {
                    return "El campo {$field} debe ser uno de: " . implode(', ', $allowedValues);
                }
                break;

            case 'unique':
                // Verificar unicidad en base de datos
                $table = $param;
                $db = Database::getInstance();
                $exists = $db->count("SELECT COUNT(*) FROM {$table} WHERE {$field} = ?", [$value]);
                if ($exists > 0) {
                    return "El {$field} ya está en uso";
                }
                break;
        }

        return null;
    }
}
