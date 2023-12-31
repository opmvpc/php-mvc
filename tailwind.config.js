/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ["./resources/views/**/*.php"],
  theme: {
    extend: {},
  },
  plugins: [require("@tailwindcss/typography"), require("@tailwindcss/forms")],
};
