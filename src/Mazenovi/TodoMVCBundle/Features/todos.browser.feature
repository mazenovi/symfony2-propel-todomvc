Feature: ui testing
	In ordrer to organize my tasks
	I should be able to manage todos

	@javascript
	Scenario: Creating a new todo
    	Given I am on "/todos"
    	# see http://sahi.co.in/forums/viewtopic.php?pid=8838#p8838 for filling bug with firefox 
    	When I fill in "new-todo" with "new todo"
    	And I press enter key
    	Then I should see 2 "li" elements
    	Then I should see "My Todo"
    	Then I should see "new todo"
	
	@javascript
    Scenario: Mark all todos as done	    
    	Given I am on "/todos"
    	When I check "mark-all-as-done"
    	Then all the checkboxes should be checked
    	Then I should see "Clear 2 completed item"

    @javascript
	Scenario: Mark a todo as undone
		Given I am on "/todos"
		When I uncheck first todo checkbox
		Then I should see "Clear 1 completed item"
    	
    @javascript
	Scenario: clear completed todo
	    Given I am on "/todos"
	    When I click on Clear 1 completed item
	    Then I should see an "li" element
	    Then I should not see "My Todo"
	    Then I should see "new todo"

	@javascript
	Scenario: Deleting a Todo
	    Given I am on "/todos"
	    When I click on todo destroy link
	    Then I should not see an "li" element
		Then I should not see "My Todo"
	    Then I should not see "new todo"
	    