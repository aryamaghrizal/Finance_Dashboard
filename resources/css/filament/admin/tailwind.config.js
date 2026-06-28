import preset from './vendor/filament/filament/tailwind.config.preset'
import colors from 'tailwindcss/colors'

export default {
    presets: [preset],
    content: [
        './app/Filament/**/*.php',
        './resources/views/filament/**/*.blade.php',
        './vendor/filament/**/*.blade.php',
    ],
    theme: {
        extend: {
            colors: {
                // Baris ini secara eksplisit menambahkan semua varian warna 'slate'
                // agar bisa digunakan di theme.css
                slate: colors.slate,
            },
        },
    },
}