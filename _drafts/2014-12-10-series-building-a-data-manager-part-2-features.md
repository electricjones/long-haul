---
layout: post
title:  Building a Data Manager Part II - Features and Contracts
date:   2018-08-03 -0600
tags: [literature-reviews, progression-systems, adaptive-gaming]
excerpt: This post is part of The Future of Digital Content series, which discusses six traits I believe will be at the heart what content will look like in the coming years. These traits form a roadmap that lies at the heart of my research and experiments. The traits also work together, mixing and meshing, to paint a picture of how our future selves may read, watch, learn, and listen.
---

_This post is part of a series aimed at **beginning** PHP coders. Follow step-by-step from ground zero to build a simple [data manager](http://github.com/chrismichaels84/data-manager)._

_You can see the finished version for the first part at [chrismichaels84/data-manager/tree/tutorial-part-2](https://github.com/chrismichaels84/data-manager/tree/tutorial-part-2) or use the finished, feature-complete, supported DataManager at [chrismichaels84/data-manager](https://github.com/chrismichaels84/data-manager)_

_Setting Up - **Features and Contracts** - Dot Notation  
_

----------

[Last time, we started building a simple data manager](//phoenixlabstech.org/2015/04/17/building-a-data-manager/) for things like configuration settings (or any kind of collection, really). Our feature goals were:

-   **C**reate, **R**etrieve, **U**pdate, and **D**elete single items or complex items (array)
-   Get all items as an array
-   Clear all items
-   Confirm or deny that an item exists in its collection
-   Handle deeply nested items through dot-notation (this.one.here)
-   Throw exceptions as needed, or fail silently based on configuration
-   Allow for fallback values if get() no item

We set up a repository, defined our API, created an interface, and implemented the first feature (add and get a single item) using Test Driven Development. If you don't remember, [check it out](http://phoenixlabstech.org/2015/04/17/building-a-data-manager/)!

Now we are going to implement the rest of the basic features and begin checking things off of our api list. We can actual check some things off now!

[code language="php"]  
//$manager = new MichaelsDataManager();  
//$manager->add('name', $item);  
$manager->add(['name' => $item, 'name2' => $item2]);  
$manager->add('namespace.item', $item);  
//$manager->get('name');  
$manager->get('namespace.item');  
$manager->get('doesntexist', 'fallback');  
//$manager->getAll();  
$manager->set('name' $newValue);  
$manager->set(['name1' => $newValue1, 'name2' => $newValue2]);  
$manager->clear();  
$manager->remove('name');  
$manager->remove('namespace.name');  
$manager->has('item'); // true or false  
$manager->exists('item'); // same as above  
[/code]

## Basic C.R.U.D.

### Step Six: Add Multiple Items at Once

Let's tack line 3 of the above code. It should be simple enough to add an array of items. While we're at it, lets start adding different kinds of data, just to be on the safe side.

First, the test

[code language="php"]  
public function testAddMultipleItemsAtOnce()  
{  
$manager = new Manager();  
$manager->add([  
'objectTest' => new StdClass(),  
'closureTest' => function () {  
return true;  
},  
'stringTest' => 'value'  
]);

$items = $manager->getAll();

$this->assertArrayHasKey('objectTest', $items, 'Array Items does not have key `objectTest`');  
$this->assertArrayHasKey('closureTest', $items, 'Array Items does not have key `closureTest`');  
$this->assertArrayHasKey('stringTest', $items, 'Array Items does not have key `stringTest`');  
}  
[/code]

When we run "phpunit", we get an errors. Let's make the tests pass by fixing DataManager.php

[code language="php"]  
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
[/code]

All this does is check if we are adding at array of items, and sends it back through the same method for each item to add.

Run "phpunit" and we are golden.

### Step Seven: Confirm or deny that an item exists

Let's do this one in two tests: First check to see if it confirms the existence of an item:

[code language="php"]  
public function testReturnTrueIfItemExists()  
{  
$manager = new Manager();  
$manager->add('test', 'value');

$this->assertTrue($manager->exists('test'));  
}  
[/code]

And then confirm that an item **doesn't** exist:

[code language="php"]  
public function testReturnFalseIfItemDoesNotExist()  
{  
$manager = new Manager();

$this->assertFalse($manager->exists('test'));  
}  
[/code]

Create this method in DataManagerTest.php and run the unit test. It should fail. Yep. To make it pass, were just going to return the result of isset()

[code language="php"]  
public function exists($alias)  
{  
return (isset($this->items[$alias]));  
}  
[/code]

All green. Good job. See how easy this actually is? Since we also have a has() method, lets just point it to exists().

[code language="php"]  
public function has($alias)  
{  
return $this->exists($alias);  
}  
[/code]

### Step Eight: Allow for fallback values

If we try to get an item that doesn't exist, right now it would throw an error. I want to be able to provide a fallback value if my item doesn't exist, so I know I'll get a default value.

[code language="php"]

$manager->get('doesntexist', 'fallback');

[/code]

Like always, we write the test first so that it fails or errors:

[code language="php"]  
public function testCanProvideFallbackValue()  
{  
$manager = new Manager();  
$manager->add('one', 'one-value');

$actual = $manager->get('two', 'default-value');

$this->assertEquals('default-value', $actual, 'failed to return a fallback value');  
}  
[/code]

We get an undefined index error. That's what we expect. There is no index for 'two'. Let's make it pass using the exists() method we've already made.

[code language="php"]  
public function get($alias, $fallback)  
{  
if (!$this->exists($alias) && $fallback) {  
return $fallback;  
}

return $this->items[$alias];  
}  
[/code]

Well that was interesting. We made that test pass, but we broke another test. in testGetSingleItem() we use $manager->get() and don't provide a fallback. But $fallback is required. This is why we write automated tests. It may have taken me months to come across that bug.

Let's fix it so all our tests pass. We want fallback to be optional, so we'll set it to null by default.

[code language="php"]  
public function get($alias, $fallback = null)  
{  
if (!$this->exists($alias) && !is_null($fallback)) {  
return $fallback;  
}

return $this->items[$alias];  
}  
[/code]

Bam! Green! Let's move on.

If we do not have a fallback value, we should throw an exception.

Test:

[code language="php"]  
/**  
* @expectedException MichaelsManagerItemNotFoundException  
*/  
public function testThrowsExceptionIfItemNotFound()  
{  
$manager = new Manager();  
$manager->get('doesntexist');  
}  
[/code]

Updated get() method:

[code language="php"]  
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
[/code]

And of course, we have to create ItemNotFoundException.php inside of src.

[code language="php"]  
namespace MichaelsManager;

class ItemNotFoundException extends Exception  
{

}  
[/code]

### Step Nine: Updating a Value

We have the C and R from CRUD. Let's take on the U - Update. What we want is

[code language="php"]

$manager->set('name' $newValue);

$manager->set(['name1' => $newValue1, 'name2' => $newValue2]);

[/code]

So, let's write our tests.

[code language="php"]  
public function testUpdateSingleItem()  
{  
$manager = new Manager();  
$manager->add('item', 'original-value');  
$manager->set('item', 'new-value');

$this->assertEquals('new-value', $manager->get('item'), 'failed to update a single item');  
}

public function testUpdateMultipleItems()  
{  
$manager = new Manager();  
$manager->add('item', 'original-value');  
$manager->add('item2', 'other-original-value');  
$manager->set(['item' => 'new-value', 'item2' => 'other-new-value']);

$this->assertEquals('new-value', $manager->get('item'), 'failed to update first item');  
$this->assertEquals('other-new-value', $manager->get('item2'), 'failed to update second item');  
}  
[/code]

From previous experience, we know that set() requires **2** arguments, but in the second test we only provide one. So, we will get an error. We will fix this the same way as before.

Actually, this is all identical to what we've done before, right? We're just going to create or update an index in an array. Why don't we cheat and just point to the add() method? It can't hurt to try. We don't want to copy and past the code because it may change in the future and we don't want to change it twice.

[code language="php"]  
public function set($alias, $item = null)  
{  
return $this->add($alias, $item);  
}  
[/code]

Everything is GREEN. Kudos to us for saving ourselves some work.

### Step Ten: Remove An Item

We don't want to use the word delete, because it can be reserved and cause weird bugs. So we want to remove individual items and we want to clear the entire manager. Let's remove a single item first. We know our test will fail before we create the method, so I'm going to do both at the same time for brevity.

Test:

[code language="php"]  
public function testRemoveItem()  
{  
$manager = new Manager();  
$manager->add([  
'one' => 'one',  
'two' => 'two'  
]);

$manager->remove('one');

$items = $manager->getAll();

$this->assertArrayNotHasKey('one', $items, 'failed to remove `one`');  
$this->assertArrayHasKey('two', $items, 'failed to leave `two` intact');  
}  
[/code]

Method in DataManager. When doing this, we want to

1.  Check if the item exists at all
2.  Remove it if it does

[code language="php"]  
public function remove($alias)  
{  
if ($this->exists($alias)) {  
unset($this->items[$alias]);  
}  
}  
[/code]

Let's look at this code for a second. We use the already created exists() method. If it does exist, then we pop the item out of the array and delete it. When it comes time to return, I used a short syntax for the if statement that says "if $removed exists) then return it, otherwise, return false". Remove won't exist unless we removed something.

### Step Eleven: Clear the Entire Manager

As a last helper, it would be nice to totally empty the manager. This should be easy.

Test:

[code language="php"]  
public function testClear()  
{  
$manager = new Manager();  
$manager->add([  
'one' => 'one',  
'two' => 'two'  
]);

$manager->clear();

$items = $manager->getAll();

$this->assertEmpty($items, "Failed to empty manager");  
}  
[/code]

Code:

[code language="php"]  
public function clear()  
{  
$this->items = [];  
return $this;  
}  
[/code]

## Checking Our Progress

What can we safely check off now?

[code language="php"]  
//$manager = new MichaelsDataManager();  
//$manager->add('name', $item);  
//$manager->add(['name' => $item, 'name2' => $item2]);  
$manager->add('namespace.item', $item);  
//$manager->get('name');  
$manager->get('namespace.item');  
//$manager->get('doesntexist', 'fallback');  
//$manager->getAll();  
//$manager->set('name' $newValue);  
//$manager->set(['name1' => $newValue1, 'name2' => $newValue2]);  
// $manager->clear();  
//$manager->remove('name');  
$manager->remove('namespace.name');  
//$manager->has('item'); // true or false  
//$manager->exists('item'); // same as above  
[/code]

Wow, that's everything except the namespaced dot-notation which we will tackle next time.

### Step Twelve: Abiding By a Contract

So, for good measure, let's implement an interface. This is important because it allows others to create interchangeable classes. I will discuss this more later, but it' a really good habit. We'll have to modify what we created earlier. If your editor won't extract an interface, the easiest thing to do is copy and past the class into the new file and then remove all the code. Leave your docblocks!

And that is all for now. All that is left is to implement the namespacing feature and handle missing items, and then publish out work to the world! See you later.
