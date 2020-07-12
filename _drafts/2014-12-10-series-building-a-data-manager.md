---
layout: post
title:  Series - Building a Data Manager
date:   2018-08-03 -0600
tags: [literature-reviews, progression-systems, adaptive-gaming]
excerpt: This post is part of The Future of Digital Content series, which discusses six traits I believe will be at the heart what content will look like in the coming years. These traits form a roadmap that lies at the heart of my research and experiments. The traits also work together, mixing and meshing, to paint a picture of how our future selves may read, watch, learn, and listen.
---

I decided to extract some core classes I've used in several of my projects into their own package, a [data-manager](https://github.com/chrismichaels84/data-manager). DataManager is a container that does exactly what it says: manages item data for things like configuration settings. It also handles dot notation and exceptions. It should:

1.  Be stupid simple and lean -- no extra features or code
2.  **C**reate, **R**etrieve, **U**pdate, **D**elete and confirm/deny single items or complex items (array)
3.  Allow for fallback values if get() no item
4.  Handle deeply nested items through dot-notation (this.one.here)
5.  Be super extendable super easily

I worked for a few hours and cranked out exactly what I needed using Test Driven Development. You can [use the Manager](https://github.com/chrismichaels84/data-manager) freely from github or composer. But, I wanted to share my process. This series will lead you through, step-by-step, the entire creation workflow for a php composer package using Test Driven Development. This is great for beginners who want to see TDD in practice.

_You can see the finished version of this tutorial at [chrismichaels84/data-manager/tree/tutorial-part-3](https://github.com/chrismichaels84/data-manager/tree/tutorial-part-3) or use the finished, feature-complete, supported DataManager at [chrismichaels84/data-manager](https://github.com/chrismichaels84/data-manager)._

### Series Contents

1.  [Setting Up](http://chrismichaelsauthor.com/code/building-a-data-manager-part-i/) - Define our goals and get our skeleton in place
2.  [Features and Contracts](http://chrismichaelsauthor.com/code/building-a-data-manager-part-ii/) - Define our API and get some core functionality
3.  [Dot Notation](http://chrismichaelsauthor.com/blog/building-a-data-manager-part-iii/) - Dig deep into array structures (one.two.three)
