const path = require('path');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const HtmlWebpackPlugin = require('html-webpack-plugin');

module.exports = (env, argv) => {
    const isProduction = argv.mode === 'production';
    
    return {
        entry: {
            app: './public/js/app.js',
            admin: './public/js/admin.js',
            'routine-builder': './public/js/routine-builder.js',
            store: './public/js/store.js',
            checkout: './public/js/checkout.js'
        },
        
        output: {
            path: path.resolve(__dirname, 'public/dist'),
            filename: isProduction ? 'js/[name].[contenthash].js' : 'js/[name].js',
            chunkFilename: isProduction ? 'js/[name].[contenthash].chunk.js' : 'js/[name].chunk.js',
            publicPath: '/public/dist/',
            clean: true
        },
        
        module: {
            rules: [
                // JavaScript
                {
                    test: /\.js$/,
                    exclude: /node_modules/,
                    use: {
                        loader: 'babel-loader',
                        options: {
                            presets: ['@babel/preset-env'],
                            plugins: [
                                '@babel/plugin-proposal-class-properties',
                                '@babel/plugin-transform-runtime'
                            ]
                        }
                    }
                },
                
                // CSS/SCSS
                {
                    test: /\.(css|scss|sass)$/,
                    use: [
                        isProduction ? MiniCssExtractPlugin.loader : 'style-loader',
                        {
                            loader: 'css-loader',
                            options: {
                                importLoaders: 2,
                                sourceMap: !isProduction
                            }
                        },
                        {
                            loader: 'postcss-loader',
                            options: {
                                postcssOptions: {
                                    plugins: [
                                        ['autoprefixer'],
                                        ['cssnano', { preset: 'default' }]
                                    ]
                                },
                                sourceMap: !isProduction
                            }
                        },
                        {
                            loader: 'sass-loader',
                            options: {
                                sourceMap: !isProduction
                            }
                        }
                    ]
                },
                
                // Imágenes
                {
                    test: /\.(png|jpe?g|gif|svg|webp)$/i,
                    type: 'asset/resource',
                    generator: {
                        filename: 'images/[name].[hash][ext]'
                    },
                    parser: {
                        dataUrlCondition: {
                            maxSize: 8 * 1024 // 8kb
                        }
                    }
                },
                
                // Fuentes
                {
                    test: /\.(woff|woff2|eot|ttf|otf)$/i,
                    type: 'asset/resource',
                    generator: {
                        filename: 'fonts/[name].[hash][ext]'
                    }
                },
                
                // Videos
                {
                    test: /\.(mp4|webm|ogg)$/i,
                    type: 'asset/resource',
                    generator: {
                        filename: 'videos/[name].[hash][ext]'
                    }
                }
            ]
        },
        
        plugins: [
            // Extraer CSS
            new MiniCssExtractPlugin({
                filename: isProduction ? 'css/[name].[contenthash].css' : 'css/[name].css',
                chunkFilename: isProduction ? 'css/[name].[contenthash].chunk.css' : 'css/[name].chunk.css'
            }),
            
            // Generar manifest para cache busting
            ...(isProduction ? [
                new (require('webpack-manifest-plugin').WebpackManifestPlugin)({
                    fileName: 'manifest.json',
                    publicPath: '/public/dist/',
                    writeToFileEmit: true
                })
            ] : [])
        ],
        
        optimization: {
            splitChunks: {
                chunks: 'all',
                cacheGroups: {
                    vendor: {
                        test: /[\\/]node_modules[\\/]/,
                        name: 'vendors',
                        chunks: 'all',
                        priority: 10
                    },
                    common: {
                        name: 'common',
                        minChunks: 2,
                        chunks: 'all',
                        priority: 5,
                        reuseExistingChunk: true
                    }
                }
            },
            
            runtimeChunk: {
                name: 'runtime'
            },
            
            ...(isProduction ? {
                minimize: true,
                minimizer: [
                    new (require('terser-webpack-plugin'))({
                        terserOptions: {
                            compress: {
                                drop_console: true,
                                drop_debugger: true
                            }
                        }
                    }),
                    new (require('css-minimizer-webpack-plugin'))()
                ]
            } : {})
        },
        
        resolve: {
            extensions: ['.js', '.jsx', '.ts', '.tsx', '.json'],
            alias: {
                '@': path.resolve(__dirname, 'public/js'),
                '@css': path.resolve(__dirname, 'public/css'),
                '@images': path.resolve(__dirname, 'public/images'),
                '@fonts': path.resolve(__dirname, 'public/fonts')
            }
        },
        
        devtool: isProduction ? 'source-map' : 'eval-source-map',
        
        devServer: {
            static: {
                directory: path.join(__dirname, 'public')
            },
            compress: true,
            port: 3000,
            hot: true,
            open: true,
            historyApiFallback: true,
            proxy: {
                '/api': {
                    target: 'http://localhost:8000',
                    changeOrigin: true,
                    secure: false
                }
            }
        },
        
        performance: {
            hints: isProduction ? 'warning' : false,
            maxEntrypointSize: 512000,
            maxAssetSize: 512000
        },
        
        stats: {
            colors: true,
            modules: false,
            children: false,
            chunks: false,
            chunkModules: false
        }
    };
};
