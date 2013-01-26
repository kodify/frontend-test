Feature: list videos
  As a content manager
  I want a list of all videos that I have selected to cut

  Background:
    Given a logged in user
    And "KodifyAdminBundle:Video" table is empty

  Scenario: List all videos
    Given the following video exist:
      | originalfilename  | status          |
      | file 1            | Video::READY    |
      | file 2            | Video::BLOCKED  |
      | file 3            | Video::READY    |
    When I go to route "get_video"
    Then I should see "Showing 1 to 3 of 3 entries"
    And I should see "file 1"
    And I should see "file 2"
    And I should see "file 3"

  Scenario: Filter by status
    Given the following video exist:
      | originalfilename  | status          |
      | file 1            | Video::READY    |
      | file 2            | Video::BLOCKED  |
    When I go to route "get_video"
    And I select "Ready" from "filter_status"
    And I click element with attribute "id" equal to "crud_search_button"
    Then I should see "Showing 1 to 1 of 1 entries"
    And I should see "file 1"
    But I should not see "file 2"

  Scenario: Default filter, user only should see videos in status Blocked and Ready
    Given the following video exist:
      | originalfilename  | status          |
      | file 1            | Video::READY    |
      | file 2            | Video::BLOCKED  |
      | file 3            | Video::CUT      |
    When I go to route "get_video"
    Then I should see "Showing 1 to 2 of 2 entries"
    And I should see "file 1"
    And I should see "file 2"
    But I should not see "file 3"

  Scenario: In list content manager should see video duration
    Given the following video exist:
      | originalfilename  | status          | duration   |
      | file 1            | Video::READY    | 3600000    |
      | file 2            | Video::READY    | 3601000    |
      | file 3            | Video::READY    | 11710000   |
      | file 4            | Video::READY    | 2829726   |
    When I go to route "get_video"
    Then in row "1" of table "crud_table" I should see text "01:00:00"
    And in row "2" of table "crud_table" I should see text "01:00:01"
    And in row "3" of table "crud_table" I should see text "03:15:10"

  Scenario: time ago
    Given the following video exist:
      | originalfilename  | status          | timeAgo   |
      | file 1            | Video::READY    | 3600      |
      | file 2            | Video::READY    | 7200      |
      | file 3            | Video::READY    | 86400     |
    When I go to route "get_video"
    Then in row "1" of table "crud_table" I should see text "1 hour ago"
    And in row "2" of table "crud_table" I should see text "2 hours ago"
    And in row "3" of table "crud_table" I should see text "1 day ago"

