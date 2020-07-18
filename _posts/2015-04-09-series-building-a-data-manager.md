---
layout: post
title:  Series - Building a Data Manager
date:   2015-04-09
image: datablog_stall.jpg
tags: [php, library, tutorial]
excerpt: An introduction to TDD and Library Building in PHP by creating a full data manager with array access, dot notation, and helpers.
---

I decided to extract some core classes I've used in several of my projects into their own package, a [data-manager](https://github.com/electricjones/data-manager). DataManager is a container that does exactly what it says: manages item data for things like configuration settings. It also handles dot notation and exceptions. It should:

1.  Be stupid simple and lean -- no extra features or code
2.  **C**reate, **R**etrieve, **U**pdate, **D**elete and confirm/deny single items or complex items (array)
3.  Allow for fallback values if get() no item
4.  Handle deeply nested items through dot-notation (this.one.here)
5.  Be super extendable super easily

I worked for a few hours and cranked out exactly what I needed using Test Driven Development. You can [use the Manager](https://github.com/electricjones/data-manager) freely from github or composer. But, I wanted to share my process. This series will lead you through, step-by-step, the entire creation workflow for a php composer package using Test Driven Development. This is great for beginners who want to see TDD in practice.

_You can see the finished version of this tutorial at [electricjones/data-manager/tree/tutorial-part-3](https://github.com/electricjones/data-manager/tree/tutorial-part-3) or use the finished, feature-complete, supported DataManager at [electricjones/data-manager](https://github.com/electricjones/data-manager)._


### Series Contents
<!-- 1.  [Setting Up]({% post_url 2015-04-17-series-building-a-data-manager-part-1-setting-up %}) - Define our goals and get our skeleton in place
2.  [Features and Contracts]({% post_url 2015-04-23-series-building-a-data-manager-part-2-features %}) - Define our API and get some core functionality
3.  [Dot Notation]({% post_url 2015-04-30-series-building-a-data-manager-part-3-dot-notation %}) - Dig deep into array structures (one.two.three) -->
