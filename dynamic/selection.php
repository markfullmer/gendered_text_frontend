<?php

/**
 * @file
 * Defines the logic for allowing a user to assign genders to characters.
 */

$legend = [];
$genders = ['female' => 0, 'male' => 1, 'non-binary' => 2];
$choices = '';
$selection = '';
if (isset($_GET['selection'])) {
  $selection = $_GET['selection'];
}
else {
  die();
}
$legend = get_text_legend($selection, $api);
$texts = get_text_list($api);
$title = $texts[$selection]->title;
if (!empty($legend)) {
  foreach ($legend as $key => $values) {
    $names = explode("/", $values->id);
    $default_name = $names[$genders[$values->gender]];

    $choices .= '<label for="characters[' . $values->id . ']">Gender for "' . $default_name . '": ';
    $choices .= '<select id="characters[' . $values->id . ']" name="characters[' . $values->id . ']">';
    foreach ($genders as $key => $gender) {
      $choices .= '<option value="' . $key . '"';
      if ($values->gender == $key) {
        $choices .= ' selected="selected"';
      }
      $choices .= '>' . ucfirst($key) . ' ("' . $names[$genders{$key}] . '")</option>';
    }
    $choices .= '</select>';
  }

  echo '<h3>Choose Genders</h3>';
  echo '<h4><em>' . $title . '</em></h4>';
  echo '<div class="row"><div class="two-thirds column">';
  echo '<form action="' . $base_url . 'read" method="POST">';
  echo '<input type="hidden" name="text" value="' . $selection . '" />';
  echo '<span class="button" id="female" onclick="female()">All Female</span>';
  echo '<span class="button" id="male" onclick="male()">All Male</span>';
  echo '<span class="button" id="randomize" onclick="randomize()">Randomize</span>';
  echo $choices;
  echo '</div>  <div class="row"><div class="one-third column">';
  echo '<br /><input class="button button-primary" type="submit" name="submit" value="Read the text" /></div>';
  echo '</form>';
}
