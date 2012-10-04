Feature: ui testing
	In ordrer to organize my tasks
	I should be able to manage todos

	@javascript
	Scenario: Connecting
    	Given I am on "/login"
    	When I fill in "username" with "todomvcguest"
    	And I fill in "password" with "todomvc"
    	And I click on Login
    	Then I should see 2 "#todo-list li" elements
    	Then I should see "My done todo"
    	Then I should see "My todo"    	

	@javascript
	Scenario: Creating a new todo
    	# see http://sahi.co.in/forums/viewtopic.php?pid=8838#p8838 for filling bug with firefox 
    	When I fill in "new-todo" with "new todo"
    	And I press enter key
    	Then I should see 3 "#todo-list li" elements
    	Then I should see "My done todo"
    	Then I should see "My todo"    	
    	Then I should see "new todo"
	
	@javascript
    Scenario: Mark all todos as done	    
    	When I check "toggle-all"
    	Then all the checkboxes should be checked
    	Then I should see "Clear my completed (2)"

    @javascript
	Scenario: Mark a todo as undone
		When I uncheck first todo checkbox
		Then I should see "Clear my completed (1)"
    	
    @javascript
	Scenario: clear completed todo
	    When I click on Clear my completed
	    Then I should see an "li" element
	    Then I should see "My done todo"
	    Then I should see "My todo"
	    Then I should not see "new todo"

	@javascript
	Scenario: Deleting a Todo
	    When I click on first todo destroy link
		Then I should not see "My done todo"
	    Then I should see "My todo"
	    Then I should not see "new todo"
	    