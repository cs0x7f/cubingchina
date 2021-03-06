<div class="col-lg-12">
  <?php $this->widget('GridView', array(
    'dataProvider'=>$model->search(),
    // 'filter'=>false,
    'enableSorting'=>false,
    'front'=>true,
    'emptyText'=>Yii::t('Competition', 'No competitions now.'),
    'rowCssClassExpression'=>'$data->isEnded() ? "active" : "info"',
    'columns'=>array(
      array(
        'name'=>'date',
        'type'=>'raw',
        'value'=>'$data->getDisplayDate()',
      ),
      array(
        'name'=>'name',
        'type'=>'raw',
        'value'=>'$data->getCompetitionLink()',
      ),
      array(
        'name'=>'province_id',
        'type'=>'raw',
        'value'=>'$data->province->getAttributeValue("name")',
      ),
      array(
        'name'=>'city_id',
        'type'=>'raw',
        'value'=>'$data->city->getAttributeValue("name")',
      ),
      array(
        'name'=>'venue',
        'type'=>'raw',
        'value'=>'$data->getAttributeValue("venue")',
      ),
    ),
  )); ?>
</div>