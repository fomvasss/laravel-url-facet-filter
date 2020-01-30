# Laravel URL Facet Filter

[![License](https://img.shields.io/packagist/l/fomvasss/laravel-url-facet-filter.svg?style=for-the-badge)](https://packagist.org/packages/fomvasss/laravel-url-facet-filter)
[![Build Status](https://img.shields.io/github/stars/fomvasss/laravel-url-facet-filter.svg?style=for-the-badge)](https://github.com/fomvasss/laravel-url-facet-filter)
[![Latest Stable Version](https://img.shields.io/packagist/v/fomvasss/laravel-url-facet-filter.svg?style=for-the-badge)](https://packagist.org/packages/fomvasss/laravel-url-facet-filter)
[![Total Downloads](https://img.shields.io/packagist/dt/fomvasss/laravel-url-facet-filter.svg?style=for-the-badge)](https://packagist.org/packages/fomvasss/laravel-url-facet-filter)
[![Quality Score](https://img.shields.io/scrutinizer/g/fomvasss/laravel-url-facet-filter.svg?style=for-the-badge)](https://scrutinizer-ci.com/g/fomvasss/laravel-url-facet-filter)

Building and displaying facet filter parameters and managing them

----------

## Installation

Run from the command line:

```bash
composer require fomvasss/laravel-url-facet-filter
```

## Publishing

```bash
php artisan vendor:publish --provider="Fomvasss\UrlFacetFilter\ServiceProvider"
```

## Usage

### Build url`s
```blade
<a href="{{ \FacetFilter::build('color', 'red') }}">Red</a>	
<a href="{{ \FacetFilter::build('color', 'green') }}">Green</a>	
<a href="{{ \FacetFilter::build('size', 'm') }}">M</a>	
<a href="{{ \FacetFilter::build('size', 'xl') }}">XL</a>	
```

### Usage in controller
`app/Http/Controllers/ProductControllr.php`

```php
<?php 

namespace App\Http\Controllers;

class ProductController extends Controller 
{
        public function index(Request $request)
        {
            $filterAttrs = \FacetFilter::toArray($request->get(\FacetFilter::getFilterUrlKey()));
            
            $products = Product::with('media')
                ->facetFilterable($filterAttrs) // facetFilterable - for example your scope
                ->get();

            return view('article.index', [
                'articles' => $products,
            ]);
        }  
}
```

Example, in url string:
```text
https://my-site.com/products?⛃=color☛red⚬green♦size☛m♦ergonomic☛form-1⚬form-2
```

In php controller (after prepare `FacetFilter::toArray()`):

```html
array:3 [▼
  "color" => array:2 [▼
    0 => "red"
    1 => "green"
  ]
  "size" => array:1 [▼
    0 => "m"
  ]
  "ergonomic" => array:2 [▼
    0 => "form-1"
    1 => "form-2"
  ]
]
```

## Links
