<?php $this->extends('layouts/guest/app'); ?>

<h1>
    <?php echo $this->escape($article->title()); ?>
</h1>

<p><?php echo $this->escape($article->content()); ?></p>

<div class="flex space-x-4">
    <a href="<?php echo $this->route('articles.index'); ?>">Retour à la liste des articles</a>
</div>
