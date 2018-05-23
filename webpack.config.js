// webpack.config.js
var Encore = require('@symfony/webpack-encore');

Encore
    .setOutputPath('web/build/')
    .setPublicPath('/build')
	.cleanupOutputBeforeBuild()
    // read main.js     -> output as web/build/app.js
    .addEntry('app', './web/js/main.js')
    // read global.scss -> output as web/build/global.css
    .addStyleEntry('global', './web/css/main.scss')
	
	//add style entry for each marques
    .addStyleEntry('nord', './web/css/style-nord.scss')
    .addStyleEntry('atalante', './web/css/style-ata.scss')
	
	//add style entry for responsive design
    .addStyleEntry('mobile', './web/css/responsive.scss')

    //add style entry for back office and marques
    .addStyleEntry('global-back', './web/css/main-back.scss')

    // enable features!
    .enableSassLoader(function(sassOptions) {}, {
        resolveUrlLoader: false
     })
	.autoProvidejQuery()
    .enableSourceMaps(!Encore.isProduction())
;

module.exports = Encore.getWebpackConfig();