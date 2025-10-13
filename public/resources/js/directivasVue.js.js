/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/ClientSide/javascript.js to edit this template
 */

/**
 * Description of viewEmployee
 * @author Cristian R. Paz
 * @Date 13 oct. 2025
 * @Time 12:45:10
 */



/**
 * Directiva Vue: v-numbers-only
 * Permite solo números y decimales en inputs
 */

const NumbersOnlyDirective = {
    install: function (Vue) {
        Vue.directive('numbers-only', {
            bind: function (el, binding, vnode) {
                el.addEventListener('keydown', function (e) {
                    const validKeys = [
                        'Backspace', 'Delete', 'Tab', 'Escape',
                        'ArrowLeft', 'ArrowRight', 'ArrowUp', 'ArrowDown',
                        'Home', 'End', 'Insert',
                        'Enter'
                    ];
                    const key = e.key;

                    // Permitir si es número
                    if (/^\d$/.test(key))
                        return;

                    // Permitir si es una tecla de navegación
                    if (validKeys.includes(key))
                        return;

                    // Permitir punto si se permite decimal
                    if (e.key === '.' && binding.value && binding.value.decimal && !el.value.includes('.')) {
                        return;
                    }

                    // Permitir Ctrl+A, Ctrl+C, Ctrl+V, Ctrl+X
                    if ((e.ctrlKey || e.metaKey) && ['a', 'c', 'v', 'x'].includes(key.toLowerCase())) {
                        return;
                    }

                    // Bloquear todo lo demás
                    e.preventDefault();
                });
            }
        });
    }
};

// ==================== 2. DIRECTIVA: v-currency ====================
const CurrencyDirective = {
    install: function (Vue) {
        Vue.directive('currency', {
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
    }
};

// ==================== 3. DIRECTIVA: v-email-only ====================
const EmailOnlyDirective = {
    install: function (Vue) {
        Vue.directive('email-only', {
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
    }
};

// ==================== 4. DIRECTIVA: v-uppercase ====================
const UppercaseDirective = {
    install: function (Vue) {
        Vue.directive('uppercase', {
            bind: function (el, binding) {
                el.addEventListener('input', function (e) {
                    el.value = el.value.toUpperCase();
                });

                // Convertir valor inicial si existe
                if (el.value) {
                    el.value = el.value.toUpperCase();
                }
            }
        });
    }
};

// ==================== 5. DIRECTIVA: v-lowercase ====================
const LowercaseDirective = {
    install: function (Vue) {
        Vue.directive('lowercase', {
            bind: function (el, binding) {
                el.addEventListener('input', function (e) {
                    el.value = el.value.toLowerCase();
                });

                // Convertir valor inicial si existe
                if (el.value) {
                    el.value = el.value.toLowerCase();
                }
            }
        });
    }
};

// ==================== 6. DIRECTIVA: v-phone-only ====================
const PhoneOnlyDirective = {
    install: function (Vue) {
        Vue.directive('phone-only', {
            bind: function (el, binding) {
                const format = binding.value && binding.value.format || 'basic'; // basic, US, ES

                el.addEventListener('keydown', function (e) {
                    const validKeys = [
                        'Backspace', 'Delete', 'Tab', 'Escape',
                        'ArrowLeft', 'ArrowRight', 'ArrowUp', 'ArrowDown',
                        'Home', 'End', 'Insert', 'Enter'
                    ];

                    if (/^\d$/.test(e.key))
                        return;
                    if (validKeys.includes(e.key))
                        return;
                    if ((e.ctrlKey || e.metaKey) && ['a', 'c', 'v', 'x'].includes(e.key.toLowerCase()))
                        return;

                    e.preventDefault();
                });

                el.addEventListener('blur', function (e) {
                    let value = el.value.replace(/\D/g, '');

                    if (format === 'US' && value.length >= 10) {
                        value = '(' + value.slice(0, 3) + ') ' + value.slice(3, 6) + '-' + value.slice(6, 10);
                    } else if (format === 'ES' && value.length >= 9) {
                        value = value.slice(0, 3) + '-' + value.slice(3, 6) + '-' + value.slice(6, 9);
                    }

                    el.value = value;
                });
            }
        });
    }
};

// ==================== 7. DIRECTIVA: v-trim ====================
const TrimDirective = {
    install: function (Vue) {
        Vue.directive('trim', {
            bind: function (el, binding) {
                el.addEventListener('blur', function (e) {
                    el.value = el.value.trim();
                    el.dispatchEvent(new Event('input'));
                });
            }
        });
    }
};

// ==================== 8. DIRECTIVA: v-max-length ====================
const MaxLengthDirective = {
    install: function (Vue) {
        Vue.directive('max-length', {
            bind: function (el, binding) {
                const maxLength = binding.value;

                el.addEventListener('input', function (e) {
                    if (el.value.length > maxLength) {
                        el.value = el.value.slice(0, maxLength);
                    }
                });
            }
        });
    }
};

// ==================== 9. DIRECTIVA: v-no-spaces ====================
const NoSpacesDirective = {
    install: function (Vue) {
        Vue.directive('no-spaces', {
            bind: function (el, binding) {
                el.addEventListener('input', function (e) {
                    el.value = el.value.replace(/\s/g, '');
                });
            }
        });
    }
};

// ==================== 10. DIRECTIVA: v-autofocus ====================
const AutofocusDirective = {
    install: function (Vue) {
        Vue.directive('autofocus', {
            inserted: function (el, binding) {
                if (binding.value !== false) {
                    el.focus();
                }
            }
        });
    }
};

// ==================== EXPORTAR TODAS LAS DIRECTIVAS ====================
(function (global) {
    // Exportar globalmente
    const AllDirectives = {
        install: function (Vue) {
            Vue.use(NumbersOnlyDirective);
            Vue.use(CurrencyDirective);
            Vue.use(EmailOnlyDirective);
            Vue.use(UppercaseDirective);
            Vue.use(LowercaseDirective);
            Vue.use(PhoneOnlyDirective);
            Vue.use(TrimDirective);
            Vue.use(MaxLengthDirective);
            Vue.use(NoSpacesDirective);
            Vue.use(AutofocusDirective);
        }
    };

    if (typeof module !== 'undefined' && module.exports) {
        module.exports = AllDirectives;
    } else {
        global.AllDirectives = AllDirectives;
    }
})(typeof window !== 'undefined' ? window : global);


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
