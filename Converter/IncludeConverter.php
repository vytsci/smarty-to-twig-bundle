<?php

namespace VytSci\Bundle\SmartyToTwigBundle\Converter;

/**
 * Class IncludeConverter
 * @package VytSci\Bundle\SmartyToTwigBundle\Converter
 */
class IncludeConverter extends ConverterAbstract
{

	public function convert(\SplFileInfo $file, $content)
	{
		return $this->replace($content);
	}

	public function getPriority()
	{
		return 100;
	}

	public function getName()
	{
		return 'include';
	}

	public function getDescription()
	{
		return 'Convert smarty include to twig include';
	}

	private function replace($content)
	{
		$pattern = '/\{include\b\s*([^{}]+)?\}/';
		$string = '{% include :template :with :vars %}';

		return preg_replace_callback($pattern, function($matches) use ($string) {

	        $match   = $matches[1];
	        $attr    = $this->attributes($match);

	        $replace = array();
	        $replace['template'] = $attr['file'];

	        // If we have any other variables
	        if (count($attr) > 1) {
	            $replace['with'] = 'with';
	            unset($attr['file']); // We won't need in vars

	             $vars = array();
	            foreach ($attr as $key => $value) {
	            	$value  = $this->value($value);
	                $vars[] = "'".$key."' : ".$value;
	            }

	            $replace['vars'] = '{'.implode(', ',$vars).'}';
	        }

	        $string  = $this->vsprintf($string,$replace);

	        // Replace more than one space to single space
	        $string = preg_replace('!\s+!', ' ', $string);

	        return str_replace($matches[0], $string, $matches[0]);

	      },$content);

	}

}
