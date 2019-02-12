<?php
  // get the CLDR data with using simpleXMLElement
  $xml = simplexml_load_file('https://unicode.org/repos/cldr/trunk/common/supplemental/supplementalData.xml');
  
  // get the CLDR data with using DOMDocument
  $file = 'https://unicode.org/repos/cldr/trunk/common/supplemental/supplementalData.xml';
  $doc = new DOMDocument();
  $doc->load($file);
  $xp = new DOMXPath($doc);

  $comments = $xp->evaluate('/supplementalData/territoryInfo/territory/languagePopulation/following::comment()[1]');

  $langNames = Array();

  // loop over all comments and push each comment to langNames array.
  foreach ($comments as $comment) {
    $langNames[] = (string)$comment->data;
  }

  // loop over territories and assign population info to a population variable.
  foreach ($xml->territoryInfo->territory as $rows) {
    $population = $rows->attributes()['population'];

    // loop over each row and get the type, population percent attributes, then
    // then calculate totalPerson.
    foreach ($rows as $languagePopulation) {
      $type = isset($languagePopulation) ? '' . $languagePopulation->attributes()['type'] : null;
      $populationPercent = isset($languagePopulation) ? $languagePopulation->attributes()['populationPercent'] : null;
      $totalPerson = round($populationPercent * $population * 0.01);
      $lang['type'] = $type;
      $lang['totalPerson'] = $totalPerson;

      // get the first english language name from langNames array.
      $lang['langNames'] = $langNames[0] ?? null;

      // set the langNames array with removing first element of its elements.
      $langNames = array_slice($langNames, 1);

      // push lang associative array into data array.
      $data[] = $lang;
    }
  }

  // loop over data array and create a tully array.
  foreach ($data as $d) {
    if (empty($tully[$d['type']])) {
      $tully[$d['type']] = [$d['totalPerson'], $d['langNames']];
    } else {
      $total = $d['totalPerson'] + $tully[$d['type']][0];
      $tully[$d['type']] = [$total, $d['langNames']];
    }
  }

  // sort the tully array based on values in an ascending order.
  arsort($tully);

  // echo the data like so: 'en English 1234567'.
  foreach ($tully as $key => $value) {
    echo $key . ' ' . $value[1] . ' ' . $value[0] . "\n";
  }