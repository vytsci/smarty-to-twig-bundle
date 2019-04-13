<?php

namespace VytSci\Bundle\SmartyToTwigBundle\Converter;

use SebastianBergmann\Diff\Diff;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo as FinderSplFileInfo;
use VytSci\Bundle\SmartyToTwigBundle\Converter\Config\ConfigInterface;

/**
 * Class Converter
 * @package VytSci\Bundle\SmartyToTwigBundle\Converter
 */
class Converter
{
    const VERSION = '0.1-DEV';

    protected $converters = array();
    protected $configs = array();

    public function registerBuiltInConverters()
    {
        foreach (Finder::create()->files()->depth('== 0')->in(__DIR__) as $file) {
            $class = 'VytSci\\Bundle\\SmartyToTwigBundle\\Converter\\'.basename($file, '.php');
            $reflection = new \ReflectionClass($class);
            if ($reflection->isSubclassOf(ConverterAbstract::class)) {
                $this->addConverter(new $class());
            }
        }
    }

    public function registerCustomConverters($converter)
    {
        foreach ($converter as $convert) {
            $this->addConverter($convert);
        }
    }

    public function addConverter(ConverterAbstract $convert)
    {
        $this->converters[] = $convert;
    }

    /**
     * @return mixed|ConverterAbstract[]
     */
    public function getConverters()
    {
        $this->sortConverters();

        return $this->converters;
    }

    /**
     * @throws \ReflectionException
     */
    public function registerBuiltInConfigs()
    {
        foreach (Finder::create()->files()->in(__DIR__.'/Config') as $file) {
            $class = 'VytSci\\Bundle\\SmartyToTwigBundle\\Converter\\Config\\'.basename($file, '.php');
            if (strpos($class, 'Interface')) {
                continue;
            }
            $reflection = new \ReflectionClass($class);
            if ($reflection->implementsInterface(ConfigInterface::class)) {
                $this->addConfig(new $class());
            }
        }
    }

    public function addConfig(ConfigInterface $config)
    {
        $this->configs[] = $config;
    }

    /**
     * @return array|ConfigInterface[]
     */
    public function getConfigs()
    {
        return $this->configs;
    }

    /**
     * Fixes all files for the given finder.
     *
     * @param ConfigInterface $config A ConfigInterface instance
     * @param Boolean $dryRun Whether to simulate the changes or not
     * @param Boolean $diff Whether to provide diff
     * @return array
     */
    public function convert(ConfigInterface $config, $dryRun = false, $diff = false, $outputExt='')
    {
        $this->sortConverters();

        $converter = $this->prepareConverters($config);
        $changed = array();
        foreach ($config->getFinder() as $file) {
            if ($file->isDir()) {
                continue;
            }

            if ($fixInfo = $this->conVertFile($file, $converter, $dryRun, $diff, $outputExt)) {
                if ($file instanceof FinderSplFileInfo) {
                    $changed[$file->getRelativePathname()] = $fixInfo;
                } else {
                    $changed[$file->getPathname()] = $fixInfo;
                }
            }
        }

        return $changed;
    }

    public function conVertFile(\SplFileInfo $file, array $converter, $dryRun, $diff, $outputExt)
    {
        $new = $old = file_get_contents($file->getRealpath());
        $appliedConverters = array();

        foreach ($converter as $convert) {
            if (!$convert->supports($file)) {
                continue;
            }

            $new1 = $convert->convert($file, $new);
            if ($new1 != $new) {
                $appliedConverters[] = $convert->getName();
            }
            $new = $new1;
        }

        if ($new != $old) {
            if (!$dryRun) {

                $filename = $file->getRealpath();

                $ext = strrchr($filename, '.');
                if ($outputExt) {
                    $filePathinfo = pathinfo($filename);
                    $filename = pathinfo($filename, PATHINFO_DIRNAME)
                        . DIRECTORY_SEPARATOR
                        . pathinfo($filename, PATHINFO_FILENAME)
                        . '.' . $outputExt
                    ;
                }

                file_put_contents($filename, $new);
            }

            $fixInfo = array('appliedConverters' => $appliedConverters);

            if ($diff) {
                $fixInfo['diff'] = $this->stringDiff($old, $new);
            }

            return $fixInfo;
        }
    }

    protected function stringDiff($old, $new)
    {
        $diff = new Diff($old, $new);

        $diff = implode(PHP_EOL, array_map(function ($string) {
            $string = preg_replace('/^(\+){3}/', '<info>+++</info>', $string);
            $string = preg_replace('/^(\+){1}/', '<info>+</info>', $string);

            $string = preg_replace('/^(\-){3}/', '<error>---</error>', $string);
            $string = preg_replace('/^(\-){1}/', '<error>-</error>', $string);

            $string = str_repeat(' ', 6) . $string;

            return $string;
        }, explode(PHP_EOL, $diff)));

        return $diff;
    }

    private function sortConverters()
    {
        usort($this->converters, function (ConverterAbstract $a, ConverterAbstract $b) {
            if ($a->getPriority() == $b->getPriority()) {
                return 0;
            }

            return $a->getPriority() > $b->getPriority() ? -1 : 1;
        });
    }

    private function prepareConverters(ConfigInterface $config)
    {
        $converters = $config->getConverters();

        /*foreach ($converters as $converter) {
            if ($converter instanceof ConverterAbstract) {
                $converter->setConfig($config);
            }
        }*/

        return $converters;
    }
}
