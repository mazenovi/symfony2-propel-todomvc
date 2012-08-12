define([
	'jquery',
	'underscore',
	'backbone',
	'text!templates/users.html',
	'common'
], function( $, _, Backbone, usersTemplate, Common ) {

	var UserView = Backbone.View.extend({

		tagName:  'div',

		template: _.template( usersTemplate ),

		// The TodoView listens for changes to its model, re-rendering. Since there's
		// a one-to-one correspondence between a **Todo** and a **TodoView** in this
		// app, we set a direct reference on the model for convenience.
		initialize: function() {
			this.model.on( 'change', this.render, this );
		},

		// Re-render the titles of the todo item.
		render: function() {
			var $el = $( this.el );

			$el.html( this.template( this.model.toJSON() ) );

			return this;
		},

	});

	return UserView;
});
