<?php $this->extends('layouts/guest'); ?>

<h1>
    Créer un article
</h1>

<form action="<?php $this->route('articles.store'); ?>" method="post">
    <div>
        <label for="title">Titre</label>
        <input type="text" name="title" id="title" />

    </div>

    <div>
        <label for="content">Contenu</label>
        <textarea name="content" id="content" cols="30" rows="10"></textarea>
    </div>

    <div>
        <button type="submit">Créer</button>
    </div>
</form>
