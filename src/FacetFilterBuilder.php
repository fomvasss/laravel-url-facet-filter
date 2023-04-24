<?php

namespace Fomvasss\UrlFacetFilter;

class FacetFilterBuilder
{
    /** @var mixed The Laravel application configs. */
    protected $config;

    /** @var string URL-path */
    protected $urlPath;

    /**
     * FacetFilterBuilder constructor.
     * @param null $app
     */
    public function __construct($app = null)
    {
        if (!$app) {
            $app = app();   //Fallback when $app is not given
        }
        $this->app = $app;

        $this->config = $this->app['config'];
    }

    /**
     * @return string
     */
    public function getFilterUrlKey(): string
    {
        return $this->config->get('laravel-url-facet-filter.url_keys.filter', '⛃');
    }

    /**
     * @return string
     */
    public function getAttrsDelimiter(): string
    {
        return $this->config->get('laravel-url-facet-filter.url_keys.attrs_delimiter', '♦');
    }

    /**
     * @return string
     */
    public function getValuesDelimiter(): string
    {
        return $this->config->get('laravel-url-facet-filter.url_keys.values_delimiter', '⚬');
    }

    /**
     * @return string
     */
    public function getAttrValuesDelimiter(): string
    {
        return $this->config->get('laravel-url-facet-filter.url_keys.attr_values_delimiter', '☛');
    }

    /**
     * @param string|null $attr
     * @param string|null $value
     * @param bool $replaceValue
     * @return string
     */
    public function build(string $attr = null, string $value = null, bool $replaceValue = false): string
    {
        $currentFilter = request($this->getFilterUrlKey(), '');
        $newFilter = $this->toggle($currentFilter, $attr, $value, $replaceValue);

        $keys = $this->config->get('laravel-url-facet-filter.except_keys', []);
        $persistParameters = request()->except(array_merge($keys, [$this->getFilterUrlKey()]));

        if ($newFilter) {
            $queryString = urldecode(http_build_query(array_merge($persistParameters, [
                $this->getFilterUrlKey() => $newFilter,
            ])));

            return url($this->getUrlPath().'?'.$queryString);
        }

        return url($this->getUrlPath());
    }

    /**
     * @return string
     */
    public function reset(array $add = []): string
    {
        $keys = $this->config->get('laravel-url-facet-filter.except_keys', []);
        $except = array_merge($keys, [$this->getFilterUrlKey()], $add);
        $queryString = urldecode(http_build_query(request()->except($except)));

        return rtrim(url($this->getUrlPath().'?'.$queryString), '?');
    }

    /**
     * @return string
     */
    public function getUrlPath(): string
    {
        if ($this->urlPath) {
            return $this->urlPath;
        }

        return request()->path();
    }

    /**
     * @param string $urlPath
     * @return FacetFilterBuilder
     */
    public function setUrlPath(string $urlPath): self
    {
        $this->urlPath = $urlPath;

        return $this;
    }

    /**
     * @return bool
     */
    public function issetFilter(array $add = []): bool
    {
        return request()->hasAny(array_merge([$this->getFilterUrlKey()], $add));
    }

    /**
     * @param string $attr
     * @param string|null $value
     * @return bool
     */
    public function has(string $attr, string $value = null): bool
    {
        $currentFilter = request($this->getFilterUrlKey(), '');

        $filterArray = $this->toArray($currentFilter);

        if ($value) {
            return in_array($value, $filterArray[$attr] ?? []);
        }

        return in_array($attr, array_keys($filterArray));
    }

    /**
     * Количество выбранных значений для атрибута.
     *
     * @param string $attr
     * @return int
     */
    public function countValues(string $attr): int
    {
        $currentFilter = request($this->getFilterUrlKey(), '');

        $filterArray = $this->toArray($currentFilter);

        if (isset($filterArray[$attr]) && is_array($filterArray[$attr])) {
            return count($filterArray[$attr]);
        }

        return 0;
    }

    /**
     * @param string $currentFilter
     * @param string|null $attr
     * @param string|null $value
     * @param bool $replaceValue
     * @return string
     */
    public function toggle(string $currentFilter, string $attr = null, string $value = null, bool $replaceValue = false): string
    {
        if (empty($attr)) {
            return '';
        }

        $filterArray = $this->toArray($currentFilter);

        if (empty($value)) {
            unset($filterArray[$attr]);
            return $this->toStr($filterArray);
        }

        if (isset($filterArray[$attr]) && (($index = array_search($value, $filterArray[$attr])) !== false)) {
            unset($filterArray[$attr][$index]);
            if (!count($filterArray[$attr])) {
                unset($filterArray[$attr]);
            }
        } else {
            if ($replaceValue) {
                $filterArray[$attr] = [];
                $filterArray[$attr][0] = $value;
            } else {
                $filterArray[$attr][] = $value;
            }
        }

        return $this->toStr($filterArray);
    }

    /**
     * @param string|null $str
     * @return array
     */
    public function toArray(string $str = null): array
    {
        $facet = [];
        if (count($filterAttrsValues = explode($this->getAttrsDelimiter(), $str))) {
            foreach ($filterAttrsValues as $row) {
                if (count($res = explode($this->getAttrValuesDelimiter(), $row)) == 2) {
                    if (count($values = explode($this->getValuesDelimiter(), $res[1]))) {
                        $facet[$res[0]] = $values;
                    }
                }
            }
        }

        return $facet;
    }

    /**
     * @param array $array
     * @return string
     */
    public function toStr(array $array): string
    {
        $str = '';
        foreach ($array as $attr => $values) {
            $str .= $attr . $this->getAttrValuesDelimiter() . implode($this->getValuesDelimiter(), $values) . $this->getAttrsDelimiter();
        }

        return trim($str, $this->getAttrsDelimiter());
    }
}