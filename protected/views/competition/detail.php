<div class="col-lg-12 competition-<?php echo strtolower($competition->type); ?>">
	<dl>
		<?php if ($competition->type == Competition::TYPE_WCA): ?>
		<dt><?php echo Yii::t('Competition', 'WCA Competition'); ?></dt>
		<dd>
			<?php echo Yii::t('Competition', 'This competition is recognized as an official World Cube Association competition. Therefore, all competitors should be familiar with the {regulations}.', array(
			'{regulations}'=>CHtml::link(Yii::t('Competition', 'WCA regulations'), $competition->getWcaRegulationUrl(), array('target'=>'_blank')),
		));?>
		</dd>
		<?php endif; ?>
		<?php if ($competition->wca_competition_id != ''): ?>
		<dt><?php echo Yii::t('Competition', 'WCA Official Page'); ?></dt>
		<dd><?php echo CHtml::link($competition->getWcaUrl(), $competition->getWcaUrl(), array('target'=>'_blank')); ?>
		<?php endif; ?>
		<dt><?php echo Yii::t('Competition', 'Date'); ?></dt>
		<dd><?php echo date('Y-m-d', $competition->date) . ($competition->end_date > 0 ? '~' . date('Y-m-d', $competition->end_date) : ''); ?></dd>
		<dt><?php echo Yii::t('Competition', 'Location'); ?></dt>
		<dd>
			<?php if ($this->isCN): ?>
			<?php echo $competition->province->getAttributeValue('name');?><?php echo $competition->city->getAttributeValue('name');?><?php echo $competition->getAttributeValue('venue'); ?>
			<?php else: ?>
			<?php echo $competition->getAttributeValue('venue'); ?>, <?php echo $competition->city->getAttributeValue('name');?>, <?php echo $competition->province->getAttributeValue('name');?>
			<?php endif; ?>
		</dd>
		<dt><?php echo Yii::t('Competition', 'Organizers'); ?></dt>
		<dd>
			<?php foreach ($competition->organizer as $key=>$organizer): ?>
			<?php if ($key > 0) echo Yii::t('common', ', '); ?>
			<span><?php echo CHtml::mailto('<i class="fa fa-envelope"></i> ' . $organizer->user->getAttributeValue('name'), $organizer->user->email); ?></span>
			<?php endforeach; ?>
		</dd>
		<?php if ($competition->delegate !== array()): ?>
		<dt><?php echo Yii::t('Competition', 'Delegates'); ?></dt>
		<dd>
			<?php foreach ($competition->delegate as $key=>$delegate): ?>
			<?php if ($key > 0) echo Yii::t('common', ', '); ?>
			<span><?php echo CHtml::mailto('<i class="fa fa-envelope"></i> ' . $delegate->user->getAttributeValue('name'), $delegate->user->email); ?></span>
			<?php endforeach; ?>
		</dd>
		<?php endif; ?>
		<dt><?php echo Yii::t('Competition', 'Events'); ?></dt>
		<dd>
			<?php echo implode(Yii::t('common', ', '), array_map(function($event) use ($competition) {
				return Yii::t('event', $competition->getFullEventName($event));
			}, array_keys($competition->getRegistrationEvents()))); ?>
		</dd>
		<dt><?php echo Yii::t('Competition', 'Base Entry Fee'); ?></dt>
		<dd><?php echo $competition->entry_fee; ?></dd>
		<dt><?php echo Yii::t('Competition', 'Registration Ending Time'); ?></dt>
		<dd><?php echo date('Y-m-d 23:59:59', $competition->reg_end_day); ?></dd>
		<?php if (trim(strip_tags($competition->getAttributeValue('information'))) != ''): ?>
		<dt><?php echo Yii::t('Competition', 'About the Competition'); ?></dt>
		<dd>
			<?php echo $competition->getAttributeValue('information'); ?>
		</dd>
		<?php endif; ?>
	</dl>
</div>