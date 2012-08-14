// node r.js -o app.build.js
({
    baseUrl: "./",
    name: "main",
    out: "main-built.js",
    paths: {
        jquery: '../../manymulesjquery/js/jquery.min',
		underscore: '../../manymulesunderscorejs/js/underscore.min',
		backbone: '../../manymulesbackbonejs/js/backbone.min',
		text: '../../manymulesrequirejs/js/plugins/text.min'
	}
})
                