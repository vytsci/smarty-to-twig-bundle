<?php

namespace VytSci\Bundle\SmartyToTwigBundle\Converter;

/**
 * Class CommentConverter
 * @package VytSci\Bundle\SmartyToTwigBundle\Converter
 */
class CommentConverter extends ConverterAbstract
{

	public function convert(\SplFileInfo $file, $content)
	{
		return str_replace(array('{*','*}'), array('{#','#}'), $content);
	}

	public function getPriority()
	{
		return 52;
	}

	public function getName()
	{
		return 'comment';
	}

	public function getDescription()
	{
		return 'Convert smarty comments {* *} to twig {# #}';
	}

}
