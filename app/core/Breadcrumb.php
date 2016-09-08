<?php

namespace app\core;

/**
 * Generate breadcrumb
 */
class Breadcrumb
{
	protected $urls = [];
    protected $options = [];

	public function __construct($label = null, $link = null, array $args = [])
	{
        if ($label) {
            $this->add($label, $link, $args);
        }
	}

    /**
     * Set options
     * @param array $options
     */
    public function setOptions(array $options)
    {
        $this->options = $options;

        return $this;
    }

	/**
	 * Add breadcrumb
	 * @param string $label
	 * @param string $link
	 * @param array  $args
	 */
	public function add($label, $link = null, array $args = [])
	{
		$this->urls[] = ['label'=>$label,'link'=>$link,'args'=>$args];

		return $this;
	}

	/**
	 * Remove item
	 * @param  int $index
	 */
	public function remove($index)
	{
		unset($this->urls[$index]);

		return $this;
	}

	/**
	 * Render breadcrumb
	 * @param  array  $options
	 * @return string
	 */
	public function render(array $options = [])
	{
		$li = '';
		$app = App::instance();
		$urls = $this->urls;
		$last = array_pop($urls);
		foreach ($urls as $key => $url) {
			$li .= '<li><a href="'.$app->url($url['link'], $url['args']).'">'.$url['label'].'</a></li>';
		}
		$li .= '<li class="active">'.$last['label'].'</li>';

		$options += [
			'class'=>'breadcrumb',
		];

		return '<ul class="'.$options['class'].'">'.$li.'</ul>';
	}

    public function __toString()
    {
        return $this->render($this->options);
    }
}
