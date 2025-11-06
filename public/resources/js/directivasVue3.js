/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/ClientSide/javascript.js to edit this template
 */


(function (global) {
    // 🔹 Creamos un "plugin" que contiene todas tus directivas
    const AllDirectives = {
        install(app) {
            // ==================== v-numbers-only ====================
            app.directive('numbers-only', {
                beforeMount(el, binding) {
                    el.addEventListener('keydown', function (e) {
                        const validKeys = [
                            'Backspace', 'Delete', 'Tab', 'Escape',
                            'ArrowLeft', 'ArrowRight', 'ArrowUp', 'ArrowDown',
                            'Home', 'End', 'Insert', 'Enter'
                        ];
                        const key = e.key;

                        if (/^\d$/.test(key))
                            return;
                        if (validKeys.includes(key))
                            return;
                        if (e.key === '.' && binding.value?.decimal && !el.value.includes('.'))
                            return;
                        if ((e.ctrlKey || e.metaKey) && ['a', 'c', 'v', 'x'].includes(key.toLowerCase()))
                            return;

                        e.preventDefault();
                    });
                }
            });

            // ==================== 2. DIRECTIVA: v-currency ====================
            app.directive('currency', {
                bind: function (el, binding) {
                    const decimals = binding.value && binding.value.decimals || 2;
                    const symbol = binding.value && binding.value.symbol || '$';

                    el.addEventListener('blur', function (e) {
                        let value = el.value.replace(/[^\d.,]/g, '');
                        value = value.replace(/,/g, '.');
                        value = parseFloat(value) || 0;

                        el.value = symbol + ' ' + value.toFixed(decimals).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
                    });

                    el.addEventListener('focus', function (e) {
                        el.value = el.value.replace(/[^\d.,]/g, '');
                    });
                }
            });

            // ==================== 3. DIRECTIVA: v-email-only ====================
            app.directive('email-only', {
                bind: function (el, binding) {
                    el.addEventListener('keypress', function (e) {
                        const validChars = /[a-zA-Z0-9._@-]/;

                        if (!validChars.test(e.key) && e.key !== 'Backspace') {
                            e.preventDefault();
                        }
                    });

                    el.addEventListener('blur', function (e) {
                        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

                        if (el.value && !emailRegex.test(el.value)) {
                            el.classList.add('is-invalid');
                            el.title = 'Correo electrónico inválido';
                        } else {
                            el.classList.remove('is-invalid');
                            el.title = '';
                        }
                    });
                }
            });

            // ==================== v-uppercase ====================
            app.directive('uppercase', {
                beforeMount(el) {
                    el.addEventListener('input', function () {
                        el.value = el.value.toUpperCase();
                    });
                    if (el.value)
                        el.value = el.value.toUpperCase();
                }
            });

            // ==================== v-lowercase ====================
            app.directive('lowercase', {
                beforeMount(el) {
                    el.addEventListener('input', function () {
                        el.value = el.value.toLowerCase();
                    });
                    if (el.value)
                        el.value = el.value.toLowerCase();
                }
            });

            // ==================== v-trim ====================
            app.directive('trim', {
                beforeMount(el) {
                    el.addEventListener('blur', function () {
                        el.value = el.value.trim();
                        el.dispatchEvent(new Event('input'));
                    });
                }
            });


// ==================== 8. DIRECTIVA: v-max-length ====================

            app.directive('max-length', {
                bind: function (el, binding) {
                    const maxLength = binding.value;

                    el.addEventListener('input', function (e) {
                        if (el.value.length > maxLength) {
                            el.value = el.value.slice(0, maxLength);
                        }
                    });
                }
            });

// ==================== 9. DIRECTIVA: v-no-spaces ====================

            app.directive('no-spaces', {
                bind: function (el, binding) {
                    el.addEventListener('input', function (e) {
                        el.value = el.value.replace(/\s/g, '');
                    });
                }
            });

// ==================== 10. DIRECTIVA: v-autofocus ====================

            app.directive('autofocus', {
                inserted: function (el, binding) {
                    if (binding.value !== false) {
                        el.focus();
                    }
                }
            });

            app.directive('tooltip', {
                mounted(el, binding) {
                    // Inicializa el tooltip
                    new bootstrap.Tooltip(el, {
                        title: binding.value || el.getAttribute('title'),
                        placement: binding.arg || 'top',
                        trigger: 'hover'
                    });
                },
                updated(el, binding) {
                    // Actualiza el tooltip si cambia el valor
                    const tooltip = bootstrap.Tooltip.getInstance(el);
                    if (tooltip && binding.value) {
                        tooltip.setContent({'.tooltip-inner': binding.value});
                    }
                },
                unmounted(el) {
                    // Destruye el tooltip al desmontar
                    const tooltip = bootstrap.Tooltip.getInstance(el);
                    if (tooltip) {
                        tooltip.dispose();
                    }
                }
            });





        }
    };

    // 🔹 Exportar el plugin al objeto global (window)
    global.AllDirectives = AllDirectives;
})(window);

// ==================== EJEMPLOS DE USO ====================

/*
 
 // CANTIDAD (solo números enteros)
 <input v-model="item.qty" v-numbers-only="{ decimal: false }" type="text">
 
 // PRECIO (números con decimales)
 <input v-model="item.price" v-numbers-only="{ decimal: true }" type="text">
 
 // MONEDA (formato con símbolo y separadores)
 <input v-currency="{ decimals: 2, symbol: '$' }" type="text">
 
 // EMAIL
 <input v-email-only type="text" placeholder="correo@example.com">
 
 // MAYÚSCULAS
 <input v-uppercase type="text" placeholder="Nombre">
 
 // MINÚSCULAS
 <input v-lowercase type="text" placeholder="usuario@mail.com">
 
 // TELÉFONO (con formato)
 <input v-phone-only="{ format: 'ES' }" type="text" placeholder="123-456-789">
 
 // SIN ESPACIOS
 <input v-no-spaces type="text" placeholder="SinEspacios">
 
 // LIMPIAR ESPACIOS AL SALIR
 <input v-trim type="text" placeholder="Texto">
 
 // MÁXIMO DE CARACTERES
 <input v-max-length="10" type="text" placeholder="Max 10 caracteres">
 
 // AUTOFOCUS (enfoque automático)
 <input v-autofocus type="text" placeholder="Se enfoca automáticamente">
 
 */