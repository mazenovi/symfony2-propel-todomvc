define([
	'jquery',
	'underscore',
	'backbone',
	'collections/todos',
	'views/todos',
	'models/user',
	'views/users',
	'text!templates/stats.html',
	'common'
], function( $, _, Backbone, Todos, TodoView, User, UserView, statsTemplate, Common ) {

	var AppView = Backbone.View.extend({

		// Instead of generating a new element, bind to the existing skeleton of
		// the App already present in the HTML.
		el: $('#todoapp'),

		// Compile our stats template
		template: _.template( statsTemplate ),

		// Delegated events for creating new items, and clearing completed ones.
		events: {
			'keypress #new-todo':		'createOnEnter',
			'click #clear-completed':	'clearCompleted',
			'click #toggle-all':		'toggleAllComplete'
		},

		// At initialization we bind to the relevant events on the `Todos`
		// collection, when items are added or changed. Kick things off by
		// loading any preexisting todos that might be saved in *localStorage*.
		initialize: function() {
			this.input = this.$('#new-todo');
			this.allCheckbox = this.$('#toggle-all')[0];
			this.$footer = $('#footer');
			this.$main = $('#main');

			// set token as a body tag's attribute
			// see also http://stackoverflow.com/questions/220231/accessing-http-headers-in-javascript
      		// @todo best style integrate token?
      		// @todo set this token with twig will 
      		var req = new XMLHttpRequest();
      		req.open('GET', Routing.generate('mazenovi_user_api_getusertoken'), false);
      		req.setRequestHeader('Accept', 'application/json, text/javascript, */*; q=0.01');
      		req.send(null);
      		token = req.getResponseHeader('X-CSRF-Token');
      		$('body').attr('data-token', token);

      		// get the current user from server
      		// @todo how to share this user between class (collection) without a ne http request?
      		// @todo set the user attribute somewhere in the dom? (is this the solution?)
      		this.user = new User();
			this.user.fetch();			
			
			Todos.on( 'add', this.addOne, this );
			Todos.on( 'reset', this.addAll, this );
			Todos.on( 'all', this.render, this );
			Todos.fetch();

			var view = new UserView({ model: this.user });
			$('#new-todo').after( view.render().el );

		},

		// Re-rendering the App just means refreshing the statistics -- the rest
		// of the app doesn't change.
		render: function() {
			var completed = Todos.completed().length;
			var remaining = Todos.remaining().length;

			if ( Todos.length ) {
				this.$main.show();
				this.$footer.show();

				this.$footer.html(this.template({
					completed: completed,
					remaining: remaining
				}));

				this.$('#filters li a')
					.removeClass('selected')
					.filter( '[href="#/' + ( Common.TodoFilter || '' ) + '"]' )
					.addClass('selected');
			} else {
				this.$main.hide();
				this.$footer.hide();
			}
			if(typeof(this.allCheckbox) != 'undefined')
			{
				this.allCheckbox.checked = !remaining;
			}
		},

		// Add a single todo item to the list by creating a view for it, and
		// appending its element to the `<ul>`.
		addOne: function( todo ) {
			var view = new TodoView({ model: todo, user: this.user });
			$('#todo-list').append( view.render().el );
		},

		// Add all items in the **Todos** collection at once.
		addAll: function() {
			this.$('#todo-list').html('');

			switch( Common.TodoFilter ) {
				case 'active':
					_.each( Todos.remaining(), this.addOne );
					break;
				case 'completed':
					_.each( Todos.completed(), this.addOne );
					break;
				default:
					Todos.each( this.addOne, this );
					break;
			}
		},

		// Generate the attributes for a new Todo item.
		newAttributes: function() {
			return {
				title: this.input.val().trim(),
				order: Todos.nextOrder(),
				completed: false
			};
		},

		// If you hit return in the main input field, create new **Todo** model,
		// persisting it to *localStorage*.
		createOnEnter: function( e ) {
			if ( e.which !== Common.ENTER_KEY || !this.input.val().trim() ) {
				return;
			}

			Todos.create( this.newAttributes() );
			this.input.val('');
		},

		// Clear all completed todo items, destroying their models.
		clearCompleted: function() {
			_.each( Todos.completed(), function( todo ) {
				// @todo better way to condition features
				if( todo.userHasPermission('clear', user) )
				{
					todo.clear();
				}
			});

			return false;
		},

		// toggle all user's todos
		toggleAllComplete: function() {
			var completed = this.allCheckbox.checked;
			var user = this.user;
			Todos.each(function( todo ) {
				// @todo better way to condition features
				if( todo.userHasPermission('toggle', user) )
				{
					todo.save({
						'completed': completed
					});
				}
			});
		}
	});

	return AppView;
});
