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
     * @param string $publicSubDir
     *
     * @return string file path
     *
     * @throws /InvalidArgumentException If bundle does not exist.
     */
    public function getImagePath($logicalImageName, $publicSubDir = '/Resources/public')
    {
        $pos = strpos($logicalImageName, ':');
        $imagesSubDir = is_dir($publicSubDir.'/images') ? $publicSubDir.'/images/' : $publicSubDir.'/img/';

        // add support for ::$imagePath syntax as in twig
        // @see http://symfony.com/doc/current/book/page_creation.html#optional-step-3-create-the-template
        if ($pos === false || $pos === 0) {
            return $this->kernel->getRootDir().$imagesSubDir.ltrim($logicalImageName, ':');
        }

        $bundleName = substr($logicalImageName, 0, $pos);
        $imageName = substr($logicalImageName, $pos + 1);

        $bundle = $this->kernel->getBundle($bundleName);
        $bundlePath = $bundle->getPath();

        return $bundlePath.$imagesSubDir.$imageName;
    }
}
