<?php

/**
 * This file was created by the developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://bitbag.shop and write us
 * an email on kontakt@bitbag.pl.
 */

declare(strict_types=1);

namespace Tests\BitBag\CmsPlugin\Behat\Page\Shop\FrequentlyAskedQuestion;

use Sylius\Behat\Page\SymfonyPage;

/**
 * @author Mikołaj Król <mikolaj.krol@bitbag.pl>
 */
final class IndexPage extends SymfonyPage implements IndexPageInterface
{
    /**
     * {@inheritdoc}
     */
    public function getRouteName(): string
    {
        return 'bitbag_shop_frequently_asked_question_index';
    }

    /**
     * {@inheritdoc}
     */
    public function hasFrequentlyAskedQuestionsNumber(int $number): bool
    {
        $frequentlyAskedQuestionsOnPage = $this->getElement('faqs')->findAll('css', '.bitbag-question');

        return $number === count($frequentlyAskedQuestionsOnPage);
    }

    /**
     * {@inheritdoc}
     */
    public function hasQuestionWithPositionPrefixAtValidIndex(int $position): bool
    {
        $frequentlyAskedQuestionsOnPage = $this->getElement('faqs')->findAll('css', '.bitbag-question');
        $index = $position - 1;

        if (false === array_key_exists($index, $frequentlyAskedQuestionsOnPage)) {
            return false;
        }

        $frequentlyAskedQuestionOnPage = $frequentlyAskedQuestionsOnPage[$index];
        $question = $frequentlyAskedQuestionOnPage->getText();

        $questionParts = explode('. ', $question);
        $positionInQuestion = (int)str_replace('. ', '', $questionParts[0]);

        if ($position === $positionInQuestion) {
            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'faqs' => '#bitbag-faqs',
        ]);
    }
}
