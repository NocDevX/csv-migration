<?php

namespace classes;

require_once('QueryBuilder.php');

use Exception;

class UpdateQueryBuilder extends QueryBuilder
{
    protected function buildRowData($row, $options = [])
    {
        $separator = empty($options['separator']) ? ',' : $options['separator'];
        $regex = empty($options['remove_regex']) ? '/\r|\n/' : $options['remove_regex'];

        $rowData = preg_replace($regex, '', $row);
        $rowData = explode($separator, $rowData);

        return $rowData;
    }

    protected function buildUpdate($values, $options)
    {
        $conditions = $this->buildConditions($values, $options);

        if (!empty($options['columns'])) {
            $values = $this->removeFilteredValues($values, $options);
        }

        $updatePairs = [];
        $columns = $this->getColumns();

        foreach ($values as $key => $val) {
            if (array_key_exists($key, $columns)) {
                $updatePairs[$key] = "{$columns[$key]} = '{$val}'";

                if (floatval($val)) {
                    $updatePairs[$key] = "{$columns[$key]} = {$val}";
                }
            }
        }

        $table = $this->getTable();

        $updatePairsSql = implode(', ', $updatePairs);
        $updatePairsSql = "UPDATE {$table} SET {$updatePairsSql} ";

        if (!empty($conditions)) {
            $updatePairsSql .= $conditions;
        }

        $updatePairsSql = trim($updatePairsSql);
        $updatePairsSql .= ';';

        $this->addQuery($updatePairsSql);
    }

    public function buildQueryFromCsv($options = [])
    {
        $file = fopen($this->getFile(), 'r');
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
                    $rowData = $this->buildRowData($row, $options);
                    $this->buildUpdate($rowData, $options);
                }
            }

            $lineCounter++;
        }

        fclose($file);
    }
}
