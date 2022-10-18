<?php

namespace classes;

use Exception;

require_once(__DIR__ . '/classes/Builder.php');

if (empty($_FILES['file']['tmp_name'])) {
    throw new Exception('Arquivo não encontrado!');
}

$builder = new Builder;
$builder = $builder->getBuilderInstance('insert');

$tmpFilePath = $_FILES['file']['tmp_name'];
$folderPath = __DIR__ . '/tmp/';
$fileName = empty($_FILES['file']['name']) ? $_FILES['file']['name'] : $_FILES['file']['name'];

$options = [];
$options['conditions'] = [
    'id_item' => '$1$',
    'id_me' => '$2$',
];
$options['separator'] = ';';

$builder->createFileFromUpload($tmpFilePath, $folderPath, $fileName);
$builder->setTable('db_itensmenu');
$builder->setColumns(['id_item', 'help']);

if (isset($_POST['gerar_sql'])) {
    $builder->buildQueryFromCsv($options);
    $query = $builder->getQuery(true);
    $builder->saveSqlToFile($query);
}
