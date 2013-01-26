Feature: Mixpanel tracking system
  As a admin
  I want to track when a content manager cuts a video
  And I want to track the number of clips created by the content manager


  Scenario: Check that mixpanel tracked the video cut and clips created
    Given "KodifyAdminBundle:Video" table is empty
    And "KodifyAdminBundle:Pornstar" table is empty
    And "KodifyAdminBundle:Tag" table is empty
    And "KodifyAdminBundle:Clip" table is empty
    And a logged in user
    And the following video exist:
      | originalfilename  | status          | blockedBy |
      | file 1            | Video::READY    |           |
    And the following pornstar exist:
      | name      | enabled        |
      | aaaaaa1   | 1              |
    And the following tags exist:
      | name      | enabled        |
      | tag1      | 1              |
    And I go to route "cut_video" with params "{'id':1}"
    And The Mixpanel code is mocked
    And I wait for 2 seconds
    And I fill in element "1" with xpath "//input[@name='clip[start][]']" with "00:00:00"
    And I fill in element "1" with xpath "//input[@name='clip[end][]']" with "00:00:10"
    And I fill in element "1" with xpath "//input[@name='clip[title][]']" with "Clip 1 Title"
    And I fill in element "2" with xpath "//input[@class='ui-autocomplete-input']" with "tag1,"
    When I press "Finish & next >>"
    And I wait for 2 second
    Then Mixpanel should track the event "video_cut"
    And Mixpanel should track the event "clip_created"
