# Making working with Fractal a breeze

[![Latest Version on Packagist](https://img.shields.io/packagist/v/spatie/fractalistic.svg?style=flat-square)](https://packagist.org/packages/spatie/fractalistic)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://travis-ci.org/spatie/fractalistic.svg?branch=master)](https://travis-ci.org/spatie/fractalistic)
[![SensioLabsInsight](https://img.shields.io/sensiolabs/i/2551eaa3-34df-49e0-a170-709b96f2ac3e.svg?style=flat-square)](https://insight.sensiolabs.com/projects/2551eaa3-34df-49e0-a170-709b96f2ac3e)
[![Quality Score](https://img.shields.io/scrutinizer/g/spatie/fractalistic.svg?style=flat-square)](https://scrutinizer-ci.com/g/spatie/fractalistic)
[![StyleCI](https://styleci.io/repos/76027929/shield?branch=master)](https://styleci.io/repos/76027929)
[![Total Downloads](https://img.shields.io/packagist/dt/spatie/fractalistic.svg?style=flat-square)](https://packagist.org/packages/spatie/fractalistic)

[Fractal](http://fractal.thephpleague.com/) is an amazing package to transform data before using it in an API. Unfortunately working with Fractal can be a bit verbose.

Using Fractal data can be transformed like this:

```php
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;

$books = [
   ['id'=>1, 'title'=>'Hogfather', 'characters' => [...]], 
   ['id'=>2, 'title'=>'Game Of Kill Everyone', 'characters' => [...]]
];

$manager = new Manager();

$resource = new Collection($books, new BookTransformer());

$manager->parseIncludes('characters');

$manager->createData($resource)->toArray();
```

This package makes that process a tad easier:

```php
fractal()
   ->collection($books)
   ->transformWith(new BookTransformer())
   ->includeCharacters()
   ->toArray();
```

There's also a very short syntax available to quickly transform data:

```php
fractal($books, new BookTransformer())->toArray();
```

Spatie is a webdesign agency based in Antwerp, Belgium. You'll find an overview of all 
our open source projects [on our website](https://spatie.be/opensource).

## Postcardware

You're free to use this package (it's [MIT-licensed](LICENSE.md)), but if it makes it to your production environment you are required to send us a postcard from your hometown, mentioning which of our package(s) you are using.

Our address is: Spatie, Samberstraat 69D, 2060 Antwerp, Belgium.

The best postcards will get published on the open source page on our website.

## Install

You can pull in the package via composer:
``` bash
$ composer require spatie/fractalistic
```

## Usage

In the following examples were going to use the following array as example input:

```php
$books = [['id'=>1, 'title'=>'Hogfather'], ['id'=>2, 'title'=>'Game Of Kill Everyone']];
```

But know that any structure that can be looped (for instance a collection) can be used.

Let's start with a simple transformation.

```php
fractal()
   ->collection($books)
   ->transformWith(function($book) { return ['id' => $book['id']];})
   ->toArray();
``` 

This will return:
```php
['data' => [['id' => 1], ['id' => 2]]
```

Instead of using a closure you can also pass [a Transformer](http://fractal.thephpleague.com/transformers/):

```php
fractal()
   ->collection($books)
   ->transformWith(new BookTransformer())
   ->toArray();
```

To make your code a bit shorter you could also pass the transform closure or class as a 
second parameter of the `collection`-method:

```php
fractal()->collection($books, new BookTransformer())->toArray();
```

Want to get some sweet json output instead of an array? No problem!
```php
fractal()->collection($books, new BookTransformer())->toJson();
```

A single item can also be transformed:
```php
fractal()->item($books[0], new BookTransformer())->toArray();
```

## Using a serializer

Let's take a look again a the output of the first example:

```php
['data' => [['id' => 1], ['id' => 2]];
```

Notice that `data`-key? That's part of Fractal's default behaviour. Take a look at
[Fractals's documentation on serializers](http://fractal.thephpleague.com/serializers/) to find out why that happens.

If you want to use another serializer you can specify one with the `serializeWith`-method.
The `Spatie\Fractalistic\ArraySerializer` comes out of the box. It removes the `data` namespace for
both collections and items.

```php
fractal()
   ->collection($books)
   ->transformWith(function($book) { return ['id' => $book['id']];})
   ->serializeWith(new \Spatie\Fractalistic\ArraySerializer())
   ->toArray();

//returns [['id' => 1], ['id' => 2]]
```

### Changing the default serializer

You can change the default serializer by providing the classname or an instantiation of your favorite serializer in
the config file.

## Using includes

Fractal provides support for [optionally including data](http://fractal.thephpleague.com/transformers/) on the relationships for
the data you're exporting. You can use Fractal's `parseIncludes` which accepts a string or an array:

```php
fractal()
   ->collection($this->testBooks, new TestTransformer())
   ->parseIncludes(['characters', 'publisher'])
   ->toArray();
```

To improve readablity you can also use a function named `include` followed by the name
of the include you want to... include:

```php
fractal()
   ->collection($this->testBooks, new TestTransformer())
   ->includeCharacters()
   ->includePublisher()
   ->toArray();
```

## Using excludes

Similar to includes Fractal also provides support for [optionally excluding data](http://fractal.thephpleague.com/transformers/) on the relationships for
the data you're exporting. You can use Fractal's `parseExcludes` which accepts a string or an array:

```php
fractal()
   ->collection($this->testBooks, new TestTransformer())
   ->parseExcludes(['characters', 'publisher'])
   ->toArray();
```

To improve readability you can also use a function named `exclude` followed by the name
of the include you want to... exclude:

```php
fractal()
   ->collection($this->testBooks, new TestTransformer())
   ->excludeCharacters()
   ->excludePublisher()
   ->toArray();
```

## Including meta data

Fractal has support for including meta data. You can use `addMeta` which accepts 
one or more arrays:

```php
fractal()
   ->collection($this->testBooks, function($book) { return ['name' => $book['name']];})
   ->addMeta(['key1' => 'value1'], ['key2' => 'value2'])
   ->toArray();
```

This will return the following array:

```php
[
   'data' => [
        ['title' => 'Hogfather'],
        ['title' => 'Game Of Kill Everyone'],
    ],
   'meta' => [
        ['key1' => 'value1'], 
        ['key2' => 'value2'],
    ]
];
```

## Using pagination

Fractal provides a Laravel-specific paginator, `IlluminatePaginatorAdapter`, which accepts an instance of Laravel's `LengthAwarePaginator`
and works with paginated Eloquent results. When using some serializers, such as the `JsonApiSerializer`, pagination data can be
automatically generated and included in the result set:

```php
$paginator = Book::paginate(5);
$books = $paginator->getCollection();

fractal()
    ->collection($books, new TestTransformer())
    ->serializeWith(new JsonApiSerializer())
    ->paginateWith(new IlluminatePaginatorAdapter($paginator))
    ->toArray();
```

## Using a cursor

Fractal provides a simple cursor class, `League\Fractal\Pagination\Cursor`. You can use any other cursor class as long as it implements the `League\Fractal\Pagination\CursorInterface` interface. When using it, the cursor information will be automatically included in the result metadata:

```php
$books = $paginator->getCollection();

$currentCursor = 0;
$previousCursor = null;
$count = count($books);
$newCursor = $currentCursor + $count;

fractal()
  ->collection($books, new TestTransformer())
  ->serializeWith(new JsonApiSerializer())
  ->withCursor(new Cursor($currentCursor, $previousCursor, $newCursor, $count))
  ->toArray();
```

## Setting a custom resource name

Certain serializers wrap the array output with a `data` element. The name of this element can be customized:

```php
fractal()
    ->collection($this->testBooks, new TestTransformer())
    ->serializeWith(new ArraySerializer())
    ->withResourceName('books')
    ->toArray();
```

```php
fractal()
    ->item($this->testBooks[0], new TestTransformer(), 'book')
    ->serializeWith(new ArraySerializer())
    ->toArray();
```

## Quickly transform data with the short function syntax

You can also pass arguments to the `fractal`-function itself. The first arguments should be the data you which to transform. The second one should be a transformer or a `closure` that will be used to transform the data. The third one should be a serializer.

Here are some examples

```php
fractal($books, new BookTransformer())->toArray();

fractal($books, new BookTransformer(), new ArraySerializer())->toArray();

fractal(['item1', 'item2'], function ($item) {
   return $item . '-transformed';
})->toArray();
```

## Quickly creating a transformer

You can run the `make:transformer` command to quickly generate a dummy transformer. By default it will be stored in the `app\Transformers` directory.

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email freek@spatie.be instead of using the issue tracker.

## Credits

- [Freek Van der Herten](https://twitter.com/freekmurze)
- [All contributors](../../contributors)

## About Spatie
Spatie is a webdesign agency based in Antwerp, Belgium. You'll find an overview of all our open source projects [on our website](https://spatie.be/opensource).

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
