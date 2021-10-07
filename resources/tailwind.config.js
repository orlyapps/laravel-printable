import tailwindcssTypography from "https://cdn.skypack.dev/@tailwindcss/typography";

window.tailwindConfig = {
    plugins: [tailwindcssTypography],
    theme: {
        fontFamily: {
            sans: "Arial, sans-serif",
        },
        fontSize: {
            xs: "7pt",
            sm: "10pt",
            base: "11pt",
            lg: "14pt",
        },
        extend: {
            typography: {
                DEFAULT: {
                    css: {
                        color: "#000",
                    },
                },
            },
            screens: {
                print: {
                    raw: "print",
                },
            },
            screens: {
                print: {
                    raw: "print",
                },
            },
        },
    },
};
