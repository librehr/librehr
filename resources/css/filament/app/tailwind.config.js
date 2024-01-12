import preset from '../../../../vendor/filament/filament/tailwind.config.preset'

export default {
    presets: [preset],
    content: [
        './app/Filament/**/*.php',
        './resources/views/filament/**/*.blade.php',
        './resources/views/forms/**/*.blade.php',
        './resources/js/**/*.js',
        './resources/views/components/**/*.blade.php',
        './vendor/filament/**/*.blade.php',
        './vendor/filament/pages/**/*.blade.php',
    ],
}
