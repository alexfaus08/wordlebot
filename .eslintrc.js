module.exports = {
    extends: [
        'plugin:vue/vue3-recommended',
        'plugin:vue/vue3-essential',
        'plugin:tailwindcss/recommended',
        'google'
    ],
    parserOptions: {
        ecmaVersion: 12,
        parser: '@typescript-eslint/parser',
        sourceType: 'module'
    },
    plugins: [
        'tailwindcss',
        'vue',
        '@typescript-eslint'
    ],
    rules: {
        'max-len': 'off',
        'indent': ['error', 4],
        'quotes': ['error', 'single'],
        'vue/mustache-interpolation-spacing': ['error', 'always'],
        'comma-dangle': ['error',
            {
                'arrays': 'never',
                'objects': 'never',
                'imports': 'never',
                'exports': 'never',
                'functions': 'never'
            }
        ],
        'require-jsdoc': ['error', {
            'require': {
                'FunctionDeclaration': false,
                'MethodDefinition': false,
                'ClassDeclaration': false,
                'ArrowFunctionExpression': false,
                'FunctionExpression': false
            }
        }]
    },
    root: true
};
