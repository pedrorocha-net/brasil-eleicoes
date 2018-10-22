<?php

class DataRepository {
  //$data_files['prestacao_de_contas'] = "./data/prestacao_de_contas_eleitorais_candidatos_${ano}/despesas_contratadas_candidatos_${ano}_${uf}.csv";

  public function __construct($ano, $uf, $codigo_cargo) {
    $this->ano = $ano;
    $this->uf = $uf;
    $this->codigo_cargo = $codigo_cargo;
  }

  public function getDadosCandidaturas() {
    $candidaturas = [];
    $file_path = './data/consulta_cand_' . $this->ano . '/consulta_cand_' . $this->ano . '_' . $this->uf . '.csv';

    $handle = fopen($file_path, "r");
    $first_line = TRUE;
    if ($handle) {
      while (($line = fgetcsv($handle, 0, ';')) !== FALSE) {
        if ($first_line) {
          $first_line = FALSE;
          continue;
        }
        if ($line[13] == $this->codigo_cargo && $line[25] == 'DEFERIDO') {
          switch ($this->codigo_cargo) {
            case CODIGO_CARGO_DEP_FEDERAL:
              $Result = new ResultDepFederal($this->uf, $this->ano, $line);
              break;
            case CODIGO_CARGO_DEP_ESTADUAL:
              $Result = new ResultDepEstadual($this->uf, $this->ano, $line);
              break;
          }
          if (isset($Result)) {
            $candidaturas[] = $Result;
          }
        }
      }

      fclose($handle);
    }
    else {
      print 'Erro abrindo o arquivo candidaturas';
    }
    return $candidaturas;
  }

  public function getDadosVotacao() {
    $data_votacao = [];
    $file_path = './data/votacao_candidato_munzona_' . $this->ano . '/votacao_candidato_munzona_' . $this->ano . '_' . $this->uf . '.csv';

    $handle = fopen($file_path, "r");
    if ($handle) {
      $first_line = TRUE;
      while (($line = fgetcsv($handle, 0, ';')) !== FALSE) {
        if ($first_line) {
          $first_line = FALSE;
          continue;
        }
        if (!isset($data_votacao[$line[18]])) {
          $data_votacao[$line[18]] = [
            'cidades' => [],
            'total' => 0
          ];
        }
        if (!isset($data_votacao[$line[18]]['cidades'][$line[13]])) {
          $data_votacao[$line[18]]['cidades'][$line[13]] = [
            'nome' => $line[14],
            'zonas' => []
          ];
        }
        $data_votacao[$line[18]]['total'] += $line[37];
        $data_votacao[$line[18]]['cidades'][$line[13]]['zonas'][] = [
          'zonaNumero' => $line[15],
          'votos' => $line[37]
        ];
      }
      fclose($handle);
    }
    else {
      print 'Erro abrindo o arquivo votacao_candidato_munzona';
    }
    return $data_votacao;
  }
}

?>