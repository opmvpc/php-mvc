<?php $this->extends('layouts/auth/app'); ?>

<h1>
    <?php echo $article->title(); ?>
</h1>

<p><?php echo $article->content(); ?></p>

<div class="flex space-x-4">
    <a href="<?php echo $this->route('admin.articles.index'); ?>">Retour à la liste des articles</a>
    <form action="<?php echo $this->route('admin.articles.destroy', ['articleId' => $article->id()]); ?>" method="POST">
        <?php echo $this->csrf(); ?>
        <?php $this->includes('components/btn', ['text' => 'Supprimer']); ?>
    </form>
</div>
