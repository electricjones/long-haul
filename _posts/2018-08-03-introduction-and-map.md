---
layout: post
title:  Introduction and Map
date:   2018-08-03 -0600
tags: [start-here, guides, constantly-updated]
excerpt: What to expect and how to navigate this blog.
---


- More than 150 million Americans play video games, and 64 percent of American households are home to at least one person who plays video games regularly, or at least three hours per week.
- 60 percent of Americans play video games daily.
- The average gamer is 34 years old and 72 percent are age 18 or older. Women age 18 and older represent a significantly greater portion of the video game-playing population (33 percent) than boys under age 18 (17 percent).
- Most parents (70 percent) say video games are a positive part of their child’s life. Most parents (67 percent) also play video games with their child at least once weekly and 94 percent say they pay attention to the video games played by their child.
*[Entertainment Software Association](http://www.theesa.com/about-esa/industry-facts/)*

And those are just videogames. Gaming is part of every culture, and a definitive, powerful part of the human experience.


When I decided that Game Sciences was going to be my research career, I searched high and low for the best way to move forward.
Obviously, the first step was to finish a Ph.D. in Game Studies, as my endgoal was to be a research professor. 
I figured finding those programs would be the easy part.

I was wrong.

In the cross-discipline field of Game Studies (or as I prefer Game Sciences), 
I found a lack of centralized spaces, resource, lists, and guides, not to speark of graduate programs dedicated to an important part of the human experience.
Unfortunately, there are *very few* if *any* truly focused research programs for Game Studies or Game Sciences (especially in the United States).
In most cases, Game Studies faculty live inside departments like Computer Science, Psychology, Communication, Art, and just about everywhere else.

This means that an individual must cobble together threads and resources from vast corners of academia, corners that don't normally talk to each other.

Enter this Blog. There will be formal collections of resources, guides, and literature reviews on various topics of Game Studies.
But, this is also meant to be a window into my journey as I try to carve out my place on this amazing, but burgeoning field.

Gaming is an important topic -- a central part of the human experience for virtually all people across all time.
Gaming is worth studying, and that research **is being done**. Join us!

### Topics:

{% if site.data.tags.manifest %}
    {% assign comma = true %}
    {% for tag in site.data.tags.manifest %}
        <a href="/tags/{{ tag }}.html">{{ tag }}</a>
        {% if comma %}
          ,
            {% assign comma = false %}
        {% else %}
            {% assign comma = true %}
        {% endif %}
    {% endfor %}
{% endif %}
