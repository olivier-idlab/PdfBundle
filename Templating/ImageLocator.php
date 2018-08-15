<?php

/*
 * Copyright 2011 Piotr Sliwa <peter.pl7@gmail.com>
 *
 * License information is in LICENSE file
 */

namespace Ps\PdfBundle\Templating;

use Symfony\Component\HttpKernel\Kernel;

/**
 * Image locator
 *
 * @author Piotr Sliwa <peter.pl7@gmail.com>
 */
class ImageLocator implements ImageLocatorInterface
{
    private $kernel;

    public function __construct(Kernel $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * Converts image logical name in "BundleName:image-name.extension" format to absolute file path.
     *
     * @param string $logicalImageName
     *
     * @throws /InvalidArgumentException If bundle does not exist
     *
     * @return string file path
     */
    public function getImagePath($logicalImageName)
    {
        $pos = mb_strpos($logicalImageName, ':');

        // add support for ::$imagePath syntax as in twig
        // @see http://symfony.com/doc/current/book/page_creation.html#optional-step-3-create-the-template
        if (false === $pos || 0 === $pos) {
            return static::getImageDir($this->kernel->getRootDir()).ltrim($logicalImageName, ':');
        }

        $bundleName = mb_substr($logicalImageName, 0, $pos);
        $imageName = mb_substr($logicalImageName, $pos + 1);
        $bundlePath = $this->kernel->getBundle($bundleName)->getPath();

        return static::getImageDir($bundlePath).$imageName;
    }

    /**
     * @param string $rootDir
     *
     * @return string dir path
     */
    private static function getImageDir($rootDir)
    {
        return $rootDir.'/Resources/public'.(is_dir($rootDir.'/Resources/public/images') ? '/images/' : '/img/');
    }
}
