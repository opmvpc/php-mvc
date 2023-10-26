<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP MVC Framework</title>
</head>
<body>
    <header>
        <?php $this->includes('layouts/nav'); ?>
    </header>
    <main>
        <?php echo $contents; ?>
    </main>
</body>
</html>
