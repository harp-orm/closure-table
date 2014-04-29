<?php

namespace CL\LunaClosuretable;

use CL\Luna\Model\Schema;
use CL\Luna\Model\Model;
use CL\Atlas\DB;

/**
 * @author    Ivan Kerin <ikerin@gmail.com>
 * @copyright 2014, Clippings Ltd.
 * @license   http://spdx.org/licenses/BSD-3-Clause
 */
trait ModelTrait
{
    public static $closureTable;

    public static function getClosureTable()
    {
        return static::$closureTable;
    }

    public static function initialize(Schema $schema)
    {
        $modelClass = $schema->getModelClass();
        $modelClass::$closureTable = new ClosureTable($schema);
    }

    public function depth()
    {
        $ct = self::$closureTable;

        $select = $ct->select()
            ->where([
                $ct->getDescendantKey() => $this->getId()
            ])
            ->order($ct->getDepthKey(), 'DESC')
            ->limit(1)
            ->column($ct->getDepthKey());

        return $select
            ->execute()
            ->fetchColumn();
    }

    public function ansestors()
    {
        return self::findAll()
            ->ansestorsOf($this);
    }

    public function descendants()
    {
        return self::findAll()
            ->descendantsOf($this);
    }

    public function isDescendantOf(Model $ansestor)
    {
        return (bool) $this->ansestors()
            ->whereKey($ansestor->getId())
            ->loadCount();
    }

    public function isAnsestorOf(Model $descendant)
    {
        return (bool) $this->descendants()
            ->whereKey($descendant->getId())
            ->loadCount();
    }

}
