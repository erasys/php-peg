<?php

require_once 'Benchmark/Timer.php';

function cmp($cmp, $txt, $o, $l)
{
  return 0 === strcasecmp($cmp, substr($txt, $o, strlen($cmp)));
}
function cmp2($cmp, $txt, $o, $l)
{
  return 0 === substr_compare(
    $txt, $cmp, $o,
    strlen($cmp), true
  );
}
function cmp_u($cmp, $txt, $o, $l)
{
  return mb_strtolower($cmp, "utf-8") === mb_strtolower(
    mb_strcut($txt, $o, strlen($cmp), 'utf-8'),
    'utf-8'
  );
}
function cmp_pcre($cmp, $txt, $o)
{
  return preg_match('/\G'.preg_quote($cmp).'/i', $txt, $m, 0, $o);
}
function cmp_pcre_u($cmp, $txt, $o)
{
  return preg_match('/\G'.preg_quote($cmp).'/iu', $txt, $m, 0, $o);
}

$text = file_get_contents(__DIR__.'/lorem-utf8.txt');


$txt_len = mb_strlen($text, 'utf-8');
$cmp_len = 20;

$txt_1 = mb_strcut($text, 0, $cmp_len, 'utf-8');
$cmp_1 = mb_strtoupper($txt_1, 'utf-8');
printf("#1: Compare '%s' to '%s'\n", $txt_1, $cmp_1);
var_dump(strlen($txt_1) === strlen($cmp_1));

$idx_2 = rand(0, $txt_len - $cmp_len);
$txt_2 = mb_strcut($text, $idx_2, $cmp_len, 'utf-8');
$cmp_2 = mb_strtoupper($txt_2, 'utf-8');
printf("#2: Compare '%s' to '%s'\n", $txt_2, $cmp_2);
var_dump(strlen($txt_1) === strlen($cmp_1));

$idx_3 = rand(0, $txt_len - $cmp_len);
$txt_3 = mb_strcut($text, $idx_3, $cmp_len, 'utf-8');
$cmp_3 = mb_strtoupper($txt_3, 'utf-8');
printf("#3: Compare '%s' to '%s'\n", $txt_3, $cmp_3);
var_dump(strlen($txt_1) === strlen($cmp_1));

$idx_4 = $txt_len - $cmp_len;
$txt_4 = mb_strcut($text, $idx_4, $cmp_len, 'utf-8');
$cmp_4 = mb_strtoupper($txt_4, 'utf-8');
printf("#4: Compare '%s' to '%s'\n", $txt_4, $cmp_4);
var_dump(strlen($txt_1) === strlen($cmp_1));


$timer = new Benchmark_Timer();
$nb_iterations = 1000;
$timer->start();

echo "cmp\n";
$r = array();
for ($i = 0; $i < $nb_iterations; $i++) {
  $r[0] = cmp($cmp_1, $text, 0,       $cmp_len);
  $r[1] = cmp($cmp_2, $text, $idx_2,  $cmp_len);
  $r[2] = cmp($cmp_3, $text, $idx_3,  $cmp_len);
  $r[3] = cmp($cmp_4, $text, $idx_4,  $cmp_len);
}
var_dump($r);
$timer->setMarker('cmp');

echo "cmp2\n";
$r = array();
for ($i = 0; $i < $nb_iterations; $i++) {
  $r[0] = cmp($cmp_1, $text, 0,       $cmp_len);
  $r[1] = cmp($cmp_2, $text, $idx_2,  $cmp_len);
  $r[2] = cmp($cmp_3, $text, $idx_3,  $cmp_len);
  $r[3] = cmp($cmp_4, $text, $idx_4,  $cmp_len);
}
var_dump($r);
$timer->setMarker('cmp2');

echo "cmp_pcre\n";
$r = array();
for ($i = 0; $i < $nb_iterations; $i++) {
  $r[0] = cmp_pcre($cmp_1, $text, 0);
  $r[1] = cmp_pcre($cmp_2, $text, $idx_2);
  $r[2] = cmp_pcre($cmp_3, $text, $idx_3);
  $r[3] = cmp_pcre($cmp_4, $text, $idx_4);
}
var_dump($r);
$timer->setMarker('cmp_pcre');

echo "cmp_u\n";
$r = array();
for ($i = 0; $i < $nb_iterations; $i++) {
  $r[0] = cmp_u($cmp_1, $text, 0,       $cmp_len);
  $r[1] = cmp_u($cmp_2, $text, $idx_2,  $cmp_len);
  $r[2] = cmp_u($cmp_3, $text, $idx_3,  $cmp_len);
  $r[3] = cmp_u($cmp_4, $text, $idx_4,  $cmp_len);
}
var_dump($r);
$timer->setMarker('cmp_u');

echo "cmp_pcre_u\n";
$r = array();
for ($i = 0; $i < $nb_iterations; $i++) {
  $r[0] = cmp_pcre_u($cmp_1, $text, 0);
  $r[1] = cmp_pcre_u($cmp_2, $text, $idx_2);
  $r[2] = cmp_pcre_u($cmp_3, $text, $idx_3);
  $r[3] = cmp_pcre_u($cmp_4, $text, $idx_4);
}
var_dump($r);
$timer->setMarker('cmp_pcre_u');


echo $timer->getOutput();
