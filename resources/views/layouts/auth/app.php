<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP MVC Framework</title>
    <?php $this->viteAssets(); ?>
</head>
<body class="min-h-screen bg-blue-950">
    <header>
        <?php $this->includes('layouts/auth/nav'); ?>
    </header>
    <main class="my-16 p-4 mx-auto container bg-blue-300 rounded-md prose prose-zinc">
        <?php echo $contents; ?>
    </main>
</body>
</html>
