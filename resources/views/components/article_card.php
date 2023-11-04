<a href="/articles/<?php echo $article->id(); ?>">
    <h2><?php echo $this->escape($article->title()); ?></h2>
    <p><?php echo $this->escape($article->content()); ?></p>
</a>
