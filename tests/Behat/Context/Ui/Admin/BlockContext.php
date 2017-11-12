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
     * @var CurrentPageResolverInterface
     */
    private $currentPageResolver;

    /**
     * @var NotificationCheckerInterface
     */
    private $notificationChecker;

    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var RandomStringGeneratorInterface
     */
    private $randomStringGenerator;

    /**
     * @var BlockRepositoryInterface
     */
    private $blockRepository;

    /**
     * @param IndexPageInterface $indexPage
     * @param CreatePageInterface $createPage
     * @param UpdatePageInterface $updatePage
     * @param CurrentPageResolverInterface $currentPageResolver
     * @param NotificationCheckerInterface $notificationChecker
     * @param SharedStorageInterface $sharedStorage
     * @param RandomStringGeneratorInterface $randomStringGenerator
     * @param BlockRepositoryInterface $blockRepository
     */
    public function __construct(
        IndexPageInterface $indexPage,
        CreatePageInterface $createPage,
        UpdatePageInterface $updatePage,
        CurrentPageResolverInterface $currentPageResolver,
        NotificationCheckerInterface $notificationChecker,
        SharedStorageInterface $sharedStorage,
        RandomStringGeneratorInterface $randomStringGenerator,
        BlockRepositoryInterface $blockRepository
    )
    {
        $this->createPage = $createPage;
        $this->updatePage = $updatePage;
        $this->indexPage = $indexPage;
        $this->currentPageResolver = $currentPageResolver;
        $this->notificationChecker = $notificationChecker;
        $this->sharedStorage = $sharedStorage;
        $this->randomStringGenerator = $randomStringGenerator;
        $this->blockRepository = $blockRepository;
    }

    /**
     * @When I go to the cms blocks page
     */
    public function iGoToTheCmsBlocksPage()
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
     * @When I go to the update :code block page
     */
    public function iGoToTheUpdateBlockPage(string $code)
    {
        $id = $this->blockRepository->findOneBy(['code' => $code])->getId();

        $this->updatePage->open(['id' => $id]);
    }

    /**
     * @When I fill :fields fields
     */
    public function iFillFields(string $fields): void
    {
        $fields = explode(',', $fields);

        foreach ($fields as $field) {
            $this->resolveCurrentPage()->fillField(trim($field), $this->randomStringGenerator->generate(5));
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
     * @When I upload the :video video
     */
    public function iUploadTheVideo(string $video): void
    {
        $this->resolveCurrentPage()->uploadVideo($video);
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
     * @When I remove this image block
     */
    public function iRemoveThisImageBlock(): void
    {
        /** @var BlockInterface $block */
        $block = $this->sharedStorage->get('block');
        $code = $block->getCode();

        $this->indexPage->removeBlock($code);
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
     * @Then I should see :number dynamic content blocks with :type type
     */
    public function iShouldSeeDynamicContentBlocksWithType(int $number, string $type): void
    {
        Assert::eq($number, $this->indexPage->getBlocksWithTypeCount($type));
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
     * @Then I should be notified that this block was removed
     */
    public function iShouldBeNotifiedThatThisBlockWasRemoved(): void
    {
        $this->notificationChecker->checkNotification(
            "Block has been successfully deleted.",
            NotificationType::success()
        );
    }

    /**
     * @Then I should be notified that :fields cannot be blank
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
     * @When I add :firstSection and :secondSection sections to it
     */
    public function iAddAndSectionsToIt(string ...$sectionNames): void
    {
        $this->resolveCurrentPage()->associateSections($sectionNames);
    }

    /**
     * @return CreatePageInterface|UpdatePageInterface|SymfonyPageInterface
     */
    private function resolveCurrentPage(): SymfonyPageInterface
    {
        return $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);
    }
}
