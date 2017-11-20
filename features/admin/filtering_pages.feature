@managing_inventory
Feature: Filtering pages
    As an Administrator
    I want to be able to filter pages

    Background:
        Given the store operates on a single channel in "United States"
        And I am logged in as an administrator
        And there is an existing page with "about_us" code
        And there is an existing page with "contact" code

    @ui
    Scenario: Filtering pages by a chosen value
        When I browse pages
        And I filter pages containing "about"
        Then I should see 1 page in the list
        And I should see an page with "about_us" code
        But I should not see an page with "contact" code