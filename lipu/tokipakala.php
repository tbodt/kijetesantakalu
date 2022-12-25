<?php if (!empty($toki_pakala)) { ?>
<p class="toki-pakala">
pakala a
<ul class="toki-pakala">
    <?php foreach ($toki_pakala as $pakala) { ?>
    <li><?= htmlentities($pakala) ?>
    <?php } ?>
</ul>
<?php } ?>
