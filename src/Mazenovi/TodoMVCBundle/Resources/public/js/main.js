// Require.js allows us to configure shortcut alias
require.config({
	// The shim config allows us to configure dependencies for
	// scripts that do not call define() to register a module
	deps: ["bootstrap", "google_analytics"],
	excludeShallow: ["context"],
	paths: {
		jquery: '../../bmatznerjquery/js/jquery',
		underscore: '../../bmatznerunderscore/js/underscore',
		backbone: '../../bmatznerbackbone/js/backbone',
		text: '../../bmatznerrequire/js/plugins/text',
		bootstrap: 'assets/bootstrap',
		google_analytics: 'libs/google_analytics',
		tpl: 'libs/tpl',
		context: 'empty'
	},
	pragmasOnSave: {
        excludeTpl: true
    },
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
		},
		'bootstrap': {
     		deps: ["jquery"]
    	}
	}
});

require([
	'views/app',
	'routers/router',
	'context'
], function( AppView, Workspace, Context ) {
	
	// see also http://stackoverflow.com/questions/7785079/how-use-token-authentication-with-rails-devise-and-backbone-js
	// @todo où est la bonne place / quelle est la bonne façon de faire
  	Backbone.old_sync = Backbone.sync;
  	Backbone.sync = function(method, model, options) {
    	var new_options =  _.extend({
	        beforeSend: function(xhr) {
    	      	if(typeof Context.user.username !== 'undefined' && typeof Context.wsse.password_digest !== 'undefined' && Context.wsse.nonce !== 'undefined' && Context.wsse.created !== 'undefined' )
          		{
            		xhr.setRequestHeader('X-WSSE', 'UsernameToken Username="' + Context.user.username + '", PasswordDigest="' +  Context.wsse.password_digest + '", Nonce="' + Context.wsse.nonce + '", Created="' + Context.wsse.created + '"');
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
