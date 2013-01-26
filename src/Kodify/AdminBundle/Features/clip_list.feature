Feature: list clips
  As a content manager
  I want to see a list of clips I have cut

  Background:
    Given "KodifyAdminBundle:Clip" table is empty
    And the following clip exist:
      | title     | contentmanager | status          | timeAgo   |
      | file 1    | manager 1      | Clip::SUCCESS   | 3600      |
      | file 2    | manager 1      | Clip::PENDING   | 7200      |
      | file 3    | manager 1      | Clip::SUCCESS   | 86400     |

  Scenario: Check that most recen clips are on top
    Given a logged in user
    When I go to route "get_clip"
    Then in row "1" of table "crud_table" I should see text "file 3"
    And in row "2" of table "crud_table" I should see text "file 2"
    And in row "3" of table "crud_table" I should see text "file 1"

  Scenario: List all clips
    Given a logged in user
    When I go to route "get_clip"
    Then I should see "Showing 1 to 3 of 3 entries"
    And I should see "file 1"
    And I should see "file 2"
    And I should see "file 3"

  Scenario: Filter by status
    Given a logged in user
    When I go to route "get_clip"
    And I select "Pending" from "filter_status"
    And I click element with attribute "id" equal to "crud_search_button"
    Then I should see "Showing 1 to 1 of 1 entries"
    And I should see "file 2"
    But I should not see "file 1"
    But I should not see "file 3"

  Scenario: time ago
    Given a logged in user
    When I go to route "get_clip"
    Then in row "1" of table "crud_table" I should see text "1 day ago"
    And in row "2" of table "crud_table" I should see text "2 hours ago"
    And in row "3" of table "crud_table" I should see text "1 hour ago"