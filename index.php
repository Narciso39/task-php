<?php

header('Content-Type: application/json');
require 'db.php';

// $dadosTesteSaida = file_get_contents('dataSaida.json');
// $dadosTesteEntrada = file_get_contents('dataEntrada.json');
// $dadosJson = json_decode($dadosTesteEntrada);

// echo '<pre>';
// print_r($dadosJson);
// echo '</pre>';


$dadosEntradaJson = file_get_contents("php://input");
$dadosEntradaJsonParaArray = json_decode($dadosEntradaJson, true);

if (!isset($dadosEntradaJsonParaArray['data_source']['table']) || !isset($dadosEntradaJsonParaArray['data_source']['columns'])) {
    echo json_encode(["error" => "parametro inválido"]);
    exit;
}

$regexDeVerificacaoDeSQLInjectionTabela = preg_replace("/[^a-zA-Z0-9_]/", "", $dadosEntradaJsonParaArray['data_source']['table']);
$regexDeVerificacaoDeSQLInjectionColuna = array_map(fn($col) => preg_replace("/[^a-zA-Z0-9_]/", "", $col), $dadosEntradaJsonParaArray['data_source']['columns']);
$colunaArrayParaString = implode(", ", $regexDeVerificacaoDeSQLInjectionColuna);

$queryInicial = "SELECT $colunaArrayParaString FROM $regexDeVerificacaoDeSQLInjectionTabela";
$queryFiltros = [];
$arrFiltroEntrada = [];

if (isset($dadosEntradaJsonParaArray['data_source']['filters']) &&  is_array($dadosEntradaJsonParaArray['data_source']['filters'])) {
    foreach ($dadosEntradaJsonParaArray['data_source']['filters'] as $filtro => $filtroEntrada) {
        $regexDeVerificacaoDeSQLInjectionFiltro = preg_replace("/[^a-zA-Z0-9_]/", "", $filtro);
        $queryFiltros[] = "$regexDeVerificacaoDeSQLInjectionFiltro = :$regexDeVerificacaoDeSQLInjectionFiltro";
        $arrFiltroEntrada[$regexDeVerificacaoDeSQLInjectionFiltro] = $filtroEntrada;
    }
}


if (!empty($queryFiltros)) {
    $queryFinal = $queryInicial . " WHERE " . implode(" AND ", $queryFiltros);
}



try {
    $stmt = $pdo->prepare($queryFinal);
    $stmt->execute($arrFiltroEntrada);
    $resultado = $stmt->fetchAll();
    $wrapper = $dadosEntradaJsonParaArray['response_format']['wrapper'] ?? ['data'];
    echo json_encode([$wrapper => $resultado]);
} catch (PDOException $e) {
    echo json_encode(["error" => "caiu no bloco do catch" . $e->getMessage()]);
}
