<?php

/**
 * @file
 * Lists available texts.
 */

$texts = get_text_list($api);
$allowed = (array) json_decode(file_get_contents('allowed.json'));

if ($texts) {
  $rows = [];
  $genres = [];
  foreach ($texts as $id => $values) {
    if (in_array($id, $allowed)) {
      // This first if statement is only to support legacy
      // text listing. It can be removed after verifying update.
      if (is_string($values)) {
        $rows[$id]['title'] = '<a href="/selection/' . $id . '">' . $values . '</a>';
        $rows[$id]['genre'] = '';
        
      }
      else {
        $rows[$id]['title'] = $values->title;
        $rows[$id]['link'] = '<a href="/selection/' . $id . '">' . $values->title . '</a>';
        $rows[$id]['genre'] = $values->genre;
        
        $genrekey = strtolower(preg_replace("/[^A-Za-z0-9]/", '', html_entity_decode($values->genre)));
        $genres[$genrekey] = $values->genre;
      }
    }
  }
}

if (!empty($rows)) {
  echo '<h3>Available Texts</h3>';
  // Genre selection.
  if (!empty($genres)) {
    asort($genres);
    echo '<form action="/text_list" method="get">';
    echo '<label for="bygenre">View by genre</label>';
    echo '<select name="bygenre">';
    echo '<option value="any">-Any-</option>';
    foreach ($genres as $key => $genre) {
      echo '<option value="' . $key . '"';
      if (isset($_GET['bygenre'])) {
        if ($_GET['bygenre'] == $key) {
          echo ' selected="selected"';
        }
      }
      echo '>' . $genre . '</option>';
    }
    echo '</select>';
    echo '<input type="submit" name="submit" value="Filter" />';
    echo '</form>';
  }
  if (isset($_GET['bygenre']) && in_array($_GET['bygenre'], array_keys($genres))) {
    foreach ($rows as $id => $row) {
      $thisgenre = $_GET['bygenre'];
      if ($row['genre'] != $genres[$thisgenre]) {
        unset($rows[$id]);
      }
    }
  }
  // Sort functionality:
  if (isset($_GET['sort'])) {
    if ($_GET['sort'] == 'title') {
      usort($rows, function ($a, $b) {
        return strcmp($a["title"], $b["title"]);
      });
    }
    if ($_GET['sort'] == 'genre') {
      usort($rows, function ($a, $b) {
        return strcmp($a["genre"], $b["genre"]);
      });
    }
  }
 $queries = isset($_GET['bygenre']) ? '&bygenre=' . $_GET['bygenre'] : '';
  echo '<table>';
  echo '<thead><th><a href="/text_list?sort=title' . $queries . '">Title</a></th><th><a href="/text_list?sort=genre' . $queries . '">Genre</a></th></thead>';
  foreach ($rows as $id => $row) {
    echo '<tr><td>' . $row['link'] . '</td><td>' . $row['genre'] . '</td></tr>';
  }
  echo '</table>';
}