/**
 * STYLOFITNESS - Constructor de Rutinas
 * JavaScript para crear y editar rutinas interactivamente
 */

class RoutineBuilder {
    constructor() {
        this.exercises = [];
        this.routine = {
            days: {}
        };
        this.currentDay = 1;
        this.draggedExercise = null;
        
        this.init();
    }
    
    init() {
        this.loadExercises();
        this.setupEventListeners();
        this.setupDragAndDrop();
        this.setupDayTabs();
        
        // Inicializar con datos existentes si es edición
        if (window.existingRoutine) {
            this.loadExistingRoutine(window.existingRoutine);
        }
    }
    
    setupEventListeners() {
        // Búsqueda de ejercicios
        document.getElementById('exercise-search').addEventListener('input', (e) => {
            this.searchExercises(e.target.value);
        });
        
        // Filtros de categoría
        document.getElementById('category-filter').addEventListener('change', (e) => {
            this.filterByCategory(e.target.value);
        });
        
        // Filtros de dificultad
        document.getElementById('difficulty-filter').addEventListener('change', (e) => {
            this.filterByDifficulty(e.target.value);
        });
        
        // Botón de añadir día
        document.getElementById('add-day-btn').addEventListener('click', () => {
            this.addDay();
        });
        
        // Botón de guardar rutina
        document.getElementById('save-routine-btn').addEventListener('click', () => {
            this.saveRoutine();
        });
        
        // Botón de vista previa
        document.getElementById('preview-routine-btn').addEventListener('click', () => {
            this.previewRoutine();
        });
        
        // Modal de configuración de ejercicio
        document.getElementById('save-exercise-config').addEventListener('click', () => {
            this.saveExerciseConfig();
        });
    }
    
    setupDragAndDrop() {
        // Hacer ejercicios arrastrables
        this.updateExerciseList();
        
        // Configurar zonas de drop
        this.setupDropZones();
    }
    
    setupDropZones() {
        const dayContainers = document.querySelectorAll('.day-exercises');
        
        dayContainers.forEach(container => {
            container.addEventListener('dragover', (e) => {
                e.preventDefault();
                container.classList.add('drag-over');
            });
            
            container.addEventListener('dragleave', () => {
                container.classList.remove('drag-over');
            });
            
            container.addEventListener('drop', (e) => {
                e.preventDefault();
                container.classList.remove('drag-over');
                
                const dayNumber = parseInt(container.dataset.day);
                this.addExerciseToDay(dayNumber, this.draggedExercise);
            });
        });
    }
    
    setupDayTabs() {
        const dayTabs = document.querySelectorAll('.day-tab');
        
        dayTabs.forEach(tab => {
            tab.addEventListener('click', () => {
                const dayNumber = parseInt(tab.dataset.day);
                this.switchToDay(dayNumber);
            });
        });
    }
    
    async loadExercises() {
        try {
            const response = await fetch('/api/exercises?limit=100');
            const data = await response.json();
            
            if (data.success) {
                this.exercises = data.data;
                this.updateExerciseList();
            }
        } catch (error) {
            console.error('Error loading exercises:', error);
            this.showNotification('Error al cargar ejercicios', 'error');
        }
    }
    
    updateExerciseList() {
        const container = document.getElementById('exercise-list');
        container.innerHTML = '';
        
        this.exercises.forEach(exercise => {
            const exerciseElement = this.createExerciseElement(exercise);
            container.appendChild(exerciseElement);
        });
    }
    
    createExerciseElement(exercise) {
        const div = document.createElement('div');
        div.className = 'exercise-item';
        div.draggable = true;
        div.dataset.exerciseId = exercise.id;
        
        div.innerHTML = `
            <div class="exercise-info">
                <h4>${exercise.name}</h4>
                <p class="exercise-description">${exercise.description || ''}</p>
                <div class="exercise-meta">
                    <span class="difficulty ${exercise.difficulty_level}">${exercise.difficulty_level}</span>
                    <span class="duration">${exercise.duration_minutes || 0} min</span>
                    <span class="calories">${exercise.calories_burned || 0} cal</span>
                </div>
            </div>
            <div class="exercise-actions">
                <button class="btn-add-exercise" onclick="routineBuilder.addExerciseToCurrentDay(${exercise.id})">
                    <i class="fas fa-plus"></i>
                </button>
                <button class="btn-exercise-info" onclick="routineBuilder.showExerciseInfo(${exercise.id})">
                    <i class="fas fa-info"></i>
                </button>
            </div>
        `;
        
        // Configurar drag
        div.addEventListener('dragstart', (e) => {
            this.draggedExercise = exercise;
            div.classList.add('dragging');
        });
        
        div.addEventListener('dragend', () => {
            div.classList.remove('dragging');
        });
        
        return div;
    }
    
    addExerciseToCurrentDay(exerciseId) {
        const exercise = this.exercises.find(e => e.id == exerciseId);
        if (exercise) {
            this.addExerciseToDay(this.currentDay, exercise);
        }
    }
    
    addExerciseToDay(dayNumber, exercise) {
        if (!this.routine.days[dayNumber]) {
            this.routine.days[dayNumber] = [];
        }
        
        const exerciseConfig = {
            id: exercise.id,
            name: exercise.name,
            sets: 3,
            reps: '10',
            weight: '',
            rest_seconds: 60,
            tempo: '',
            notes: '',
            order: this.routine.days[dayNumber].length
        };
        
        this.routine.days[dayNumber].push(exerciseConfig);
        this.updateDayView(dayNumber);
        this.showNotification(`${exercise.name} añadido al día ${dayNumber}`, 'success');
    }
    
    removeExerciseFromDay(dayNumber, exerciseIndex) {
        if (this.routine.days[dayNumber]) {
            this.routine.days[dayNumber].splice(exerciseIndex, 1);
            this.updateDayView(dayNumber);
            this.showNotification('Ejercicio eliminado', 'info');
        }
    }
    
    updateDayView(dayNumber) {
        const container = document.querySelector(`#day-${dayNumber} .day-exercises`);
        if (!container) return;
        
        container.innerHTML = '';
        
        if (this.routine.days[dayNumber]) {
            this.routine.days[dayNumber].forEach((exercise, index) => {
                const exerciseElement = this.createDayExerciseElement(exercise, dayNumber, index);
                container.appendChild(exerciseElement);
            });
        }
        
        this.updateRoutineStats();
    }
    
    createDayExerciseElement(exercise, dayNumber, index) {
        const div = document.createElement('div');
        div.className = 'day-exercise';
        div.dataset.exerciseId = exercise.id;
        div.dataset.index = index;
        
        div.innerHTML = `
            <div class="exercise-order">${index + 1}</div>
            <div class="exercise-details">
                <h5>${exercise.name}</h5>
                <div class="exercise-config">
                    <span class="config-item">
                        <label>Series:</label>
                        <input type="number" class="sets-input" value="${exercise.sets}" min="1" max="10">
                    </span>
                    <span class="config-item">
                        <label>Reps:</label>
                        <input type="text" class="reps-input" value="${exercise.reps}" placeholder="10 o 8-12">
                    </span>
                    <span class="config-item">
                        <label>Peso:</label>
                        <input type="text" class="weight-input" value="${exercise.weight}" placeholder="20kg">
                    </span>
                    <span class="config-item">
                        <label>Descanso:</label>
                        <input type="number" class="rest-input" value="${exercise.rest_seconds}" min="0" max="300">s
                    </span>
                </div>
                <div class="exercise-notes">
                    <input type="text" class="notes-input" value="${exercise.notes}" placeholder="Notas adicionales...">
                </div>
            </div>
            <div class="exercise-controls">
                <button class="btn-move-up" onclick="routineBuilder.moveExercise(${dayNumber}, ${index}, -1)" ${index === 0 ? 'disabled' : ''}>
                    <i class="fas fa-arrow-up"></i>
                </button>
                <button class="btn-move-down" onclick="routineBuilder.moveExercise(${dayNumber}, ${index}, 1)">
                    <i class="fas fa-arrow-down"></i>
                </button>
                <button class="btn-remove" onclick="routineBuilder.removeExerciseFromDay(${dayNumber}, ${index})">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        `;
        
        // Configurar eventos de cambio
        const inputs = div.querySelectorAll('input');
        inputs.forEach(input => {
            input.addEventListener('change', () => {
                this.updateExerciseConfig(dayNumber, index, input);
            });
        });
        
        return div;
    }
    
    updateExerciseConfig(dayNumber, index, input) {
        const exercise = this.routine.days[dayNumber][index];
        
        switch (input.className) {
            case 'sets-input':
                exercise.sets = parseInt(input.value);
                break;
            case 'reps-input':
                exercise.reps = input.value;
                break;
            case 'weight-input':
                exercise.weight = input.value;
                break;
            case 'rest-input':
                exercise.rest_seconds = parseInt(input.value);
                break;
            case 'notes-input':
                exercise.notes = input.value;
                break;
        }
        
        this.updateRoutineStats();
    }
    
    moveExercise(dayNumber, index, direction) {
        const exercises = this.routine.days[dayNumber];
        const newIndex = index + direction;
        
        if (newIndex >= 0 && newIndex < exercises.length) {
            // Intercambiar ejercicios
            [exercises[index], exercises[newIndex]] = [exercises[newIndex], exercises[index]];
            this.updateDayView(dayNumber);
        }
    }
    
    switchToDay(dayNumber) {
        this.currentDay = dayNumber;
        
        // Actualizar tabs
        document.querySelectorAll('.day-tab').forEach(tab => {
            tab.classList.remove('active');
        });
        
        document.querySelector(`[data-day="${dayNumber}"]`).classList.add('active');
        
        // Mostrar contenido del día
        document.querySelectorAll('.day-content').forEach(content => {
            content.classList.remove('active');
        });
        
        document.getElementById(`day-${dayNumber}`).classList.add('active');
    }
    
    addDay() {
        const maxDays = 7;
        const currentDays = Object.keys(this.routine.days).length;
        
        if (currentDays >= maxDays) {
            this.showNotification('Máximo 7 días permitidos', 'warning');
            return;
        }
        
        const newDay = currentDays + 1;
        this.routine.days[newDay] = [];
        
        this.createDayTab(newDay);
        this.createDayContent(newDay);
        this.switchToDay(newDay);
    }
    
    createDayTab(dayNumber) {
        const tabsContainer = document.querySelector('.day-tabs');
        const tab = document.createElement('button');
        tab.className = 'day-tab';
        tab.dataset.day = dayNumber;
        tab.textContent = `Día ${dayNumber}`;
        
        tab.addEventListener('click', () => {
            this.switchToDay(dayNumber);
        });
        
        tabsContainer.appendChild(tab);
    }
    
    createDayContent(dayNumber) {
        const contentContainer = document.querySelector('.routine-days');
        const content = document.createElement('div');
        content.className = 'day-content';
        content.id = `day-${dayNumber}`;
        
        content.innerHTML = `
            <div class="day-header">
                <h3>Día ${dayNumber}</h3>
                <button class="btn-remove-day" onclick="routineBuilder.removeDay(${dayNumber})">
                    <i class="fas fa-trash"></i> Eliminar día
                </button>
            </div>
            <div class="day-exercises" data-day="${dayNumber}">
                <div class="empty-day">
                    <i class="fas fa-dumbbell"></i>
                    <p>Arrastra ejercicios aquí o usa el botón + para añadirlos</p>
                </div>
            </div>
        `;
        
        contentContainer.appendChild(content);
        this.setupDropZones();
    }
    
    removeDay(dayNumber) {
        if (Object.keys(this.routine.days).length <= 1) {
            this.showNotification('Debe haber al menos un día', 'warning');
            return;
        }
        
        if (confirm('¿Estás seguro de que quieres eliminar este día?')) {
            delete this.routine.days[dayNumber];
            
            // Remover elementos del DOM
            document.querySelector(`[data-day="${dayNumber}"]`).remove();
            document.getElementById(`day-${dayNumber}`).remove();
            
            // Cambiar a otro día
            const remainingDays = Object.keys(this.routine.days);
            if (remainingDays.length > 0) {
                this.switchToDay(parseInt(remainingDays[0]));
            }
        }
    }
    
    updateRoutineStats() {
        const totalExercises = Object.values(this.routine.days).reduce((total, day) => total + day.length, 0);
        const totalDays = Object.keys(this.routine.days).length;
        
        document.getElementById('total-exercises').textContent = totalExercises;
        document.getElementById('total-days').textContent = totalDays;
        
        // Calcular duración estimada
        let estimatedDuration = 0;
        Object.values(this.routine.days).forEach(day => {
            day.forEach(exercise => {
                estimatedDuration += (exercise.sets * (exercise.rest_seconds || 60)) / 60; // Convertir a minutos
            });
        });
        
        document.getElementById('estimated-duration').textContent = Math.round(estimatedDuration);
    }
    
    searchExercises(query) {
        const filteredExercises = this.exercises.filter(exercise => 
            exercise.name.toLowerCase().includes(query.toLowerCase()) ||
            (exercise.description && exercise.description.toLowerCase().includes(query.toLowerCase()))
        );
        
        this.displayFilteredExercises(filteredExercises);
    }
    
    filterByCategory(categoryId) {
        if (!categoryId) {
            this.updateExerciseList();
            return;
        }
        
        const filteredExercises = this.exercises.filter(exercise => 
            exercise.category_id == categoryId
        );
        
        this.displayFilteredExercises(filteredExercises);
    }
    
    filterByDifficulty(difficulty) {
        if (!difficulty) {
            this.updateExerciseList();
            return;
        }
        
        const filteredExercises = this.exercises.filter(exercise => 
            exercise.difficulty_level === difficulty
        );
        
        this.displayFilteredExercises(filteredExercises);
    }
    
    displayFilteredExercises(exercises) {
        const container = document.getElementById('exercise-list');
        container.innerHTML = '';
        
        exercises.forEach(exercise => {
            const exerciseElement = this.createExerciseElement(exercise);
            container.appendChild(exerciseElement);
        });
    }
    
    showExerciseInfo(exerciseId) {
        const exercise = this.exercises.find(e => e.id == exerciseId);
        if (!exercise) return;
        
        const modal = document.getElementById('exercise-info-modal');
        const content = modal.querySelector('.modal-content');
        
        content.innerHTML = `
            <div class="modal-header">
                <h3>${exercise.name}</h3>
                <button class="modal-close">&times;</button>
            </div>
            <div class="modal-body">
                <div class="exercise-details">
                    <div class="detail-group">
                        <h4>Descripción</h4>
                        <p>${exercise.description || 'No disponible'}</p>
                    </div>
                    <div class="detail-group">
                        <h4>Instrucciones</h4>
                        <p>${exercise.instructions || 'No disponible'}</p>
                    </div>
                    <div class="detail-group">
                        <h4>Músculos trabajados</h4>
                        <div class="muscle-groups">
                            ${exercise.muscle_groups ? exercise.muscle_groups.map(m => `<span class="muscle-tag">${m}</span>`).join('') : 'No especificado'}
                        </div>
                    </div>
                    <div class="detail-group">
                        <h4>Información adicional</h4>
                        <div class="info-grid">
                            <div class="info-item">
                                <span class="label">Nivel:</span>
                                <span class="value">${exercise.difficulty_level}</span>
                            </div>
                            <div class="info-item">
                                <span class="label">Duración:</span>
                                <span class="value">${exercise.duration_minutes || 0} min</span>
                            </div>
                            <div class="info-item">
                                <span class="label">Calorías:</span>
                                <span class="value">${exercise.calories_burned || 0} cal</span>
                            </div>
                            <div class="info-item">
                                <span class="label">Equipo:</span>
                                <span class="value">${exercise.equipment_needed || 'No especificado'}</span>
                            </div>
                        </div>
                    </div>
                    ${exercise.video_url ? `
                    <div class="detail-group">
                        <h4>Video demostrativo</h4>
                        <video controls class="exercise-video">
                            <source src="${exercise.video_url}" type="video/mp4">
                            Tu navegador no soporta el elemento video.
                        </video>
                    </div>
                    ` : ''}
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" onclick="routineBuilder.addExerciseToCurrentDay(${exercise.id}); routineBuilder.closeModal('exercise-info-modal');">
                    <i class="fas fa-plus"></i> Añadir al día actual
                </button>
            </div>
        `;
        
        modal.style.display = 'block';
        
        // Configurar cierre del modal
        const closeBtn = content.querySelector('.modal-close');
        closeBtn.addEventListener('click', () => {
            this.closeModal('exercise-info-modal');
        });
    }
    
    closeModal(modalId) {
        document.getElementById(modalId).style.display = 'none';
    }
    
    previewRoutine() {
        const routineData = this.getRoutineData();
        const modal = document.getElementById('routine-preview-modal');
        const content = modal.querySelector('.modal-content');
        
        let previewHTML = `
            <div class="modal-header">
                <h3>Vista previa de la rutina</h3>
                <button class="modal-close">&times;</button>
            </div>
            <div class="modal-body">
                <div class="routine-summary">
                    <h4>${routineData.name || 'Rutina sin nombre'}</h4>
                    <p>${routineData.description || 'Sin descripción'}</p>
                    <div class="routine-stats">
                        <span>Días: ${Object.keys(routineData.days).length}</span>
                        <span>Ejercicios: ${Object.values(routineData.days).reduce((total, day) => total + day.length, 0)}</span>
                        <span>Duración estimada: ${routineData.estimated_duration_minutes || 0} min</span>
                    </div>
                </div>
        `;
        
        Object.entries(routineData.days).forEach(([dayNumber, exercises]) => {
            previewHTML += `
                <div class="day-preview">
                    <h5>Día ${dayNumber}</h5>
                    <div class="exercises-preview">
            `;
            
            exercises.forEach((exercise, index) => {
                previewHTML += `
                    <div class="exercise-preview">
                        <span class="exercise-number">${index + 1}</span>
                        <span class="exercise-name">${exercise.name}</span>
                        <span class="exercise-config">${exercise.sets} x ${exercise.reps}</span>
                        ${exercise.weight ? `<span class="exercise-weight">${exercise.weight}</span>` : ''}
                        <span class="exercise-rest">${exercise.rest_seconds}s descanso</span>
                    </div>
                `;
            });
            
            previewHTML += `
                    </div>
                </div>
            `;
        });
        
        previewHTML += `
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="routineBuilder.closeModal('routine-preview-modal')">Cerrar</button>
                <button class="btn btn-primary" onclick="routineBuilder.saveRoutine()">Guardar rutina</button>
            </div>
        `;
        
        content.innerHTML = previewHTML;
        modal.style.display = 'block';
        
        // Configurar cierre del modal
        const closeBtn = content.querySelector('.modal-close');
        closeBtn.addEventListener('click', () => {
            this.closeModal('routine-preview-modal');
        });
    }
    
    getRoutineData() {
        return {
            name: document.getElementById('routine-name').value,
            description: document.getElementById('routine-description').value,
            objective: document.getElementById('routine-objective').value,
            difficulty_level: document.getElementById('routine-difficulty').value,
            duration_weeks: parseInt(document.getElementById('routine-duration-weeks').value),
            sessions_per_week: parseInt(document.getElementById('routine-sessions-week').value),
            estimated_duration_minutes: parseInt(document.getElementById('estimated-duration').textContent),
            is_template: document.getElementById('routine-is-template').checked,
            client_id: document.getElementById('routine-client-id') ? document.getElementById('routine-client-id').value : null,
            days: this.routine.days
        };
    }
    
    async saveRoutine() {
        const routineData = this.getRoutineData();
        
        // Validar datos
        if (!routineData.name) {
            this.showNotification('El nombre de la rutina es obligatorio', 'error');
            return;
        }
        
        if (Object.keys(routineData.days).length === 0) {
            this.showNotification('La rutina debe tener al menos un día con ejercicios', 'error');
            return;
        }
        
        try {
            const url = window.isEditing ? `/routines/update/${window.routineId}` : '/routines/store';
            const method = window.isEditing ? 'PUT' : 'POST';
            
            const response = await fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(routineData)
            });
            
            const result = await response.json();
            
            if (result.success) {
                this.showNotification('Rutina guardada exitosamente', 'success');
                setTimeout(() => {
                    window.location.href = `/routines/view/${result.routine_id || window.routineId}`;
                }, 1500);
            } else {
                this.showNotification(result.message || 'Error al guardar la rutina', 'error');
            }
        } catch (error) {
            console.error('Error saving routine:', error);
            this.showNotification('Error al guardar la rutina', 'error');
        }
    }
    
    loadExistingRoutine(routine) {
        // Cargar datos básicos
        document.getElementById('routine-name').value = routine.name || '';
        document.getElementById('routine-description').value = routine.description || '';
        document.getElementById('routine-objective').value = routine.objective || '';
        document.getElementById('routine-difficulty').value = routine.difficulty_level || '';
        document.getElementById('routine-duration-weeks').value = routine.duration_weeks || 4;
        document.getElementById('routine-sessions-week').value = routine.sessions_per_week || 3;
        
        if (document.getElementById('routine-is-template')) {
            document.getElementById('routine-is-template').checked = routine.is_template || false;
        }
        
        if (document.getElementById('routine-client-id')) {
            document.getElementById('routine-client-id').value = routine.client_id || '';
        }
        
        // Cargar ejercicios por día
        if (routine.exercises) {
            const exercisesByDay = {};
            
            routine.exercises.forEach(exercise => {
                if (!exercisesByDay[exercise.day_number]) {
                    exercisesByDay[exercise.day_number] = [];
                }
                
                exercisesByDay[exercise.day_number].push({
                    id: exercise.exercise_id,
                    name: exercise.exercise_name,
                    sets: exercise.sets,
                    reps: exercise.reps,
                    weight: exercise.weight,
                    rest_seconds: exercise.rest_seconds,
                    tempo: exercise.tempo,
                    notes: exercise.notes,
                    order: exercise.order_index
                });
            });
            
            // Ordenar ejercicios por orden
            Object.values(exercisesByDay).forEach(dayExercises => {
                dayExercises.sort((a, b) => a.order - b.order);
            });
            
            this.routine.days = exercisesByDay;
            
            // Crear tabs y contenido para cada día
            Object.keys(exercisesByDay).forEach(dayNumber => {
                const day = parseInt(dayNumber);
                if (day > 1) {
                    this.createDayTab(day);
                    this.createDayContent(day);
                }
                this.updateDayView(day);
            });
        }
        
        this.updateRoutineStats();
    }
    
    showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        notification.innerHTML = `
            <div class="notification-content">
                <i class="fas fa-${type === 'success' ? 'check' : type === 'error' ? 'times' : type === 'warning' ? 'exclamation' : 'info'}"></i>
                <span>${message}</span>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.classList.add('show');
        }, 100);
        
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => {
                document.body.removeChild(notification);
            }, 300);
        }, 3000);
    }
}

// Inicializar el constructor de rutinas cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('routine-builder')) {
        window.routineBuilder = new RoutineBuilder();
    }
});
