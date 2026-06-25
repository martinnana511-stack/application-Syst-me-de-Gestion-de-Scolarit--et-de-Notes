import purgecss from '@fullhuman/postcss-purgecss'

export default {
    plugins: [
        purgecss({
            content: [
                './storage/framework/views/*.php',
                './resources/views/**/*.blade.php',
                './resources/js/**/*.js',
            ],
            safelist: {
                standard: [
                    'show', 'fade', 'collapse', 'collapsing', 'active', 'modal-open'
                ],
                greedy: [
                    /^nav-/,      // Garde les styles des menus de navigation
                    /^dropdown-/, // Garde les menus déroulants
                    /^modal-/,    // Garde les fenêtres pop-up (modals)
                    /^carousel-/, // Garde les sliders s'il y en a
                    /^alert-/     // Garde les messages d'alertes (succès, erreur)
                ]
            }
        })
    ],
}