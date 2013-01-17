<div class="mails view">
<h2><?php  echo __('Mail'); ?></h2>
	<dl>
		<dt><?php echo __('Id'); ?></dt>
		<dd>
			<?php echo h($mail['Mail']['id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Hash'); ?></dt>
		<dd>
			<?php echo h($mail['Mail']['hash']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('From User Id'); ?></dt>
		<dd>
			<?php echo h($mail['Mail']['from_user_id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('To User Id'); ?></dt>
		<dd>
			<?php echo h($mail['Mail']['to_user_id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Uid'); ?></dt>
		<dd>
			<?php echo h($mail['Mail']['uid']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Source'); ?></dt>
		<dd>
			<?php echo h($mail['Mail']['source']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Created'); ?></dt>
		<dd>
			<?php echo h($mail['Mail']['created']); ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit Mail'), array('action' => 'edit', $mail['Mail']['id'])); ?> </li>
		<li><?php echo $this->Form->postLink(__('Delete Mail'), array('action' => 'delete', $mail['Mail']['id']), null, __('Are you sure you want to delete # %s?', $mail['Mail']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('List Mails'), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Mail'), array('action' => 'add')); ?> </li>
	</ul>
</div>
