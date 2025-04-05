// phpcs:disable

/**
 * This is a main entrypoint for Webpack config.
 *
 * @since 1.0.0
 */
const path = require('path');
const fs = require('fs');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');

// Paths to find our files and provide BrowserSync functionality.
const projectPaths = {
    projectDir: __dirname,
    projectJsPath: path.resolve(__dirname, 'src/js'),
    projectJsAcfPath: path.resolve(__dirname, 'src/js/acf-blocks'),
    projectScssPath: path.resolve(__dirname, 'src/scss'),
    projectScssAcfPath: path.resolve(__dirname, 'src/scss/acf-blocks'),
    projectScssAloneBlockPath: path.resolve(__dirname, 'src/scss/alone-blocks'),
    projectImagesPath: path.resolve(__dirname, 'src/images'),
    projectOutput: path.resolve(__dirname, 'assets/dist'),
    projectWebpack: path.resolve(__dirname, 'webpack'),
    projectFontsPath: path.resolve(__dirname, 'assets/dist/fonts'),
};

// Generate entry points from provided files
const generateEntryPoints = (entryPointsData) => {
    const entries = {};

    const addEntriesFromDirectory = (dirPath, fileExtension, prefix = '') => {
        fs.readdirSync(dirPath).forEach((file) => {
            if (path.extname(file) === `.${fileExtension}`) {
                const basename = path.basename(file, `.${fileExtension}`);
                entries[prefix + basename] = path.resolve(dirPath, file);
            }
        });
    };

    if (entryPointsData.length) {
        entryPointsData.forEach((entryPointData) => {
            addEntriesFromDirectory(
                entryPointData.path,
                entryPointData.ext,
                entryPointData.prefix
            );
        });
    }

    return entries;
};

const entryPoints = [
    {
        path: projectPaths.projectJsAcfPath,
        ext: 'ts',
        prefix: 'js-',
    },
    {
        path: projectPaths.projectScssAcfPath,
        ext: 'scss',
        prefix: '',
    },
    {
        path: projectPaths.projectScssAloneBlockPath,
        ext: 'scss',
        prefix: '',
    },
];

const entries = generateEntryPoints(entryPoints);

// Define main SCSS file as an additional entry point
entries.main = `${projectPaths.projectScssPath}/main.scss`;
entries.frontend = `${projectPaths.projectJsPath}/main.ts`;
entries.backend = `${projectPaths.projectJsPath}/backend.ts`;

// Files to bundle
const projectFiles = {
    plugins: [
        new MiniCssExtractPlugin({
            filename: 'css/[name].css', // Output to assets/dist/css/main.css
        }),
    ],

    // BrowserSync settings
    browserSync: {
        enable: false,
        host: 'localhost',
        port: 3000,
        mode: 'server',
        server: { baseDir: ['public'] },
        files: '**/**/**.php',
        reload: true,
    },

    // JS configurations
    projectJs: {
        eslint: true,
        filename: 'js/[name].js',
        entry: entries,
        rules: {
            test: /\.tsx?$/,
        },
        options: {
            configFile: path.resolve('./tsconfig.json'),
        },
    },

    // CSS configurations
    projectCss: {
        postCss: `${projectPaths.projectWebpack}/postcss.config.js`,
        stylelint: true,
        filename: 'css/[name].css',
        use: 'scss',
        rules: {
            scss: {
                test: /\.s[ac]ss$/i,
            },
            postcss: {
                test: /\.pcss$/i,
            },
        },
    },

    // Image configurations
    projectImages: {
        rules: {
            test: /\.(jpe?g|png|gif|svg)$/i,
        },
        minimizerOptions: {
            plugins: [
                ['gifsicle', { interlaced: true }],
                ['jpegtran', { progressive: true }],
                ['optipng', { optimizationLevel: 5 }],
                ['svgo', { plugins: [{ removeViewBox: false }] }],
            ],
        },
    },

    // SCSS Rules
    module: {
        rules: [
            {
                test: /\.s[ac]ss$/i,
                use: [
                    MiniCssExtractPlugin.loader,
                    'css-loader',
                    'postcss-loader',
                    'sass-loader',
                ],
            },
        ],
    },
};

// Merging projectFiles & projectPaths objects
const projectOptions = {
    ...projectPaths,
    ...projectFiles,
    projectConfig: {},
};

// Get development or production setup based on the script from package.json
module.exports = (env) => {
    if (env.NODE_ENV === 'production') {
        return require('./webpack/config.production')(projectOptions);
    }
    return require('./webpack/config.development')(projectOptions);
};
