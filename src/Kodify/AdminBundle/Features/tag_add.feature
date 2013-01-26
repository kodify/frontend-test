Feature: add tag
    In order to add new tags
    As a logged user
    I want to access to add tag form and create new tag

Scenario: Add new tag properly
    Given a logged in user
    When I go to route "add_tag"
    Then the url should match route "add_tag"
    And I fill in "tag_name" with "Tag 1"
    And I wait for 1 second
    And I press "Create"
    And the url should match route "get_tag"
    And I should see "Success!"

Scenario: Try to add new tag but don't fill name field
    Given a logged in user
    When I go to route "add_tag"
    Then the url should match route "add_tag"
    And I press "Create"
    And I wait for 1 second
    And the url should match route "add_tag"
    And I should see "Error saving"

Scenario: tag name should be unique
    Given a logged in user
    And the following tags exist:
      | name      | enabled     |
      | aaaaaa    | 1           |
    And I go to route "add_tag"
    When I fill in "tag_name" with "aaaaaa"
    And I wait for 1 second
    And I press "Create"
    Then I should see "This value is already used."
