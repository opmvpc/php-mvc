<?php $this->extends('layouts/guest'); ?>

<h1>Articles</h1>
<?php foreach ($articles as $article) { ?>
    <?php $this->includes('components/article_card', ['article' => $article]); ?>
<?php } ?>
