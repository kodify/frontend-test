Feature:
  As a content manager
  I want to have pornstar thumbnails on video editor
  So that I can easily identify pornstars

  Background:
    Given "KodifyAdminBundle:Video" table is empty
    And "KodifyAdminBundle:Pornstar" table is empty
    And "KodifyAdminBundle:Tag" table is empty
    And "KodifyAdminBundle:Clip" table is empty
    And a logged in user

  Scenario: autocomplete pornstar list with images
    Given the following video exist:
      | originalfilename  | status          |
      | file 1            | Video::READY    |
    And the following pornstar exist:
      | name      | enabled       | thumbnailUrl  |
      | aaaaaa1   | 1             | /img1.jpg     |
      | aaaaaa2   | 1             | /img2.jpg     |
    And fixtures "img1.jpg" is in "/web"
    And fixtures "img2.jpg" is in "/web"
    And I go to route "cut_video" with params "{'id':1}"
    And I wait for 2 second
    And I fill in element "1" with xpath "//input[@class='ui-autocomplete-input']" with "aaa"
    And I wait for 4 second
    When I press key down
    And I wait for 2 second
    Then I should see image with url "/img1.jpg"
    When I press key down
    And I wait for 1 second
    Then I should see image with url "/img2.jpg"
    When I click element with attribute "text" equal to "aaaaaa1"
    Then I should not see image with url "/img2.jpg"
    And I should not see image with url "/img1.jpg"


  Scenario: autocomplete pornstar list
    Given the following video exist:
      | originalfilename  | status          |
      | file 1            | Video::READY    |
    And the following pornstar exist:
      | name      | enabled       |
      | aaaaaa1   | 1             |
      | aaaaaa2   | 1             |
      | bbbbbb    | 1             |
    And I go to route "cut_video" with params "{'id':1}"
    And I wait for 2 second
    When I fill in element "1" with xpath "//input[@class='ui-autocomplete-input']" with "a"
    And I wait for 4 second
    Then I should not see "aaaaaa1"
    When I fill in element "1" with xpath "//input[@class='ui-autocomplete-input']" with "aaa"
    And I wait for 4 second
    Then I should see "aaaaaa1"
    And I should see "aaaaaa2"
    But I should not see "bbbbbb"
    When I click element with attribute "text" equal to "aaaaaa1"
    Then I should see "aaaaaa1"
    But I should not see "aaaaaa2"

  Scenario: Validate pornstar list
    Given the following video exist:
      | originalfilename  | status          |
      | file 1            | Video::READY    |
    And the following pornstar exist:
      | name      | enabled     |
      | aaaaaa    | 1           |
      | bbbbbb    | 1           |
    And I go to route "cut_video" with params "{'id':1}"
    And I wait for 2 second
    And I fill in element "1" with xpath "//input[@class='ui-autocomplete-input']" with "invalid pornstar,"
    And I fill in element "1" with xpath "//input[@class='ui-autocomplete-input']" with "aaaaaa,"
    When I press "Finish & next >>"
    And I wait for 2 second
    Then I should see "Invalid pornstars: invalid pornstar,"
    When the following pornstar exist:
      | name                | enabled     |
      | invalid pornstar    | 1           |
    And I wait for 2 second
    And I press "Finish & next >>"
    And I wait for 2 second
    Then I should not see "Invalid pornstars: invalid pornstar,"