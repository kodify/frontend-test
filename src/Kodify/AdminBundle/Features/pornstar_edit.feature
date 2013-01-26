Feature: edit pornstar
    In order to edit existent pornstars
    As a logged user
    I want to access to edit pornstar list and edit existent pornstar

Scenario: Edit existent pornstar
    Given a logged in user
    And "KodifyAdminBundle:Pornstar" table is empty
    And "1" pornstars are created
    And I go to route "get_pornstar"
    When I follow "edit_1"
    Then the "pornstar_name" field should contain "name 1"
    And I should see "Save"
    When I fill in "pornstar_alias_tag" with "pepeStar"
    And I fill in "pornstar_name" with "pepe"
    And I wait for 2 second
    And I press "Save"
    Then the url should match route "get_pornstar"
    And I should see "Success!"
    And the entity "Pornstar" with attribute "name" "Pepe" should be stored in the database
    And I should see "Pepe"
