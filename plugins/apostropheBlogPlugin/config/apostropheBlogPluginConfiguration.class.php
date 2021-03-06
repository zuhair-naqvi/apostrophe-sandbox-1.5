<?php

/**
 * aBlogPlugin configuration.
 * 
 * @package     apostropheBlogPlugin
 * @subpackage  config
 * @author      Your name here
 * @version     SVN: $Id: PluginConfiguration.class.php 17207 2009-04-10 15:36:26Z Kris.Wallsmith $
 */
class apostropheBlogPluginConfiguration extends sfPluginConfiguration
{

  static $registered = false;
  /**
   * @see sfPluginConfiguration
   */
  public function initialize()
  {
    // Yes, this can get called twice. This is Fabien's workaround:
    // http://trac.symfony-project.org/ticket/8026
    
    if (!self::$registered)
    {
      $this->dispatcher->connect('a.getGlobalButtons', array('apostropheBlogPluginConfiguration', 
        'getGlobalButtons'));
      // This was inadvertently removed just prior to 1.4. Now apostrophe:migrate hooks up properly again
      $this->dispatcher->connect('command.post_command', array('aBlogEvents',  
        'listenToCommandPostCommandEvent'));
      $this->dispatcher->connect('a.get_categorizables', array($this, 'listenToGetCategorizables'));
      $this->dispatcher->connect('a.get_count_by_category', array($this, 'listenToGetCountByCategory'));
      $this->dispatcher->connect('a.merge_category', array($this, 'listenToMergeCategory'));
      
      self::$registered = true;
    }
  }

  public function listenToGetCategorizables($event, $results)
  {
    // You must play nice and append to what is already there
    $results['aEvent'] = array('class' => 'aEvent', 'name' => 'Events');
    $results['aBlogPost'] = array('class' => 'aBlogPost', 'name' => 'Blog Posts');
    return $results;
  }
  
  // Also includes the above info so we know what the result is referring to
  public function listenToGetCountByCategory($event, $results)
  {
    // You must play nice and append to what is already there
    $info = array('class' => 'aEvent', 'name' => 'Events');
    $counts = Doctrine::getTable('aEvent')->getCountByCategory();
    $info['counts'] = $counts;
    $results['aEvent'] = $info;
    $info = array('class' => 'aBlogPost', 'name' => 'Blog Posts');
    $counts = Doctrine::getTable('aBlogPost')->getCountByCategory();
    $info['counts'] = $counts;
    $results['aBlogPost'] = $info;
    return $results;
  }

  public function listenToMergeCategory($event)
  {
    Doctrine::getTable('aBlogItemToCategory')->mergeCategory($event['old_id'], $event['new_id']);
  }
  
  static public function getGlobalButtons()
  {
    $user = sfContext::getInstance()->getUser();
 
    if ($user->hasCredential('blog_author') || $user->hasCredential('blog_admin'))
    {
      aTools::addGlobalButtons(array(
        new aGlobalButton('blog', 'Blog', '@a_blog_admin', 'a-blog-btn'),
        new aGlobalButton('events', 'Events', '@a_event_admin', 'a-events day-'.date('j'))
      ));
    }
  }
}
