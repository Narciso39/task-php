<?php 

require 'db.php';

$dadosTesteSaida = file_get_contents('dataSaida.json');
$dadosTesteEntrada = file_get_contents('dataEntrada.json');
$dadosJson = json_decode($dadosTesteEntrada);

echo '<pre>';
print_r($dadosJson);
echo '</pre>';



