<?php

  $xml = simplexml_load_file('https://unicode.org/repos/cldr/trunk/common/supplemental/supplementalData.xml');

  $file = 'https://unicode.org/repos/cldr/trunk/common/supplemental/supplementalData.xml';
  $doc = new DOMDocument();
  $doc->load($file);
  $xp = new DOMXPath($doc);

  $comments = $xp->evaluate('/supplementalData/territoryInfo/territory/languagePopulation/following::comment()[1]');

  $names = Array();

  foreach ($comments as $comment) {
    $names[] = (string)$comment->data;
  }

  foreach ($xml->territoryInfo->territory as $rows) {
    $population = $rows->attributes()['population'];
    foreach ($rows as $languagePopulation) {
      $type = isset($languagePopulation) ? '' . $languagePopulation->attributes()['type'] : null;
      $populationPercent = isset($languagePopulation) ? $languagePopulation->attributes()['populationPercent'] : null;
      $totalPerson = round($populationPercent * $population * 0.01);
      $lang['type'] = $type;
      $lang['totalPerson'] = $totalPerson;
      $lang['langNames'] = $names[0] ?? null;
      $names = array_slice($names, 1);
      $data[] = $lang;
    }
  }

  foreach ($data as $d) {
    if (empty($tully[$d['type']])) {
      $tully[$d['type']] = [$d['totalPerson'], $d['langNames']];
    } else {
      $total = $d['totalPerson'] + $tully[$d['type']][0];
      $tully[$d['type']] = [$total, $d['langNames']];
    }
  }

  arsort($tully);
  foreach ($tully as $key => $value) {
    echo $key . ' ' . $value[1] . ' ' . $value[0] . "\n";
  }