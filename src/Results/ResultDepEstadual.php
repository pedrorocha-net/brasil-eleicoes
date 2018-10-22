<?php

class ResultDepEstadual implements ResultInterface {

  public function getHeader() {
    return [
      'Partido',
      'Cargo',
      'Numero',
      'Nome',
      'Genero',
      'CorRaca',
      'FECF',
      'Fundopartidario',
      'Privados',
      'Estimaveis',
      'Total',
      'Votos',
    ];
  }

  public function getData() {
    return [
      $this->partidoSigla,
      $this->cargo,
      $this->candidatoNumero,
      $this->candidatoNome,
      $this->candidatoGenero,
      $this->descricaoCorRaca,
      $this->fundoEspecial,
      $this->fundosPartidarios,
      $this->totalFinanceiro,
      $this->totalEstimados,
      $this->totalRecebido,
      $this->votosTotal,
    ];
  }

  public function __construct($uf, $ano, $record) {
    $this->id = $record[15];
    $this->uf = $uf;
    $this->ano = $ano;
    $this->cargo_id = CODIGO_CARGO_DEP_ESTADUAL;
    $this->cargo = 'Dep Estadual';
    $this->partidoNumero = $record[27];
    $this->partidoSigla = $record[28];
    $this->candidatoNumero = $record[16];
    $this->candidatoNome = $record[18];
    $this->candidatoGenero = $record[42];
    $this->descricaoCorRaca = $record[48];
    $this->dataDeNascimento = $record[38];
    $this->grauInstrucao = $record[44];
    $this->totalDoacaoFcc = 0;
    $this->totalReceitaOutCand = 0;
    $this->totalProprios = 0;
    $this->totalRoni = 0;
    $this->totalInternet = 0;
    $this->totalPartidos = 0;
    $this->totalReceitaPJ = 0;
    $this->totalReceitaPF = 0;
    $this->totalEstimados = 0;
    $this->totalFinanceiro = 0;
    $this->totalRecebido = 0;
    $this->fundosPartidarios = 0;
    $this->fundoEspecial = 0;
    $this->votosTotal = 0;
  }

  public function processRecord($record_votacao) {
    $this->votosTotal = $record_votacao['total'];

    $record_contas = $this->getAPIData();
    if (
      is_object($record_contas)
      && is_object($record_contas->dadosConsolidados)
      && is_object($record_contas->despesas)
    ) {
      $fields = [
        'totalDoacaoFcc',
        'totalReceitaOutCand',
        'totalProprios',
        'totalRoni',
        'totalInternet',
        'totalPartidos',
        'totalReceitaPJ',
        'totalReceitaPF',
        'totalEstimados',
        'totalFinanceiro',
        'totalRecebido',
      ];
      foreach ($fields as $field) {
        $this->$field = (int) $record_contas->dadosConsolidados->$field;
      }
      $this->fundosPartidarios = (int) $record_contas->despesas->fundosPartidarios;
      $this->fundoEspecial = (int) $record_contas->despesas->fundoEspecial;
    }

    //    foreach ($record_votacao['cidades'] as $c_key => $cidade) {
    //      foreach ($cidade['zonas'] as $z_key => $zona) {
    //        $this->votosTotal = $record_votacao['total'];
    //      }
    //    }
  }

  public function getAPIData() {
    try {
      $path_base = 'http://divulgacandcontas.tse.jus.br/divulga/rest/v1';
      $path = $path_base . "/prestador/consulta/2022802018/$this->ano/$this->uf/$this->cargo_id/$this->partidoNumero/$this->candidatoNumero/$this->id";
      $json = file_get_contents($path);
      if (!$json) {
        throw new Exception("Erro chamando API do TSE para candidato $this->candidatoNumero");
      }
      return json_decode($json);
    } catch (Exception $e) {
      return FALSE;
    }
  }

  public function getFileName() {
    return "DepEstadual_" . $this->ano . "_" . $this->uf . "_recursos_votos";
  }
}

?>