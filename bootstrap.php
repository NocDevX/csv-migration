<?php

namespace classes;

use Exception;

require_once(__DIR__ . '/classes/Builder.php');

if (empty($_FILES['file']['tmp_name'])) {
    throw new Exception('Arquivo não fornecido!');
}

$builder = new Builder;
$builder = $builder->getBuilderInstance('insert');

$tmpFilePath = $_FILES['file']['tmp_name'];
$fileName = $_FILES['file']['name'];

$options = [];
$options['conditions'] = [
    'id_item' => '$1$',
    'id_me' => '$2$',
];
$options['separator'] = ';';

$builder->createFileFromUpload($tmpFilePath, $fileName);
$builder->setTable('db_itensmenu');
$builder->setColumns(['id_item', 'help']);

if (isset($_POST['gerar_sql'])) {
    $builder->buildQueryFromCsv($options);
    $query = $builder->getQuery(true);
    $sqlFileName = $builder->saveSqlToFile($query);
}

$sqlFileName = empty($sqlFileName) ? '' : $sqlFileName;
?>

<head>
    <link rel="stylesheet" href="./style/styles.css"/>
</head>

<body>
    <div class="container">
        <a class="file" href="<?= $sqlFileName ? "output/{$sqlFileName}" : '' ?>"><?= $sqlFileName ? $sqlFileName : 'Nenhum arquivo foi gerado.' ?></a>
        <a class="home" href="index.php">Voltar ao início</a>
    </div>
</body>