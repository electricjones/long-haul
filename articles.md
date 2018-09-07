---
layout: default
title: Electric Jones
---

<p class="meta">
  <strong>Topics: </strong>
  {% if site.data.tags.manifest %}
  yes
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
</p>

<hr />

<div id="articles">
  <ul class="posts noList">
    {% for post in site.posts %}
      <li>
      	<span class="date">{{ post.date | date_to_string }}</span>
      	<h3><a href="{{ post.url }}">{{ post.title }}</a></h3>
      	<p class="description">{% if post.excerpt %}{{ post.excerpt  | strip_html | strip_newlines | truncate: 120 }}{% else %}{{ post.content | strip_html | strip_newlines | truncate: 120 }}{% endif %}</p>
      </li>
    {% endfor %}
  </ul>
</div>