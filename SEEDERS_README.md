# STYLOFITNESS - Seeders de Base de Datos

## 📋 Descripción

Este directorio contiene los seeders necesarios para poblar la base de datos de STYLOFITNESS con datos de ejemplo para las secciones de la landing page.

## 🗂️ Archivos Creados

### Seeders Principales
- `DatabaseSeeder.php` - Seeder principal que coordina todos los demás
- `GymsSeeder.php` - Datos de gimnasios de ejemplo
- `UsersSeeder.php` - Usuarios del sistema (admin, instructores, clientes)
- `SpecialOffersSeeder.php` - Ofertas especiales para la landing page
- `WhyChooseUsSeeder.php` - Características "Por qué elegirnos"
- `TestimonialsSeeder.php` - Testimonios de clientes
- `LandingPageConfigSeeder.php` - Configuración de secciones de la landing page
- `FeaturedProductsConfigSeeder.php` - Configuración de productos destacados

### Archivos de Utilidad
- `seed_database.php` - Script para ejecutar todos los seeders
- `SEEDERS_README.md` - Este archivo de documentación

## 🚀 Cómo Ejecutar los Seeders

### Opción 1: Usando Artisan (Recomendado)
```bash
# Ejecutar todos los seeders
php artisan db:seed

# Ejecutar un seeder específico
php artisan db:seed --class=GymsSeeder
php artisan db:seed --class=TestimonialsSeeder
```

### Opción 2: Usando el Script Personalizado
```bash
# Ejecutar el script personalizado
php seed_database.php
```

### Opción 3: Ejecutar Seeders Individuales
```bash
php artisan db:seed --class=Database\\Seeders\\GymsSeeder
php artisan db:seed --class=Database\\Seeders\\UsersSeeder
php artisan db:seed --class=Database\\Seeders\\SpecialOffersSeeder
php artisan db:seed --class=Database\\Seeders\\WhyChooseUsSeeder
php artisan db:seed --class=Database\\Seeders\\TestimonialsSeeder
php artisan db:seed --class=Database\\Seeders\\LandingPageConfigSeeder
php artisan db:seed --class=Database\\Seeders\\FeaturedProductsConfigSeeder
```

## 📊 Datos que se Crearán

### Gimnasios (2 registros)
- STYLOFITNESS Lima Centro
- STYLOFITNESS Miraflores

### Usuarios (4 registros)
- **Admin**: admin@stylofitness.com / admin123
- **Instructor**: carlos.rodriguez@stylofitness.com / instructor123
- **Cliente 1**: maria.gonzalez@email.com / cliente123
- **Cliente 2**: ana.morales@email.com / cliente123

### Ofertas Especiales (5 registros)
- Mega descuento en suplementos (50% OFF)
- Membresía premium (3x2)
- Rutinas personalizadas gratis
- Clases grupales primera semana gratis
- Evaluación nutricional (50% OFF)

### Por Qué Elegirnos (6 registros)
- Rutinas Personalizadas
- Tienda Especializada
- Clases Grupales
- Tecnología Avanzada
- Nutrición Especializada
- Comunidad Activa

### Testimonios (8 registros)
- 4 testimonios destacados (featured)
- 4 testimonios regulares
- Incluye datos de redes sociales y verificaciones

### Configuración de Landing Page (8 secciones)
- Hero Banner
- Ofertas Especiales
- Por Qué Elegirnos
- Productos Destacados
- Clases Grupales
- Testimonios
- Formulario de Contacto
- Footer

## ⚠️ Requisitos Previos

1. **Base de datos configurada**: Asegúrate de que el archivo `.env` tenga la configuración correcta de la base de datos.

2. **Tablas creadas**: Ejecuta las migraciones antes de los seeders:
   ```bash
   # Si usas archivos SQL
   mysql -u usuario -p nombre_base_datos < database/stylofitness_complete.sql
   mysql -u usuario -p nombre_base_datos < database/landing_sections_migration.sql
   
   # O si usas migraciones de Laravel
   php artisan migrate
   ```

3. **Dependencias instaladas**:
   ```bash
   composer install
   ```

## 🔧 Solución de Problemas

### Error: "Class not found"
- Verifica que los archivos de seeders estén en `database/seeders/`
- Ejecuta `composer dump-autoload`

### Error: "Table doesn't exist"
- Asegúrate de haber ejecutado las migraciones primero
- Verifica la conexión a la base de datos en `.env`

### Error: "Duplicate entry"
- Los seeders pueden ejecutarse múltiples veces
- Si hay errores de duplicados, trunca las tablas primero:
  ```sql
  TRUNCATE TABLE special_offers;
  TRUNCATE TABLE why_choose_us;
  TRUNCATE TABLE testimonials;
  TRUNCATE TABLE landing_page_config;
  TRUNCATE TABLE featured_products_config;
  ```

## 📝 Personalización

Puedes modificar los datos en cada seeder según tus necesidades:

- **Imágenes**: Cambia las rutas de las imágenes en los seeders
- **Textos**: Modifica los títulos, descripciones y testimonios
- **Configuración**: Ajusta los parámetros de configuración en `LandingPageConfigSeeder`
- **Usuarios**: Cambia las credenciales y datos de usuarios

## 🎯 Resultado Esperado

Después de ejecutar los seeders, tu landing page tendrá:

✅ Secciones con contenido real
✅ Ofertas especiales atractivas
✅ Testimonios convincentes
✅ Características destacadas
✅ Configuración completa
✅ Usuarios de prueba para testing

## 📞 Soporte

Si tienes problemas ejecutando los seeders:

1. Verifica los logs de Laravel en `storage/logs/`
2. Revisa la configuración de la base de datos
3. Asegúrate de que todas las tablas existan
4. Verifica los permisos de escritura en la base de datos

---

**¡Listo!** Tu aplicación STYLOFITNESS ahora tiene datos de ejemplo para mostrar todas las secciones de la landing page.