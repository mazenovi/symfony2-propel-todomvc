define([
	'jquery',
	'underscore',
	'backbone',
	'collections/todos',
	'views/todos',
	'models/user',
	'views/users',
	'tpl!templates/stats.html',
	'common',
	'context'
], function( $, _, Backbone, Todos, TodoView, User, UserView, statsTemplate, Common, Context ) {

	var AppView = Backbone.View.extend({

		// Instead of generating a new element, bind to the existing skeleton of
		// the App already present in the HTML.
		el: $('#todoapp'),

		// Compile our stats template
		//template: _.template( statsTemplate ),

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
			this.allCheckbox = this.$('#toggle-all').first();
			this.$footer = $('#footer');
			this.$main = $('#main');

			this.user = new User();
			this.user.set(Context.user);
			
			Todos.on( 'add', this.addOne, this );
			Todos.on( 'reset', this.addAll, this );
			Todos.on( 'all', this.render, this );
			Todos.fetch();

			if(!this.user.id)
			{
				this.input.remove();				
				this.allCheckbox.remove();
				this.$footer.remove();
			}
			else
			{
				var view = new UserView({ model: this.user });
				$('#new-todo').after( view.render().el );
			}

		},

		// Re-rendering the App just means refreshing the statistics -- the rest
		// of the app doesn't change.
		render: function() {
			var completed = Todos.completed().length;
			var remaining = Todos.remaining().length;

			if ( Todos.length ) {
				this.$main.show();
				this.$footer.show();

				this.$footer.html(statsTemplate({
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
			var view = new TodoView({ model: todo });
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
				completed: false,
				username: this.$('#header a.author').text(),
				permissions: {
        			"object": [
            			"VIEW",
            			"CREATE",
            			"EDIT",
            			"DELETE",
            			"UNDELETE",
            			"OPERATOR",
            			"MASTER",
            			"OWNER"
        			]
        		}
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
				if( todo.isGranted('DELETE') )
				{
					todo.clear();
				}
			});

			return false;
		},

		// toggle all user's todos
		toggleAllComplete: function() {
			var completed = this.allCheckbox.is(':checked');
			var user = this.user;
			Todos.each(function( todo ) {
				if( todo.isGranted('EDIT', 'completed') ) {
					todo.save({
						'completed': completed
					});
				}
			});
		}
	});

	return AppView;
});
