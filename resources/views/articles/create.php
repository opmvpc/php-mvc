<?php $this->extends('layouts/guest'); ?>

<h1>
    Créer un article
</h1>

<form action="<?php echo $this->route('articles.store'); ?>?test=coucou" method="post" class="flex flex-col space-y-4">
    <div class="flex flex-col space-y-1">
        <?php $this->includes('components/label', [
            'for' => 'title',
            'text' => 'Titre',
        ]); ?>
        <?php $this->includes('components/input', [
            'name' => 'title',
            'id' => 'title',
        ]); ?>
        <?php $this->includes('components/input_error', [
            'message' => 'content',
        ]); ?>
    </div>

    <div class="flex flex-col space-y-1">
        <?php $this->includes('components/label', [
            'for' => 'content',
            'text' => 'Contenu',
        ]); ?>
        <?php $this->includes('components/textarea', [
            'name' => 'content',
            'id' => 'content',
            'rows' => 10,
        ]); ?>
        <?php $this->includes('components/input_error', [
            'message' => 'content',
        ]); ?>
    </div>

    <div>
        <?php $this->includes('components/btn', [
            'text' => 'Créer',
        ]); ?>
    </div>
</form>
