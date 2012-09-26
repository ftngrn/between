<div class="maps form">
<?php echo $this->Form->create('Map'); ?>
	<fieldset>
		<legend><?php echo __('Add Map'); ?></legend>
	<?php
		echo $this->Form->input('is_active');
		echo $this->Form->input('sender_email_id');
		echo $this->Form->input('receiver_email_id');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('List Maps'), array('action' => 'index')); ?></li>
	</ul>
</div>
