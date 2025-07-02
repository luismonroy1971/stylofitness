<?php
use StyleFitness\Helpers\AppHelper;

/**
 * Vista de Clases Grupales - STYLOFITNESS
 * Muestra listado de clases grupales disponibles
 */
?>

<div class="classes-container">
    <div class="classes-header">
        <h1>Nuestras Clases</h1>
        <p class="subtitle">Descubre nuestras clases grupales y reserva tu lugar</p>
    </div>
    
    <div class="classes-filters">
        <form action="/classes" method="GET" class="filter-form">
            <div class="filter-group">
                <input type="text" name="search" placeholder="Buscar clases..." value="<?= htmlspecialchars($filters['search'] ?? '') ?>">
            </div>
            <div class="filter-group">
                <select name="type">
                    <option value="">Todos los tipos</option>
                    <option value="yoga" <?= ($filters['class_type'] ?? '') === 'yoga' ? 'selected' : '' ?>>Yoga</option>
                    <option value="pilates" <?= ($filters['class_type'] ?? '') === 'pilates' ? 'selected' : '' ?>>Pilates</option>
                    <option value="spinning" <?= ($filters['class_type'] ?? '') === 'spinning' ? 'selected' : '' ?>>Spinning</option>
                    <option value="zumba" <?= ($filters['class_type'] ?? '') === 'zumba' ? 'selected' : '' ?>>Zumba</option>
                    <option value="hiit" <?= ($filters['class_type'] ?? '') === 'hiit' ? 'selected' : '' ?>>HIIT</option>
                    <option value="crossfit" <?= ($filters['class_type'] ?? '') === 'crossfit' ? 'selected' : '' ?>>CrossFit</option>
                </select>
            </div>
            <div class="filter-group">
                <select name="difficulty">
                    <option value="">Todas las dificultades</option>
                    <option value="principiante" <?= ($filters['difficulty'] ?? '') === 'principiante' ? 'selected' : '' ?>>Principiante</option>
                    <option value="intermedio" <?= ($filters['difficulty'] ?? '') === 'intermedio' ? 'selected' : '' ?>>Intermedio</option>
                    <option value="avanzado" <?= ($filters['difficulty'] ?? '') === 'avanzado' ? 'selected' : '' ?>>Avanzado</option>
                </select>
            </div>
            <div class="filter-group">
                <select name="day">
                    <option value="">Todos los días</option>
                    <option value="lunes" <?= ($filters['day'] ?? '') === 'lunes' ? 'selected' : '' ?>>Lunes</option>
                    <option value="martes" <?= ($filters['day'] ?? '') === 'martes' ? 'selected' : '' ?>>Martes</option>
                    <option value="miercoles" <?= ($filters['day'] ?? '') === 'miercoles' ? 'selected' : '' ?>>Miércoles</option>
                    <option value="jueves" <?= ($filters['day'] ?? '') === 'jueves' ? 'selected' : '' ?>>Jueves</option>
                    <option value="viernes" <?= ($filters['day'] ?? '') === 'viernes' ? 'selected' : '' ?>>Viernes</option>
                    <option value="sabado" <?= ($filters['day'] ?? '') === 'sabado' ? 'selected' : '' ?>>Sábado</option>
                    <option value="domingo" <?= ($filters['day'] ?? '') === 'domingo' ? 'selected' : '' ?>>Domingo</option>
                </select>
            </div>
            <div class="filter-group">
                <button type="submit" class="btn-filter">Filtrar</button>
                <a href="/classes" class="btn-clear">Limpiar</a>
            </div>
        </form>
    </div>
    
    <div class="classes-grid">
        <?php if (empty($classes)): ?>
            <div class="no-classes">
                <p>No se encontraron clases que coincidan con los filtros seleccionados.</p>
                <a href="/classes" class="btn-primary">Ver todas las clases</a>
            </div>
        <?php else: ?>
            <?php foreach ($classes as $class): ?>
                <div class="class-card">
                    <div class="class-image">
                        <img src="<?= htmlspecialchars($class['image_url'] ?: AppHelper::asset('images/placeholder.jpg')) ?>" alt="<?= htmlspecialchars($class['name']) ?>">
                        <div class="class-difficulty <?= strtolower($class['difficulty_level']) ?>">
                            <?= htmlspecialchars($class['difficulty_level']) ?>
                        </div>
                    </div>
                    <div class="class-content">
                        <h3 class="class-title"><?= htmlspecialchars($class['name']) ?></h3>
                        <p class="class-type"><?= htmlspecialchars($class['class_type']) ?></p>
                        <div class="class-details">
                            <div class="detail">
                                <i class="fas fa-clock"></i>
                                <span><?= htmlspecialchars($class['duration_minutes']) ?> min</span>
                            </div>
                            <div class="detail">
                                <i class="fas fa-users"></i>
                                <span>Max: <?= htmlspecialchars($class['max_participants']) ?></span>
                            </div>
                            <div class="detail">
                                <i class="fas fa-map-marker-alt"></i>
                                <span><?= htmlspecialchars($class['room']) ?></span>
                            </div>
                        </div>
                        <p class="class-description"><?= htmlspecialchars(substr($class['description'], 0, 100)) ?>...</p>
                        <div class="class-schedule">
                            <h4>Horarios disponibles:</h4>
                            <div class="schedule-list">
                                <?php foreach ($class['schedules'] as $schedule): ?>
                                    <div class="schedule-item">
                                        <span class="day"><?= htmlspecialchars($schedule['day_of_week']) ?></span>
                                        <span class="time"><?= htmlspecialchars(substr($schedule['start_time'], 0, 5)) ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="class-actions">
                            <a href="/classes/<?= $class['id'] ?>" class="btn-details">Ver detalles</a>
                            <?php if (AppHelper::isLoggedIn()): ?>
                                <a href="/classes/<?= $class['id'] ?>/book" class="btn-book">Reservar</a>
                            <?php else: ?>
                                <a href="/login?redirect=/classes/<?= $class['id'] ?>" class="btn-login-to-book">Iniciar sesión para reservar</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Animación para las tarjetas de clases
        const classCards = document.querySelectorAll('.class-card');
        classCards.forEach((card, index) => {
            card.style.animationDelay = `${index * 0.1}s`;
        });
    });
</script>