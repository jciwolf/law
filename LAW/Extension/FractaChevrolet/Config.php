<?php
/**
 * @DESCRIPTION
 * 
 * 
 * @MODIFY
 * 13-11-19 下午3:59 create file
 * 
 * @author Kevin.Hu, Inc. <huwenhua@group-net.cn>
 * @version v2.1
 */

class XP_Extension_FractaChevrolet_Config
{
	public static $test = true;
	public static function getConfig()
	{
		return array(
			'publicAccountId'				=> '19',
			'md5key'						=> 'M*)y!Qu0Tm-3',
			'ver10'							=> '1.0',
			'templateId'						=> array(
                //'bookit'					=> 'lRGsxwGpVqecNvNRuqG9f9aDq-ZYQ4Rrnb1_mPBbakE',
                'maintenanceReservation'    => 'MOyKNZoNe3dqzJ9vJr9CxX94RrovWgC6DHmkhStVRIQ',
				'pointDeal'					=> 'IFJySXXKw0nQhBH1Qzc2eyaCQI0cuI60g65vgPnuXgs',
				'pointOutTime'				=> 'wahMxlxFjxsFN7mIaqcp4ZjB6-QFpzBQ8Wn4rdnnk8Y',
				'memberUpgrade'				=> 'TMdo4fi2NSNFZHQKjsEbspuHjs9XMK4KeqbDHMS2_-4',
				'memberKeepGrade'			=> 'fkNSsG1meDe3rD3QLGsdrrg9hpseuWfcNryZYz9hbY0',
				'memberDowngrade'			=> 'fkNSsG1meDe3rD3QLGsdrrg9hpseuWfcNryZYz9hbY0',
				'memberBirthday'			=> '',
			),
		);
	}
}