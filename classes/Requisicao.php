<?php

namespace classes;

use Exception;

class Requisicao
{
    private $_table = '';
    private $_insert = [];
    private $_colunas = [];
    private $_update = [];

    // Rework needed
    public function addInsert($valores)
    {
        $valores = implode(',', $valores);
        $this->_insert[] = "INSERT INTO {$this->_table} ({$this->_colunas}) VALUES ({$valores})";
    }

    public function addUpdate($values, $options)
    {
        $updatePairs = [];
        foreach ($values as $key => $val) {
            $updatePairs[$this->_colunas[$key]] = $val;
        }

        $updatePairsSql = '';
        foreach ($updatePairs as $column => $value) {
            $updatePairsSql .= "$column = '$value', ";

            if (floatval($value) > 0) {
                $updatePairsSql .= "$column = $value, ";
            }
        }

        $updatePairsSql = rtrim($updatePairsSql, ', ');
        $updatePairsSql = "UPDATE {$this->_table} SET {$updatePairsSql}";

        if (!empty($options['condicoes'])) {
            $condicoes = implode(' AND ', $options['condicoes']);
            $updatePairsSql .= " WHERE {$condicoes}";
        }

        $this->_update[] = $updatePairsSql;
    }

    public function setTable($tableName)
    {
        $this->_table = $tableName;
    }

    public function setColumns($colunas)
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

    public function buildFromCsv($filePath, $options = [])
    {
        $file = fopen($filePath, 'r');
        $rowData = [];
        $lineCounter = 1;
        $skipRows = empty($options['skip_rows']) ? [] : $options['skip_rows'];

        if (empty($file)) {
            throw new Exception('Arquivo não encontrado');
        }

        while (!feof($file)) {
            $row = fgets($file);

            if (!in_array($lineCounter, $skipRows)) {
                $rowData = $this->getColumns($row, $options);
                $this->addUpdate($rowData, $options);
            };

            $lineCounter++;
        }

        fclose($file);
    }

    protected function getColumns($row, $options = [])
    {
        $separator = empty($options['separator']) ? ',' : $options['separator'];
        $columns = empty($options['columns']) ? [] : $options['columns'];
        $regex = empty($options['remove_regex']) ? '/\r|\n/' : $options['remove_regex'];

        $rowData = preg_replace($regex, '', $row);
        $rowData = explode($separator, $rowData);

        if (!empty($options['columns'])) {
            $rowDataSpecificColumns = [];

            foreach ($columns as $index) {
                $rowDataSpecificColumns[$index - 1] = $rowData[$index - 1];
            }

            $rowData = $rowDataSpecificColumns;
        }

        return $rowData;
    }

    public function sqlToFile($sql, $filename = 'dump')
    {
        $timestamp = time();
        $folderPath = __DIR__ . '/../output';
        $fileName = "{$filename}_{$timestamp}.sql";

        if (!file_exists($folderPath)) {
            mkdir($folderPath);
            @chmod($folderPath, 0777);
        }

        $file = fopen("{$folderPath}/{$fileName}", 'w');
        fwrite($file, "$sql");
        fclose($file);

        return $fileName;
    }
}
