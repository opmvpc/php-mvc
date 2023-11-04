<?php
use Framework\Support\Str;

$this->extends('layouts/auth/app');

?>

<div class="flex justify-between">
    <h1>Articles</h1>
    <div>
        <?php $this->includes('components/link', ['href' => $this->route('admin.articles.create'), 'text' => Str::__('create')]); ?>
    </div>
</div>
<?php foreach ($articles as $article) { ?>
    <?php $this->includes('components/article_card', ['article' => $article, 'link' => $this->route('admin.articles.show', ['articleId' => $article->id()])]); ?>
<?php } ?>
