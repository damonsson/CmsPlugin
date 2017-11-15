<?php

/**
 * This file was created by the developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://bitbag.shop and write us
 * an email on mikolaj.krol@bitbag.pl.
 */

declare(strict_types=1);

namespace Tests\BitBag\CmsPlugin\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use Behat\Behat\Tester\Exception\PendingException;
use BitBag\CmsPlugin\Entity\BlockInterface;
use BitBag\CmsPlugin\Exception\TemplateTypeNotFound;
use BitBag\CmsPlugin\Repository\BlockRepositoryInterface;
use Sylius\Behat\NotificationType;
use Sylius\Behat\Page\SymfonyPageInterface;
use Sylius\Behat\Service\NotificationCheckerInterface;
use Sylius\Behat\Service\Resolver\CurrentPageResolverInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Tests\BitBag\CmsPlugin\Behat\Page\Admin\Block\CreatePageInterface;
use Tests\BitBag\CmsPlugin\Behat\Page\Admin\Block\IndexPageInterface;
use Tests\BitBag\CmsPlugin\Behat\Page\Admin\Block\UpdatePageInterface;
use Tests\BitBag\CmsPlugin\Behat\Service\RandomStringGeneratorInterface;
use Webmozart\Assert\Assert;

/**
 * @author Mikołaj Król <mikolaj.krol@bitbag.pl>
 */
final class BlockContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var CurrentPageResolverInterface
     */
    private $currentPageResolver;

    /**
     * @var NotificationCheckerInterface
     */
    private $notificationChecker;

    /**
     * @var IndexPageInterface
     */
    private $indexPage;

    /**
     * @var CreatePageInterface
     */
    private $createPage;

    /**
     * @var UpdatePageInterface
     */
    private $updatePage;

    /**
     * @var RandomStringGeneratorInterface
     */
    private $randomStringGenerator;

    /**
     * @var BlockRepositoryInterface
     */
    private $blockRepository;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param CurrentPageResolverInterface $currentPageResolver
     * @param NotificationCheckerInterface $notificationChecker
     * @param IndexPageInterface $indexPage
     * @param CreatePageInterface $createPage
     * @param UpdatePageInterface $updatePage
     * @param RandomStringGeneratorInterface $randomStringGenerator
     * @param BlockRepositoryInterface $blockRepository
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        CurrentPageResolverInterface $currentPageResolver,
        NotificationCheckerInterface $notificationChecker,
        IndexPageInterface $indexPage,
        CreatePageInterface $createPage,
        UpdatePageInterface $updatePage,
        RandomStringGeneratorInterface $randomStringGenerator,
        BlockRepositoryInterface $blockRepository
    )
    {
        $this->sharedStorage = $sharedStorage;
        $this->currentPageResolver = $currentPageResolver;
        $this->notificationChecker = $notificationChecker;
        $this->indexPage = $indexPage;
        $this->createPage = $createPage;
        $this->updatePage = $updatePage;
        $this->randomStringGenerator = $randomStringGenerator;
        $this->blockRepository = $blockRepository;
    }

    /**
     * @When I go to the blocks page
     */
    public function iGoToTheBlocksPage()
    {
        $this->indexPage->open();
    }

    /**
     * @When I go to the create :blockType block page
     */
    public function iGoToTheCreateImageBlockPage(string $blockType): void
    {

        if (BlockInterface::TEXT_BLOCK_TYPE === $blockType) {
            $this->createPage->open(['type' => BlockInterface::TEXT_BLOCK_TYPE]);

            return;
        }

        if (BlockInterface::HTML_BLOCK_TYPE === $blockType) {
            $this->createPage->open(['type' => BlockInterface::HTML_BLOCK_TYPE]);

            return;
        }

        if (BlockInterface::IMAGE_BLOCK_TYPE === $blockType) {
            $this->createPage->open(['type' => BlockInterface::IMAGE_BLOCK_TYPE]);

            return;
        }

        throw new TemplateTypeNotFound($blockType);
    }

    /**
     * @When I delete this block
     */
    public function iDeleteThisBlock()
    {
        $block = $this->sharedStorage->get('block');

        $this->indexPage->deleteBlock($block->getCode());
    }

    /**
     * @When I go to the update :code block page
     */
    public function iGoToTheUpdateBlockPage(string $code)
    {
        $id = $this->blockRepository->findOneBy(['code' => $code])->getId();

        $this->updatePage->open(['id' => $id]);
    }

    /**
     * @When I want to edit this block
     */
    public function iWantToEditThisBlock()
    {
        $block = $this->sharedStorage->get('block');

        $this->updatePage->open(['id' => $block->getId()]);
    }

    /**
     * @When I fill :fields fields
     */
    public function iFillFields(string $fields): void
    {
        $fields = explode(',', $fields);

        foreach ($fields as $field) {
            $this->resolveCurrentPage()->fillField(trim($field), $this->randomStringGenerator->generate());
        }
    }

    /**
     * @When /^I fill "([^"]*)" fields with (\d+) (?:character|characters)$/
     */
    public function iFillFieldsWithCharacters(string $fields, int $length): void
    {
        $fields = explode(',', $fields);

        foreach ($fields as $field) {
            $this->resolveCurrentPage()->fillField(trim($field), $this->randomStringGenerator->generate($length));
        }
    }

    /**
     * @When I fill the code with :code
     */
    public function iFillTheCodeWith(string $code): void
    {
        $this->resolveCurrentPage()->fillCode($code);
    }

    /**
     * @When I fill the name with :name
     */
    public function iFillTheNameWith(string $name): void
    {
        $this->resolveCurrentPage()->fillName($name);
    }

    /**
     * @When I fill the link with :link
     */
    public function iFillTheLinkWith(string $link): void
    {
        $this->resolveCurrentPage()->fillLink($link);
    }

    /**
     * @When I upload the :image image
     */
    public function iUploadTheImage(string $image): void
    {
        $this->resolveCurrentPage()->uploadImage($image);
    }

    /**
     * @When I disable it
     */
    public function iDisableIt(): void
    {
        $this->resolveCurrentPage()->disable();
    }

    /**
     * @When I fill the content with :content
     */
    public function iFillTheContentWith(string $content): void
    {
        $this->resolveCurrentPage()->fillContent($content);
    }

    /**
     * @When I add :firstSection and :secondSection sections to it
     */
    public function iAddAndSectionsToIt(string ...$sectionNames): void
    {
        $this->resolveCurrentPage()->associateSections($sectionNames);
    }

    /**
     * @When I add it
     * @When I try to add it
     */
    public function iAddIt(): void
    {
        $this->createPage->create();
    }

    /**
     * @When I update it
     */
    public function iUpdateIt(): void
    {
        $this->updatePage->saveChanges();
    }

    /**
     * @Then I should be notified that the block has been created
     */
    public function iShouldBeNotifiedThatNewImageBlockHasBeenCreated(): void
    {
        $this->notificationChecker->checkNotification(
            "Block has been successfully created.",
            NotificationType::success()
        );
    }

    /**
     * @Then I should be notified that the block has been successfully updated
     */
    public function iShouldBeNotifiedThatTheBlockHasBeenSuccessfullyUpdated(): void
    {
        $this->notificationChecker->checkNotification(
            "Block has been successfully updated.",
            NotificationType::success()
        );
    }

    /**
     * @Then I should be notified that the block has been deleted
     */
    public function iShouldBeNotifiedThatTheBlockHasBeenDeleted(): void
    {
        $this->notificationChecker->checkNotification(
            "Block has been successfully deleted.",
            NotificationType::success()
        );
    }

    /**
     * @Then this block should be disabled
     */
    public function thisBlockShouldBeDisabled(): void
    {
        Assert::false($this->resolveCurrentPage()->isBlockDisabled());
    }

    /**
     * @Then I should be notified that :fields fields cannot be blank
     */
    public function iShouldBeNotifiedThatCannotBeBlank(string $fields): void
    {
        $fields = explode(',', $fields);

        foreach ($fields as $field) {
            Assert::true($this->resolveCurrentPage()->containsErrorWithMessage(sprintf(
                "%s cannot be blank.",
                trim($field)
            )));
        }
    }

    /**
     * @Then I should be notified that there is already an existing block with provided code
     */
    public function iShouldBeNotifiedThatThereIsAlreadyAnExistingBlockWithCode(): void
    {
        Assert::true($this->resolveCurrentPage()->containsErrorWithMessage(
            "There is an existing block with this code.",
            false
        ));
    }

    /**
     * @Then I should be notified that :fields fields are too long
     */
    public function iShouldBeNotifiedThatFieldsAreTooLong(string $fields): void
    {
        $fields = explode(',', $fields);

        foreach ($fields as $field) {
            Assert::true($this->resolveCurrentPage()->containsErrorWithMessage(sprintf(
                "%s can not be longer than",
                trim($field)
            ), false));
        }
    }

    /**
     * @Then I should be able to select between :firstBlockType, :secondBlockType and :thirdBlockType block types under Create button
     */
    public function iShouldBeAbleToSelectBetweenAndBlockTypesUnderCreateButton(string ...$blockTypes): void
    {
        $blockTypesOnPage = $this->indexPage->getBlockTypes();

        Assert::eq(count($blockTypesOnPage), count($blockTypes));

        foreach ($blockTypes as $blockType) {
            Assert::oneOf($blockType, $blockTypesOnPage);
        }
    }

    /**
     * @Then the code field should be disabled
     */
    public function theCodeFieldShouldBeDisabled(): void
    {
        Assert::true($this->resolveCurrentPage()->isCodeDisabled());
    }

    /**
     * @Then I should see empty list of blocks
     */
    public function iShouldSeeEmptyListOfBlocks(): void
    {
        $this->resolveCurrentPage()->isEmpty();
    }

    /**
     * @return IndexPageInterface|CreatePageInterface|UpdatePageInterface|SymfonyPageInterface
     */
    private function resolveCurrentPage(): SymfonyPageInterface
    {
        return $this->currentPageResolver->getCurrentPageWithForm([
            $this->indexPage,
            $this->createPage,
            $this->updatePage,
        ]);
    }

    /**
     * @When /^I browse blocks$/
     */
    public function iBrowseBlocks()
    {
        throw new PendingException();
    }

    /**
     * @Given /^I filter blocks containing "([^"]*)"$/
     */
    public function iFilterBlocksContaining($arg1)
    {
        throw new PendingException();
    }

    /**
     * @Then /^I should see (\d+) block in the list$/
     */
    public function iShouldSeeBlockInTheList($arg1)
    {
        throw new PendingException();
    }

    /**
     * @Given /^I should see an block with "([^"]*)" code$/
     */
    public function iShouldSeeAnBlockWithCode($arg1)
    {
        throw new PendingException();
    }

    /**
     * @Given /^I should not see an block with "([^"]*)" code$/
     */
    public function iShouldNotSeeAnBlockWithCode($arg1)
    {
        throw new PendingException();
    }

    /**
     * @Given /^I am browsing blocks$/
     */
    public function iAmBrowsingBlocks()
    {
        throw new PendingException();
    }

    /**
     * @When /^I start sorting blocks by code$/
     */
    public function iStartSortingBlocksByCode()
    {
        throw new PendingException();
    }

    /**
     * @Then /^I should see (\d+) blocks in the list$/
     */
    public function iShouldSeeBlocksInTheList($arg1)
    {
        throw new PendingException();
    }

    /**
     * @Given /^the first block on the list should have code "([^"]*)"$/
     */
    public function theFirstBlockOnTheListShouldHaveCode($arg1)
    {
        throw new PendingException();
    }

    /**
     * @Given /^I switch the way blocks are sorted by code$/
     */
    public function iSwitchTheWayBlocksAreSortedByCode()
    {
        throw new PendingException();
    }

    /**
     * @When /^I start sorting blocks by name$/
     */
    public function iStartSortingBlocksByName()
    {
        throw new PendingException();
    }

    /**
     * @Given /^the first block on the list should have type "([^"]*)"$/
     */
    public function theFirstBlockOnTheListShouldHaveType($arg1)
    {
        throw new PendingException();
    }

    /**
     * @Given /^the blocks are already sorted by type$/
     */
    public function theBlocksAreAlreadySortedByType()
    {
        throw new PendingException();
    }

    /**
     * @When /^I switch the way blocks are sorted by type$/
     */
    public function iSwitchTheWayBlocksAreSortedByType()
    {
        throw new PendingException();
    }
}
