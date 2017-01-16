<?php
namespace Psito\Themes\Twig\Extensions;

use Psito\Themes\Themes as ThemeHandler;
use Twig_Extension;
use Twig_SimpleFunction;
use Twig_SimpleFilter;

class Themes extends Twig_Extension
{
	/**
	 * @var Psito\Themes\Themes
	 */
	protected $theme;

	/**
	 * Create a new Themes Twig_Extension instance.
	 *
	 * @param Psito\Themes\Themes  $theme
	 */
	public function __construct(ThemeHandler $theme)
	{
		$this->theme = $theme;
	}

	public function getName()
	{
		return 'Caffeinated_Themes_Extension_Themes';
	}

	public function getFunctions()
	{
		return [
			new Twig_SimpleFunction('theme_asset', [$this->theme, 'asset']),
			new Twig_SimpleFunction('theme_secure_asset', [$this->theme, 'secureAsset']),
		];
	}
}
