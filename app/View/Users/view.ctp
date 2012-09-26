<div class="users view">
<h2><?php  echo __('User'); ?></h2>
	<dl>
		<dt><?php echo __('Id'); ?></dt>
		<dd>
			<?php echo h($user['User']['id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Username'); ?></dt>
		<dd>
			<?php echo h($user['User']['username']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Password'); ?></dt>
		<dd>
			<?php echo h($user['User']['password']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Disp Name'); ?></dt>
		<dd>
			<?php echo h($user['User']['disp_name']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Updated'); ?></dt>
		<dd>
			<?php echo h($user['User']['updated']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Created'); ?></dt>
		<dd>
			<?php echo h($user['User']['created']); ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit User'), array('action' => 'edit', $user['User']['id'])); ?> </li>
		<li><?php echo $this->Form->postLink(__('Delete User'), array('action' => 'delete', $user['User']['id']), null, __('Are you sure you want to delete # %s?', $user['User']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('List Users'), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New User'), array('action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List User Emails'), array('controller' => 'user_emails', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New User Email'), array('controller' => 'user_emails', 'action' => 'add')); ?> </li>
	</ul>
</div>
<div class="related">
	<h3><?php echo __('Related User Emails'); ?></h3>
	<?php if (!empty($user['UserEmail'])): ?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php echo __('Id'); ?></th>
		<th><?php echo __('User Id'); ?></th>
		<th><?php echo __('Email'); ?></th>
		<th><?php echo __('Memo'); ?></th>
		<th><?php echo __('Updated'); ?></th>
		<th><?php echo __('Created'); ?></th>
		<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($user['UserEmail'] as $userEmail): ?>
		<tr>
			<td><?php echo $userEmail['id']; ?></td>
			<td><?php echo $userEmail['user_id']; ?></td>
			<td><?php echo $userEmail['email']; ?></td>
			<td><?php echo $userEmail['memo']; ?></td>
			<td><?php echo $userEmail['updated']; ?></td>
			<td><?php echo $userEmail['created']; ?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View'), array('controller' => 'user_emails', 'action' => 'view', $userEmail['id'])); ?>
				<?php echo $this->Html->link(__('Edit'), array('controller' => 'user_emails', 'action' => 'edit', $userEmail['id'])); ?>
				<?php echo $this->Form->postLink(__('Delete'), array('controller' => 'user_emails', 'action' => 'delete', $userEmail['id']), null, __('Are you sure you want to delete # %s?', $userEmail['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New User Email'), array('controller' => 'user_emails', 'action' => 'add')); ?> </li>
		</ul>
	</div>
</div>
