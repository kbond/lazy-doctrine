const { default: markdownItShiki } = require('markdown-it-shiki');

module.exports = {
    // Customize engine on configuration file directly
    engine: ({ marp }) => marp.use(markdownItShiki, { theme: 'github-dark' }),
    theme: './slides/dracula.css',
    allowLocalFiles: true,
}
