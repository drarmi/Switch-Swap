const gulp = require("gulp");
const sass = require("gulp-sass")(require("sass"));
const sourcemaps = require("gulp-sourcemaps");
const autoprefixer = require("gulp-autoprefixer");
const notify = require("gulp-notify");
const del = require("del");
const browserSync = require("browser-sync").create();
const imagemin = require("gulp-imagemin");
const newer = require("gulp-newer");
const plumber = require("gulp-plumber");
const postcssResponsiveFont = require("postcss-responsive-font");
const cssnext = require("postcss-cssnext");
const cssnano = require("gulp-cssnano");
const postcssLineHeightPxToUnitless = require("postcss-line-height-px-to-unitless");
const imageminJpegRecompress = require("imagemin-jpeg-recompress");
const imageminPngquant = require("imagemin-pngquant");
const postcss = require("gulp-postcss");
const MiniCssExtractPlugin = require("mini-css-extract-plugin");
const CssMinimizerPlugin = require("css-minimizer-webpack-plugin");
const TerserPlugin = require("terser-webpack-plugin");
const webpack = require("webpack");
const webpackStream = require("webpack-stream");
const zip = require("gulp-zip");
const ignore = require("gulp-ignore");
const rename = require("gulp-rename");

gulp.task("zip-theme", function () {
    return gulp
        .src(["**/*", "!node_modules/**/*"])
        .pipe(ignore.exclude("node_modules"))
        .pipe(
            rename(function (path) {
                path.dirname = "omnisBase/" + path.dirname;
            })
        )
        .pipe(zip("omnisBase.zip"))
        .pipe(gulp.dest(".."));
});

gulp.task("delete-zip-theme", () => del(["../omnisBase.zip"], {force: true}));

gulp.task("server", function () {
    browserSync.init({
        proxy: "localhost/default-wp/",
        "notify": false
    });
});

gulp.task("php", function () {
    return gulp
        .src("*.php")
        .pipe(plumber())
        .pipe(browserSync.reload({stream: true}));
});

gulp.task("sass", function () {
    var plugins = [cssnext({browsers: ["last 2 versions"]}), postcssResponsiveFont, postcssLineHeightPxToUnitless()];
    return gulp
        .src("./assets/src/sass/*.scss")
        .pipe(plumber())
        .pipe(sourcemaps.init())
        .pipe(sass({outputStyle: "expanded"}).on("error", notify.onError()))
        .pipe(postcss(plugins))
        .pipe(cssnano())
        .pipe(sourcemaps.write())
        .pipe(gulp.dest("./assets/dist/css/"))
        .pipe(browserSync.reload({stream: true}));
});

gulp.task("css", function () {
    return gulp.src("./assets/src/css/**/*").pipe(gulp.dest("./assets/dist/css"));
});

gulp.task("fonts", function () {
    return gulp.src("./assets/src/fonts/**/*").pipe(gulp.dest("./assets/dist/fonts"));
});

gulp.task("js", function () {
    return gulp
        .src("./assets/src/js/main.js")
        .pipe(plumber())
        .pipe(
            webpackStream({
                mode: process.env.NODE_ENV === "production" ? "production" : "development",
                devtool: "eval-source-map",
                module: {
                    rules: [
                        {
                            test: /\.m?js$/,
                            exclude: /(node_modules)/,
                            use: {
                                loader: "babel-loader",
                                options: {
                                    presets: ["@babel/preset-env"],
                                    plugins: ["@babel/plugin-transform-runtime"],
                                },
                            },
                        },
                    ],
                },
                optimization: {
                    minimizer: [new TerserPlugin(), new CssMinimizerPlugin()],
                    minimize: true,
                },
                performance: {
                    hints: false,
                    maxEntrypointSize: 512000,
                    maxAssetSize: 512000,
                },
                output: {
                    filename: "main.js",
                },
            })
        )
        .pipe(gulp.dest("./assets/dist/js"))
        .pipe(browserSync.reload({stream: true}));
});

gulp.task("vendor", function () {
    return gulp
        .src("./assets/src/vendor/vendor.js")
        .pipe(plumber())
        .pipe(
            webpackStream({
                mode: process.env.NODE_ENV === "production" ? "production" : "development",
                module: {
                    rules: [
                        {
                            test: /\.m?js$/,
                            exclude: /(node_modules|bower_components)/,
                            use: {
                                loader: "babel-loader",
                                options: {
                                    presets: ["@babel/preset-env"],
                                    plugins: ["@babel/plugin-transform-runtime"],
                                },
                            },
                        },
                        {
                            test: /\.s[ac]ss$/i,
                            use: [
                                {
                                    loader: MiniCssExtractPlugin.loader,
                                },
                                {
                                    loader: "css-loader",
                                    options: {sourceMap: true},
                                },
                                {
                                    loader: "sass-loader",
                                    options: {
                                        sourceMap: true,
                                    },
                                },
                            ],
                            sideEffects: true,
                        },
                    ],
                },
                plugins: [
                    new MiniCssExtractPlugin({
                        filename: "vendor.css",
                    }),
                ],
                optimization: {
                    minimizer: [new TerserPlugin(), new CssMinimizerPlugin()],
                    minimize: true,
                },
                performance: {
                    hints: false,
                    maxEntrypointSize: 512000,
                    maxAssetSize: 512000,
                },
                devtool: "eval-source-map",
                output: {
                    filename: "vendor.js",
                },
            })
        )
        .pipe(gulp.dest("./assets/dist/vendor"))
        .pipe(browserSync.reload({stream: true}));
});

gulp.task("images", function () {
    return gulp
        .src("./assets/src/images/**/*")
        .pipe(plumber())
        .pipe(newer("./assets/dist/images"))
        .pipe(
            imagemin([
                imagemin.gifsicle({interlaced: true}),
                imageminJpegRecompress({
                    progressive: true,
                    min: 70,
                    max: 80,
                }),
                imageminPngquant({quality: [0.7, 0.8]}),
                imagemin.svgo({plugins: [{removeViewBox: true}]}),
            ])
        )
        .pipe(gulp.dest("./assets/dist/images"))
        .pipe(browserSync.reload({stream: true}));
});

gulp.task("clean", function () {
    return del(["build"]);
});

gulp.task("clean", function () {
    return del(["./assets/dist"]);
});

gulp.task("uploads", function () {
    return gulp
        .src("../../uploads/**/**/*.*")
        .pipe(plumber())
        .pipe(newer("../../uploads/"))
        .pipe(imagemin())
        .src("../../uploads/")
        .pipe(browserSync.reload({stream: true}));
});

gulp.task("watch", function () {
    gulp.watch("./**/*.php", gulp.series("php"));
    gulp.watch("./assets/src/**/*.scss", gulp.series("sass"));
    gulp.watch("./assets/src/js/**/*.js", gulp.series("js"));
    gulp.watch("./assets/src/vendor/vendor.js", gulp.series("vendor"));
    gulp.watch("./assets/src/vendor/vendor.scss", gulp.series("vendor"));
    gulp.watch("./assets/src/images/**/*", gulp.series("images"));
    gulp.watch("./assets/src/fonts/**/*", gulp.series("fonts"));
    gulp.watch("./assets/src/css/**/*", gulp.series("css"));
});

gulp.task("start", gulp.series("clean", gulp.parallel("php", "sass", "vendor", "js", "css", "fonts", "images"), gulp.parallel("watch", "server")));

gulp.task("build", gulp.series("clean", gulp.parallel("php", "sass", "vendor", "js", "css", "fonts", "images")));
