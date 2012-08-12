define([
	'underscore',
	'backbone'
], function( _, Backbone ) {

	var User = Backbone.Model.extend({
		
		url: Routing.generate('mazenovi_user_api_getme'),
		
		// Default attributes for the todo.
		defaults: {
			id: 0,
			avatar: 'u_u',
			username: 'anonymous',
			roles: [ ]

		},
	});

	return User;
});
