<?php

namespace Fomvasss\UrlFacetFilter;

class Facade extends \Illuminate\Support\Facades\Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'url-facet-filter';
    }
}
