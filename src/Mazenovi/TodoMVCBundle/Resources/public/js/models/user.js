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

		can: function( acl ) {
			console.log(acl['object']);
			var roles;
			var all_permissions = acl['object'].get('permissions');
			if(typeof acl['field']  !== 'undefined'){
				if( typeof all_permissions['fields'] !== 'undefined' && typeof all_permissions['fields'][acl['field']] !== 'undefined') {
					roles = all_permissions['fields'][acl['field']];
				}
				else {
					return false;
				}
			}
			else {
				if( typeof all_permissions['object'] !== 'undefined' ){
					roles = all_permissions['object'];
				}
				else {
					return false;
				}
			}
			
			return roles.indexOf(acl['action']) != -1;

		}
	});

	return User;
});
