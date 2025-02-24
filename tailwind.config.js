/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        './resources/views/**/*.blade.php',
<<<<<<< HEAD
        './resources/js/**/*.js'
=======
        './resources/**/*.js',
        './resources/**/*.vue',
>>>>>>> 9c15d1aae1a791efaa56f70173cc365afe1dc949
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', 'sans-serif'],
            },
        },
    },
    plugins: [
        require('@tailwindcss/forms'),
        require('@tailwindcss/typography'),
    ],
};
