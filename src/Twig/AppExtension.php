<?php

namespace  App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension{

    public function getFilters()
    {
        return [
            new TwigFilter('html', [$this, 'html'], ['is_safe' => ['html','css']]),
        ];
    }

    public function html($html)
    {
        return $html;
    }

    public function getName()
    {
        return 'acme_extension';
    }

}