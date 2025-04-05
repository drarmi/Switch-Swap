// phpcs:disable

/**
 * Webpack's configurations for the development environment
 * based on the script from package.json
 * Run with: "npm run dev" or "npm run dev:watch"
 *
 * @since 1.0.0
 */

const ESLintPlugin = require('eslint-webpack-plugin'); // Find and fix problems in your JavaScript code
const StylelintPlugin = require('stylelint-webpack-plugin');
const MiniCssExtractPlugin = require('mini-css-extract-plugin'); // Для сборки CSS в отдельные файлы
const path = require('path'); // Helps you avoid errors and enforce conventions in your styles

module.exports = (projectOptions) => {
    process.env.NODE_ENV = 'development'; // Set environment level to 'development'

    /**
     * The base skeleton
     */
    const Base = require('./config.base')(projectOptions);

    /**
     * CSS rules
     */
    const cssRules = {
        test: /\.s[ac]ss$/i,
        use: [
            MiniCssExtractPlugin.loader,
            'css-loader',
            'postcss-loader',
            'sass-loader'
        ],
    };

    /**
     * JS rules
     */
    const jsRules = {
        ...Base.jsRules,
        ...{
            // add JS rules for development here
        },
    };

    /**
     * Image rules
     */
    const imageRules = {
        ...Base.imageRules,
        ...{
            // add image rules for development here
        },
    };

    /**
     * Font rules
     */
    const fontRules = {
        ...Base.fontRules,
        ...{
            // add font rules for development here
        },
    };

    /**
     * Optimization rules
     */
    const optimizations = {
        ...Base.optimizations,
        ...{
            // add optimization rules for development here
        },
    };

    /**
     * Plugins
     */
    const plugins = [
        ...Base.plugins,
        new MiniCssExtractPlugin({
            filename: 'css/[name].css',
        }),
        // Add eslint to plugins if enabled
        projectOptions.projectJs.eslint && new ESLintPlugin(),
        // Add stylelint to plugins if enabled
        projectOptions.projectCss.stylelint && new StylelintPlugin(),
    ].filter(Boolean); // Remove false or undefined entries from plugins array

    /**
     * Add sourcemap for development if enabled
     */
    const sourceMap = { devtool: false };
    if (
        projectOptions.projectSourceMaps &&
        projectOptions.projectSourceMaps.enable === true &&
        (projectOptions.projectSourceMaps.env === 'dev' ||
            projectOptions.projectSourceMaps.env === 'dev-prod')
    ) {
        sourceMap.devtool = projectOptions.projectSourceMaps.devtool;
    }

    /**
     * The configuration that's being returned to Webpack
     */
    return {
        mode: 'development',
        entry: projectOptions.projectJs.entry, // Define the starting point of the application.
        output: {
            path: projectOptions.projectOutput,
            filename: projectOptions.projectJs.filename,
            assetModuleFilename: 'fonts/[name][ext]',
        },
        devtool: sourceMap.devtool,
        optimization: optimizations,
        module: { rules: [cssRules, jsRules, imageRules, fontRules] },
        plugins,
        resolve: {
            modules: [path.resolve(__dirname, 'src'), 'node_modules'],
            extensions: ['.ts', '.js', '.json'],
        },
    };
};
