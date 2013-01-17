<div class="mails">

<section>
	<h3><?php echo __('Subject'); ?></h3>
	<p><?php echo h($mail['subject']); ?></p>
</section>

<section>
	<h3><?php echo __('Body'); ?></h3>
	<p><?php echo nl2br(h($mail['body']), true); ?></p>
</section>

<section>
	<h3><?php echo __('Attachments'); ?></h3>
	<?php foreach($mail['attach'] as $i => $at): ?>
	<p><?php printf ("%d. %s", $i, $at['filename']); ?></p>
	<?php endforeach; ?>
</section>

</div>
