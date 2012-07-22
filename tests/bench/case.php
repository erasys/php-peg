<?php

require_once 'Benchmark/Timer.php';

function casecmp_internal($str1, $str2)
{
  return 0 === strcasecmp($str1, $str2);
}
function casecmp_pcre($str1, $str2)
{
  return preg_match('/'.preg_quote($str1, '/').'/i', $str2);
}
function casecmp_mbstring($str1, $str2)
{
  return mb_strtolower($str1, "utf-8") === mb_strtolower($str2, "utf-8");
}
function casecmp_mbstring_2($str1, $str2)
{
  return $str1 === mb_strtolower($str2, "utf-8");
}
function casecmp_pcre_u($str1, $str2)
{
  return preg_match('/'.preg_quote($str1, '/').'/iu', $str2);
}

$timer = new Benchmark_Timer();
$nb_iterations = 100000;
$timer->start();

echo "casecmp_internal\n";
for ($i = 0; $i < $nb_iterations; $i++) {
  $r = casecmp_internal('wam\bam', 'Wam\BAM');
}
var_dump($r);
$timer->setMarker('casecmp_internal');

echo "casecmp_pcre\n";
for ($i = 0; $i < $nb_iterations; $i++) {
   $r = casecmp_pcre('wam/bam', 'Wam/BAM');
}
var_dump($r);
$timer->setMarker('casecmp_pcre');

echo "casecmp_mbstring\n";
for ($i = 0; $i < $nb_iterations; $i++) {
   $r = casecmp_mbstring('Wœm bŒm', 'wœm bœm');
}
var_dump($r);
$timer->setMarker('casecmp_mbstring');

echo "casecmp_mbstring_2\n";
for ($i = 0; $i < $nb_iterations; $i++) {
   $r = casecmp_mbstring_2('wœm bœm', 'Wœm bŒm');
}
var_dump($r);
$timer->setMarker('casecmp_mbstring_2');


echo "casecmp_pcre_u\n";
for ($i = 0; $i < $nb_iterations; $i++) {
   $r = casecmp_pcre_u('Wœm{}bŒm', 'wœm{}bœm');
}
var_dump($r);
$timer->setMarker('casecmp_pcre_u');

echo $timer->getOutput();

