Feature: cut videos
  As a content manager
  I want a video editor
  So that I can create multiple clips from 1 video source file. When user selects one video to cut, other
  users can not cut the same video as it's blocked. In the form i can create as many clips as i want.
  I user press cancel button video gets unblocked.
  If End time is before Start time an error should be displayer.
  Tags and pornstars fields should have autocomplete functionality
  Tags and pornstars list should be validated before users leaves cut videos pages.
  When user finish to cut one video should be redirected to next ready video if there are.
  Video should autostart when page is loaded.

  Background:
    Given "KodifyAdminBundle:Video" table is empty
    And "KodifyAdminBundle:Pornstar" table is empty
    And "KodifyAdminBundle:Tag" table is empty
    And "KodifyAdminBundle:Clip" table is empty
    And a logged in user


  Scenario: autostart video when page is loaded
    Given the following video exist:
      | originalfilename  | status          | blockedBy |
      | file 1            | Video::READY    |           |
    When I go to route "cut_video" with params "{'id':1}"
    Then jwplayer should be configured with autostart true

  Scenario: mark video as unsuitable
    Given the following video exist:
      | originalfilename  | status          | blockedBy |
      | file 1            | Video::READY    |           |
    And I go to route "cut_video" with params "{'id':1}"
    When I press "Mark video as unsuitable"
    And I wait for 2 seconds
    And I follow "OK"
    Then the url should match route "get_video"
    When I select "Cancelled" from "filter_status"
    And I click element with attribute "id" equal to "crud_search_button"
    Then I should see "Showing 1 to 1 of 1 entries"
    And I should see "file 1"

  Scenario: mark video as unsuitable with more videos
    Given the following video exist:
      | originalfilename  | status          | blockedBy |
      | file 1            | Video::READY    |           |
      | file 2            | Video::READY    |           |
    And I go to route "cut_video" with params "{'id':1}"
    When I press "Mark video as unsuitable"
    And I wait for 2 seconds
    And I follow "OK"
    Then the url should match route "cut_video" with params "{'id':2}"


  Scenario: finish to cut one video and go to next ready video
    Given the following video exist:
      | originalfilename  | status          | blockedBy |
      | file 1            | Video::READY    |           |
      | file 2            | Video::READY    |           |
    And the following pornstar exist:
      | name      | enabled        |
      | aaaaaa1   | 1              |
    And the following tags exist:
      | name      | enabled        |
      | tag1      | 1              |
    And I go to route "cut_video" with params "{'id':1}"
    And I wait for 2 seconds
    When I fill in element "1" with xpath "//input[@name='clip[start][]']" with "00:00:00"
    And I fill in element "1" with xpath "//input[@name='clip[end][]']" with "00:00:10"
    And I fill in element "1" with xpath "//input[@name='clip[title][]']" with "Clip 1 Title"
    And I fill in element "2" with xpath "//input[@class='ui-autocomplete-input']" with "tag1,"
    Then I press "Finish & next >>"
    And I wait for 4 second
    And the url should match route "cut_video" with params "{'id':2}"


  Scenario: cut video in 3 clips
    Given the following video exist:
      | originalfilename  | status          | blockedBy |
      | file 1            | Video::READY    |           |
    And the following pornstar exist:
      | name      | enabled      |
      | aaaaaa1   | 1            |
      | aaaaaa2   | 1            |
      | aaaaaa3   | 1            |
      | bbbbbb    | 1            |
    And the following tags exist:
      | name      | enabled      |
      | tag1      | 1            |
      | tag2      | 1            |
      | tag3      | 1            |
    And I go to route "cut_video" with params "{'id':1}"
    And I wait for 2 second

    When I fill in element "1" with xpath "//input[@name='clip[start][]']" with "00:00:00"
    And I fill in element "1" with xpath "//input[@name='clip[end][]']" with "00:00:10"
    And I fill in element "1" with xpath "//input[@name='clip[title][]']" with "Clip 1 Title"
    And I fill in element "1" with xpath "//input[@class='ui-autocomplete-input']" with "aaa"
    And I wait for 4 second
    And I click element with attribute "text" equal to "aaaaaa1"
    And I fill in element "2" with xpath "//input[@class='ui-autocomplete-input']" with "tag1,"
    And I press "New Clip"

    And I fill in element "1" with xpath "//input[@name='clip[start][]']" with "00:00:10"
    And I fill in element "1" with xpath "//input[@name='clip[end][]']" with "00:00:20"
    And I fill in element "1" with xpath "//input[@name='clip[title][]']" with "Clip 2 Title"
    And I fill in element "1" with xpath "//input[@class='ui-autocomplete-input']" with "aaaaaa2,"
    And I fill in element "2" with xpath "//input[@class='ui-autocomplete-input']" with "tag2,"
    And I press "New Clip"

    And I fill in element "1" with xpath "//input[@name='clip[start][]']" with "00:00:20"
    And I fill in element "1" with xpath "//input[@name='clip[end][]']" with "00:00:30"
    And I fill in element "1" with xpath "//input[@name='clip[title][]']" with "Clip 3 Title"
    And I fill in element "1" with xpath "//input[@class='ui-autocomplete-input']" with "aaaaaa3,"
    And I fill in element "2" with xpath "//input[@class='ui-autocomplete-input']" with "tag3,"

    Then I press "Finish & next >>"
    And I wait for 4 second
    And the url should match route "get_clip"
    And in row "1" of table "crud_table" I should see text "Clip 1 Title"
    And in row "1" of table "crud_table" I should see text "tag1"
    And in row "1" of table "crud_table" I should see text "aaaaaa1"
    And in row "2" of table "crud_table" I should see text "Clip 2 Title"
    And in row "2" of table "crud_table" I should see text "tag2"
    And in row "2" of table "crud_table" I should see text "aaaaaa2"
    And in row "3" of table "crud_table" I should see text "Clip 3 Title"
    And in row "3" of table "crud_table" I should see text "tag3"
    And in row "3" of table "crud_table" I should see text "aaaaaa3"

  Scenario: tag are mandatory
    Given the following video exist:
      | originalfilename  | status          | blockedBy |
      | file 1            | Video::READY    |           |
    And the following pornstar exist:
      | name      | enabled      |
      | aaaaaa1   | 1            |
    And the following tags exist:
      | name      | enabled      |
    And I go to route "cut_video" with params "{'id':1}"
    And I wait for 2 second
    When I fill in element "1" with xpath "//input[@name='clip[start][]']" with "00:00:00"
    And I fill in element "1" with xpath "//input[@name='clip[end][]']" with "00:00:10"
    And I fill in element "1" with xpath "//input[@name='clip[title][]']" with "Clip 1 Title"
    And I fill in element "1" with xpath "//input[@class='ui-autocomplete-input']" with "aaa"
    Then I press "Finish & next >>"
    And I wait for 4 second
    And I should see "Clip require at least 1 tag"

  Scenario: pornstars are optional
    Given the following video exist:
      | originalfilename  | status          | blockedBy |
      | file 1            | Video::READY    |           |
    And the following pornstar exist:
      | name      | enabled      |
      | aaaaaa1   | 1            |
    And the following tags exist:
      | name      | enabled      |
      | tag       | 1      |
    And I go to route "cut_video" with params "{'id':1}"
    And I wait for 2 second
    When I fill in element "1" with xpath "//input[@name='clip[start][]']" with "00:00:00"
    And I fill in element "1" with xpath "//input[@name='clip[end][]']" with "00:00:10"
    And I fill in element "1" with xpath "//input[@name='clip[title][]']" with "Clip 1 Title"
    And I fill in element "2" with xpath "//input[@class='ui-autocomplete-input']" with "tag,"
    Then I press "Finish & next >>"
    And I wait for 4 second
    And the url should match route "get_clip"
    And in row "1" of table "crud_table" I should see text "Clip 1 Title"

  Scenario: Validate tag list
    Given the following video exist:
      | originalfilename  | status          |
      | file 1            | Video::READY    |
    And the following tags exist:
      | name      | enabled     |
      | aaaaaa    | 1           |
      | bbbbbb    | 1           |
    And I go to route "cut_video" with params "{'id':1}"
    And I wait for 2 second
    And I fill in element "2" with xpath "//input[@class='ui-autocomplete-input']" with "invalid tag,"
    And I fill in element "2" with xpath "//input[@class='ui-autocomplete-input']" with "aaaaaa,"
    When I press "Finish & next >>"
    And I wait for 2 second
    Then I should see "Invalid tags: invalid tag,"
    When the following tags exist:
      | name           | enabled     |
      | invalid tag    | 1           |
    And I wait for 2 second
    And I press "Finish & next >>"
    And I wait for 2 second
    Then I should not see "Invalid tags: invalid tag,"


  Scenario: Blocked Video
    Given the following video exist:
      | originalfilename  | status          | blockedBy |
      | file 1            | Video::BLOCKED  | test      |
    When I go to route "get_video"
    And I follow "cut_1"
    Then the url should match route "get_video"
    And I should see "This video is blocked by test"

  Scenario: Block new Video
    Given a logged in user
    And the following video exist:
      | originalfilename  | status          |
      | file 1            | Video::READY    |
    And I go to route "get_video"
    When I follow "cut_1"
    And I go to route "get_video"
    Then in row "1" of table "crud_table" I should see text "Blocked"
    And in row "1" of table "crud_table" I should see text "admin"

  Scenario: Unblock blocked Video
    Given the following video exist:
      | originalfilename  | status          | blockedBy |
      | file 1            | Video::BLOCKED  | admin      |
    And I go to route "cut_video" with params "{'id':1}"
    When I press "cancel_cutting"
    And I wait for 2 second
    And I follow "OK"
    And I wait for 2 second
    Then the url should match route "get_video"
    And in row "1" of table "crud_table" I should see text "Ready"
    And in row "1" of table "crud_table" I should not see text "admin"

  Scenario: Add and remove clip forms
    Given the following video exist:
      | originalfilename  | status          |
      | file 1            | Video::READY    |
    When I go to route "cut_video" with params "{'id':1}"
    Then I should see "1" element with css ".clip_form"
    When I press "New Clip"
    Then I should see "2" element with css ".clip_form"
    When I press "Remove"
    And I wait for 2 second
    And I follow "OK"
    And I wait for 2 second
    Then I should see "1" element with css ".clip_form"

  Scenario: In cutting screen I should see video thumbnails
    Given the following video exist:
      | originalfilename  | status          | duration |
      | file 1            | Video::READY    | 300000   |
    When I go to route "cut_video" with params "{'id':1}"
    Then I should see "10" element with xpath "//div[@id='video-thumbnails']/img"



