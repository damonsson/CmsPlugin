@managing_products
Feature: Sorting faq
    As an Administrator
    I want to sort faq

    Background:
        Given the store operates on a single channel in "United States"
        And I am logged in as an administrator
        And there is an existing faq with "a_is_first" code
        And this block has an "image" type
        And there is an existing block with "middle" code
        And this block has a "text" type
        And there is an existing block with "z_is_last" code
        And this block has an "html" type

    @ui
    Scenario: Blocks can be sorted by code in ascending order
        Given I am browsing blocks
        When I start sorting blocks by code
        Then I should see 3 blocks in the list
        And the first block on the list should have code "a_is_first"

    @ui
    Scenario: Changing the order of sorting by code
        Given I am browsing blocks
        When I start sorting blocks by code
        And I switch the way blocks are sorted by code
        Then I should see 3 blocks in the list
        And the first block on the list should have code "z_is_last"

    @ui
    Scenario: Blocks can be sorted by their types
        Given I am browsing blocks
        When I start sorting blocks by name
        Then I should see 3 blocks in the list
        And the first block on the list should have type "html"

    @ui
    Scenario: Changing the order of sorting blocks by their types
        Given I am browsing blocks
        And the blocks are already sorted by type
        When I switch the way blocks are sorted by type
        Then I should see 3 blocks in the list
        And the first block on the list should have type "image"