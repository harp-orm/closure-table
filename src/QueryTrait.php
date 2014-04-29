<?php

namespace CL\LunaClosuretable;

use CL\Luna\Model\Model;
use CL\Atlas\SQL\SQL;

/**
 * @author    Ivan Kerin <ikerin@gmail.com>
 * @copyright 2014, Clippings Ltd.
 * @license   http://spdx.org/licenses/BSD-3-Clause
 */
trait QueryTrait
{
    public static function childrenOf(Model $parent)
    {
        $ct = $this->getClosureTable();

        return $this
            ->join($ct->getTable(), [
                $ct->getDescendantKey() => $ct->getKey(),
                $ct->getDepthKey() => new SQL('?', [1])
            ])
            ->where([
                "{$ct->getTable()}.{$ct->getAnsestorKey()}" => $parent->getId()
            ]);
    }

    public function getClosureTable()
    {
        $modelClass = $this->getSchema()->getModelClass();
        return $modelClass::getClosureTable();
    }

    public function descendantsOf(Model $parent)
    {
        $ct = $this->getClosureTable();

        return $this
            ->join(
                $ct->getTable(),
                "ON {$ct->getDescendantKey()} = {$ct->getKey()} AND {$ct->getDepthKey()} > 0"
            )
            ->where([
                "{$ct->getTable()}.{$ct->getAnsestorKey()}" => $parent->getId()
            ]);
    }

    public function descendantsOfAndSelf(Model $parent)
    {
        $ct = $this->getClosureTable();

        return $this
            ->join($ct->getTable(), [$ct->getDescendantKey() => $ct->getKey()])
            ->where([
                "{$ct->getTable()}.{$ct->getAnsestorKey()}" => $parent->getId()
            ]);
    }

    public function ansestorsOf(Model $child)
    {
        $ct = $this->getClosureTable();

        return $this
            ->join(
                $ct->getTable(),
                "ON {$ct->getAnsestorKey()} = {$ct->getKey()} AND {$ct->getDepthKey()} > 0"
            )
            ->where([
                "{$ct->getTable()}.{$ct->getDescendantKey()}" => $child->getId()
            ]);
    }

    public function ansestorsOfAndSelf(Model $child)
    {
        $ct = $this->getClosureTable();

        return $this
            ->join($ct->getTable(), [$ct->getAnsestorKey() => $ct->getKey()])
            ->where([
                "{$ct->getTable()}.{$ct->getDescendantKey()}" => $child->getId()
            ]);
    }
}
