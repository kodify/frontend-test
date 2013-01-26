Feature: edit tag
    In order to edit existent tag
    As a logged user
    I want to access to edit tag list and edit existent tag

Scenario: Edit existent tag
    Given a logged in user
    And "KodifyAdminBundle:Tag" table is empty
    And "1" tags are created
    And I go to route "get_tag"
    When I follow "edit_1"
    Then the "tag_name" field should contain "name 1"
    And I should see "Save"
    When I fill in "tag_name" with "edited"
    And I press "Save"
    Then the url should match route "get_tag"
    And I should see "Success!"
    And the entity "Tag" with attribute "name" "edited" should be stored in the database
    And I should see "edited"
