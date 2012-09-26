<div class="maps view">
<h2><?php  echo __('Map'); ?></h2>
	<dl>
		<dt><?php echo __('Id'); ?></dt>
		<dd>
			<?php echo h($map['Map']['id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Is Active'); ?></dt>
		<dd>
			<?php echo h($map['Map']['is_active']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Sender Email Id'); ?></dt>
		<dd>
			<?php echo h($map['Map']['sender_email_id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Receiver Email Id'); ?></dt>
		<dd>
			<?php echo h($map['Map']['receiver_email_id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Updated'); ?></dt>
		<dd>
			<?php echo h($map['Map']['updated']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Created'); ?></dt>
		<dd>
			<?php echo h($map['Map']['created']); ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit Map'), array('action' => 'edit', $map['Map']['id'])); ?> </li>
		<li><?php echo $this->Form->postLink(__('Delete Map'), array('action' => 'delete', $map['Map']['id']), null, __('Are you sure you want to delete # %s?', $map['Map']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('List Maps'), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Map'), array('action' => 'add')); ?> </li>
	</ul>
</div>
