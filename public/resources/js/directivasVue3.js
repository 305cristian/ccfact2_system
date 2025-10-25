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

            // Puedes seguir agregando las demás igual (v-email-only, v-currency, etc.)
        }
    };

    // 🔹 Exportar el plugin al objeto global (window)
    global.AllDirectives = AllDirectives;
})(window);
