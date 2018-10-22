<?php

class ResultDepFederal extends ResultDepEstadual {

  public function __construct($uf, $ano, $record) {
    $this->id = $record[15];
    $this->uf = $uf;
    $this->ano = $ano;
    $this->cargo_id = CODIGO_CARGO_DEP_FEDERAL;
    $this->cargo = 'Dep Federal';
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

  public function getFileName() {
    return "DepFederal__" . $this->ano . "_" . $this->uf . "_recursos_votos";
  }
}

?>