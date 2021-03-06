<?php

/**
 * This is the model class for table "Results".
 *
 * The followings are the available columns in table 'Results':
 * @property string $id
 * @property string $competitionId
 * @property string $eventId
 * @property string $roundId
 * @property integer $pos
 * @property integer $best
 * @property integer $average
 * @property string $personName
 * @property string $personId
 * @property string $countryId
 * @property string $formatId
 * @property integer $value1
 * @property integer $value2
 * @property integer $value3
 * @property integer $value4
 * @property integer $value5
 * @property string $regionalSingleRecord
 * @property string $regionalAverageRecord
 */
class Results extends ActiveRecord {
	/**
	 * @return string the associated database table name
	 */
	public function tableName() {
		return 'Results';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('pos, best, average, value1, value2, value3, value4, value5', 'numerical', 'integerOnly'=>true),
			array('competitionId', 'length', 'max'=>32),
			array('eventId', 'length', 'max'=>6),
			array('roundId, formatId', 'length', 'max'=>1),
			array('personName', 'length', 'max'=>80),
			array('personId', 'length', 'max'=>10),
			array('countryId', 'length', 'max'=>50),
			array('regionalSingleRecord, regionalAverageRecord', 'length', 'max'=>3),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, competitionId, eventId, roundId, pos, best, average, personName, personId, countryId, formatId, value1, value2, value3, value4, value5, regionalSingleRecord, regionalAverageRecord', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations() {
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels() {
		return array(
			'id' => Yii::t('Results', 'ID'),
			'competitionId' => Yii::t('Results', 'Competition'),
			'eventId' => Yii::t('Results', 'Event'),
			'roundId' => Yii::t('Results', 'Round'),
			'pos' => Yii::t('Results', 'Pos'),
			'best' => Yii::t('Results', 'Best'),
			'average' => Yii::t('Results', 'Average'),
			'personName' => Yii::t('Results', 'Person Name'),
			'personId' => Yii::t('Results', 'Person'),
			'countryId' => Yii::t('Results', 'Country'),
			'formatId' => Yii::t('Results', 'Format'),
			'value1' => Yii::t('Results', 'Value1'),
			'value2' => Yii::t('Results', 'Value2'),
			'value3' => Yii::t('Results', 'Value3'),
			'value4' => Yii::t('Results', 'Value4'),
			'value5' => Yii::t('Results', 'Value5'),
			'regionalSingleRecord' => Yii::t('Results', 'Regional Single Record'),
			'regionalAverageRecord' => Yii::t('Results', 'Regional Average Record'),
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search() {
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria = new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('competitionId',$this->competitionId,true);
		$criteria->compare('eventId',$this->eventId,true);
		$criteria->compare('roundId',$this->roundId,true);
		$criteria->compare('pos',$this->pos);
		$criteria->compare('best',$this->best);
		$criteria->compare('average',$this->average);
		$criteria->compare('personName',$this->personName,true);
		$criteria->compare('personId',$this->personId,true);
		$criteria->compare('countryId',$this->countryId,true);
		$criteria->compare('formatId',$this->formatId,true);
		$criteria->compare('value1',$this->value1);
		$criteria->compare('value2',$this->value2);
		$criteria->compare('value3',$this->value3);
		$criteria->compare('value4',$this->value4);
		$criteria->compare('value5',$this->value5);
		$criteria->compare('regionalSingleRecord',$this->regionalSingleRecord,true);
		$criteria->compare('regionalAverageRecord',$this->regionalAverageRecord,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * @return CDbConnection the database connection used for this class
	 */
	public function getDbConnection() {
		return Yii::app()->wcaDb;
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Results the static model class
	 */
	public static function model($className = __CLASS__) {
		return parent::model($className);
	}
}
