define([
	'jquery',
	'underscore',
	'backbone',
	'text!templates/todos.html',
	'models/user',
	'common'
], function( $, _, Backbone, todosTemplate, User, Common ) {

	var TodoView = Backbone.View.extend({

		tagName:  'li',

		template: _.template( todosTemplate ),

		// The DOM events specific to an item.
		events: {
			'click .toggle':	'togglecompleted',
			'dblclick .view':	'edit',
			'click .destroy':	'clear',
			'keypress .edit':	'updateOnEnter',
			'blur .edit':		'close'
		},

		// The TodoView listens for changes to its model, re-rendering. Since there's
		// a one-to-one correspondence between a **Todo** and a **TodoView** in this
		// app, we set a direct reference on the model for convenience.
		initialize: function() {
			// @todo we can save this call? true? 
			// @todo should we return an object rather than a class in user's model?
			this.user = new User();
			this.user.fetch();

			this.model.on( 'change', this.render, this );
			this.model.on( 'destroy', this.remove, this );
		},

		// Re-render the titles of the todo item.
		render: function() {
			var $el = $( this.el );
			// @todo how to feed this template with current user to show delete button only if userHasPermission
			$el.html( this.template( this.model.toJSON() ) );
			$el.toggleClass( 'completed', this.model.get('completed') );

			this.input = this.$('.edit');
			return this;
		},

		// Toggle the `"completed"` state of the model.
		togglecompleted: function() {
			this.model.toggle();
		},

		// Switch this view into `"editing"` mode, displaying the input field.
		edit: function() {
			// @todo better way to condition features
			if(this.model.userHasPermission('toggle', this.user))
			{
				$( this.el ).addClass('editing');
				this.input.focus();
			}
		},

		// Close the `"editing"` mode, saving changes to the todo.
		close: function() {
			var value = this.input.val().trim();

			if ( value ){
				this.model.save({ title: value });
			} else {
				this.clear();
			}

			$( this.el ).removeClass('editing');
		},

		// If you hit `enter`, we're through editing the item.
		updateOnEnter: function( e ) {
			if ( e.keyCode === Common.ENTER_KEY ) {
				this.close();
			}
		},

		// Remove the item, destroy the model.
		clear: function() {
			// @todo better way to condition features
			if(this.model.userHasPermission('toggle', this.user))
			{
				this.model.clear();
			}
		}
	});

	return TodoView;
});
