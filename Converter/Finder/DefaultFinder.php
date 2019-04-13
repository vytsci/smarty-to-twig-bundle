<?php

namespace VytSci\Bundle\SmartyToTwigBundle\Converter\Finder;

use Symfony\Component\Finder\Finder;

/**
 * Class DefaultFinder
 * @package VytSci\Bundle\SmartyToTwigBundle\Converter\Finder
 */
class DefaultFinder extends Finder implements FinderInterface
{
    public function __construct()
    {
        parent::__construct();

        $files = $this->getFilesToExclude();

        $this
            ->files()
            ->name('*.tpl')
            ->ignoreDotFiles(true)
            ->ignoreVCS(true)
            ->exclude('vendor')
            ->filter(function (\SplFileInfo $file) use ($files) {
                return !in_array($file->getRelativePathname(), $files);
            })
        ;
    }

    public function setDir($dir)
    {
        $this->in($this->getDirs($dir));
    }

    /**
     * Gets the directories that needs to be scanned for files to validate.
     *
     * @return array
     */
    protected function getDirs($dir)
    {
        return array($dir);
    }

    /**
     * Excludes files because modifying them would break (mainly useful for fixtures in unit tests).
     *
     * @return array
     */
    protected function getFilesToExclude()
    {
        return array();
    }
}
