<?php

class ResultDeputado implements ResultInterface {

  public function getHeader() {
    $item = [
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
    foreach (array_keys(get_object_vars($this)) as $field) {
      if (strpos($field, 'Z_') !== FALSE) {
        $item[] = $field;
      }
    }
    return $item;
  }

  public function getData() {
    $item = [
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
    foreach (get_object_vars($this) as $key => $field) {
      if (strpos($key, 'Z_') !== FALSE) {
        $item[] = $field;
      }
    }
    return $item;
  }

  public function __construct($uf, $ano, $cargo_id, $record) {
    $this->id = $record[15];
    $this->uf = $uf;
    $this->ano = $ano;
    $this->cargo_id = $cargo_id;
    if ($this->cargo_id == CODIGO_CARGO_DEP_FEDERAL) {
      $this->cargo = 'Dep Federal';
    }
    else {
      $this->cargo = 'Dep Estadual';
    }
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
        $this->$field = (float) $record_contas->dadosConsolidados->$field;
      }
      $this->fundosPartidarios = (float) $record_contas->despesas->fundosPartidarios;
      $this->fundoEspecial = (float) $record_contas->despesas->fundoEspecial;
    }

    $this->votosTotal = $record_votacao['total'];

    foreach (array_keys($record_votacao['zonas']) as $zona_numero) {
      $this->{'Z_' . $zona_numero} = $record_votacao['zonas'][$zona_numero];
    }
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
    if ($this->cargo_id == CODIGO_CARGO_DEP_FEDERAL) {
      return "DepFederal_" . $this->ano . "_" . $this->uf . "_recursos_votos";
    }
    else {
      return "DepEstadual_" . $this->ano . "_" . $this->uf . "_recursos_votos";
    }
  }
}

?>