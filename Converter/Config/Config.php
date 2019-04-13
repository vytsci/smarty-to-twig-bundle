<?php

namespace VytSci\Bundle\SmartyToTwigBundle\Converter\Config;

use VytSci\Bundle\SmartyToTwigBundle\Converter\ConverterAbstract;
use VytSci\Bundle\SmartyToTwigBundle\Converter\Finder\DefaultFinder;
use VytSci\Bundle\SmartyToTwigBundle\Converter\Finder\FinderInterface;

/**
 * Class Config
 * @package VytSci\Bundle\SmartyToTwigBundle\Converter\Config
 */
class Config implements ConfigInterface
{
	protected $name;
	protected $description;
	protected $finder;
	protected $converter;
	protected $dir;
	protected $customConverter;

	public function __construct($name = 'default', $description = 'A default configuration')
	{
		$this->name = $name;
		$this->description = $description;
		$this->converter = ConverterAbstract::ALL_LEVEL;
		$this->finder = new DefaultFinder();
		$this->customConverter = array();
	}

	public static function create()
	{
		return new static();
	}

	public function setDir($dir)
	{
		$this->dir = $dir;
	}

	public function getDir()
	{
		return $this->dir;
	}

	public function finder(\Traversable $finder)
	{
		$this->finder = $finder;

		return $this;
	}

	public function getFinder()
	{
		if ($this->finder instanceof FinderInterface && $this->dir !== null) {
			$this->finder->setDir($this->dir);
		}

		return $this->finder;
	}

	public function converters($converter)
	{
		$this->converter = $converter;

		return $this;
	}

	public function getConverters()
	{
		return $this->converter;
	}

	public function getName()
	{
		return $this->name;
	}

	public function getDescription()
	{
		return $this->description;
	}

	public function addCustomConverter(ConverterAbstract $converter)
	{
		$this->customConverter[] = $converter;
	}

	public function getCustomConverters()
	{
		return $this->customConverter;
	}
}
