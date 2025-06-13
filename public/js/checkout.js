/**
 * STYLOFITNESS - Sistema de Checkout
 * JavaScript para el proceso de compra y pago
 */

class CheckoutManager {
    constructor() {
        this.currentStep = 1;
        this.totalSteps = 4;
        this.formData = {};
        this.paymentMethods = {};
        this.shippingMethods = {};
        
        this.init();
    }
    
    init() {
        this.bindEvents();
        this.initializeAddressForms();
        this.initializePaymentMethods();
        this.validateCartItems();
        this.calculateTotals();
    }
    
    bindEvents() {
        // Botones de navegación entre pasos
        document.addEventListener('click', (e) => {
            if (e.target.matches('.btn-next-step')) {
                e.preventDefault();
                this.nextStep();
            }
            
            if (e.target.matches('.btn-prev-step')) {
                e.preventDefault();
                this.prevStep();
            }
            
            if (e.target.matches('.step-indicator')) {
                e.preventDefault();
                const step = parseInt(e.target.dataset.step);
                this.goToStep(step);
            }
        });
        
        // Copiar dirección de facturación a envío
        const copyAddressCheckbox = document.getElementById('same-as-billing');
        if (copyAddressCheckbox) {
            copyAddressCheckbox.addEventListener('change', () => {
                this.toggleShippingAddress();
            });
        }
        
        // Selección de método de pago
        document.addEventListener('change', (e) => {
            if (e.target.matches('.payment-method-radio')) {
                this.selectPaymentMethod(e.target.value);
            }
            
            if (e.target.matches('.shipping-method-radio')) {
                this.selectShippingMethod(e.target.value);
            }
        });
        
        // Validación de formularios en tiempo real
        document.addEventListener('input', (e) => {
            if (e.target.matches('.checkout-input')) {
                this.validateField(e.target);
            }
        });
        
        // Procesar orden
        const checkoutForm = document.getElementById('checkout-form');
        if (checkoutForm) {
            checkoutForm.addEventListener('submit', (e) => {
                e.preventDefault();
                this.processOrder();
            });
        }
    }
    
    nextStep() {
        if (this.currentStep >= this.totalSteps) return;
        
        if (this.validateCurrentStep()) {
            this.saveStepData();
            this.currentStep++;
            this.updateStepDisplay();
            this.scrollToTop();
        }
    }
    
    prevStep() {
        if (this.currentStep <= 1) return;
        
        this.currentStep--;
        this.updateStepDisplay();
        this.scrollToTop();
    }
    
    goToStep(step) {
        if (step < 1 || step > this.totalSteps) return;
        if (step > this.currentStep && !this.validateStepsUpTo(step - 1)) return;
        
        this.currentStep = step;
        this.updateStepDisplay();
        this.scrollToTop();
    }
    
    updateStepDisplay() {
        // Ocultar todos los pasos
        const steps = document.querySelectorAll('.checkout-step');
        steps.forEach(step => {
            step.classList.remove('active');
        });
        
        // Mostrar paso actual
        const currentStepElement = document.getElementById(`step-${this.currentStep}`);
        if (currentStepElement) {
            currentStepElement.classList.add('active');
        }
        
        // Actualizar indicadores de progreso
        const indicators = document.querySelectorAll('.step-indicator');
        indicators.forEach((indicator, index) => {
            const stepNumber = index + 1;
            indicator.classList.remove('active', 'completed');
            
            if (stepNumber < this.currentStep) {
                indicator.classList.add('completed');
            } else if (stepNumber === this.currentStep) {
                indicator.classList.add('active');
            }
        });
        
        // Actualizar barra de progreso
        const progressBar = document.querySelector('.checkout-progress-bar');
        if (progressBar) {
            const progress = ((this.currentStep - 1) / (this.totalSteps - 1)) * 100;
            progressBar.style.width = `${progress}%`;
        }
        
        // Mostrar/ocultar botones de navegación
        const prevBtn = document.querySelector('.btn-prev-step');
        const nextBtn = document.querySelector('.btn-next-step');
        const submitBtn = document.querySelector('.btn-submit-order');
        
        if (prevBtn) {
            prevBtn.style.display = this.currentStep > 1 ? 'inline-block' : 'none';
        }
        
        if (nextBtn) {
            nextBtn.style.display = this.currentStep < this.totalSteps ? 'inline-block' : 'none';
        }
        
        if (submitBtn) {
            submitBtn.style.display = this.currentStep === this.totalSteps ? 'inline-block' : 'none';
        }
    }
    
    validateCurrentStep() {
        switch (this.currentStep) {
            case 1:
                return this.validateBillingAddress();
            case 2:
                return this.validateShippingMethod();
            case 3:
                return this.validatePaymentMethod();
            case 4:
                return true; // Paso de revisión
            default:
                return true;
        }
    }
    
    validateStepsUpTo(step) {
        for (let i = 1; i <= step; i++) {
            this.currentStep = i;
            if (!this.validateCurrentStep()) {
                return false;
            }
        }
        return true;
    }
    
    validateBillingAddress() {
        const requiredFields = [
            'billing_first_name',
            'billing_last_name',
            'billing_email',
            'billing_phone',
            'billing_address',
            'billing_city',
            'billing_postal_code'
        ];
        
        let isValid = true;
        const errors = {};
        
        requiredFields.forEach(fieldName => {
            const field = document.getElementById(fieldName);
            if (field && !field.value.trim()) {
                errors[fieldName] = 'Este campo es obligatorio';
                isValid = false;
            }
        });
        
        // Validar email
        const emailField = document.getElementById('billing_email');
        if (emailField && emailField.value) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(emailField.value)) {
                errors['billing_email'] = 'Email inválido';
                isValid = false;
            }
        }
        
        // Validar teléfono
        const phoneField = document.getElementById('billing_phone');
        if (phoneField && phoneField.value) {
            const phoneRegex = /^[+]?[\d\s\-\(\)]{8,15}$/;
            if (!phoneRegex.test(phoneField.value)) {
                errors['billing_phone'] = 'Teléfono inválido';
                isValid = false;
            }
        }
        
        this.displayValidationErrors(errors);
        return isValid;
    }
    
    validateShippingMethod() {
        const selectedMethod = document.querySelector('.shipping-method-radio:checked');
        if (!selectedMethod) {
            this.showNotification('Selecciona un método de envío', 'error');
            return false;
        }
        return true;
    }
    
    validatePaymentMethod() {
        const selectedPayment = document.querySelector('.payment-method-radio:checked');
        if (!selectedPayment) {
            this.showNotification('Selecciona un método de pago', 'error');
            return false;
        }
        
        // Validar datos específicos del método de pago
        if (selectedPayment.value === 'credit_card') {
            return this.validateCreditCardData();
        }
        
        return true;
    }
    
    validateCreditCardData() {
        const requiredFields = ['card_number', 'card_expiry', 'card_cvv', 'card_name'];
        let isValid = true;
        const errors = {};
        
        requiredFields.forEach(fieldName => {
            const field = document.getElementById(fieldName);
            if (field && !field.value.trim()) {
                errors[fieldName] = 'Este campo es obligatorio';
                isValid = false;
            }
        });
        
        // Validar número de tarjeta
        const cardNumber = document.getElementById('card_number');
        if (cardNumber && cardNumber.value) {
            const cleanNumber = cardNumber.value.replace(/\s/g, '');
            if (!/^\d{13,19}$/.test(cleanNumber)) {
                errors['card_number'] = 'Número de tarjeta inválido';
                isValid = false;
            } else if (!this.luhnCheck(cleanNumber)) {
                errors['card_number'] = 'Número de tarjeta inválido';
                isValid = false;
            }
        }
        
        // Validar fecha de vencimiento
        const cardExpiry = document.getElementById('card_expiry');
        if (cardExpiry && cardExpiry.value) {
            const expiryRegex = /^(0[1-9]|1[0-2])\/\d{2}$/;
            if (!expiryRegex.test(cardExpiry.value)) {
                errors['card_expiry'] = 'Formato de fecha inválido (MM/YY)';
                isValid = false;
            } else {
                const [month, year] = cardExpiry.value.split('/');
                const expiry = new Date(2000 + parseInt(year), parseInt(month) - 1);
                const now = new Date();
                
                if (expiry <= now) {
                    errors['card_expiry'] = 'Tarjeta vencida';
                    isValid = false;
                }
            }
        }
        
        // Validar CVV
        const cardCvv = document.getElementById('card_cvv');
        if (cardCvv && cardCvv.value) {
            if (!/^\d{3,4}$/.test(cardCvv.value)) {
                errors['card_cvv'] = 'CVV inválido';
                isValid = false;
            }
        }
        
        this.displayValidationErrors(errors);
        return isValid;
    }
    
    luhnCheck(cardNumber) {
        let sum = 0;
        let isEven = false;
        
        for (let i = cardNumber.length - 1; i >= 0; i--) {
            let digit = parseInt(cardNumber.charAt(i));
            
            if (isEven) {
                digit *= 2;
                if (digit > 9) {
                    digit -= 9;
                }
            }
            
            sum += digit;
            isEven = !isEven;
        }
        
        return sum % 10 === 0;
    }
    
    validateField(field) {
        const errorElement = document.getElementById(`${field.id}-error`);
        let isValid = true;
        let errorMessage = '';
        
        // Validación básica de campos requeridos
        if (field.hasAttribute('required') && !field.value.trim()) {
            isValid = false;
            errorMessage = 'Este campo es obligatorio';
        }
        
        // Validaciones específicas por tipo
        if (field.value && field.type === 'email') {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(field.value)) {
                isValid = false;
                errorMessage = 'Email inválido';
            }
        }
        
        // Mostrar/ocultar error
        if (errorElement) {
            errorElement.textContent = errorMessage;
            errorElement.style.display = isValid ? 'none' : 'block';
        }
        
        // Actualizar estilo del campo
        field.classList.toggle('error', !isValid);
        field.classList.toggle('valid', isValid && field.value.trim());
        
        return isValid;
    }
    
    displayValidationErrors(errors) {
        // Limpiar errores anteriores
        document.querySelectorAll('.field-error').forEach(error => {
            error.style.display = 'none';
        });
        
        document.querySelectorAll('.checkout-input').forEach(input => {
            input.classList.remove('error');
        });
        
        // Mostrar nuevos errores
        Object.keys(errors).forEach(fieldName => {
            const field = document.getElementById(fieldName);
            const errorElement = document.getElementById(`${fieldName}-error`);
            
            if (field) {
                field.classList.add('error');
            }
            
            if (errorElement) {
                errorElement.textContent = errors[fieldName];
                errorElement.style.display = 'block';
            }
        });
    }
    
    saveStepData() {
        const currentStepElement = document.getElementById(`step-${this.currentStep}`);
        if (!currentStepElement) return;
        
        const inputs = currentStepElement.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            if (input.type === 'radio' || input.type === 'checkbox') {
                if (input.checked) {
                    this.formData[input.name] = input.value;
                }
            } else {
                this.formData[input.name] = input.value;
            }
        });
    }
    
    initializeAddressForms() {
        // Autocompletado de direcciones (Google Places API)
        this.initializeAddressAutocomplete();
        
        // Formateo de campos
        this.initializeFieldFormatting();
    }
    
    initializeAddressAutocomplete() {
        const addressFields = ['billing_address', 'shipping_address'];
        
        addressFields.forEach(fieldId => {
            const field = document.getElementById(fieldId);
            if (field && window.google && window.google.maps) {
                const autocomplete = new google.maps.places.Autocomplete(field, {
                    types: ['address'],
                    componentRestrictions: { country: 'pe' }
                });
                
                autocomplete.addListener('place_changed', () => {
                    const place = autocomplete.getPlace();
                    this.fillAddressFromPlace(place, fieldId.startsWith('billing') ? 'billing' : 'shipping');
                });
            }
        });
    }
    
    fillAddressFromPlace(place, prefix) {
        if (!place.geometry) return;
        
        const components = place.address_components;
        const fieldMappings = {
            street_number: `${prefix}_address`,
            route: `${prefix}_address`,
            locality: `${prefix}_city`,
            administrative_area_level_1: `${prefix}_state`,
            postal_code: `${prefix}_postal_code`,
            country: `${prefix}_country`
        };
        
        // Limpiar campos
        Object.values(fieldMappings).forEach(fieldId => {
            const field = document.getElementById(fieldId);
            if (field && fieldId !== `${prefix}_address`) {
                field.value = '';
            }
        });
        
        // Llenar campos
        let streetAddress = '';
        components.forEach(component => {
            const type = component.types[0];
            
            if (type === 'street_number') {
                streetAddress = component.long_name + ' ';
            } else if (type === 'route') {
                streetAddress += component.long_name;
                const addressField = document.getElementById(`${prefix}_address`);
                if (addressField) {
                    addressField.value = streetAddress;
                }
            } else if (fieldMappings[type]) {
                const field = document.getElementById(fieldMappings[type]);
                if (field) {
                    field.value = component.long_name;
                }
            }
        });
    }
    
    initializeFieldFormatting() {
        // Formateo de número de tarjeta
        const cardNumberField = document.getElementById('card_number');
        if (cardNumberField) {
            cardNumberField.addEventListener('input', (e) => {
                let value = e.target.value.replace(/\s/g, '');
                let formattedValue = value.replace(/(.{4})/g, '$1 ').trim();
                
                if (formattedValue.length > 23) {
                    formattedValue = formattedValue.substring(0, 23);
                }
                
                e.target.value = formattedValue;
                
                // Detectar tipo de tarjeta
                this.detectCardType(value);
            });
        }
        
        // Formateo de fecha de vencimiento
        const cardExpiryField = document.getElementById('card_expiry');
        if (cardExpiryField) {
            cardExpiryField.addEventListener('input', (e) => {
                let value = e.target.value.replace(/\D/g, '');
                
                if (value.length >= 2) {
                    value = value.substring(0, 2) + '/' + value.substring(2, 4);
                }
                
                e.target.value = value;
            });
        }
        
        // Formateo de teléfono
        const phoneFields = document.querySelectorAll('input[type="tel"]');
        phoneFields.forEach(field => {
            field.addEventListener('input', (e) => {
                let value = e.target.value.replace(/\D/g, '');
                
                // Formato peruano: +51 XXX XXX XXX
                if (value.startsWith('51')) {
                    value = value.substring(2);
                }
                
                if (value.length > 0) {
                    if (value.length <= 3) {
                        e.target.value = value;
                    } else if (value.length <= 6) {
                        e.target.value = value.substring(0, 3) + ' ' + value.substring(3);
                    } else {
                        e.target.value = value.substring(0, 3) + ' ' + value.substring(3, 6) + ' ' + value.substring(6, 9);
                    }
                }
            });
        });
    }
    
    detectCardType(cardNumber) {
        const cardTypeElement = document.querySelector('.card-type-icon');
        if (!cardTypeElement) return;
        
        const cardTypes = {
            visa: /^4/,
            mastercard: /^5[1-5]/,
            amex: /^3[47]/,
            discover: /^6(?:011|5)/,
            diners: /^3[068]/,
            jcb: /^35/
        };
        
        let detectedType = '';
        
        for (const [type, regex] of Object.entries(cardTypes)) {
            if (regex.test(cardNumber)) {
                detectedType = type;
                break;
            }
        }
        
        cardTypeElement.className = `card-type-icon ${detectedType}`;
        cardTypeElement.style.display = detectedType ? 'block' : 'none';
    }
    
    toggleShippingAddress() {
        const checkbox = document.getElementById('same-as-billing');
        const shippingSection = document.getElementById('shipping-address-section');
        
        if (!checkbox || !shippingSection) return;
        
        if (checkbox.checked) {
            // Copiar datos de facturación a envío
            this.copyBillingToShipping();
            shippingSection.style.display = 'none';
        } else {
            shippingSection.style.display = 'block';
        }
    }
    
    copyBillingToShipping() {
        const billingFields = [
            'first_name', 'last_name', 'phone', 'address', 
            'address_2', 'city', 'state', 'postal_code', 'country'
        ];
        
        billingFields.forEach(field => {
            const billingField = document.getElementById(`billing_${field}`);
            const shippingField = document.getElementById(`shipping_${field}`);
            
            if (billingField && shippingField) {
                shippingField.value = billingField.value;
            }
        });
    }
    
    selectPaymentMethod(method) {
        // Ocultar todos los formularios de pago
        document.querySelectorAll('.payment-form').forEach(form => {
            form.style.display = 'none';
        });
        
        // Mostrar formulario del método seleccionado
        const selectedForm = document.getElementById(`${method}-form`);
        if (selectedForm) {
            selectedForm.style.display = 'block';
        }
        
        // Actualizar resumen de pago
        this.updatePaymentSummary(method);
    }
    
    selectShippingMethod(method) {
        const methodData = this.shippingMethods[method];
        if (!methodData) return;
        
        // Actualizar costo de envío en el resumen
        const shippingCostElements = document.querySelectorAll('.shipping-cost');
        shippingCostElements.forEach(element => {
            element.textContent = methodData.price > 0 ? `S/ ${methodData.price.toFixed(2)}` : 'Gratis';
        });
        
        // Recalcular totales
        this.calculateTotals();
    }
    
    updatePaymentSummary(method) {
        const summaryElement = document.querySelector('.payment-summary');
        if (!summaryElement) return;
        
        const methodNames = {
            credit_card: 'Tarjeta de Crédito/Débito',
            paypal: 'PayPal',
            bank_transfer: 'Transferencia Bancaria',
            cash_on_delivery: 'Pago Contra Entrega'
        };
        
        summaryElement.innerHTML = `
            <div class="selected-payment-method">
                <i class="fas fa-${this.getPaymentIcon(method)}"></i>
                <span>${methodNames[method] || method}</span>
            </div>
        `;
    }
    
    getPaymentIcon(method) {
        const icons = {
            credit_card: 'credit-card',
            paypal: 'paypal',
            bank_transfer: 'university',
            cash_on_delivery: 'money-bill-wave'
        };
        return icons[method] || 'credit-card';
    }
    
    calculateTotals() {
        // Esta función se llamaría normalmente desde el servidor
        // Aquí mantenemos los cálculos del lado del cliente actualizados
        
        const subtotalElements = document.querySelectorAll('.checkout-subtotal');
        const shippingElements = document.querySelectorAll('.checkout-shipping');
        const taxElements = document.querySelectorAll('.checkout-tax');
        const totalElements = document.querySelectorAll('.checkout-total');
        
        // Los valores reales vendrían del servidor
        // Aquí solo actualizamos la visualización
    }
    
    async processOrder() {
        if (!this.validateCurrentStep()) {
            return;
        }
        
        this.saveStepData();
        
        const submitButton = document.querySelector('.btn-submit-order');
        this.setButtonLoading(submitButton, true);
        
        try {
            const formData = new FormData(document.getElementById('checkout-form'));
            
            const response = await fetch('/checkout/process', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            const data = await response.json();
            
            if (data.success) {
                if (data.payment_data && data.payment_data.redirect_url) {
                    // Redirigir a procesador de pagos externo
                    window.location.href = data.payment_data.redirect_url;
                } else {
                    // Redirigir a página de éxito
                    window.location.href = `/checkout/success/${data.order_id}`;
                }
            } else {
                this.showNotification(data.error || 'Error al procesar la orden', 'error');
                
                if (data.validation_errors) {
                    this.displayValidationErrors(data.validation_errors);
                }
            }
        } catch (error) {
            console.error('Error processing order:', error);
            this.showNotification('Error al procesar la orden', 'error');
        } finally {
            this.setButtonLoading(submitButton, false);
        }
    }
    
    validateCartItems() {
        // Validar que los items del carrito siguen siendo válidos
        // (stock, precios, etc.)
    }
    
    initializePaymentMethods() {
        // Cargar métodos de pago disponibles
        const paymentMethodsData = window.paymentMethods || {};
        this.paymentMethods = paymentMethodsData;
        
        // Cargar métodos de envío
        const shippingMethodsData = window.shippingMethods || {};
        this.shippingMethods = shippingMethodsData;
    }
    
    setButtonLoading(button, loading) {
        if (!button) return;
        
        if (loading) {
            button.disabled = true;
            button.dataset.originalText = button.innerHTML;
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Procesando...';
        } else {
            button.disabled = false;
            button.innerHTML = button.dataset.originalText || button.innerHTML;
        }
    }
    
    scrollToTop() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    }
    
    showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        notification.innerHTML = `
            <div class="notification-content">
                <i class="fas fa-${this.getNotificationIcon(type)}"></i>
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
                if (document.body.contains(notification)) {
                    document.body.removeChild(notification);
                }
            }, 300);
        }, 5000);
    }
    
    getNotificationIcon(type) {
        const icons = {
            success: 'check',
            error: 'times',
            warning: 'exclamation-triangle',
            info: 'info-circle'
        };
        return icons[type] || 'info-circle';
    }
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('checkout-form')) {
        window.checkoutManager = new CheckoutManager();
    }
});
