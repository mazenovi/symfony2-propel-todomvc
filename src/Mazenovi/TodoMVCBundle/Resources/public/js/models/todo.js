define([
	'underscore',
	'backbone'
], function( _, Backbone ) {

	var Todo = Backbone.Model.extend({
		
		url: function() {

			var base = Routing.generate('mazenovi_todomvc_api_index');
			if (this.isNew()) {
				return base;
			}
			return base + (base.charAt(base.length - 1) == '/' ? '' : '/') + this.id;
		},
		
		// Default attributes for the todo.
		defaults: {
			title: '',
			completed: false,
			username: 'anonymous'

		},

		// Ensure that each todo created has `title`.
		initialize: function() {
			if ( !this.get('title') ) {
				this.set({
					'title': this.defaults.title
				});
			}
		},

		// Toggle the `completed` state of this todo item.
		toggle: function() {
			this.save({
				completed: !this.get('completed')
			});
		},

		// Remove this Todo from *localStorage* and delete its view.
		clear: function() {
			this.destroy();
		}
	});

	return Todo;
});
