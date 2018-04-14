<?php
/**
 * This file is part of CMSGears Framework. Please view License file distributed
 * with the source code for license details.
 *
 * @link https://www.cmsgears.org/
 * @copyright Copyright (c) 2015 VulpineCode Technologies Pvt. Ltd.
 */

// CMG Imports
use cmsgears\core\common\config\CoreGlobal;

use cmsgears\core\common\base\Migration;

use cmsgears\core\common\models\entities\Site;
use cmsgears\core\common\models\entities\User;
use cmsgears\core\common\models\resources\Form;
use cmsgears\core\common\models\resources\FormField;

use cmsgears\core\common\utilities\DateUtil;

/**
 * The google adsense migration inserts the base data required to manage ads from google.
 *
 * @since 1.0.0
 */
class m180414_021215_google_adsense extends Migration {

	// Public Variables

	// Private Variables

	private $prefix;

	private $site;
	private $master;

	private $uploadsDir;
	private $uploadsUrl;

	public function init() {

		// Table prefix
		$this->prefix	= Yii::$app->migration->cmgPrefix;

		$this->site		= Site::findBySlug( CoreGlobal::SITE_MAIN );
		$this->master	= User::findByUsername( Yii::$app->migration->getSiteMaster() );

		$this->uploadsDir	= Yii::$app->migration->getUploadsDir();
		$this->uploadsUrl	= Yii::$app->migration->getUploadsUrl();

		Yii::$app->core->setSite( $this->site );
	}

	public function up() {

		// Create various config
		$this->insertFileConfig();

		// Init default config
		$this->insertDefaultConfig();
	}

	private function insertFileConfig() {

		$this->insert( $this->prefix . 'core_form', [
			'siteId' => $this->site->id,
			'createdBy' => $this->master->id, 'modifiedBy' => $this->master->id,
			'name' => 'Config File', 'slug' => 'config-file',
			'type' => CoreGlobal::TYPE_SYSTEM,
			'description' => 'File configuration form.',
			'success' => 'All configurations saved successfully.',
			'captcha' => false,
			'visibility' => Form::VISIBILITY_PROTECTED,
			'status' => Form::STATUS_ACTIVE, 'userMail' => false, 'adminMail' => false,
			'createdAt' => DateUtil::getDateTime(),
			'modifiedAt' => DateUtil::getDateTime()
		] );

		$config = Form::findBySlugType( 'config-file', CoreGlobal::TYPE_SYSTEM );

		$columns = [ 'formId', 'name', 'label', 'type', 'compress', 'meta', 'active', 'validators', 'order', 'icon', 'htmlOptions' ];

		$fields = [
			[ $config->id, 'active', 'Active', FormField::TYPE_TOGGLE, false, true, true, 'required', 0, NULL, '{"title":"Active"}' ],
			[ $config->id, 'publisher_id', 'Publisher Id', FormField::TYPE_TEXT, false, true, true, 'required', 0, NULL, '{"title":"Publisher Id","placeholder":"Publisher Id"}' ],
			[ $config->id, 'ad_unit_types', 'Ad Unit Types', FormField::TYPE_SELECT, false, false, true, NULL, 0, NULL, '{"title":"Ad Unit Type","items":{"none":"Choose Unit Type"}}' ],
			[ $config->id, 'ad_unit_sizes', 'Ad Unit Sizes', FormField::TYPE_SELECT, false, false, true, NULL, 0, NULL, '{"title":"Ad Unit Size","items":{"none":"Choose Unit Size"}}' ],
			[ $config->id, 'ad_unit_form', 'Ad Unit Form', FormField::TYPE_TEXT, false, false, true, 'required', 0, NULL, '{"title":"Document Extensions","placeholder":"Document Extensions"}' ],
			[ $config->id, 'ad_units', 'Ad Units', FormField::TYPE_TEXT, false, false, true, 'required', 0, NULL, '{"title":"Document Extensions","placeholder":"Document Extensions"}' ]
		];

		$this->batchInsert( $this->prefix . 'core_form_field', $columns, $fields );
	}

	private function insertDefaultConfig() {

		$columns = [ 'modelId', 'name', 'label', 'type', 'active', 'valueType', 'value', 'data' ];

		$metas = [
			[ $this->site->id, 'active', 'Active', 'google-adsense', 1, 'flag', '1', NULL ],
			[ $this->site->id, 'publisher_id', 'Publisher Id', 'google-adsense', 1, 'text', NULL, NULL ],
			[ $this->site->id, 'ad_units', 'Ad Units', 'google-adsense', 1, 'card', NULL, NULL ]
		];

		$this->batchInsert( $this->prefix . 'core_site_meta', $columns, $metas );
	}

	public function down() {

		echo "m160622_061028_file_manager will be deleted with m160621_014408_core.\n";

		return true;
	}

}
