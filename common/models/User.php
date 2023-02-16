<?php

namespace common\models;

use common\models\staff\Employee;
use dektrium\user\models\Token;
use Yii;
use yii\helpers\Html;

/**
 * @property string $Host [char(60)]
 * @property string $User [char(80)]
 * @property string $Password [char(41)]
 * @property string $Select_priv [enum('N', 'Y')]
 * @property string $Insert_priv [enum('N', 'Y')]
 * @property string $Update_priv [enum('N', 'Y')]
 * @property string $Delete_priv [enum('N', 'Y')]
 * @property string $Create_priv [enum('N', 'Y')]
 * @property string $Drop_priv [enum('N', 'Y')]
 * @property string $Reload_priv [enum('N', 'Y')]
 * @property string $Shutdown_priv [enum('N', 'Y')]
 * @property string $Process_priv [enum('N', 'Y')]
 * @property string $File_priv [enum('N', 'Y')]
 * @property string $Grant_priv [enum('N', 'Y')]
 * @property string $References_priv [enum('N', 'Y')]
 * @property string $Index_priv [enum('N', 'Y')]
 * @property string $Alter_priv [enum('N', 'Y')]
 * @property string $Show_db_priv [enum('N', 'Y')]
 * @property string $Super_priv [enum('N', 'Y')]
 * @property string $Create_tmp_table_priv [enum('N', 'Y')]
 * @property string $Lock_tables_priv [enum('N', 'Y')]
 * @property string $Execute_priv [enum('N', 'Y')]
 * @property string $Repl_slave_priv [enum('N', 'Y')]
 * @property string $Repl_client_priv [enum('N', 'Y')]
 * @property string $Create_view_priv [enum('N', 'Y')]
 * @property string $Show_view_priv [enum('N', 'Y')]
 * @property string $Create_routine_priv [enum('N', 'Y')]
 * @property string $Alter_routine_priv [enum('N', 'Y')]
 * @property string $Create_user_priv [enum('N', 'Y')]
 * @property string $Event_priv [enum('N', 'Y')]
 * @property string $Trigger_priv [enum('N', 'Y')]
 * @property string $Create_tablespace_priv [enum('N', 'Y')]
 * @property string $Delete_history_priv [enum('N', 'Y')]
 * @property string $ssl_type [enum('', 'ANY', 'X509', 'SPECIFIED')]
 * @property string $ssl_cipher [blob]
 * @property string $x509_issuer [blob]
 * @property string $x509_subject [blob]
 * @property int $max_questions [int(11) unsigned]
 * @property int $max_updates [int(11) unsigned]
 * @property int $max_connections [int(11) unsigned]
 * @property int $max_user_connections [int(11)]
 * @property string $plugin [char(64)]
 * @property string $authentication_string
 * @property string $password_expired [enum('N', 'Y')]
 * @property Token $token
 *
 * @property Client $client
 * @property Employee $employee
 * @property-read string $name
 * @property int $password_last_changed [timestamp]
 * @property int $password_lifetime [smallint(5) unsigned]
 * @property string $account_locked [enum('N', 'Y')]
 */

class User extends \dektrium\user\models\User
{
	public $password = '';

	public static function tableName()
	{
		return '{{%user}}';
	}

	public function getClient()
	{
		return $this->hasOne(Client::class, ["user_id" => "id"]);
	}

	public function getEmployee()
	{
		return $this->hasOne(Employee::class, ['user_id' => 'id']);
	}

	public function getType()
	{
		if ( isset($this->client) ) {
			return $this->client;
		} else if (isset($this->employee)) {
			return $this->employee;
		} else {
			return null;
		}
	}

	public function getToken()
	{
		return $this->hasOne(Token::class, ['user_id' => 'id']);
	}

	public function isClient()
	{
		return Client::findOne(["user_id" => $this->id]) !== null;
	}

	public function getName()
	{
		if ( !empty($this->profile->name) ) {
			return $this->profile->name;
		}
		if ( $this->isClient() ) {
			return $this->client->name;
		}
		if ( $this->employee ) {
			return $this->employee->getFullname();
		}
		return $this->username;
	}

    public static function findIdentityByAccessToken($code, $type = null)
    {
        $token = Token::findOne(['code' => $code]);
	return static::findOne($token->user_id);
    }
}