---
layout: post
title:  Building a Data Manager Part III - Dot Notation
date:   2018-08-03 -0600
image: datablog_stall.jpg
tags: [php, library, tutorial]
excerpt: Creating the features of a Data Manager, implementing interfaces, and more TDD.
---

_This post is part of a series aimed at **beginning** PHP coders. Follow step-by-step from ground zero to build a simple [data manager](http://github.com/electricjones/data-manager)._

_You can see the finished version for the first part at [electricjones/data-manager/tree/tutorial-part-3](https://github.com/electricjones/data-manager/tree/tutorial-part-3) or use the finished, feature-complete, supported DataManager at [electricjones/data-manager](https://github.com/electricjones/data-manager)_

- [Setting Up]({% post_url 2015-04-17-series-building-a-data-manager-part-1-setting-up %}) 
- [Features and Contracts](% post_url 2015-04-23-series-building-a-data-manager-part-2-features %})
- **Dot Notation**

In the previous posts, we build a data manager from scratch using Test Driven Development. There are two features we have left to implement: deeply nested values via dot notation and throwing exceptions as needed. You can review the source code and lessons for the earlier bits, but this tutorial should stand on its own fairly well.

Dot notation is a common convention for getting at values that are deeply rooted in multidimensional arrays. The best way to show this is to show it.

```php
$array = [
    'michael' => [
        'family' => [
            'sisters' => [
                'oldest' => 'Alicia',
                'youngest' => 'Erika'
            ]
        ],
        'favorites' => [
            'color' => 'purple',
            'foods' => ['tacos', 'pasta', 'all the bad for you stuff']
        ]
    ],
    'other' => 'value'
];

// array.michael.family.sisters.oldest returns Alicia
// array.michael.family returns sisters array
// array.michael.favorites.foods returns my favorite foods
// array.other returns value
```

With dot notation, we can easily dig deep into our Manager object. We want to be able to Create, Retrieve, Update, and Delete using dot notation. So, let's get cracking.

# Step Thirteen: Creating Our Tests

In this case, I want to create all of our tests at once so we know exactly what we're getting into. I am sure we will create some private helper methods that we will reuse and it's always good to have an overview of what will happen.

According to our API checklist, this is what's left:

```php
$manager->add('namespace.item', $item);
$manager->exists('namespace.name'); // also has()
$manager->get('namespace.item', $fallback);
$manager->remove('namespace.name');
```

And we'll do it in that order. As we learned in the last post, set() is the same as add() and has() is the same as exists(). Looks like four tests to me.

We also have to be careful of false positives. If we add an item "one.two.three" it will create an item with that name without actually nesting arrays, so most of the tests will pass. To get by this, we will verify the entire contents of the manager in every test.

```php
public function testAddNestedItems()
{
    $manager = new Manager();
    $manager->add('one.two.three', 'three-value');
    $manager->add('one.two.four.five', 'five-value');
    $manager->add('one.six', ['seven' => 'seven-value']);
    $manager->add('one.six.eight', 'eight-value');
    $manager->add('top', 'top-value');

    $expected = [
        'one' => [
            'two' => [
                'three' => 'three-value',
                'four' => [
                    'five' => 'five-value'
                ],
            ],
            'six' => [
                'seven' => 'seven-value',
                'eight' => 'eight-value'
                ]
        ],
        'top' => 'top-value',
    ];

    $actual = $manager->getAll();

    $this->assertEquals($expected, $actual, 'failed to add nested items');
}

public function testCheckExistenceOfNestedItems()
{
    $manager = new Manager();
    $manager->add('one.two.three', 'three-value');

    // Always match against full contents
    $expected = ['one' => ['two' => ['three' => 'three-value']]];
    $actual = $manager->getAll();

    $this->assertEquals($expected, $actual, 'failed to add nested items');

    $this->assertTrue($manager->exists('one.two.three'), 'failed to confirm existence of a nested item');
    $this->assertFalse($manager->exists('one.two.no'), 'failed to deny existence of a nested item');
}

public function testGetNestedItems()
{
    $manager = new Manager();
    $manager->add('one.two.three', 'three-value');

    // Always match against full contents
    $expected = ['one' => ['two' => ['three' => 'three-value']]];
    $actual = $manager->getAll();

    $this->assertEquals($expected, $actual, 'failed to add nested items');
    $this->assertEquals('three-value', $manager->get('one.two.three'), 'failed to get a single item');
}

public function testRemoveNestedItems()
{
    $manager = new Manager();
    $manager->add('one.two.three', 'three-value');
    $manager->add('one.two.four', 'four-value');

    // Always match against full contents
    $expected = ['one' => ['two' => ['three' => 'three-value', 'four' => 'four-value']]];
    $actual = $manager->getAll();
    $this->assertEquals($expected, $actual, 'failed to add nested items');

    $manager->remove('one.two.three');
    $manager->remove('does.not.exist');

    $this->assertTrue($manager->exists('one.two.four'), 'failed to leave nested item in tact');
    $this->assertFalse($manager->exists('one.two.three'), 'failed to remove nested item');
}
```

Great! All our tests fail or error. (Wow, that sounds weird).

Let's Talk About Nested Arrays

Before we dive into modifying nested arrays, lets look at how we use loops to navigate nested arrays. When given a string like "this.is.my.dot.notation" what we are really wanting to do is loop through a given array 5 times, changing our location in the array by one step. So, on the first loop we go to `$array['this']` and then to $array`['this']['is']` and so on.

The best way to achieve this is to use php references from variables. What this does is allow two variables to point to the exact same bit of data. You modify one, you modify the other. For instance,

```php
$loc = &$this->items['this']['is']['my'];
```

does not set $loc to the value of that array (['dot' => ['notation']]). Instead, $loc now refers to or points to or aliases that array.

Knowing that, we can navigate through our deeply nested items using a foreach in this basic way:

```php
$alias = "one.two.three";

$loc = &$this->items;

foreach (explode('.', $alias) as $step) {
    if (isset($loc[$step])) {
        $loc = &$loc[$step];
    }
}
```

We are going to modify this basic control structure (and use a while loop variant) to do all of our nesting. Note that even if an alias does not have a dot (and therefore refers to the top level), the loop will still execute once. That means we don't need to check if an alias is nested. We can just treat all aliases as nested and the method will return the correct value.

# Step Fourteen: Adding Nested Items

Let's get our feet wet. Our add() method now is

```php
public function add($alias, $item = null)
{
    // Are we adding multiple items?
    if (is_array($alias)) {

        foreach ($alias as $key => $value) {
            $this->add($key, $value);
        }

        return $this;
    }

    // No, we are adding a single item
    $this->items[$alias] = $item;

    return $this;
}
```

We want to leave adding multiple items the same. If it ain't broke, don't fix it. We'll use our foreach loop to replace `$this->items[$alias] = $item`

```php
// No, we are adding a single item
$loc = &$this->items;

foreach (explode('.', $alias) as $step) {
    $loc = &$loc[$step];
}

$loc = $item; // remember that $loc now refers to the right place in the nested items array
```

First, we set a temporary location variable to refer to our items array. Second, we loop through that items array "x" times, depending on how deep the alias is nested. At each loop, we change the $loc variable to refer to the new level. Finally, we simply set final location (which refers to the items array) to the correct value. Viola!

For now, comment out the other three tests, run phpunit, and watch everything pass.

# Step Fifteen: Check the Existence of Nested Items

Since we are going to want to check the existence of things before we get or remove them (in order to avoid "index not found" errors), let's whip this one out first.

Our current exists() method is pretty simple:

```php
public function exists($alias)
{
    return (isset($this->items[$alias]));
}
```

We want to cycle through the array in the same way we did for add(). It's good practice to return early, so we can return false if at any time we run into a nonexistent item. We return true at the end.

```php
public function exists($alias)
{
    $loc = &$this->items;
    foreach (explode('.', $alias) as $step) {
        if (!isset($loc[$step])) {
            return false; // returning early so the loop ends
        } else {
            $loc = &$loc[$step];
        }
    }
    return true;
}
```

This passes our second test, and breaks no other tests. Well done.

# Step Sixteen: Getting a Nested Item

Right now we're looking at

```php
public function get($alias, $fallback = null)
{
    $exists = $this->exists($alias);

    if (!$exists && !is_null($fallback)) {
        return $fallback;

    } elseif (!$exists) {
        throw new ItemNotFoundException();
    }

    return $this->items[$alias];
}
```

Let's break this down logically. We have three ways any attempted get() can go

The item does exist. Return the value
The item does not exist, but we have a fallback. Return the fallback
The item does not exist, and there is no fallback. Throw an error.

This is the perfect time for an if, elseif, else statement. When it comes time to return the value if it exists, we will use the same foreach structure as we did to add.

```php
// Check for existence
$exists = $this->exists($alias);

// The item does exist, return the value
if ($exists) {
    $loc = &$this->items;
    foreach (explode('.', $alias) as $step) {
        $loc = &$loc[$step];
    }

    return $loc;

// The item does not exist, but we have a fallback
} elseif ($fallback !== null) {
    return $fallback;

// The item does not exist, and there is no fallback
} else {
    throw new ItemNotFoundException();
}
```

# Step Seventeen: Remove a Nested Value

That brings us to the last of our CRUD for nested values. I'll be honest. This one stumped me for a moment. I tried a simple foreach, found that foreach was going through ALL the items in the . I googled it and found Laravel's Array Helpers which led me to this solution:

```php
public function remove($alias)
{
    $loc = &$this->items;
    $parts = explode('.', $alias);

    while (count($parts) > 1) {
        $step = array_shift($parts);

        if (isset($loc[$step]) && is_array($loc[$step])) {
            $loc =& $loc[$step];
        }
    }

    unset($loc[array_shift($parts)]);
}
```

Here, we iterate over the alias array, but don't hit the very last member.

Now all of our tests pass. We can refactor with joy because anything we change that will break something causes phpunit to throw a fit.

### A Little Polish

There are just two more things I would like to do. First, add a constructor so you can populate the manager at instantiation.

First a test.

```php
public function testPopulateAtInstantiation()
{
    $expected = ['one' => ['two' => ['three' => 'three-value']]];
    $manager = new Manager($expected);

    $this->assertEquals($expected, $manager->getAll(), 'failed to populate array at construction');
}
```

And to make it pass

```php
public function __construct(array $items = [])
{
    $this->items = $items;
}
```

All done!

And that, my friends, is that. We have a fully featured Manager, well tested, and ready for the world. Add in some docblocks and refactor to you hearts content. See the full version at http://github.com/electricjones/data-manager. There are different branches. The "master" is the up-to-date version with new features. Tutorial branches are married to these tutorials.

Cheers!