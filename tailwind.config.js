import preset from './vendor/filament/support/tailwind.config.preset'

import defaultTheme from 'tailwindcss/defaultTheme';
const defaultTheme = require('tailwindcss/defaultTheme')

export default {
    presets: [preset],
    content: [
        './app/Filament/**/*.php',
        './resources/views/**/*.blade.php',
        './vendor/filament/**/*.blade.php',
    ],

    darkMode: 'class',

    theme: {
        extend: {
            fontFamily: {
                sans: ['Nunito', ...defaultTheme.fontFamily.sans],
                serif: ['Lori', ...defaultTheme.fontFamily.serif],
            },

            colors: {
                'nlrc-green': { 
                    100: '#068B4F', 
                    200: '#0B5732',
                },
                'nlrc-blue': {
                    50: '#e9f1f9',
                    100: '#d3e3f3',
                    200: '#a7c7e6',
                    300: '#7babd8',
                    400: '#4e8ec9',
                    500: '#0f72ba',
                    600: '#085389',
                    700: '#03365c',
                    800: '#011b32',
                    900: '#00040d',
                    950: '#000102',
                },
                'nlrc-orange': {
                    100:'#FFA12E',
                    200:'#CB7228',
                    300:'#7D481D',
                },
                'nlrc-gray': {
                    100: '#EFF1F7',
                    200: '#BACCD7',
                    300: '#404040',
                },
            },

            container: {
                center: true,
                padding: '1rem',
                screens: {
                    sm: '499px',
                    md: '768px',
                    lg: '991px',
                    xl: '1199px',
                    '2xl': '1496px',
                },
            },

            listStyleType: {
                square: 'square'
            },

        },

    },

    plugins: [
        function({ addUtilities }) {
            addUtilities({
                '.tiny-heading': {
                    fontSize: '0.7rem',
                    lineHeight: '1rem',
                    textTransform: 'uppercase',
                },
            });
        },
    ],
}
