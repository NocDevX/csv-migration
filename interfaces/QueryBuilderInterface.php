<?php

namespace interfaces;

interface QueryBuilderInterface
{
    public function setTable($tablename);

    public function setColumns($columns);

    public function getTable();

    public function getColumns();

    public function addQuery($query);

    public function getQuery();

    public function setFile($file);

    public function getFile();
}
