<?php

namespace VytSci\Bundle\SmartyToTwigBundle\Converter\Config;

use VytSci\Bundle\SmartyToTwigBundle\Converter\ConverterAbstract;

/**
 * Interface ConfigInterface
 * @package VytSci\Bundle\SmartyToTwigBundle\Config
 */
interface ConfigInterface
{
	/**
	 * Returns the name of the configuration.
	 *
	 * The name must be all lowercase and without any spaces.
	 *
	 * @return string The name of the configuration
	 */
	public function getName();

	/**
	 * Returns the description of the configuration.
	 *
	 * A short one-line description for the configuration.
	 *
	 * @return string The description of the configuration
	 */
	public function getDescription();

	/**
	 * Returns an iterator of files to scan.
	 *
	 * @return \Traversable A \Traversable instance that returns \SplFileInfo instances
	 */
	public function getFinder();

	/**
	 * Returns the converters to run.
	 *
	 * @return array|integer A level or a list of converter names
	 */
	public function getConverters();

	/**
	 * Sets the root directory of the project.
	 *
	 * @param string $dir The project root directory
	 */
	public function setDir($dir);

	/**
	 * Returns the root directory of the project.
	 *
	 * @return string The project root directory
	 */
	public function getDir();

	/**
	 * Adds an instance of a custom converter.
	 *
	 * @param ConverterAbstract $converter
	 */
	public function addCustomConverter(ConverterAbstract $converter);

	/**
	 * Returns the custom converters to use.
	 *
	 * @return ConverterAbstract[]
	 */
	public function getCustomConverters();
}
