<?php $this->renderPartial('side', $_data_); ?>
<div class="content-wrapper col-md-10 col-sm-9">
  <?php $this->widget('GridView', array(
    'dataProvider'=>$model->searchUser(),
    // 'filter'=>false,
    'enableSorting'=>false,
    'front'=>true,
    'emptyText'=>Yii::t('Competition', 'You have not registered for any competition.'),
    'columns'=>array(
      array(
        'name'=>'competition.name',
        'type'=>'raw',
        'value'=>'$data->competition->getCompetitionLink()',
      ),
      array(
        'name'=>'events',
        'type'=>'raw',
        'value'=>'$data->getRegistrationEvents()',
      ),
      array(
        'name'=>'fee',
        'type'=>'raw',
        'value'=>'$data->getRegistrationFee()',
      ),
      // array(
      //   'name'=>'competition.province_id',
      //   'type'=>'raw',
      //   'value'=>'$data->competition->province->getAttributeValue("name")',
      // ),
      // array(
      //   'name'=>'competition.city_id',
      //   'type'=>'raw',
      //   'value'=>'$data->competition->city->getAttributeValue("name")',
      // ),
      // array(
      //   'name'=>'competition.venue',
      //   'type'=>'raw',
      //   'value'=>'$data->competition->getAttributeValue("venue")',
      // ),
      array(
        'name'=>'date',
        'type'=>'raw',
        'value'=>'date("Y-m-d H:i", $data->date)',
      ),
      'comments',
      array(
        'name'=>'status',
        'type'=>'raw',
        'value'=>'$data->getStatusText()',
      ),
    ),
  )); ?>
</div>