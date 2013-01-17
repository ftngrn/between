<div class="mails form">
<?php echo $this->Form->create('Mail'); ?>
	<fieldset>
		<legend><?php echo __('Add Mail'); ?></legend>
	<?php
		echo $this->Form->input('hash');
		echo $this->Form->input('from_user_id');
		echo $this->Form->input('to_user_id');
		echo $this->Form->input('uid');
		echo $this->Form->input('source');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('List Mails'), array('action' => 'index')); ?></li>
	</ul>
</div>
