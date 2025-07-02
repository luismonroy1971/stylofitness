<?php

namespace StyleFitness\Middleware;

class ValidationMiddleware
{
    /**
     * Validate and sanitize input data
     *
     * @param array $rules
     * @param callable $next
     * @return mixed
     */
    public function validate(array $rules, callable $next)
    {
        $data = $this->getInputData();
        $errors = [];

        foreach ($rules as $field => $rule) {
            $value = $data[$field] ?? null;
            $fieldRules = explode('|', $rule);

            foreach ($fieldRules as $singleRule) {
                $ruleParts = explode(':', $singleRule);
                $ruleName = $ruleParts[0];
                $ruleValue = $ruleParts[1] ?? null;

                if (!$this->validateField($value, $ruleName, $ruleValue)) {
                    $errors[$field][] = $this->getErrorMessage($field, $ruleName, $ruleValue);
                }
            }
        }

        if (!empty($errors)) {
            $_SESSION['validation_errors'] = $errors;
            $_SESSION['old_input'] = $data;
            return false;
        }

        // Sanitize data
        $sanitizedData = $this->sanitizeData($data);
        $_REQUEST = array_merge($_REQUEST, $sanitizedData);

        return $next();
    }

    /**
     * Get input data from request
     *
     * @return array
     */
    private function getInputData(): array
    {
        $data = [];

        // Get POST data
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = array_merge($data, $_POST);
        }

        // Get GET data
        $data = array_merge($data, $_GET);

        return $data;
    }

    /**
     * Validate individual field
     *
     * @param mixed $value
     * @param string $rule
     * @param mixed $ruleValue
     * @return bool
     */
    private function validateField($value, string $rule, $ruleValue = null): bool
    {
        switch ($rule) {
            case 'required':
                return !empty($value);

            case 'email':
                return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;

            case 'min':
                return strlen($value) >= (int)$ruleValue;

            case 'max':
                return strlen($value) <= (int)$ruleValue;

            case 'numeric':
                return is_numeric($value);

            case 'integer':
                return filter_var($value, FILTER_VALIDATE_INT) !== false;

            case 'url':
                return filter_var($value, FILTER_VALIDATE_URL) !== false;

            case 'alpha':
                return ctype_alpha($value);

            case 'alphanumeric':
                return ctype_alnum($value);

            case 'confirmed':
                $confirmField = $ruleValue ?: $value . '_confirmation';
                return $value === ($_POST[$confirmField] ?? null);

            default:
                return true;
        }
    }

    /**
     * Get error message for validation rule
     *
     * @param string $field
     * @param string $rule
     * @param mixed $ruleValue
     * @return string
     */
    private function getErrorMessage(string $field, string $rule, $ruleValue = null): string
    {
        $messages = [
            'required' => "El campo {$field} es obligatorio.",
            'email' => "El campo {$field} debe ser un email válido.",
            'min' => "El campo {$field} debe tener al menos {$ruleValue} caracteres.",
            'max' => "El campo {$field} no puede tener más de {$ruleValue} caracteres.",
            'numeric' => "El campo {$field} debe ser numérico.",
            'integer' => "El campo {$field} debe ser un número entero.",
            'url' => "El campo {$field} debe ser una URL válida.",
            'alpha' => "El campo {$field} solo puede contener letras.",
            'alphanumeric' => "El campo {$field} solo puede contener letras y números.",
            'confirmed' => "La confirmación del campo {$field} no coincide.",
        ];

        return $messages[$rule] ?? "El campo {$field} no es válido.";
    }

    /**
     * Sanitize input data
     *
     * @param array $data
     * @return array
     */
    private function sanitizeData(array $data): array
    {
        $sanitized = [];

        foreach ($data as $key => $value) {
            if (is_string($value)) {
                // Remove HTML tags and encode special characters
                $sanitized[$key] = htmlspecialchars(strip_tags(trim($value)), ENT_QUOTES, 'UTF-8');
            } elseif (is_array($value)) {
                $sanitized[$key] = $this->sanitizeData($value);
            } else {
                $sanitized[$key] = $value;
            }
        }

        return $sanitized;
    }

    /**
     * CSRF protection middleware
     *
     * @param callable $next
     * @return mixed
     */
    public function csrfProtection(callable $next)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST['csrf_token'] ?? '';
            $sessionToken = $_SESSION['csrf_token'] ?? '';

            if (!hash_equals($sessionToken, $token)) {
                http_response_code(403);
                die('CSRF token mismatch');
            }
        }

        return $next();
    }

    /**
     * Generate CSRF token
     *
     * @return string
     */
    public static function generateCsrfToken(): string
    {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['csrf_token'];
    }
}
