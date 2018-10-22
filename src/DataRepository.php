<?php

class DataRepository {

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
          $candidaturas[] = new ResultDeputado($this->uf, $this->ano, $this->codigo_cargo, $line);
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

    $file = file('./data/lista_zonas_eleitorais_' . $this->uf . '.csv');
    $zonas = [];
    foreach ($file as $key => $zona) {
      if ($key === 0) {
        continue;
      }
      $zonas[] = (int) str_replace('"', '', explode(',', $zona)[0]);
    }

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
            'zonas' => [],
            'total' => 0,
          ];
          foreach ($zonas as $zona) {
            $data_votacao[$line[18]]['zonas'][$zona] = 0;
          }
        }

        $data_votacao[$line[18]]['total'] += (int) $line[37];
        $data_votacao[$line[18]]['zonas'][$line[15]] = (int) $line[37];
      }
      fclose($handle);
    }
    else {
      print 'Erro abrindo o arquivo votacao_candidato_munzona';
    }
    return $data_votacao;
  }

  //  public function getDadosPrestacaoContas() {
  //    $data_files['prestacao_de_contas'] = "./data/prestacao_de_contas_eleitorais_candidatos_${ano}/despesas_contratadas_candidatos_${ano}_${uf}.csv";
  //    $handle = fopen($data_files['prestacao_de_contas'], "r");
  //    if ($handle) {
  //      foreach ($candidaturas as $candidatura) {
  //        $first_line = TRUE;
  //        while (($line = fgetcsv($handle, 0, ';')) !== FALSE) {
  //          print_r($line);
  //          die();
  //          //      if ($first_line) {
  //          //        $first_line = FALSE;
  //          //        continue;
  //          //      }
  //          //        if ($line[13] == $codigo_cargo) {
  //          //          $Result = new ResultDepEstadual($uf, $ano, $line[15]);
  //          //          $Result->processRecord($line);
  //          //        }
  //          $candidatura->processRecord($line, 2);
  //        }
  //      }
  //      fclose($handle);
  //    }
  //    else {
  //      print 'Erro abrindo o arquivo votacao_candidato_munzona';
  //    }
  //
  //    print_r($candidaturas[0]);
  //  }
}

?>