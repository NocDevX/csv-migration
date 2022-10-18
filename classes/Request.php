<?php

namespace classes;

use Exception;

class Request
{
    private $_table = '';
    private $_insert = [];
    private $_colunas = [];
    private $_update = [];

    protected function buildConditions($data = [], $options = [])
    {
        $conditions = [];
        $conditions = empty($options['conditions']) ? [] : $options['conditions'];

        if (empty($conditions)) {
            return '';
        }

        foreach ($conditions as $key => $val) {
            if (strpos($val, '$') !== false) {
                preg_match('/\$\w+\$/', $val, $matches);

                $matches = str_replace('$', '', $matches[0]);
                $matches--;

                if (array_key_exists($matches, $data)) {
                    if (floatval($data[$matches]) > 0) {
                        $conditions[$key] = "$key = $data[$matches]";
                    } else {
                        $conditions[$key] = "$key = '{$data[$matches]}'";
                    }
                }
            } else {
                $conditions[$key] = "$key = $val";
            }
        }

        $conditions = implode(' AND ', $conditions);
        return "WHERE $conditions";
    }

    public function addUpdate($values, $options)
    {
        $conditions = $this->buildConditions($values, $options);

        if (!empty($options['columns'])) {
            $values = $this->removeFilteredValues($values, $options);
        }

        $updatePairs = [];
        foreach ($values as $key => $val) {
            if (array_key_exists($key, $this->_colunas)) {
                $updatePairs[$key] = "{$this->_colunas[$key]} = '{$val}'";

                if (floatval($val)) {
                    $updatePairs[$key] = "{$this->_colunas[$key]} = {$val}";
                }
            }
        }

        $updatePairsSql = implode(', ', $updatePairs);
        $updatePairsSql = "UPDATE {$this->_table} SET {$updatePairsSql} ";

        if (!empty($conditions)) {
            $updatePairsSql .= $conditions;
        }

        $updatePairsSql = trim($updatePairsSql);
        $updatePairsSql .= ';';

        $this->_update[] = $updatePairsSql;
    }

    protected function removeFilteredValues($data, $options = [])
    {
        if (empty($options['columns'])) {
            return $data;
        }

        foreach ($data as $key => $values) {
            if (!in_array($key + 1, $options['columns'])) {
                unset($data[$key]);
            }
        }

        return $data ?: [];
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

                if (!empty($row)) {
                    $rowData = $this->getColumns($row, $options);
                    $this->addUpdate($rowData, $options);
                }
            }

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

        // if (!empty($options['columns'])) {
        //     $rowDataSpecificColumns = [];

        //     foreach ($columns as $index) {
        //         $rowDataSpecificColumns[$index - 1] = $rowData[$index - 1];
        //     }

        //     $rowData = $rowDataSpecificColumns;
        // }

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
        @chmod("$folderPath/$fileName", 0777);

        fwrite($file, "$sql");
        fclose($file);

        return $fileName;
    }
}
