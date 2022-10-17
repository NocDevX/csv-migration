<?php

namespace classes;

use Exception;

require(__DIR__ . '/QueryUtils.php');

class Requisicao extends QueryUtils
{
    private $_table = '';
    private $_insert = [];
    private $_colunas = [];
    private $_colunasUpdate = [];
    private $_update = [];

    public function addInsert($valores)
    {
        $valores = implode(',', $valores);
        $this->_insert[] = "INSERT INTO {$this->_table} ({$this->_colunas}) VALUES ({$valores})";
    }

    public function addUpdate($valores, $condicoes)
    {
        $colunasCount = count($this->_colunas);
        $valoresCount = count($valores);

        if ($colunasCount !== $valoresCount) {
            throw new Exception('Valores não correspondem à quantidade de colunas!');
        }


        for ($i = 0; $i < $colunasCount; $i++) {
            $this->_colunasUpdate[$i] = "{$this->_colunas[$i]} = '{$valores[$i]}'";

            if (floatval($valores[$i]) > 0) {
                $this->_colunasUpdate[$i] = "{$this->_colunas[$i]} = {$valores[$i]}";
            }
        }

        $condicoes = implode(' ', $condicoes);
        $colunasUpdate = implode(', ', $this->_colunasUpdate);
        $colunasUpdate = preg_replace("/\r|\n/", "", $colunasUpdate);

        $this->_update[] = "UPDATE {$this->_table} SET {$colunasUpdate} WHERE {$condicoes};";
    }

    public function setTable($tableName)
    {
        $this->_table = $tableName;
    }

    public function setColunas($colunas)
    {
        if (!is_array($colunas) || empty($colunas)) {
            throw new InvalidArgumentException('Coluna tem de ser array e não pode estar vazio.');
        }

        $this->_colunas = $colunas;
    }

    public function getQuery($type = 'insert')
    {
        $query = '';

        switch ($type) {
            case 'insert':
                $query = $this->getInsertQuery();
                break;
            case 'update':
                $query = $this->getUpdateQuery();
                break;
        }

        return $query;
    }

    protected function getInsertQuery()
    {
        $sql = '';

        if (!empty($this->_insert)) {
            $sql .= implode(' ', $this->_insert);
        }

        return $sql;
    }

    protected function getUpdateQuery()
    {
        $sql = '';

        if (!empty($this->_update)) {
            $sql .= implode(PHP_EOL, $this->_update);
        }

        return $sql;
    }
}
