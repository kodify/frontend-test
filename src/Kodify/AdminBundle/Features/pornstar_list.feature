Feature: list pornstar
    In order to view pornstar stored in database
    As a logged user
    I want to access to pornstar list
    And use filters and navigate over results

Scenario: Use table filter options
    Given a logged in user
    And "KodifyAdminBundle:Pornstar" table is empty
    And "50" pornstars are created
    When I go to route "get_pornstar"
    Then I should see "Showing 1 to 25 of 50 entries"
    When I click element with attribute "id" equal to "crud_next_button"
    Then I should see "Showing 26 to 50 of 50 entries"
    When I fill in "filter_name" with "name 10"
    And I click element with attribute "id" equal to "crud_search_button"
    Then I should see "Showing 1 to 1 of 1 entries"
    And table "crud_table" should have "1" rows
    And I should see "name 10"
    But I should not see "name 11"
    When I click element with attribute "id" equal to "crud_reset_button"
    Then I should see "Showing 1 to 25 of 50 entries"

Scenario: Use page size option
    Given a logged in user
    And "KodifyAdminBundle:Pornstar" table is empty
    And "102" pornstars are created
    And I go to route "get_pornstar"
    When I select "100" from "crud_page_size"
    Then I should see "Showing 1 to 100 of 102 entries"
    And table "crud_table" should have "100" rows
    When I click element with attribute "id" equal to "crud_next_button"
    Then I should see "Showing 101 to 102 of 102 entries"
    And table "crud_table" should have "2" rows

Scenario: Use sort functionality
    Given a logged in user
    And "KodifyAdminBundle:Pornstar" table is empty
    And "50" pornstars are created
    When I go to route "get_pornstar"
    Then I should see "name 10"
    But I should not see "name 50"
    When I click element with attribute "id" equal to "crud_sort_id"
    Then I should see "name 50"
    But I should not see text matching "name 23"
    When I click element with attribute "id" equal to "crud_sort_id"
    Then I should see text matching "name 10"
    But I should not see "name 26"