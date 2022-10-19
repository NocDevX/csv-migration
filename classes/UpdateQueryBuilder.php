<?php

namespace classes;

require_once('QueryBuilder.php');

use Exception;

class UpdateQueryBuilder extends QueryBuilder
{
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
}
