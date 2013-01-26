Feature: list tags
    In order to view tags stored in database
    As a logged user
    I want to access to tag list
    And use filters and navigate over results


Background:
  Given a logged in user
    And "KodifyAdminBundle:Tag" table is empty

Scenario: Use table filter options
    Given "50" tags are created
    When I go to route "get_tag"
    Then I should see "Showing 1 to 25 of 50 entries"
    When I click element with attribute "id" equal to "crud_next_button"
    Then I should see "Showing 26 to 50 of 50 entries"
    When I fill in "filter_name" with "name 10"
    And I click element with attribute "id" equal to "crud_search_button"
    Then I should see "Showing 1 to 1 of 1 entries"
    And table "crud_table" should have "1" rows
    And I should see "name 10"
    But I should not see "name 26"
    When I click element with attribute "id" equal to "crud_reset_button"
    Then I should see "Showing 1 to 25 of 50 entries"

Scenario: Use page size option
    Given  "102" tags are created
    And I go to route "get_tag"
    When I select "100" from "crud_page_size"
    Then I should see "Showing 1 to 100 of 102 entries"
    And table "crud_table" should have "100" rows
    When I click element with attribute "id" equal to "crud_next_button"
    Then I should see "Showing 101 to 102 of 102 entries"
    And table "crud_table" should have "2" rows

Scenario: I want to have the ability to do do broad match on tag filter
  Given the following tags exist:
    | name      | enabled       |
    | asking    | 1             |
    | asked     | 1             |
    | mask      | 1             |
  And I go to route "get_tag"
  When I fill in "filter_name" with "ask"
  And I click element with attribute "id" equal to "crud_search_button"
  Then I should see "Showing 1 to 2 of 2 entries"
  And I should see "asking"
  And I should see "asked"
  But I should not see "mask"
  And table "crud_table" should have "2" rows

