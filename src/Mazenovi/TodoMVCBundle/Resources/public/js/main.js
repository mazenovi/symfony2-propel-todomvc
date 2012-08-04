// Require.js allows us to configure shortcut alias
require.config({
	// The shim config allows us to configure dependencies for
	// scripts that do not call define() to register a module
	shim: {
		'underscore': {
			exports: '_'
		},
		'backbone': {
			deps: [
				'underscore',
				'jquery'
			],
			exports: 'Backbone'
		}
	},
	paths: {
		jquery: '/bundles/manymulesjquery/js/jquery.min',
		underscore: '/bundles/manymulesunderscorejs/js/underscore.min',
		backbone: '/bundles/manymulesbackbonejs/js/backbone.min',
		text: '/bundles/manymulesrequirejs/js/plugins/text.min'
	}
});

require([
	'views/app',
	'routers/router'
], function( AppView, Workspace ) {
	
	// see also http://stackoverflow.com/questions/7785079/how-use-token-authentication-with-rails-devise-and-backbone-js
	// @zemouette où est la bonne place / quelle est la bonne façon de faire
  	Backbone.old_sync = Backbone.sync;
  	Backbone.sync = function(method, model, options) {
    	var new_options =  _.extend({
	        beforeSend: function(xhr) {
    	      	if($('body').attr('data-token') != 'undefined')
          		{
		            var token = $('body').attr('data-token');
            		xhr.setRequestHeader('X-CSRF-Token', token);
				}	
			}
    	}, options)
    	Backbone.old_sync(method, model, new_options);
  	};

	// Initialize routing and start Backbone.history()
	new Workspace();
	Backbone.history.start();

	// Initialize the application view
	new AppView();
});
