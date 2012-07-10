# http://blog.lepine.pro/wp-content/uploads/2012/04/behat-cheat-sheet.jpg

Feature: Testing the RESTfulness of todo API
    As a API client
    I need to manage todos

Scenario: List all todos
    Given that I want to list All "Todos"
    When I request "/todos/"
    Then the response is JSON
    And the response has a length equals to "1"
    And the response status code is 200

Scenario: Mark a todo as done
    Given that I want to mark a "todo" as done
    And that its "id" is "1"
    And that its "content" is "My Todo"
    When I request "/todos/"
    Then the response is JSON
    And the response has a "id" property
    And the type of the "id" property is "numeric"
    And the response has a "done" property
    And the "done" property equals "1"
    Then the response status code is 200

Scenario: Creating a new todo
    Given that I want to make a new "Todo"
    And that its "content" is "My Testing Todo"
    When I request "/todos/"
    Then the response is JSON
    And the response has a "url" property
    And the type of the "url" property is "url"
    Then the response status code is 201

Scenario: Deleting a Todo
    Given that I want to delete a "Todo"
    And that its "id" is "1"
    When I request "/todos/"
    Then the response status code is 200