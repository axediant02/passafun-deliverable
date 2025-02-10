/** @type {import('tailwindcss').Config} */
module.exports = {
  prefix: '',

  content: [
    './views/**/*.{js,jsx,vue}',
    './components/**/*.{js,jsx,vue}',
    './app/**/*.{js,jsx,vue}',
    './src/**/*.{js,jsx,vue}',
  ],
  theme: {
    container: {
      center: true,
      padding: '2rem',
      screens: {
        sm: '640px',
        md: '768px',
        lg: '1024px',
        xl: '1280px',
        '2xl': '1400px',
      },
    },
    extend: {
      colors: {
        disabled: '#CFCFCF',
        library: '#5197FF',
        error: '#EF4444',
        success: '#22C55E',
      },
      fontFamily: {
        roboto: ['Roboto', 'sans-serif'],
        sans: ['Roboto', 'sans-serif'],
        serif: ['"Noto Serif Tamil"', 'serif'],
        nunito: ['Nunito', 'sans-serif'],
        sans: ['sans-serif', 'Nunito'],
        feather: ['"Feather Bold"', 'sans-serif'],
        russo: ['"Russo One"', 'serif'],
        poppins: ['"Poppins"', 'sans-serif'],
      },
      aspectRatio: {
        square: '1 / 1',
      },
      borderRadius: {
        xl: '2rem',
        lg: '1.3rem',
        md: '1rem',
        sm: '0.5rem',
      },
      fontWeight: {
        thin: '100',
        extralight: '200',
        light: '300',
        normal: '400',
        medium: '500',
        semibold: '600',
        extrabold: '800',
        black: '900',
        extraheavy: '950',
      },
    },
  },
};
