<head>
    <link rel="stylesheet" href="./style/styles.css" />
</head>

<body>
    <form method="POST" action="bootstrap.php" enctype="multipart/form-data">
        <div class="container">
            <select name="encoding" class="select">
                <option value="UTF8">UTF-8</option>
                <option value="ISO8859-1">LATIN1</option>
            </select>

            <input type="file" name="file" />
        </div>

        <div class="container">
            <button value="1" name="gerar_sql" type="submit">
                Gerar Sql
            </button>
        </div>
    </form>
</body>