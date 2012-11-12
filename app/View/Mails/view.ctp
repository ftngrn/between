<div class="mails view">
<h2><?php  echo __('Mail'); ?></h2>
	<dl>
		<dt><?php echo __('Id'); ?></dt>
		<dd>
			<?php echo h($mail['Mail']['id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('User'); ?></dt>
		<dd>
			<?php echo $this->Html->link($mail['User']['disp_name'], array('controller' => 'users', 'action' => 'view', $mail['User']['id'])); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('User Email'); ?></dt>
		<dd>
			<?php echo $this->Html->link($mail['UserEmail']['email'], array('controller' => 'user_emails', 'action' => 'view', $mail['UserEmail']['id'])); ?>
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
		<li><?php echo $this->Html->link(__('List Users'), array('controller' => 'users', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New User'), array('controller' => 'users', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List User Emails'), array('controller' => 'user_emails', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New User Email'), array('controller' => 'user_emails', 'action' => 'add')); ?> </li>
	</ul>
</div>
