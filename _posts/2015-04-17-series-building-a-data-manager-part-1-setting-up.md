---
layout: post
title:  "Building a Data Manager Part I - Setting Up"
date:   2015-04-09 16:16:01 -0600
image: datablog_stall.jpg
tags: [php, library, tutorial, tdd]
excerpt: We begin data manager with some planning and get into Test Driven Development
---

_This post is part of a series aimed at **beginning** PHP coders. Follow step-by-step from ground zero to build a simple [data manager](http://github.com/electricjones/data-manager)._

_You can see the finished version for the first part at [electricjones/data-manager/tree/tutorial-part-1](https://github.com/electricjones/data-manager/tree/tutorial-part-1) or use the finished, feature-complete, supported DataManager at [electricjones/data-manager](https://github.com/electricjones/data-manager)_

- **Setting Up** 
- [Features and Contracts]({% post_url 2015-04-23-series-building-a-data-manager-part-2-features %})
- [Dot Notation]({% post_url 2015-04-30-series-building-a-data-manager-part-3-dot-notation %})

## Goals
I decided to extract some core classes I've used in several of my projects into their own package, a [data-manager](https://github.com/electricjones/data-manager). DataManager is a container that does exactly what it says: manages item data for things like configuration settings. It also handles dot notation and exceptions. It should:

1.  Be stupid simple and lean -- no extra features or code
2.  **C**reate, **R**etrieve, **U**pdate, **D**elete and confirm/deny single items or complex items (array)
3.  Allow for fallback values if get() no item
4.  Handle deeply nested items through dot-notation (this.one.here)
5.  Be super extendable super easily

## Getting Started

### Step One: Create a new Repository
I like to start directly from [Github](http://github.com). Login and create a new repository. I named my data-manager ([electricjones/data-manager](http://github.com/electricjones/data-manager)), gave it an MIT License, and a Composer .gitignore. Though it doesn't matter, We're about to override all this.

Next, clone your repository locally so you can edit it more easily. I use [PHP Storm](https://www.jetbrains.com/phpstorm/), but Github's app and [Sublime Text](http://www.sublimetext.com/) works just as well. I'm going to assume you know how to do this. If not [Github's bootcamp](https://help.github.com/categories/bootcamp/) is the perfect place to start.

### Step Two: Create the Skeleton
I'm a big fan of being lazy. If someone else has done it, I'm gonna steal it if I can. In this case, the [League of Extraordinary Packages](http://thephpleague.com/) has done it right and _wants_ us to steal it. The League is a collective of php coders who share their work. Some really good work that is held to the highest standards. They have a [skeleton repo](https://github.com/thephpleague/skeleton) that gets all the boilerplate for a kick-ass composer package. It's also a good idea to take a look at the [PHP Package Checklist](http://phppackagechecklist.com/).

Clone the skeleton repo (don't just download the zip file). Now, you can copy everything in the skeleton to your manager project. Overwrite anything that's already there.

With a little tweaking, customize this skeleton:

1.  I don't use scrutinzer, so I delete that file
2.  Work through the .MD files to change names, authors, and such. You can leave the badges at the top of the README.md file alone for now.
3.  Update composer.json to your awesome project.

## Test Driven Development

_Skip this if you're familiar with the TDD workflow._

It is important to do things right the first time so you don't have to do them right the second time. That's what test driven development is all about. For the beginner (and even the advanced user), I know that phrase can sound scary, like your adding a ton of work. Plus terms like _mock, stub, dummy,_ and _acceptance_ are defined a million different ways -- never consistently. I feel your pain.

TDD is simply writing automated tests for every feature of your package so that as you change things in the future, you can make sure you didn't break something along the way. We all test, even if you just pull it up in the browser and put it through its paces. The problem with that is, you _will_ miss something and a bug will creep in. These automated tests will tell you exactly what has gone wrong every step of the way. And, once you write a good test, you shouldn't have to rewrite or rexecute it unless you make sweeping changes to the package.

You also ensure that you don't write any extra code. You only test what you need and only code to pass the test. The basic process is:

1.  **Describe** the feature
2.  Make it **Red:** Write a test for that feature. This test will fail because you haven't actually coded yet.
3.  Make it **Green**: Write the least amount of code possible to make that test pass.
4.  **Refactor** with complete safety. The test will fail if you break something.
5.  **Repeat** for each feature.

You'll see what I mean as we plow through. Check out [Laracasts](https://laracasts.com/collections/testing-in-php) or [this article](http://code.tutsplus.com/tutorials/test-driven-development-in-php-first-steps--net-25796) for great getting started lessons.

## Make a Plan
I start every project by creating a PROPOSAL.md file where I figure out exactly what it is I want to do. My goals and what features I want. In this case:

-   **C**reate, **R**etrieve, **U**pdate, and **D**elete single items or complex items (array)
-   Get all items as raw array
-   Clear all items
-   Confirm or deny that an item exists in its collection
-   Handle deeply nested items through dot-notation (this.one.here)
-   Allow for fallback values if get() no item

### Step Three: Work Out Your Basic API
I also like to sketch out a basic API for the class (usually in the proposal):

```php
$manager = new MichaelsDataManager();  
$manager->add('name', $item);  
$manager->add(['name' => $item, 'name2' => $item2]);  
$manager->add('namespace.item', $item);  
$manager->get('name');  
$manager->get('namespace.item');  
$manager->get('doesntexist', 'fallback');  
$manager->getAll();  
$manager->set('name', $newValue);  
$manager->set(['name1' => $newValue1, 'name2' => $newValue2]);  
$manager->clear();  
$manager->remove('name');  
$manager->remove('namespace.name');  
$manager->has('item'); // true or false  
$manager->exists('item'); // same as above  
```

## Give Me Some Code, Already!
I hear you. We are finally ready to start coding. It may seem like a lot, but all this preliminary stuff is important, will make our lives so much easier, and will get faster with practice.

### Step Four: Write and Fail Your First Test
Inside `/tests` create `DataManagerTest.php`

```php
namespace MichaelsManagerTest;

class DataManagerTest extends \PHPUnit_Framework_TestCase  
{  
    public function testMyFirstFeature()  
    {  
        $this->assertTrue(true);  
    }  
}  
```

Be sure to change the namespace!

Before we can run this, we need to make sure we have PHPUnit installed. It couldn't be easier. Open up composer.json. It should already be added under "require". If it is, then run "composer update" from the project directory in your terminal.

Once PHPUnit is installed, we can run "phpunit" from the terminal.

All is great! Except we don't want it to be. Remember we actually want to fail our first test. It passes because we aren't actually testing anything. Let's change that.

```php
namespace MichaelsManagerTest;  
use MichaelsManagerDataManager as Manager;

class DataManagerTest extends PHPUnit_Framework_TestCase  
{  
    public function testAddSingleItem()  
    {  
        $manager = new Manager();  
        $manager->add('alias', 'value');

        $this->assertArrayHasKey('alias', $manager->getAll(), 'Array Items does not have key `alias`');  
        $this->assertEquals('value', $manager->get('alias'), 'Failed to get a single item');  
    }  
}  
```

Here, we are testing a few things. First, we create a new Manager instance. Then, we try to add a string called "alias" with a value "value". We test to see if that has worked by getting all the values from the Manager and making sure "alias" is one of them and then trying to get "alias" by itself and ensuring that the value is correct.

We are actually test **3** of our API methods: "add(), getAll(), and get()".

Give it a whirl! Run phpunit". What? Fatal error? Of course, we haven't actually created the DataManager class.

Create "DataManager.php" inside "/src" with our three testable methods that do nothing right now. Don't implement any interfaces quite yet.

```php
namespace MichaelsManager;

/**  
* Manages Basic Items  
*/  
class DataManager  
{  
    public function add($alias, $item)  
    {  
        return $this;  
    }

    public function get($alias)  
    {  
        return false;  
    }

    public function getAll()  
    {  
        return [];  
    }  
}  
```

Now when we run this test, it will not give us any errors, but it will fail. That's actually what we want!

### Step Five: Pass Your First Test
Alright, let's make DataManager do something. Remember, we are trying to do the least amount possible to make this one test pass.

1.  Start by creating a protected property called $items and set it to an array.
2.  Inside add(), simply append the desired item to $this->items
3.  Inside get(), return $this->items[$alias];
4.  Inside getAll() return $this->items;

So, your DataManager class looks like:

```php
namespace MichaelsManager;

/**  
* Manages Basic Items  
*/  
class DataManager  
{  
    protected $items = [];

    public function add($alias, $item)  
    {  
        $this->items[$alias] = $item;  
        return $this;  
    }

    public function get($alias)  
    {  
        return $this->items[$alias];  
    }

    public function getAll()  
    {  
        return $this->items;  
    }  
}  
```

And viola! When we run "phpunit" our tests are green. We have successfully managed some data. Don't forget to DocBlock your methods.

From here on, we are just going to repeat this project until all our goals are complete and our API is functional including dot notation. Hope to see you next time!
