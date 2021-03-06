<?php

/**
 * This is the model class for table "user".
 *
 * The followings are the available columns in table 'user':
 * @property string $id
 * @property string $wcaid
 * @property string $name
 * @property string $name_zh
 * @property string $email
 * @property string $password
 * @property string $birthday
 * @property integer $gender
 * @property string $mobile
 * @property integer $country_id
 * @property integer $province_id
 * @property integer $city_id
 * @property integer $role
 * @property string $reg_time
 * @property string $reg_ip
 * @property integer $status
 */
class User extends ActiveRecord {

	const GENDER_MALE = 0;
	const GENDER_FEMALE = 1;

	const ROLE_UNCHECKED = 0;
	const ROLE_CHECKED = 1;
	const ROLE_ORGANIZER = 2;
	const ROLE_DELEGATE = 3;
	const ROLE_ADMINISTRATOR = 4;

	const STATUS_NORMAL = 0;
	const STATUS_BANNED = 1;
	const STATUS_DELETED = 2;

	private $_qqwry;
	private $_qqwryFile;

	public static function getDailyUser() {
		$data = Yii::app()->db->createCommand()
			->select('FROM_UNIXTIME(MIN(reg_time), "%Y-%m-%d") as day, COUNT(1) AS count')
			->from('user')
			->where('status=' . self::STATUS_NORMAL)
			->group('FROM_UNIXTIME(reg_time, "%Y-%m-%d")')
			->queryAll();
		return $data;
	}

	public static function getHourlyUser() {
		$data = Yii::app()->db->createCommand()
			->select('FROM_UNIXTIME(MIN(reg_time), "%k") as hour, COUNT(1) AS user ')
			->from('user')
			->where('status=' . self::STATUS_NORMAL)
			->group('FROM_UNIXTIME(reg_time, "%k")')
			->queryAll();
		return $data;
	}

	public static function getUserRegion() {
		$data = Yii::app()->db->createCommand()
			->select('CASE WHEN r.id IS NULL THEN "海外" ELSE r.name_zh END AS label, count(1) AS value')
			->from('user u')
			->where('u.status=' . self::STATUS_NORMAL)
			->leftJoin('region r', 'u.province_id=r.id')
			->group('u.province_id')
			->queryAll();
		// $other = array(
		// 	'label'=>'其他',
		// 	'value'=>0,
		// );
		// foreach ($data as $key=>$value) {
		// 	if ($value['value'] < 3 || $value['label'] == '其他') {
		// 		$other['value'] += $value['value'];
		// 		unset($data[$key]);
		// 	}
		// }
		// $data[] = $other;
		usort($data, function($a, $b) {
			return $b['value'] - $a['value'];
		});
		return $data;
	}

	public static function getUserGender() {
		$data = Yii::app()->db->createCommand()
			->select('gender AS label, COUNT(1) AS value')
			->from('user')
			->where('status=' . self::STATUS_NORMAL)
			->group('gender')
			->queryAll();
		$genders = self::getGenders();
		foreach ($data as $key=>$value) {
			$data[$key]['label'] = isset($genders[$value['label']]) ? $genders[$value['label']] : Yii::t('common', 'Unknown');
		}
		return $data;
	}

	public static function getUserAge() {
		$data = Yii::app()->db->createCommand()
			->select('FROM_UNIXTIME(UNIX_TIMESTAMP() - birthday, "%Y") - 1969 AS age, COUNT(1) AS count')
			->from('user')
			->where('status=' . self::STATUS_NORMAL)
			->group('FROM_UNIXTIME(UNIX_TIMESTAMP() - birthday, "%Y")')
			->queryAll();
		return $data;
	}

	public static function getOrganizers() {
		if (Yii::app()->user->checkAccess(self::ROLE_DELEGATE)) {
			$attributes = array(
				'role'=>array(
					self::ROLE_ORGANIZER,
					self::ROLE_DELEGATE,
					self::ROLE_ADMINISTRATOR,
				),
			);
		} else {
			$attributes = array(
				'id'=>Yii::app()->user->id,
			);
		}
		return self::model()->findAllByAttributes($attributes);
	}

	public static function getRoles() {
		return array(
			self::ROLE_UNCHECKED=>Yii::t('common', 'Inactive User'),
			self::ROLE_CHECKED=>Yii::t('common', 'Normal User'),
			self::ROLE_ORGANIZER=>Yii::t('common', 'Organizer'),
			self::ROLE_DELEGATE=>Yii::t('common', 'Delegate'),
			self::ROLE_ADMINISTRATOR=>Yii::t('common', 'Administrator'),
		);
	}

	public static function getGenders() {
		return array(
			self::GENDER_MALE=>Yii::t('common', 'Male'),
			self::GENDER_FEMALE=>Yii::t('common', 'Female'),
		);
	}

	public function getGenderText() {
		$genders = self::getGenders();
		return isset($genders[$this->gender]) ? $genders[$this->gender] : Yii::t('common', 'Unknown');
	}

	public function isUnchecked() {
		return $this->role == self::ROLE_UNCHECKED;
	}

	public function isOrganizer() {
		return $this->role == self::ROLE_ORGANIZER;
	}

	public function isBanned() {
		return $this->status != self::STATUS_NORMAL;
	}

	public function getRegIpDisplay() {
		if (!extension_loaded('qqwry') || !class_exists('qqwry', false)) {
			return $this->reg_ip;
		}
		$result = $this->getQQWRY()->q($this->reg_ip);
		return CHtml::tag('button', array(
			'class'=>'btn btn-xs btn-orange tips',
			'data-toggle'=>'tooltip',
			'data-placement'=>'left',
			'title'=>implode('|', array_map(function($a) {
				return iconv('gbk', 'utf-8', $a);
			}, $result)),
		), $this->reg_ip);
	}

	public function getQQWRY() {
		if ($this->_qqwry === null) {
			$this->_qqwry = new qqwry($this->getQQWRYFile());
		}
		return $this->_qqwry;
	}

	public function getQQWRYFile() {
		if ($this->_qqwryFile === null) {
			$this->_qqwryFile = Yii::getPathOfAlias('application.data.qqwry').'.dat';
		}
		return $this->_qqwryFile;
	}

	public function getRoleName() {
		$roles = self::getRoles();
		return isset($roles[$this->role]) ? $roles[$this->role] : Yii::t('common', 'Unknown');
	}

	public function getCompetitionName() {
		$name = $this->name;
		if ($this->name_zh != '') {
			$name .= " ({$this->name_zh})";
		}
		return $name;
	}

	public function getWcaGender() {
		return $this->gender == self::GENDER_FEMALE ? 'f' : 'm';
	}

	public function getWcaLink($name = null) {
		if ($name === null) {
			$name = $this->getCompetitionName();
		}
		if ($this->wcaid === '' || $name === '') {
			return $name;
		}
		return CHtml::link($name, 'https://www.worldcubeassociation.org/results/p.php?i=' . $this->wcaid, array('target'=>'_blank'));
	}

	public function getEmailLink() {
		return CHtml::mailto($this->email, $this->email);
	}

	public function getOperationButton() {
		$buttons = array();
		$buttons[] = CHtml::link('编辑', array('/board/user/edit', 'id'=>$this->id), array('class'=>'btn btn-xs btn-blue btn-square'));
		switch ($this->status) {
			case self::STATUS_BANNED:
				$buttons[] = CHtml::link('洗白', array('/board/user/enable', 'id'=>$this->id), array('class'=>'btn btn-xs btn-green btn-square'));
				break;
			case self::STATUS_NORMAL:
				$buttons[] = CHtml::link('拉黑', array('/board/user/disable', 'id'=>$this->id), array('class'=>'btn btn-xs btn-red btn-square'));
				$buttons[] = CHtml::link('删除', array('/board/user/delete', 'id'=>$this->id), array('class'=>'btn btn-xs btn-orange btn-square delete'));
				break;
			// case self::STATUS_DELETED:
			// 	$buttons[] = CHtml::link('恢复', array('/board/user/enable', 'id'=>$this->id), array('class'=>'btn btn-xs btn-purple btn-square'));
			// 	break;
		}
		return implode(' ', $buttons);
	}

	public function getRegionName($region) {
		return $region === null ? '' : $region->getAttributeValue('name');
	}

	public function handleDate() {
		foreach (array('birthday') as $attribute) {
			if ($this->$attribute != '') {
				$date = strtotime($this->$attribute);
				if ($date !== false) {
					$this->$attribute = $date;
				} else {
					$this->$attribute = 0;
				}
			} else {
				$this->$attribute = 0;
			}
		}
	}

	public function formatDate() {
		foreach (array('birthday') as $attribute) {
			if (!empty($this->$attribute)) {
				$this->$attribute = date('Y-m-d', $this->$attribute);
			} else {
				$this->$attribute = '';
			}
		}
	}

	public function getMailUrl($action) {
		$userAction = new UserAction();
		$userAction->user_id = $this->id;
		$userAction->action = $action;
		$userAction->date = time();
		$userAction->code = $userAction->generateCode();
		$userAction->save();
		switch ($action) {
			default:
				$url = Yii::app()->createUrl('/site/' . $action, array('c'=>$userAction->code));
				break;
		}
		return Yii::app()->request->getBaseUrl(true) . $url;
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName() {
		return 'user';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name, country_id, birthday, email, password, gender', 'required'),
			array('gender, country_id, province_id, city_id, role, status', 'numerical', 'integerOnly'=>true),
			array('wcaid', 'length', 'max'=>10),
			array('name, name_zh, email, password', 'length', 'max'=>128),
			array('birthday, mobile', 'length', 'max'=>20),
			array('reg_time', 'length', 'max'=>11),
			array('reg_ip', 'length', 'max'=>15),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, wcaid, name, name_zh, email, password, birthday, gender, mobile, country_id, province_id, city_id, role, reg_time, reg_ip, status', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations() {
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'country'=>array(self::BELONGS_TO, 'Region', 'country_id'),
			'province'=>array(self::BELONGS_TO, 'Region', 'province_id'),
			'city'=>array(self::BELONGS_TO, 'Region', 'city_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels() {
		return array(
			'id' => Yii::t('User', 'ID'),
			'wcaid' => Yii::t('User', 'Wcaid'),
			'name' => Yii::t('User', 'Name'),
			'name_zh' => Yii::t('User', 'Local Name'),
			'email' => Yii::t('User', 'Email'),
			'password' => Yii::t('User', 'Password'),
			'birthday' => Yii::t('User', 'Birthday'),
			'gender' => Yii::t('User', 'Gender'),
			'mobile' => Yii::t('User', 'Mobile'),
			'country_id' => Yii::t('User', 'Region'),
			'province_id' => Yii::t('User', 'Province'),
			'city_id' => Yii::t('User', 'City'),
			'role' => Yii::t('User', 'Role'),
			'reg_time' => Yii::t('User', 'Reg Time'),
			'reg_ip' => Yii::t('User', 'Reg Ip'),
			'status' => Yii::t('User', 'Status'),
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

		$criteria->compare('id',$this->id);
		$criteria->compare('wcaid',$this->wcaid,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('name_zh',$this->name_zh,true);
		$criteria->compare('email',$this->email,true);
		$criteria->compare('password',$this->password,true);
		$criteria->compare('birthday',$this->birthday,true);
		$criteria->compare('gender',$this->gender);
		$criteria->compare('mobile',$this->mobile,true);
		$criteria->compare('country_id',$this->country_id);
		$criteria->compare('province_id',$this->province_id);
		$criteria->compare('city_id',$this->city_id);
		$criteria->compare('role',$this->role);
		$criteria->compare('reg_time',$this->reg_time,true);
		$criteria->compare('reg_ip',$this->reg_ip,true);
		$criteria->compare('status',$this->status);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'sort'=>array(
				'defaultOrder'=>'reg_time DESC',
			),
			'pagination'=>array(
				'pageSize'=>50,
			),
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return User the static model class
	 */
	public static function model($className = __CLASS__) {
		return parent::model($className);
	}
}
