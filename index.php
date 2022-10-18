<?php

namespace classes;

require_once(__DIR__ . '/util/utils.php');
require_once(__DIR__ . '/classes/Request.php');

$filePath = __DIR__ . '/csv/example.csv';

$options = [];
$options['separator'] = ',';
$options['skip_rows'] = [1];
// $options['columns'] = [2];
$options['conditions'] = [
    'id_item' => '$1$',
    'id_me' => '$1$',
];

$requisicao = new Request;
$requisicao->setTable('db_itensmenu');
$requisicao->setColumns(['id_item', 'help', 'test']);

if (isset($_POST['gerar_sql'])) {
    $requisicao->buildFromCsv($filePath, $options);

    $query = $requisicao->getQuery('update');
    $fileName = $requisicao->sqlToFile($query);
}
?>

<form action=<?= $_SERVER['PHP_SELF'] ?> method="POST">
    <input type="hidden" name="gerar_sql" value="1" />
    <button type="submit" style='padding:5px;margin:10px;text-decoration:none;color:#efefef;background:#333;'>
        Gerar Sql
    </button>
</form>

<!-- "<a href='/migration/output/{$fileName}' style='padding:5px;margin:10px;text-decoration:none;color:#efefef;background:#333'>Sql Gerado</a>"; -->
<!-- "<a href='/migration/output' style='padding:5px;margin:10px;text-decoration:none;color:#333;background:#bbb'>Lista Sql</a>"; -->