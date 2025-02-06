import { defineConfig } from 'vite';
import laravel, { refreshPaths } from 'laravel-vite-plugin';
import path from 'path';

const cssStuff = [
    'public/css/quiz/quiz.scss',
    'public/css/quiz/dashboard.scss',
    'public/css/quiz/main.scss',
    'public/css/quiz/view_quiz.scss',
    'resources/css/app.scss',
    'resources/css/app.css',
    'resources/css/filament/admin/tailwind-theme.css'
];

const jsStuff = [
    'public/js/quiz/quiz.js',
    'public/js/quiz/add_quiz.js',
    'public/js/quiz/copy_link.js',
    'public/js/quiz/text_area_count.js',
    'resources/js/app.js'
];

export default defineConfig({
    plugins: [
        laravel({
            input: [...cssStuff, ...jsStuff],
            refresh: true,
            // refresh: [
            //     ...refreshPaths,
            //     'app/Filament/**',
            //     'app/Forms/Components/**',
            //     'app/Livewire/**',
            //     'app/Infolists/Components/**',
            //     'app/Providers/Filament/**',
            //     'app/Tables/Columns/**',
            // ],
        }),
    ], resolve: {
        alias: {
          '~public'      : path.resolve(__dirname, 'public'),
          '~resources'   : path.resolve(__dirname, 'resources'),
        },
    },
})
