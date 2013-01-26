Feature: firewall
  All the application is protected by firewall
  As a anonymous user
  I only can access to login page

Scenario: List 2 files in a directory
  Given I am anonymous user
  When I go to route "get_tag"
  Then the url should match "/login"
  And I should see "Please sign in"