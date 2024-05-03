<h1>
    <!-- Título que muestra los tags con los que están etiquetados los marcadores -->
    Bookmarks tagged with
    <?= $this->Text->toList(h($tags)) ?>
</h1>

<section>
<?php foreach ($bookmarks as $bookmark): ?>
    <article>
        <!-- Enlace generado con el HtmlHelper que lleva al URL del marcador -->
        <h4><?= $this->Html->link($bookmark->title, $bookmark->url) ?></h4>
        <!-- Muestra la URL del marcador -->
        <small><?= h($bookmark->url) ?></small>

        <!-- El TextHelper se utiliza para formatear el texto, en este caso, el description del marcador -->
        <?= $this->Text->autoParagraph(h($bookmark->description)) ?>
    </article>
<?php endforeach; ?>
</section>
