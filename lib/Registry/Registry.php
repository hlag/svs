<?php
/**
 * Letzte Änderung von: $LastChangedBy: proggen $
 * Revision           : $LastChangedRevision: 134 $
 * Letzte Änderung am : $LastChangedDate: 2009-04-08 12:23:41 +0200 (Mi, 08 Apr 2009) $
 *
 */
class Registry 
{
    protected static $instance = null;
    protected $values = array();

    const KEY_TREEBUILDER = 'treebuilder';

    public static function getInstance() 
    {
        if (self::$instance === null) 
        {
            self::$instance = new Registry();
        }
        return self::$instance;
    }

    protected function __construct() 
    {
    }

    private function __clone() 
    {
    }

    protected function set($key, $value) 
    {
        $this->values[$key] = $value;
    }

    protected function get($key) 
    {
        if (isset($this->values[$key]))
        {
            return $this->values[$key];
        }
        return null;
    }

    public function setTreeBuilder(TreeBuilder $treebuilder) 
    {
        $this->set(self::KEY_TREEBUILDER, $treebuilder);
    }

    public function getTreeBuilder() 
    {
        return $this->get(self::KEY_TREEBUILDER);
    }
} 
?>