<?php

namespace VytSci\Bundle\SmartyToTwigBundle\Converter;

/**
 * Class VariableConverter
 * @package VytSci\Bundle\SmartyToTwigBundle\Converter
 */
class VariableConverter extends ConverterAbstract
{

	public function convert(\SplFileInfo $file, $content)
	{
		$content = $this->replace($content);

		return $content;
	}

	public function getPriority()
	{
		return 100;
	}

	public function getName()
	{
		return 'variable';
	}

	public function getDescription()
	{
		return 'Convert smarty variable {$var.name} to twig {{ var.name }}';
	}

	private function replace($content)
	{
		$pattern = '/\{\$([\w\.\-\>\[\]]+)\}/';
		return preg_replace_callback($pattern, function($matches) {

	        $match   = $matches[1];
	        $search  = $matches[0];

	        // Convert Object to dot
	        $match = str_replace('->', '.', $match);

	        $search  = str_replace($search, '{{ '.$match.' }}', $search);

	       return $search; 

   		},$content);

	}

}
