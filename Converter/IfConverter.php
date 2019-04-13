<?php

namespace VytSci\Bundle\SmartyToTwigBundle\Converter;

/**
 * Class IfConverter
 * @package VytSci\Bundle\SmartyToTwigBundle\Converter
 */
class IfConverter extends ConverterAbstract
{
	private $alt = array(
			'gt'  => '>',
			'lt'  => '<',
			'eq'  => '==',
			'neq' => '!=',
			'ne'  => '!=',
			'not' => '!',
			'mod' => '%',
			'or'  => '||',
			'and' => '&&'
	);

	public function convert(\SplFileInfo $file, $content)
	{
		// Replace {if }
		$content = $this->replaceIf($content);
		// Replace {elseif }
		$content = $this->replaceElseIf($content);
		// Replace {else}
		$content = preg_replace('#\{/if\s*\}#', "{% endif %}", $content);
		// Replace {/if}
		$content = preg_replace('#\{else\s*\}#', "{% else %}", $content);

		return $content;
	}

	public function getPriority()
	{
		return 50;
	}

	public function getName()
	{
		return 'if';
	}

	public function getDescription()
	{
		return 'Convert smarty if/else/elseif to twig';
	}

	private function replaceIf($content)
	{

		$pattern = "#\{if\b\s*([^{}]+)?\}#i";
		$string  = '{%% if %s %%}';

		return $this->replace($pattern, $content, $string);
	}

	private function replaceElseIf($content)
	{

		$pattern = "#\{elseif\b\s*([^{}]+)?\}#i";
		$string  = '{%% elseif %s %%}';

		return $this->replace($pattern, $content, $string);

	}


	private function replace($pattern, $content, $string)
	{
		return preg_replace_callback($pattern, function($matches) use ($string) {

				$match   = $matches[1];
				$search  = $matches[0];

				foreach ($this->alt as $key => $value) {
					$match = str_replace(" $key ", " $value ", $match);
				}

				// Replace $vars
				$match = $this->replaceVariable($match);

				$string = sprintf($string,$match);
				
				return str_replace($search, $string, $search);

			}, $content);
	}

	private function replaceVariable($string)
	{
		$pattern = '/\$([\w\.\-\>\[\]]+)/';
		return preg_replace_callback($pattern, function($matches) {
			// Convert Object to dot
	        $matches[1] = str_replace('->', '.',$matches[1]);

			return str_replace($matches[0],$matches[1],$matches[0]);
			
		}, $string);
	}
}
