# Script de Migración StyloFitness

## Descripción

Este conjunto de scripts permite migrar productos y categorías desde una base de datos de WordPress/WooCommerce hacia la nueva base de datos de StyloFitness.

## Archivos Incluidos

- `migration_script.php` - Script de migración en PHP
- `migration_script.py` - Script de migración en Python
- `config.json` - Archivo de configuración
- `stylofitness_database_complete.sql` - Script de creación de la nueva base de datos
- `Base de datos WP.sql` - Dump de la base de datos de WordPress original
- `README.md` - Este archivo de instrucciones

## Datos que se Migran

### Categorías de Productos
- **Origen**: `wp_terms` (WordPress)
- **Destino**: `product_categories` (StyloFitness)
- **Campos migrados**:
  - Nombre de la categoría
  - Slug (URL amigable)
  - Descripción generada automáticamente
  - Estado activo

### Productos
- **Origen**: `wp_posts` + `wp_postmeta` (WordPress/WooCommerce)
- **Destino**: `products` (StyloFitness)
- **Campos migrados**:
  - Nombre del producto
  - Descripción completa y corta
  - Precio regular y precio de oferta
  - SKU (generado automáticamente si no existe)
  - Stock disponible
  - Peso
  - Marca (extraída del título)
  - Estado de producto destacado
  - Estado activo/inactivo

## Categorías Identificadas

1. **PROTEÍNAS WHEY** (ID: 18)
2. **GANADORES DE MASA** (ID: 19)
3. **PROTEINAS ISOLATADAS** (ID: 20)
4. **PRE ENTRENOS Y ÓXIDO NITRICO** (ID: 21)
5. **PRECURSOR DE LA TESTO** (ID: 22)
6. **MULTIVITAMINICO Colágenos OMEGAS** (ID: 23)
7. **QUEMADORES DE GRASA** (ID: 24)
8. **AMINOÁCIDOS Y BCAA** (ID: 25)
9. **CREATINAS Y GLUTAMINAS** (ID: 26)
10. **PROTECTOR HEPÁTICO** (ID: 27)

## Productos Identificados

Se encontraron más de 50 productos en la base de datos original, incluyendo:
- Carnivor protein de 4 libras
- Mutant whey 10 libras
- Prostar Whey de 5 libras
- Nitrotech Whey de 5 libras
- Super Mass Gainer Dimatize 6 libras
- Y muchos más...

## Requisitos Previos

### Para PHP
- PHP 7.4 o superior
- Extensión PDO MySQL
- Acceso a ambas bases de datos

### Para Python
- Python 3.7 o superior
- Librería `mysql-connector-python`

```bash
pip install mysql-connector-python
```

## Configuración

### 1. Configurar Bases de Datos

Edita el archivo `config.json` con los datos de tus bases de datos:

```json
{
  "wordpress_database": {
    "host": "localhost",
    "database": "nombre_bd_wordpress",
    "username": "usuario",
    "password": "contraseña"
  },
  "stylofitness_database": {
    "host": "localhost",
    "database": "nombre_bd_stylofitness",
    "username": "usuario",
    "password": "contraseña"
  }
}
```

### 2. Preparar la Base de Datos de Destino

1. Crea la base de datos de StyloFitness:
```sql
CREATE DATABASE stylofitness_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

2. Importa el esquema:
```bash
mysql -u usuario -p stylofitness_db < stylofitness_database_complete.sql
```

### 3. Importar Base de Datos de WordPress

```bash
mysql -u usuario -p wordpress_db < "Base de datos WP.sql"
```

## Uso

### Opción 1: Script PHP

```bash
php migration_script.php
```

### Opción 2: Script Python

```bash
python migration_script.py
```

## Proceso de Migración

### Paso 1: Migración de Categorías
- Se crean las 10 categorías principales identificadas
- Se genera una descripción automática para cada categoría
- Se mapean los IDs de WordPress a los nuevos IDs de StyloFitness

### Paso 2: Migración de Productos
- Se obtienen todos los productos de tipo 'product' de WordPress
- Se extraen los metadatos (precios, stock, SKU, etc.)
- Se limpian las descripciones HTML
- Se asignan a las categorías correspondientes
- Se extraen las marcas automáticamente
- Se generan SKUs si no existen

## Características del Script

### Limpieza de Datos
- **HTML**: Se limpian los shortcodes de WordPress y HTML innecesario
- **Descripciones**: Se generan descripciones cortas automáticamente
- **Precios**: Se manejan precios regulares y de oferta
- **Stock**: Se asigna stock por defecto si no está definido

### Generación Automática
- **SKU**: Se genera automáticamente basado en el título del producto
- **Marcas**: Se extraen del título usando palabras clave
- **Slugs**: Se mantienen los slugs originales de WordPress

### Manejo de Errores
- Conexiones a base de datos con manejo de excepciones
- Validación de datos antes de insertar
- Logs detallados del proceso

## Verificación Post-Migración

### Consultas de Verificación

```sql
-- Verificar categorías migradas
SELECT COUNT(*) as total_categorias FROM product_categories;

-- Verificar productos migrados
SELECT COUNT(*) as total_productos FROM products;

-- Verificar productos por categoría
SELECT 
    pc.name as categoria,
    COUNT(p.id) as total_productos
FROM product_categories pc
LEFT JOIN products p ON pc.id = p.category_id
GROUP BY pc.id, pc.name
ORDER BY total_productos DESC;

-- Verificar productos con precios
SELECT 
    COUNT(*) as productos_con_precio
FROM products 
WHERE price > 0;

-- Verificar productos destacados
SELECT 
    COUNT(*) as productos_destacados
FROM products 
WHERE is_featured = 1;
```

## Solución de Problemas

### Error de Conexión
- Verificar credenciales en `config.json`
- Asegurar que las bases de datos existen
- Verificar permisos de usuario

### Error de Datos Duplicados
- El script usa `ON DUPLICATE KEY UPDATE` para evitar duplicados
- Se puede ejecutar múltiples veces de forma segura

### Productos sin Categoría
- Se asignan automáticamente a la categoría con ID 1
- Revisar el mapeo de categorías en el script

## Personalización

### Agregar Nuevas Marcas
Editar la lista `brands` en el método `extract_brand()`:

```php
$brands = ['CARNIVOR', 'MUTANT', 'TU_NUEVA_MARCA'];
```

### Modificar Categorías
Editar el array `categories` en el método `migrate_categories()`

### Cambiar Lógica de Limpieza
Modificar el método `clean_html()` según necesidades específicas

## Respaldo

**IMPORTANTE**: Siempre hacer respaldo de las bases de datos antes de ejecutar la migración:

```bash
# Respaldo WordPress
mysqldump -u usuario -p wordpress_db > backup_wordpress.sql

# Respaldo StyloFitness
mysqldump -u usuario -p stylofitness_db > backup_stylofitness.sql
```

## Soporte

Para problemas o dudas sobre la migración:
1. Verificar los logs de error
2. Revisar la configuración de base de datos
3. Consultar este README
4. Contactar al desarrollador

---

## 🖼️ Migración de Imágenes

### Script Adicional de Imágenes

Además de la migración básica de productos y categorías, se incluye un script especializado para migrar imágenes:

**`image_migration_script.php`** - Migración completa de imágenes

### Características de Migración de Imágenes

#### Tipos de Imágenes Migradas
- **Imágenes principales** de productos (thumbnail)
- **Galerías de imágenes** de productos
- **Imágenes de categorías**
- **Generación automática** de thumbnails en múltiples tamaños

#### Funcionalidades Avanzadas
- **Descarga desde URL** si los archivos locales no están disponibles
- **Copia de archivos físicos** desde WordPress uploads
- **Generación de thumbnails** automática (150x150, 300x300, 600x600)
- **Preservación de calidad** con compresión optimizada
- **Manejo de transparencias** para PNG
- **Validación de formatos** (JPG, PNG, GIF, WebP)

### Configuración de Imágenes

Edita las rutas en `config.json`:

```json
{
  "image_settings": {
    "wordpress_uploads_path": "C:/xampp/htdocs/wordpress/wp-content/uploads",
    "stylofitness_uploads_path": "C:/trabajos/stylofitness/public/uploads/images",
    "download_from_url": true,
    "generate_thumbnails": true,
    "max_file_size_mb": 10,
    "allowed_extensions": ["jpg", "jpeg", "png", "gif", "webp"]
  }
}
```

### Uso del Script de Imágenes

```bash
# Migrar todas las imágenes
php image_migration_script.php

# Migrar solo imágenes de productos
php image_migration_script.php products

# Migrar solo imágenes de categorías
php image_migration_script.php categories

# Generar solo thumbnails
php image_migration_script.php thumbnails
```

### Estructura de Directorios Creada

```
public/uploads/images/
├── products/           # Imágenes de productos
├── categories/         # Imágenes de categorías
├── thumbnails/         # Thumbnails generados automáticamente
└── placeholder.jpg     # Imagen por defecto
```

### Proceso de Migración de Imágenes

1. **Identificación**: Busca imágenes asociadas a productos y categorías
2. **Descarga/Copia**: Obtiene archivos desde WordPress uploads o URLs
3. **Optimización**: Genera thumbnails en múltiples tamaños
4. **Actualización**: Actualiza referencias JSON en la base de datos
5. **Reporte**: Genera reporte detallado con estadísticas

### Manejo de Errores en Imágenes

- **Archivos no encontrados**: Intenta descargar desde URL original
- **Formatos no soportados**: Registra en log de errores
- **Errores de copia**: Reporta archivos problemáticos
- **Espacio insuficiente**: Valida antes de procesar

### Verificación Post-Migración de Imágenes

```sql
-- Verificar productos con imágenes
SELECT 
    COUNT(*) as total_productos,
    COUNT(CASE WHEN images != '[]' AND images IS NOT NULL THEN 1 END) as con_imagenes
FROM products;

-- Verificar categorías con imágenes
SELECT 
    COUNT(*) as total_categorias,
    COUNT(CASE WHEN image_url IS NOT NULL THEN 1 END) as con_imagenes
FROM product_categories;
```

### Solución de Problemas de Imágenes

#### Imágenes No Migradas
- Verificar rutas en `config.json`
- Comprobar permisos de directorios
- Revisar log de errores generado

#### Thumbnails No Generados
- Verificar extensión GD de PHP
- Comprobar espacio en disco
- Validar formatos de imagen soportados

#### URLs Rotas
- Verificar conectividad a sitio WordPress original
- Comprobar URLs en base de datos WordPress
- Usar modo de copia local si es posible

---

**Nota**: La migración de imágenes es opcional pero recomendada para una experiencia completa. El script principal migra la estructura básica, mientras que el script de imágenes maneja todos los archivos multimedia.