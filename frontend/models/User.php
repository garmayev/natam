<?php

namespace frontend\models;

use Yii;

/**
 * @property Staff $staff
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
 * @property string $is_role [enum('N', 'Y')]
 * @property string $default_role [char(80)]
 * @property string $max_statement_time [decimal(12,6)]
 */

class User extends \dektrium\user\models\User
{
	public function beforeDelete()
	{
		if ( $this->staff ) {
			$this->staff->delete();
		}

		return parent::beforeDelete();
	}

	public function getStaff()
	{
		return $this->hasOne(Staff::className(), ["user_id" => "id"]);
	}

	public static function findByPhone($phone)
	{
		$staff = Staff::findOne(["phone" => $phone]);
		if ( $staff ) {
			return $staff->user;
		}
		return null;
	}

	public static function findByChatId($chat_id)
	{
		$staff = Staff::findOne(["chat_id" => $chat_id]);
		if ( $staff ) {
			return $staff->user;
		}
		return null;
	}

	public static function findByPhoneOrChatId($args)
	{
		$user = self::findByPhone($args);
		if ( !isset($user) ) {
			return $user;
		}
		return self::findByChatId($args);
	}
}