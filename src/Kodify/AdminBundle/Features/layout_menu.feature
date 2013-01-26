Feature: Highlight selected page on menu
  As a content manager
  I want to have the page I'm on highlighted on the menu
  To optimize my screen real estate. Also As a content manager
  I want to see the count of videos in my queues
  So I can ensure I have a good backlog of videos

  Scenario: I want to see the count of videos ready to cut with important class
    Given a logged in user
    And "KodifyAdminBundle:Video" table is empty
    And the following video exist:
      | originalfilename  | status                      |
      | file 1            | Video::READY                |
      | file 2            | Video::BLOCKED              |
      | file 3            | Video::READY                |
      | file 2            | Video::UPLOADING            |
      | file 2            | Video::TRANSCODING          |
      | file 2            | Video::CANCELLED            |
      | file 2            | Video::TRANSCODING_FAILED   |
      | file 1            | Video::READY                |
    When I go to route "main_dashboard"
    Then I should see "Ready to cut: 3"
    And  element with id "alert_video" should have class "badge-important"

  Scenario: I want to see the count of videos ready to cut with warning class
    Given a logged in user
    And "KodifyAdminBundle:Video" table is empty
    And exists 15 videos with status "Video::READY"
    When I go to route "main_dashboard"
    Then I should see "Ready to cut: 15"
    And  element with id "alert_video" should have class "badge-warning"

  Scenario: I want to see the count of videos ready to cut with success class
    Given a logged in user
    And "KodifyAdminBundle:Video" table is empty
    And exists 26 videos with status "Video::READY"
    When I go to route "main_dashboard"
    Then I should see "Ready to cut: 26"
    And  element with id "alert_video" should have class "badge-success"

  Scenario: I want to see the count of putiofiles with important class
    Given a logged in user
    And "KodifyAdminBundle:Video" table is empty
    And "KodifyAdminBundle:PutIoFile" table is empty
    And exists 9 PutIoFiles related with Videos with status "Video::UPLOADING"
    And exists 10 PutIoFiles related with Videos with status "Video::READY" and thumbnailsDeleted = "0"
    When I go to route "main_dashboard"
    Then I should see "Put.io: 9"
    And  element with id "alert_putio" should have class "badge-important"

  Scenario: I want to see the count of putiofiles with warning class
    Given a logged in user
    And "KodifyAdminBundle:Video" table is empty
    And "KodifyAdminBundle:PutIoFile" table is empty
    And exists 24 PutIoFiles related with Videos with status "Video::UPLOADING"
    And exists 30 PutIoFiles related with Videos with status "Video::READY"
    When I go to route "main_dashboard"
    Then I should see "Put.io: 24"
    And  element with id "alert_putio" should have class "badge-warning"

  Scenario: I want to see the count of putiofiles with success class
    Given a logged in user
    And "KodifyAdminBundle:Video" table is empty
    And "KodifyAdminBundle:PutIoFile" table is empty
    And exists 10 PutIoFiles related with Videos with status "Video::UPLOADING"
    And exists 20 PutIoFiles
    And exists 30 PutIoFiles related with Videos with status "Video::READY" and thumbnailsDeleted = "0"
    When I go to route "main_dashboard"
    Then I should see "Put.io: 30"
    And  element with id "alert_putio" should have class "badge-success"

  Scenario: I want to see the count of videos in putio
    Given a logged in user
    When I go to route "main_dashboard"

  Scenario: Check video menu element
    Given a logged in user
    When I go to route "get_video"
    Then element with id "video_li" should have class "active"
    And  element with id "clip_li" should not have class "active"
    And  element with id "tag_li" should not have class "active"

  Scenario: Check clip menu element
    Given a logged in user
    When I go to route "get_clip"
    Then element with id "clip_li" should have class "active"
    And  element with id "video_li" should not have class "active"
    And  element with id "tag_li" should not have class "active"

  Scenario: Check tag menu element
    Given a logged in user
    When I go to route "get_tag"
    Then element with id "tag_li" should have class "active"
    And  element with id "clip_li" should not have class "active"
    And  element with id "video_li" should not have class "active"
