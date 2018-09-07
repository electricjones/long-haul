<?php
$directory = __DIR__ . '/_posts';
$files = array_diff(scandir($directory), array('..', '.'));

$tags = ['map' => []];
$manifest = [];

foreach ($files as $file) {
    foreach (new SplFileObject($directory . '/' . $file) as $line) {
        $sub = substr($line, 0, 5);
        if ($sub === "tags:") {
            $list = explode(", ", trim(str_replace("tags: ", "", $line), "[]\n"));

            foreach ($list as $tag) {
                $tags['map'][$tag][] = $file;
                $manifest[] = $tag;
            }

            break;
        }
    }
}

$tags['manifest'] = array_values(array_unique($manifest));

$json = json_encode($tags);

// Save The Manifest
file_put_contents(__DIR__ . '/_data/tags.json', $json);

// Create all the post topics
foreach ($tags['manifest'] as $tag) {
    $template = "
---
layout: topic
title: \"Tag: {$tag}\"
tag: {$tag}
---
";
    file_put_contents(__DIR__ . '/tags/' . $tag . ".md", $template);
}


// Now I have a manifest
// create topic page list layout
// create each tag topic page that uses that layout
// make sure my links work