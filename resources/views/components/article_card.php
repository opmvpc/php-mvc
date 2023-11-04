<?php
$link ?? throw new \InvalidArgumentException('Missing URL parameter');
?>

<a href="<?php echo $link; ?>">
    <h2><?php echo $this->escape($article->title()); ?></h2>
    <p><?php echo $this->escape($article->content()); ?></p>
</a>
