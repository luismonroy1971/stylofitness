<?php

namespace StyleFitness\Middleware;

use StyleFitness\Helpers\AppHelper;

class AuthMiddleware
{
    /**
     * Handle authentication middleware
     *
     * @param callable $next
     * @return mixed
     */
    public function handle(callable $next)
    {
        // Check if user is authenticated
        if (!$this->isAuthenticated()) {
            // Redirect to login page
            AppHelper::redirect('/auth/login');
            return;
        }

        // Continue to next middleware or controller
        return $next();
    }

    /**
     * Check if user is authenticated
     *
     * @return bool
     */
    private function isAuthenticated(): bool
    {
        return isset($_SESSION['user_id']) && $_SESSION['user_id'] !== null;
    }

    /**
     * Check if user has specific role
     *
     * @param string $role
     * @return bool
     */
    public function hasRole(string $role): bool
    {
        if (!$this->isAuthenticated()) {
            return false;
        }

        $userRole = $_SESSION['user_role'] ?? null;
        return $userRole === $role;
    }

    /**
     * Check if user has any of the specified roles
     *
     * @param array $roles
     * @return bool
     */
    public function hasAnyRole(array $roles): bool
    {
        if (!$this->isAuthenticated()) {
            return false;
        }

        $userRole = $_SESSION['user_role'] ?? null;
        return in_array($userRole, $roles);
    }

    /**
     * Admin middleware
     *
     * @param callable $next
     * @return mixed
     */
    public function adminOnly(callable $next)
    {
        if (!$this->hasRole('admin')) {
            AppHelper::redirect('/auth/login?error=unauthorized');
            return;
        }

        return $next();
    }

    /**
     * Guest middleware (for login/register pages)
     *
     * @param callable $next
     * @return mixed
     */
    public function guestOnly(callable $next)
    {
        if ($this->isAuthenticated()) {
            AppHelper::redirect('/dashboard');
            return;
        }

        return $next();
    }
}
