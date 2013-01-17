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
	<ul>
		<li>
			<?php echo $at['filename']; ?>
			<br />
			<img src="<?php	printf("data:%s;%s,", $at['mimetype'], $at['enc']); echo base64_encode($at['value']);	?>" />
		</li>
	</ul>
	<?php endforeach; ?>
</section>

</div>
