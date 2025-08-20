<?php
if (!isset($opcoes_menu)) {
    return;
}
?>

<nav>
    <ul class="menu">
        <?php foreach($opcoes_menu as $categoria => $arquivos): ?>
            <li class="dropdown">
                <a href="#"><?= ucfirst($categoria) ?></a>
                <ul class="dropdown-menu">
                    <?php foreach($arquivos as $arquivo): ?>
                        <li>
                            <a href="<?= $arquivo ?>">
                                <?= ucfirst(str_replace("_", " ", basename($arquivo, ".php"))) ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </li>
        <?php endforeach; ?>
    </ul>
</nav>
