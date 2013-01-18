<div class="row">

<section class="span12">
	<h3><?php echo __('Subject'); ?></h3>
	<div><?php echo h($mail['subject']); ?></div>
</section>

<section class="span12">
	<h3><?php echo __('Body'); ?></h3>
	<div><?php echo nl2br($this->Text->autoLink($mail['body']), true); ?></div>
</section>

<section class="span12">
	<h3><?php echo __('Attachments'); ?></h3>
	<?php foreach($mail['attach'] as $i => $at): ?>
	<div class="row">
		<div class="span4">
			<?php echo $at['filename']; ?>
			<br />
			<img src="<?php	printf("data:%s;%s,", $at['mimetype'], $at['enc']); echo base64_encode($at['value']);	?>" class="img-polaroid" />
		</div>
	</div>
	<?php endforeach; ?>
</section>

</div>
