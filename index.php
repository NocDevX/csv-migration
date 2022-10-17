<?php
// Function prepareCsv - Arrumar em um array para tratar com mais facilidade na montagem

namespace classes;

require_once(__DIR__ . '/util/utils.php');
require_once(__DIR__ . '/classes/Requisicao.php');

$file = fopen(__DIR__ . '/csv/example.csv', 'r');
$requisicao = new Requisicao;

$line = '';
$separator = ',';

$requisicao->setTable('dummy_table');
$requisicao->setColunas(['col1', 'col2']);
$condicoes = [];

$counter = 1;

while (!feof($file)) {
    $line = fgets($file);

    if (empty($line)) {
        continue;
    }

    // if ($counter === 1) {
    //     $counter++;
    //     continue;
    // }

    $line = explode($separator, $line);

    $condicoes[] = "1 = 1";
    $requisicao->addUpdate($line, $condicoes);

    $condicoes = [];
}

if (isset($_POST['gerar_sql'])) {
    $query = $requisicao->getQuery('update');
    $fileName = $requisicao->sqlToFile($query);
}

fclose($file);
?>

<form action=<?= $_SERVER['PHP_SELF'] ?> method="POST">
    <input type="hidden" name="gerar_sql" value="1" />
    <button type="submit" style='padding:5px;margin:10px;text-decoration:none;color:#efefef;background:#333;'>
        Gerar Sql
    </button>
</form>

<!-- "<a href='/migration/output/{$fileName}' style='padding:5px;margin:10px;text-decoration:none;color:#efefef;background:#333'>Sql Gerado</a>"; -->
<!-- "<a href='/migration/output' style='padding:5px;margin:10px;text-decoration:none;color:#333;background:#bbb'>Lista Sql</a>"; -->