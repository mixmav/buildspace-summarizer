/** @type {import('tailwindcss').Config} */
module.exports = {
    content: ["./resources/**/*.{vue,js,ts,jsx,tsx}"],

    theme: {
        extend: {},
    },

    plugins: [require("@tailwindcss/typography"), require("daisyui")],

    daisyui: {
        prefix: "daisy-",
        themes: [
            {
                dracula: {
                    ...require("daisyui/src/colors/themes")[
                        "[data-theme=dracula]"
                    ],
                    primary: "#CCFF66",
                    secondary: "#C49EFA",
                },
            },
        ],
    },
};
