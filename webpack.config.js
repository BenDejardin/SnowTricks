// webpack.config.js
const Encore = require('@symfony/webpack-encore');

Encore
    .setOutputPath('public/build/') // Chemin de sortie des fichiers compilés
    .setPublicPath('/build') // Chemin public vers les fichiers compilés
    // .addEntry('app', './assets/js/app.js') // Fichier d'entrée pour vos scripts JS
    .addStyleEntry('css', './public/assets/css/app.css') // Fichier d'entrée pour vos styles CSS
    .enableSingleRuntimeChunk() // Activer un seul fichier runtime pour l'ensemble des dépendances
    .splitEntryChunks() // Diviser les dépendances en plusieurs fichiers pour une meilleure performance
    .cleanupOutputBeforeBuild() // Nettoyer les fichiers de sortie avant la compilation
    .enableSourceMaps(!Encore.isProduction()) // Activer les sourcemaps en développement
    .enableVersioning(Encore.isProduction()); // Activer la versioning des fichiers en production

module.exports = Encore.getWebpackConfig();
