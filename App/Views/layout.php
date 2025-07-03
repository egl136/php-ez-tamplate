<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= $title ?? 'My Site' ?></title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <header>
        <h1>My Template</h1>
    </header>

    <main>
        <?= $content ?>
    </main>

    <footer>
        &copy; <?= date('Y') ?>
    </footer>
</body>
</html>
