// An example Backbone application contributed by
// [Jérôme Gravel-Niquet](http://jgn.me/)
// [Vincent Mazenod](mazenod.fr)
// This example use url to persist Backbone models within propel.

// Load the application once the DOM is ready, using `jQuery.ready`:
$(function(){

  // see also http://stackoverflow.com/questions/7785079/how-use-token-authentication-with-rails-devise-and-backbone-js
  Backbone.old_sync = Backbone.sync;
  Backbone.sync = function(method, model, options) {
    var new_options =  _.extend({
        beforeSend: function(xhr) {
          if($('body').attr('data-token') != 'undefined')
          {
            var token = $('body').attr('data-token');
            xhr.setRequestHeader('X-CSRF-Token', token);
          }
        }
    }, options)
    Backbone.old_sync(method, model, new_options);
  };

  // Todo Model
  // ----------

  // Our basic **Todo** model has `content`, `order`, and `done` attributes.
  var Todo = Backbone.Model.extend({

    url : function() {

      var base = Routing.generate('mazenovi_todomvc_api_index');
      if (this.isNew()) {
        return base;
      }
      return base + (base.charAt(base.length - 1) == '/' ? '' : '/') + this.id;
    },

    // Default attributes for the todo.
    defaults: {
      content: "empty todo...",
      done: false
    },

    // Ensure that each todo created has `content`.
    initialize: function() {
      if (!this.get("content")) {
        this.set({"content": this.defaults.content});
      }
    },

    // Toggle the `done` state of this todo item.
    toggle: function() {
      this.save({done: !this.get("done")});
    },

    // Remove this Todo and delete its view.
    clear: function() {
      this.destroy();
    }

  });

  // Todo Collection
  // ---------------

  // The collection of todos is backed by a web service.
  var TodoList = Backbone.Collection.extend({

    // Reference to this collection's model.
    model: Todo,
    url: '#',

    // Filter down the list of all todo items that are finished.
    done: function() {
      return this.filter(function(todo){ return todo.get('done'); });
    },

    // Filter down the list to only todo items that are still not finished.
    remaining: function() {
      return this.without.apply(this, this.done());
    },

    // We keep the Todos in sequential order, despite being saved by unordered
    // GUID in the database. This generates the next order number for new items.
    nextOrder: function() {
      if (!this.length) return 1;
      return this.last().get('order') + 1;
    },

    // Todos are sorted by their original insertion order.
    comparator: function(todo) {
      return todo.get('order');
    }

  });

  // Create our global collection of **Todos**.
  // @zemouette best way to achive a contextualized url?
  var Todos = new TodoList;
  if(window.location.pathname != Routing.generate('mazenovi_todomvc_api_index'))
  {
    Todos.url = window.location.pathname;
  }

  // User Model
  // ----------
  var User = Backbone.Model.extend({
    //url : '/app_dev.php/users/roles'
    url: function() {
      return Routing.generate('mazenovi_user_api_getuserroles');
    }
  });

  // User Item View
  // --------------
  var UserView = Backbone.View.extend({
    el: $("#todoapp"),
    template: _.template($('#user-template').html()),
    
    initialize: function() {
      _.bindAll(this, 'render');
      //alert(this.model.toJSON());
      // @zemouette this.model is undefined !!!! comment lier le model à la vue??
      //this.model.bind('change', this.render);
    },

    render: function() {
      alert('render');
      $(this.el).html(this.template(this.model.toJSON()));
    },
  });

  var CurrentUser = new User;

  // Todo Item View
  // --------------

  // The DOM element for a todo item...
  var TodoView = Backbone.View.extend({

    //... is a list tag.
    tagName:  "li",

    // Cache the template function for a single item.
    template: _.template($('#item-template').html()),

    // The DOM events specific to an item.
    events: {
      "click .check"              : "toggleDone",
      "dblclick label.todo-content" : "edit",
      "click span.todo-destroy"   : "clear",
      "keypress .todo-input"      : "updateOnEnter",
      "blur .todo-input"          : "close"
    },

    // The TodoView listens for changes to its model, re-rendering. Since there's
    // a one-to-one correspondence between a **Todo** and a **TodoView** in this
    // app, we set a direct reference on the model for convenience.
    initialize: function() {
      _.bindAll(this, 'render', 'close', 'remove');
      this.model.bind('change', this.render);
      this.model.bind('destroy', this.remove);
    },

    // Re-render the contents of the todo item.
    render: function() {
      $(this.el).html(this.template(this.model.toJSON()));
      this.input = this.$('.todo-input');
      return this;
    },

    // Toggle the `"done"` state of the model.
    toggleDone: function() {
      this.model.toggle();
    },

    // Switch this view into `"editing"` mode, displaying the input field.
    edit: function() {
      if(this.$('.todo-input'))
      {
        $(this.el).addClass("editing");
        this.input.focus();
      }
    },

    // Close the `"editing"` mode, saving changes to the todo.
    close: function() {
      this.model.save({content: this.input.val()});
      $(this.el).removeClass("editing");
    },

    // If you hit `enter`, we're through editing the item.
    updateOnEnter: function(e) {
      if (e.keyCode == 13) this.close();
    },

    // Remove the item, destroy the model.
    clear: function() {
      this.model.clear();
    }

  });

  // Context Model
  // ----------
  // résolu à la porchou en attendant zemouette
/*
  var Context = Backbone.Model.extend({
    urlRoot: '/app_dev.php/users/me'
  });

  var LoadingView = Backbone.View.extend({
    initialize: function () {
      _.bind(this, 'render', 'step', 'startApp');
      this.initContext();
      this.render();
    },  
    render: function() {
      this.$el.html('<p>my progressbar</p>');
    },  
    initContext: function () {
      this.context = new Context();
      this.context.fetch().done(this.step);
    },
    step: function () {
      this.todoCollection = new TodoCollection();
      this.todoCollection.fetch().done(this.render);
      this.progressBar.step(1); // <-- incrémente la postion de la progressbar
    },
  
    startApp: function () {
      this.progressBar.step(2);
      new Application({
        todos: this.todoCollection,
        context: this.context
      });
      this.remove();
    }
  });
*/

  // The Application
  // ---------------

  // Our overall **AppView** is the top-level piece of UI.
  var AppView = Backbone.View.extend({

    // Instead of generating a new element, bind to the existing skeleton of
    // the App already present in the HTML.
    el: $("#todoapp"),

    // Our template for the line of statistics at the bottom of the app.
    statsTemplate: _.template($('#stats-template').html()),

    // Delegated events for creating new items, and clearing completed ones.
    events: {
      /* should be commented to get Sahi test OK */
      /*"keypress #new-todo":  "createOnEnter",*/
      "keyup #new-todo":     "showTooltip",
      "click .todo-clear a": "clearCompleted",
      "click .mark-all-done": "toggleAllComplete"
    },

    // At initialization we bind to the relevant events on the `Todos`
    // collection, when items are added or changed. Kick things off by
    // loading any preexisting todos that might be saved.
    initialize: function() {
      _.bindAll(this, 'addOne', 'addAll', 'render', 'toggleAllComplete');

      this.input = this.$("#new-todo");
      this.allCheckbox = this.$(".mark-all-done")[0];

      Todos.bind('add',     this.addOne);
      Todos.bind('reset',   this.addAll);
      Todos.bind('all',     this.render);
      
      // see also http://stackoverflow.com/questions/220231/accessing-http-headers-in-javascript
      // @zemouette la bonne solution avec le Token dans le headers / La bonne solution avec le Token dans le JSON
      // @zemoutte récupérer le role du user et l'injecter dans les tempaltes
      var req = new XMLHttpRequest();
      req.open('GET', Routing.generate('mazenovi_user_api_getusertoken'), false);
      req.setRequestHeader('Accept', 'application/json, text/javascript, */*; q=0.01');
      req.send(null);
      token = req.getResponseHeader('X-CSRF-Token');
      $('body').attr('data-token', token);

      Todos.fetch();
      
      CurrentUser.fetch();

      var CurrentUserView = new UserView;
      
    },

    // Re-rendering the App just means refreshing the statistics -- the rest
    // of the app doesn't change.
    render: function() {
      var done = Todos.done().length;
      var remaining = Todos.remaining().length;

      this.$('#todo-stats').html(this.statsTemplate({
        total:      Todos.length,
        done:       done,
        remaining:  remaining
      }));
      if(this.allCheckbox)
      {
        this.allCheckbox.checked = !remaining;
      }
    },

    // Add a single todo item to the list by creating a view for it, and
    // appending its element to the `<ul>`.
    addOne: function(todo) {
      var view = new TodoView({model: todo});
      this.$("#todo-list").append(view.render().el);
    },

    // Add all items in the **Todos** collection at once.
    addAll: function() {
      Todos.each(this.addOne);
    },

    // Generate the attributes for a new Todo item.
    newAttributes: function() {
      return {
        content: this.input.val(),
        order:   Todos.nextOrder(),
        done:    false
      };
    },

    // If you hit return in the main input field, create new **Todo** model,
    // persisting it.
    /*
    createOnEnter: function(e) {
      if (e.keyCode != 13) return;
      Todos.create(this.newAttributes());
      this.input.val('');
    },
    */
    // Clear all done todo items, destroying their models.
    clearCompleted: function() {
      _.each(Todos.done(), function(todo){ todo.clear(); });
      return false;
    },

    // Lazily show the tooltip that tells you to press `enter` to save
    // a new todo item, after one second.
    showTooltip: function(e) {
      if (e.keyCode == 13)
      {     
         Todos.create(this.newAttributes());
         this.input.val('');
      }
      var tooltip = this.$(".ui-tooltip-top");
      var val = this.input.val();
      tooltip.fadeOut();
      if (this.tooltipTimeout) clearTimeout(this.tooltipTimeout);
      if (val == '' || val == this.input.attr('placeholder')) return;
      var show = function(){ tooltip.show().fadeIn(); };
      this.tooltipTimeout = _.delay(show, 1000);
    },

    toggleAllComplete: function () {
      if(this.allCheckbox)
      {
        var done = this.allCheckbox.checked;
        Todos.each(function (todo) { todo.save({'done': done}); });
      }
    }

  });

  // Finally, we kick things off by creating the **App**.
  var App = new AppView;

});
