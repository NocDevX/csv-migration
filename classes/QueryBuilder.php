<?php

namespace classes;

require_once(__DIR__ . '/../interfaces/QueryBuilderInterface.php');

use interfaces\QueryBuilderInterface;

class QueryBuilder implements QueryBuilderInterface
{
    private $_table;
    private $_columns;
    private $_query;
    private $_file;

    public function setTable($tablename)
    {
        $this->_table = $tablename;
    }

    public function setColumns($columns)
    {
        $this->_columns = $columns;
    }

    public function getTable()
    {
        return $this->_table;
    }

    public function getColumns()
    {
        return $this->_columns;
    }

    public function getQuery($implode = false)
    {
        if ($implode) {
            return implode(PHP_EOL, $this->_query);
        }

        return $this->_query;
    }

    public function addQuery($query)
    {
        $this->_query[] = $query;
    }

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

    protected function removeFilteredValues($data, $options = [])
    {
        if (empty($options['filter_columns'])) {
            return $data;
        }

        foreach ($data as $key => $values) {
            if (in_array($key + 1, $options['filter_columns'])) {
                unset($data[$key]);
            }
        }

        return $data ?: [];
    }

    public function setFile($file)
    {
        $this->_file = $file;
    }

    public function getFile()
    {
        return $this->_file;
    }

    public function createFileFromUpload($filePath, $folderPath, $fileName = 'dump')
    {
        if (!file_exists($folderPath)) {
            mkdir($folderPath);
            @chmod($folderPath, 0777);
        }

        $timestamp = time();
        $fileName = "{$fileName}_{$timestamp}.csv";
        $newFilePath = "{$folderPath}/{$fileName}";

        move_uploaded_file($filePath, $newFilePath);

        $this->setFile($newFilePath);
    }

    public function saveSqlToFile($sql, $filename = 'dump')
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
