<?php $this->extends('layouts/guest/app'); ?>

<h1>
    Créer un compte
</h1>

<form action="<?php echo $this->route('auth.register.store'); ?>" method="post" class="flex flex-col space-y-4">
    <?php $this->csrf(); ?>
    <div class="flex flex-col space-y-1">
        <?php $this->includes('components/label', [
            'text' => 'Nom',
            'for' => 'name',
        ]); ?>
        <?php $this->includes('components/input', [
            'type' => 'text',
            'name' => 'name',
            'value' => $this->old('name'),
        ]); ?>
        <?php $this->includes('components/input_error', [
            'key' => 'name',
        ]); ?>
    </div>

    <div class="flex flex-col space-y-1">
        <?php $this->includes('components/label', [
            'text' => 'Email',
            'for' => 'email',
        ]); ?>
        <?php $this->includes('components/input', [
            'type' => 'email',
            'name' => 'email',
            'value' => $this->old('email'),
        ]); ?>
        <?php $this->includes('components/input_error', [
            'key' => 'email',
        ]); ?>
    </div>

    <div class="flex flex-col space-y-1">
        <?php $this->includes('components/label', [
            'text' => 'Mot de passe',
            'for' => 'password',
        ]); ?>
        <?php $this->includes('components/input', [
            'type' => 'password',
            'name' => 'password',
            'value' => $this->old('password'),
        ]); ?>
        <?php $this->includes('components/input_error', [
            'key' => 'password',
        ]); ?>
    </div>

    <div class="flex flex-col space-y-1">
        <?php $this->includes('components/label', [
            'text' => 'Confirmer le mot de passe',
            'for' => 'password_confirmation',
        ]); ?>
        <?php $this->includes('components/input', [
            'type' => 'password',
            'name' => 'password_confirmation',
            'value' => $this->old('password_confirmation'),
        ]); ?>
        <?php $this->includes('components/input_error', [
            'key' => 'password_confirmation',
        ]); ?>
    </div>

    <div class="flex space-x-4 pt-4">
        <?php $this->includes('components/btn', [
            'text' => 'Créer',
        ]); ?>

        <a href="<?php echo $this->route('auth.login.show'); ?>" class="btn btn-secondary">
            Déjà inscrit ?
        </a>
    </div>
</form>
