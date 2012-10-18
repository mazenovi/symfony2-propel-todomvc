define([
	'jquery',
	'underscore',
	'backbone',
	'text!templates/todos.html',
	'models/user',
	'common',
	'context'
], function( $, _, Backbone, todosTemplate, User, Common, Context ) {

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
			this.user = new User();
			this.user.set( Context.user );
			this.model.on( 'change', this.render, this );
			this.model.on( 'destroy', this.remove, this );
		},

		// Re-render the titles of the todo item.
		render: function() {
			var $el = $( this.el );
			$el.html( this.template( this.model.toJSON() ) );
			$el.toggleClass( 'completed', this.model.get('completed') );
			if(!this.model.isGranted('DELETE'))
			{
				this.$('.destroy').remove();
			}
			if(!this.model.isGranted('EDIT', 'completed'))
			{
				this.$('.toggle').first().attr('disabled', true);
			}

			this.input = this.$('.edit');
			return this;
		},

		// Toggle the `"completed"` state of the model.
		togglecompleted: function() {
			completed = this.$('.toggle').first().is(':checked');
			console.log(completed);
			if(this.model.isGranted('EDIT', 'completed'))
			{
				this.model.toggle();
			}

		},

		// Switch this view into `"editing"` mode, displaying the input field.
		edit: function() {
			if(this.model.isGranted('EDIT'))
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
			if(this.model.isGranted('DELETE')) {
				this.model.clear();
			}
		}
	});

	return TodoView;
});
