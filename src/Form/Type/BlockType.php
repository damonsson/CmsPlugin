<?php

/**
 * This file was created by the developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://bitbag.shop and write us
 * an email on mikolaj.krol@bitbag.pl.
 */

declare(strict_types=1);

namespace BitBag\CmsPlugin\Form\Type;

use BitBag\CmsPlugin\Entity\BlockInterface;
use BitBag\CmsPlugin\Form\Type\Translation\HtmlBlockTranslationType;
use BitBag\CmsPlugin\Form\Type\Translation\ImageBlockTranslationType;
use BitBag\CmsPlugin\Form\Type\Translation\TextBlockTranslationType;
use BitBag\CmsPlugin\Form\Type\Translation\VideoBlockTranslationType;
use Sylius\Bundle\ProductBundle\Form\Type\ProductAutocompleteChoiceType;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Sylius\Bundle\ResourceBundle\Form\Type\ResourceTranslationsType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Valid;

/**
 * @author Patryk Drapik <patryk.drapik@bitbag.pl>
 * @author Mikołaj Król <mikolaj.krol@bitbag.pl>
 */
final class BlockType extends AbstractResourceType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var BlockInterface $block */
        $block = $builder->getData();

        $builder
            ->add('code', TextType::class, [
                'label' => 'bitbag.ui.code',
                'disabled' => null !== $block->getCode(),
            ])
            ->add('sections', SectionAutocompleteChoiceType::class, [
                'label' => 'bitbag.ui.sections',
                'multiple' => true,
            ])
            ->add('enabled', CheckboxType::class, [
                'label' => 'bitbag.ui.enabled',
            ])
            ->add('products', ProductAutocompleteChoiceType::class, [
                'label' => 'bitbag.ui.products',
                'multiple' => true,
            ])
        ;

        $this->resolveBlockType($block, $builder);
    }

    /**
     * @param FormBuilderInterface $builder
     * @param BlockInterface $block
     */
    private function resolveBlockType(BlockInterface $block, FormBuilderInterface $builder): void
    {
        if (BlockInterface::TEXT_BLOCK_TYPE === $block->getType()) {
            $builder->add('translations', ResourceTranslationsType::class, [
                'label' => 'bitbag.ui.contents',
                'entry_type' => TextBlockTranslationType::class,
                'validation_groups' => ['bitbag_content'],
                'constraints' => [
                    new Valid(),
                ],
            ]);

            return;
        }

        if (BlockInterface::HTML_BLOCK_TYPE === $block->getType()) {
            $builder->add('translations', ResourceTranslationsType::class, [
                'label' => 'bitbag.ui.contents',
                'entry_type' => HtmlBlockTranslationType::class,
                'validation_groups' => ['bitbag_content'],
                'constraints' => [
                    new Valid(),
                ],
            ]);

            return;
        }

        if (BlockInterface::IMAGE_BLOCK_TYPE === $block->getType()) {
            $builder->add('translations', ResourceTranslationsType::class, [
                'label' => 'bitbag.ui.images',
                'entry_type' => ImageBlockTranslationType::class,
                'validation_groups' => null === $block->getId() ? ['bitbag_image'] : [],
                'constraints' => [
                    new Valid(),
                ],
            ]);

            return;
        }
//        @TODO: IF IF IF IF IF IF IF IF IF
        if (BlockInterface::VIDEO_BLOCK_TYPE === $block->getType()) {
            $builder->add('translations', ResourceTranslationsType::class, [
                'label' => 'bitbag.ui.videos',
                'entry_type' => VideoBlockTranslationType::class,
                'validation_groups' => null === $block->getId() ? ['bitbag_video'] : [],
                'constraints' => [
                    new Valid(),
                ],
            ]);

            return;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'bitbag_cms_block';
    }
}
