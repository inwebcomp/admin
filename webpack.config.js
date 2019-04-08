let srcRoot = `${__dirname}/src/resources/assets/js`;

module.exports = {
    resolve: {
        alias: {
            "~js": `${srcRoot}`,
            "~pages": `${srcRoot}/pages`,
            "~mixins": `${srcRoot}/mixins`,
            "~directives": `${srcRoot}/directives`,
            "~fields": `${srcRoot}/components/fields`,
            "~views": `${srcRoot}/components/views`,
            "~elements": `${srcRoot}/components/elements`,
            "~components": `${srcRoot}/components`,
        }
    },
};
