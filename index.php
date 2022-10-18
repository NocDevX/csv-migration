<?php
namespace classes;

require_once(__DIR__ . '/util/utils.php');
require_once(__DIR__ . '/classes/Requisicao.php');

$filePath = __DIR__ . '/csv/example.csv';

$options = [];
$options['separator'] = ',';
$options['skip_rows'] = [1];
// $options['columns'] = [2];
// $options['condicoes'] = ['item > 0'];

$requisicao = new Requisicao;
$requisicao->setTable('dummy_table');
$requisicao->setColumns(['item', 'valor', 'id']);
$requisicao->buildFromCsv($filePath, $options);

if (isset($_POST['gerar_sql'])) {
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