@managing_inventory
Feature: Filtering faq
    As an Administrator
    I want to be able to filter faq

    Background:
        Given the store operates on a single channel in "United States"
        And I am logged in as an administrator
        And there is an existing frequently asked question with "how_buy" code
        And there is an existing frequently asked question with "how_pay" code

    @ui
    Scenario: Filtering faq by a chosen value
        When I browse faq
        And I filter faq containing "buy"
        Then I should see 1 faq in the list
        And I should see an faq with "how_buy" code
        But I should not see an faq with "how_pay" code