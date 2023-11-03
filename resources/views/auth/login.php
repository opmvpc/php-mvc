<?php $this->extends('layouts/guest/app'); ?>

<h1>
    Connexion
</h1>

<form action="<?php echo $this->route('auth.login.auth'); ?>" method="post" class="flex flex-col space-y-4">
    <?php $this->csrf(); ?>

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

    <div class="flex space-x-4 pt-4">
        <?php $this->includes('components/btn', [
            'text' => 'Se connecter',
        ]); ?>

        <a href="<?php echo $this->route('auth.register.show'); ?>" class="btn btn-secondary">
            Pas encore inscrit ?
        </a>
    </div>
</form>
