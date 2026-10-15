/** @type {import('tailwindcss').Config} */
export default {
  content: ['./resources/views/**/*.blade.php'],
  theme: {
    extend: {
      colors: {
        // palet dari desain Figma
        forest: '#183c2f', forest2: '#24533f', leaf: '#e5eba9', gold: '#f5b849', cream: '#f6f2e8', sand: '#e8e8bc', mist: '#f1f2bd',
        // token yang dipakai view lama
        bg: '#f6f2e8', sf: '#ffffff', tx: '#183c2f', mu: '#5d7468', pr: '#183c2f', pt: '#ffffff', ac: '#f5b849', bd: '#ddd9c3', er: '#b3261e',
      },
      fontFamily: { sans: ['"DM Sans"', 'sans-serif'], display: ['"Libre Franklin"', 'sans-serif'] },
    },
  },
}
