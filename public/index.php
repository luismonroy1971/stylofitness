<?php
/**
 * StyloFitness - Public Entry Point
 * Este archivo redirige todas las peticiones al index.php principal
 */

// Cambiar al directorio raíz del proyecto antes de incluir
chdir(dirname(__DIR__));

// Incluir el index.php principal del proyecto
require_once dirname(__DIR__) . '/index.php';