@managing_inventory
Feature: Filtering blocks
    As an Administrator
    I want to be able to filter blocks

    Background:
        Given the store operates on a single channel in "United States"
        And I am logged in as an administrator
        And there is an existing block with "homepage_intro" code
        And there is an existing block with "homepage_outro" code

    @ui
    Scenario: Filtering blocks by a chosen value
        When I browse blocks
        And I filter blocks containing "intro"
        Then I should see 1 block in the list
        And I should see an block with "homepage_intro" code
        But I should not see an block with "homepage_outro" code