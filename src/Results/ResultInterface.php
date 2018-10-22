<?php

interface ResultInterface {

  public function processRecord($record);

  public function getFileName();

  public function getHeader();

}

?>