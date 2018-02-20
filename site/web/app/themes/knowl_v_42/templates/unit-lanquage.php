<?php $language_switcher = pll_the_languages(['raw' => 1]);
echo '<select>';
  foreach($language_switcher as $language_switcher__value)
  {
  echo '<option>';
      echo '<a href="'.$language_switcher__value['url'].'">';
      echo $language_switcher__value['id'];
      //echo $language_switcher__value['flag'];
      	// echo $language_switcher__value['slug'];
   echo '<img src="'.$language_switcher__value['flag'].'" alt=""> ';
//echo $language_switcher__value['url'];
      echo '</a>';
 echo '</option>';
  }
echo '</select>'; ?>
