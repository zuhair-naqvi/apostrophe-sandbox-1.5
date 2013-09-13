<?php

/**
 * PluginaAnchorTitleSlot
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class PluginaAnchorTitleSlot extends BaseaAnchorTitleSlot
{
  protected $editDefault = true;

	public function getTitle()
	{
		$values = $this->getArrayValue();
		return isset($values['title']) ? $values['title'] : null;
	}
	
	public function getAnchor()
	{
		$title = $this->getTitle();
		return is_null($title) ? null : aTools::slugify($title);
	}
}