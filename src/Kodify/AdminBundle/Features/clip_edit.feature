Feature:  As a content manager
  I want to be able to edit clip metadata
  So that if I make a mistake on submitted clips I can edit them.

  Background:
    Given "KodifyAdminBundle:Clip" table is empty
    And "KodifyAdminBundle:Tag" table is empty
    And "KodifyAdminBundle:Pornstar" table is empty
    And the following clip exist:
      | title     | contentmanager | status          | startTime   | endTime   |  tags           | pornstars      |
      | file 1    | manager 1      | Clip::SUCCESS   | 00:10       | 01:11     | tag1,tag2,tag3  | p1, p2,p3      |
      | file 2    | manager 1      | Clip::PENDING   | 10:10       | 11:11     | tag1,tag2,tag3  | p1, p2,p3      |
      | file 3    | manager 1      | Clip::SUCCESS   | 20:10       | 21:11     | tag1,tag2,tag3  | p1, p2,p3      |
    And the following pornstar exist:
      | name      | enabled       | thumbnailUrl  |
      | p1        | 1             | /img1.jpg     |
      | p2        | 1             | /img2.jpg     |
      | p3        | 1             | /img3.jpg     |
      | porn4     | 1             | /img3.jpg     |
      | porn5     | 0             | /img3.jpg     |
      | porn6     | 1             | /img3.jpg     |
    And the following tags exist:
      | name      | enabled        |
      | tag1      | 1              |
      | tag2      | 1              |
      | tag3      | 1              |
      | tag4      | 1              |
      | tag5      | 0              |
      | tag6      | 1              |

  Scenario: tag autocomplete
    Given a logged in user
    And I go to route "get_clip"
    And I follow "edit_1"
    And I wait for 2 second
    When I fill in "Clip_tags_tag" with "tag"
    And I press key down
    And I wait for 2 second
    Then I should see "tag4"
    And I should see "tag6"
    But I should not see "tag5"

  Scenario: pornstar autocomplete
    Given a logged in user
    And I go to route "get_clip"
    And I follow "edit_1"
    And I wait for 2 second
    When I fill in "Clip_pornstars_tag" with "porn"
    And I press key down
    And I wait for 1 second
    Then I should see "porn4"
    And I should see "porn6"
    But I should not see "porn5"

  Scenario: Tag validator
    Given a logged in user
    And I go to route "get_clip"
    And I follow "edit_1"
    And I wait for 2 second
    When I follow "Remove tag"
    And I follow "Remove tag"
    And I follow "Remove tag"
    And I press "Save"
    Then the url should match route "edit_clip" with params "{'id':1}"
    And I should see "Clip require at least 1 tag"
    When I fill in "Clip_tags_tag" with "tag1,"
    And I fill in "Clip_tags_tag" with "error,"
    And I press "Save"
    And I wait for 4 seconds
    Then the url should match route "edit_clip" with params "{'id':1}"
    And I should see "Invalid tags: error,"
    When I follow "Remove tag"
    And I follow "Remove tag"
    And I fill in "Clip_tags_tag" with "tag1,"
    And I press "Save"
    Then the url should match route "get_clip"
    And I should see "Success!"

  Scenario: Pornstar validator
    Given a logged in user
    And I go to route "get_clip"
    And I follow "edit_1"
    And I wait for 2 second
    When I fill in "Clip_pornstars_tag" with "error,"
    And I press "Save"
    And I wait for 4 seconds
    Then the url should match route "edit_clip" with params "{'id':1}"
    And I should see "Invalid pornstars: error,"
    When I follow "Remove pornstar"
    And I follow "Remove pornstar"
    And I follow "Remove pornstar"
    And I follow "Remove pornstar"
    And I fill in "Clip_pornstars_tag" with "p1,"
    And I press "Save"
    Then the url should match route "get_clip"
    And I should see "Success!"

  Scenario: Edit clip
    Given a logged in user
    And I go to route "get_clip"
    When I follow "edit_1"
    Then the url should match route "edit_clip" with params "{'id':1}"
    And the "Clip[startTime]" field should contain "00:10"
    And the "Clip[endTime]" field should contain "01:11"
    And the "Clip[title]" field should contain "file 1"
    And the "Clip[pornstars]" field should contain "p1,p2,p3"
    And the "Clip[tags]" field should contain "tag1,tag2,tag3"
    When I press "Save"
    Then the url should match route "get_clip"
    And I should see "Success!"

  Scenario: Edit clip, time validation
    Given a logged in user
    And I go to route "get_clip"
    And I follow "edit_1"
    When I fill in "Clip_startTime" with ""
    And I fill in "Clip_endTime" with ""
    And I press "Save"
    Then the url should match route "edit_clip" with params "{'id':1}"
    And div with class "div-startTime" should have class "error"
    And div with class "div-endTime" should have class "error"
    When I fill in "Clip_startTime" with "10:00"
    And I fill in "Clip_endTime" with "09:59"
    And I press "Save"
    Then the url should match route "edit_clip" with params "{'id':1}"
    And div with class "div-startTime" should have class "error"
    And div with class "div-endTime" should have class "error"
    When I fill in "Clip_startTime" with "09:00"
    And I fill in "Clip_endTime" with "09:59"
    And I press "Save"
    Then the url should match route "get_clip"
    And I should see "Success!"


