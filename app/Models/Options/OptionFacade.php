<?php
/**
 * OptionFacade
 *
 * @author  Jiří Šifalda
 * @package Flame
 *
 * @date    09.07.12
 */

namespace Flame\CMS\Models\Options;

class OptionFacade extends \Flame\Doctrine\Model\Facade
{

	/**
	 * @var string
	 */
	protected $repositoryName = '\Flame\CMS\Models\Options\Option';

	/**
	 * @var array
	 */
	private $availableOptions = array(
		'Name', 'Thumbnail:Width', 'Thumbnail:Height', 'ItemsPerPage', 'Menu:NewsreelCount', 'Menu:TagsCount',
		'Theme'
	);

	/**
	 * @return array
	 */
	public function getAvailableOptions()
	{
		return $this->availableOptions;
	}

	/**
	 * @param $name
	 * @return object
	 */
	public function getByName($name)
    {

        return null;
    }

	/**
	 * @return array
	 */
	public function getLastOptions()
    {
        return null;
    }

	/**
	 * @param $name
	 * @return null
	 */
	public function getOptionValue($name)
    {
	    $value = $this->getByName($name);
	    return $value ? $value->value : null;
    }

}
