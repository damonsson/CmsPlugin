<?php

/**
 * This file was created by the developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://bitbag.shop and write us
 * an email on kontakt@bitbag.pl.
 */

declare(strict_types=1);

namespace BitBag\CmsPlugin\Resolver;

use BitBag\CmsPlugin\Entity\PageInterface;

/**
 * @author Mikołaj Król <mikolaj.krol@bitbag.pl>
 */
interface PageResourceResolverInterface
{
    /**
     * @param string $code
     *
     * @return PageInterface|null
     */
    public function findOrLog(string $code): ?PageInterface;
}
