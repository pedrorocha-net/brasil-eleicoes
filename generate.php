<?php

include_once './src/Results/ResultInterface.php';
include_once './src/Results/ResultDeputado.php';
include_once './src/DataRepository.php';

define('CODIGO_CARGO_DEP_FEDERAL', 6);
define('CODIGO_CARGO_DEP_ESTADUAL', 7);

$ano = 2018;
$uf = 'RJ';
$codigo_cargo = CODIGO_CARGO_DEP_ESTADUAL;
$result_data = [];

$DataRepository = new DataRepository($ano, $uf, $codigo_cargo);

$candidaturas = $DataRepository->getDadosCandidaturas();
$data_votacao = $DataRepository->getDadosVotacao();

$votacao_inexistentes = 0;
foreach ($candidaturas as &$candidatura) {
  if (isset($data_votacao[$candidatura->id])) {
    $candidatura->processRecord($data_votacao[$candidatura->id]);
  }
  else {
    $votacao_inexistentes++;
    print "#$votacao_inexistentes Dados de votacao inexistentes para $candidatura->candidatoNumero ($candidatura->id)\n";
  }
}

$fp = fopen('./results/' . $candidaturas[0]->getFileName() . '.csv', 'w');
fputcsv($fp, $candidaturas[0]->getHeader());
foreach ($candidaturas as $candidatura) {
  fputcsv($fp, $candidatura->getData());
}
fclose($fp);

?>