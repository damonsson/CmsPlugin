@managing_inventory
Feature: Filtering sections
    As an Administrator
    I want to be able to filter sections

    Background:
        Given the store operates on a single channel in "United States"
        And I am logged in as an administrator
        And there is an existing section with "blog" code
        And there is an existing section with "promotions" code

    @ui
    Scenario: Filtering sections by a chosen value
        When I browse sections
        And I filter sections containing "blog"
        Then I should see 1 section in the list
        And I should see a section with "blog" code
        But I should not see a section with "promotions" code