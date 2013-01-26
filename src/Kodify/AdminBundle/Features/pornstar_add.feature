Feature: add pornstar
    In order to add new pornstars
    As a logged user
    I want to access to add pornstar form and create new pornstar

Scenario: Add new pornstar properly
    Given a logged in user
    And "KodifyAdminBundle:Pornstar" table is empty
    When I go to route "add_pornstar"
    Then the url should match route "add_pornstar"
    And I fill in "pornstar_alias_tag" with "pepeStar"
    And I fill in "pornstar_name" with "Pepe"
    And I fill in "pornstar_description" with "the best actor in the world"
    And I fill in "pornstar_twitter" with "@pepestar"
    And I wait for 2 second
    And I press "Create"
    Then the url should match route "get_pornstar"
    And I should see "Success!"
    And the entity "Pornstar" with attribute "name" "Pepe" should be stored in the database
    And I should see "Pepe"

Scenario: Try to add new pornstar but don't fill name field
    Given a logged in user
    When I go to route "add_pornstar"
    Then the url should match route "add_pornstar"
    And I should see "Create"
    And I fill in "pornstar_alias_tag" with "pepeStar"
    And I fill in "pornstar_description" with "the best actor in the world"
    And I fill in "pornstar_twitter" with "@pepestar"
    And I press "Create"
    And I wait for 1 second
    And the url should match route "edit_pornstar"
    And I should see "Error saving"

Scenario: Pornstar name should be unique
    Given a logged in user
    And the following pornstar exist:
      | name      | enabled        |
      | aaaaaa1   | 1              |
    And I go to route "add_pornstar"
    When I fill in "pornstar_alias_tag" with "pepeStar"
    And I fill in "pornstar_name" with "aaaaaa1"
    And I fill in "pornstar_description" with "the best actor in the world"
    And I fill in "pornstar_twitter" with "@pepestar"
    And I wait for 2 second
    And I press "Create"
    Then I should see "This value is already used."