<nav class="p-4 flex space-x-8 bg-blue-500 items-center justify-between">
    <div class="flex space-x-6 items-center">
        <a class="text-2xl font-semibold" href="<?php echo $this->route('admin.dashboard'); ?>">PHP MVC FRAMEWORK</a>
        <ul class="flex space-x-4 text-xl">
            <li><a href="<?php echo $this->route('admin.dashboard'); ?>">Dashboard</a></li>
        </ul>
    </div>

    <div class="flex space-x-4">
        <form action="<?php echo $this->route('auth.logout'); ?>" method="post">
            <?php $this->csrf(); ?>
            <button type="submit">Déconnexion</button>
        </form>
    </div>
</nav>
