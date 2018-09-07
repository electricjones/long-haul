---
layout: default
title: Electric Jones
---

<div class="home" id="home">
  <h1 class="pageTitle">Blog</h1>
    <p class="meta">
      <strong>Topics: </strong>
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
    </p>

    <hr />

  <ul class="posts noList">
    {% for post in paginator.posts %}
      <li>
        <span class="date">{{ post.date | date: '%B %d, %Y' }}</span>
        <h3><a class="post-link" href="{{ post.url | prepend: site.baseurl }}">{{ post.title }}</a></h3>
        <p>{% if post.excerpt %}{{ post.excerpt }}{% else %}{{ post.excerpt | strip_html }}{% endif %}</p>
      </li>
    {% endfor %}
  </ul>
  <!-- Pagination links -->
  <div class="pagination">
    {% if paginator.previous_page %}
      <a href="{{ paginator.previous_page_path | prepend: site.baseurl }}" class="previous button__outline">Newer Posts</a>
    {% endif %}
    {% if paginator.next_page %}
      <a href="{{ paginator.next_page_path | prepend: site.baseurl }}" class="next button__outline">Older Posts</a>
    {% endif %}
  </div>
</div>
