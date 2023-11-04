<?php

$this->extends('layouts/guest/app');

?>

<div class="flex justify-between">
    <h1>Articles</h1>
</div>
<?php foreach ($articles as $article) { ?>
    <?php $this->includes('components/article_card', ['article' => $article, 'link' => $this->route('articles.show', ['articleId' => $article->id()])]); ?>
<?php } ?>
