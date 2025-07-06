# Troubleshooting Guide

## Problemas Comunes

### Error de Conexión a Base de Datos
- Verificar credenciales en config.json
- Comprobar que el servidor MySQL esté ejecutándose
- Validar permisos de usuario

### Imágenes No Migradas
- Verificar rutas en image_settings
- Comprobar permisos de directorios
- Revisar extensión GD de PHP

### Productos Duplicados
- Ejecutar utilities.php para limpiar duplicados
- Verificar configuración skip_existing

## Comandos de Diagnóstico

```bash
# Verificar sintaxis
php syntax_check.php

# Verificar calidad
php code_quality_improvements.php
```
