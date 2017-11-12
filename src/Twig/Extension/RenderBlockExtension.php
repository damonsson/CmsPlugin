<?php

/**
 * This file was created by the developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://bitbag.shop and write us
 * an email on mikolaj.krol@bitbag.pl.
 */

declare(strict_types=1);

namespace BitBag\CmsPlugin\Twig\Extension;

use BitBag\CmsPlugin\Entity\Block;
use BitBag\CmsPlugin\Entity\BlockInterface;
use BitBag\CmsPlugin\Exception\TemplateTypeNotFound;
use BitBag\CmsPlugin\Repository\BlockRepositoryInterface;
use Psr\Log\LoggerInterface;

/**
 * @author Patryk Drapik <patryk.drapik@bitbag.pl>
 * @author Mikołaj Król <mikolaj.krol@bitbag.pl>
 */
final class RenderBlockExtension extends \Twig_Extension
{
    const TEXT_BLOCK_TEMPLATE = '@BitBagCmsPlugin/Shop/Block/textBlock.html.twig';
    const HTML_BLOCK_TEMPLATE = '@BitBagCmsPlugin/Shop/Block/htmlBlock.html.twig';
    const IMAGE_BLOCK_TEMPLATE = '@BitBagCmsPlugin/Shop/Block/imageBlock.html.twig';
    const VIDEO_BLOCK_TEMPLATE = '@BitBagCmsPlugin/Shop/Block/videoBlock.html.twig';

    /**
     * @var BlockRepositoryInterface
     */
    private $blockRepository;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var array
     */
    private $existBlockTypes;

    /**
     * @param BlockRepositoryInterface $blockRepository
     * @param LoggerInterface $logger
     */
    public function __construct(
        BlockRepositoryInterface $blockRepository,
        LoggerInterface $logger
    )
    {
        $this->blockRepository = $blockRepository;
        $this->logger = $logger;
        $this->existBlockTypes = $this->getExistBlockTypes();
    }

    /**
     * @return \Twig_SimpleFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new \Twig_SimpleFunction('bitbag_render_block', [$this, 'renderBlock'], ['needs_environment' => true, 'is_safe' => ['html'],]),
        ];
    }

    /**
     * @param \Twig_Environment $twigEnvironment
     * @param string $code
     *
     * @return null|string
     * @throws TemplateTypeNotFound
     */
    public function renderBlock(\Twig_Environment $twigEnvironment, $code): ?string
    {
        $block = $this->blockRepository->findEnabledByCode($code);

        if (false === $block instanceof BlockInterface) {
            $this->logger->warning(sprintf(
                'Block with "%s" code was not found in the database.',
                $code
            ));

            return null;
        }

        foreach ($this->existBlockTypes as $existBlockType) {
            if ($existBlockType === $block->getType()) {
                $blockTemplate = strtoupper($block->getType()) . '_BLOCK_TEMPLATE';
                return $twigEnvironment->render(constant('self::' . $blockTemplate), ['block' => $block]);
            }
        }

        throw new TemplateTypeNotFound($block->getType());

    }

    private function getExistBlockTypes(): array
    {
        $reflector = new \ReflectionClass(Block::class);
        $constants = $reflector->getConstants();
        $values = [];

        foreach ($constants as $constant => $value) {
            $values[] = $value;
        }

        return $values;
    }
}