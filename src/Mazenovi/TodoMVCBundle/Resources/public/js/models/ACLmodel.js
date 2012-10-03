define([
	'underscore',
	'backbone'
], function( _, Backbone ) {

	var ACLmodel = Backbone.Model.extend({
		
		isGranted: function( permission, property ) {
			var roles;
			var all_permissions = this.get('permissions');
			if(all_permissions['fields'] && all_permissions['fields'][property]) {
				roles = all_permissions['fields'][property];
			} else if(all_permissions['object']){
				roles = all_permissions['object'];
			}
			else {
				return false;
			}
			return roles.indexOf(permission) != -1;
		}
	});

	return ACLmodel;
});