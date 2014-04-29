<?php

namespace CL\LunaClosuretable;

use CL\Luna\Util\Arr;
use CL\Luna\Util\Objects;
use CL\Luna\Model\Schema;
use CL\Luna\Rel\LoadFromDataTrait;
use CL\Luna\Mapper;
use CL\Luna\ModelQuery\RelJoinInterface;
use CL\Atlas\Query\AbstractQuery;
use Closure;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class ParentRel extends Mapper\AbstractRelOne implements RelJoinInterface
{
    use LoadFromDataTrait;

    public function hasForeign(array $models)
    {
        return ! empty(Arr::extractUnique($models, $this->key));
    }

    public function getBranchesKey()
    {
        return $this->getName().'Key';
    }

    public function loadForeign(array $models)
    {
        $modelClass = $this->getSchema()->getModelClass();

        return $this
            ->getSelectQuery()
            ->column($modelClass::getBranchesTable().'.'.$modelClass::getAnsestorKey(), $this->getBranchesKey())
            ->join($modelClass::getBranchesTable(), [
                $modelClass::getAnsestorKey() => $this->getPrimaryKey(),
                $modelClass::getDepthKey() => new SQL('?', [1])
            ])
            ->where([
                $modelClass::getDescendantKey() => Arr::extractUnique($models, $this->getPrimaryKey())
            ]);
    }

    public function linkToForeign(array $models, array $foreign)
    {
        return Objects::combineArrays($models, $foreign, function($model, $foreign){
            return $model->getId() == $foreign->{$this->getBranchesKey()};
        });
    }

    public function update(Mapper\AbstractNode $model, Mapper\AbstractLink $link)
    {
        $modelClass = $this->getSchema()->getModelClass();

        if ($link->get()->isPersisted() and $link->isChanged())
        {
            $delete = $this->getSchema()->getDb()->delete();
            $delete
                ->table($modelClass::getBranchesTable())
                ->whereRaw("{$modelClass::getDescendantKey()} = ? AND {$modelClass::getDepthKey()} > 0", [$link->get()->getId()]);

            $insert = $this->getSchema()->getDb()->insert();
            $insert
                ->into($modelClass::getBranchesTable())
                ->columns([
                    $modelClass::getAnsestorKey(),
                    $modelClass::getDescendantKey(),
                    $modelClass::getDepthKey(),
                ]);

            $ansestors = $this->loadAnsestorsFor($link->get()->getId());

            foreach ($ansestors as $ansestor) {
                $insert->values([
                    $ansestor[$modelClass::getAnsestorKey()],
                    $link->get()->getId(),
                    $ansestor[$modelClass::getDescendantKey()] + 1,
                ]);
            }

            $delete->execute();
            $insert->execute();
        }
    }

    public function loadAnsestorsFor($descendantId)
    {
        $modelClass = $this->getSchema()->getModelClass();
        $select = $this->getSchema()->getDb()->select();

        return $select
            ->from($modelClass::getBranchesTable())
            ->where([
                $modelClass::getDepthKey() => $descendantId
            ])
            ->execute();
    }

    public function joinRel(AbstractQuery $query, $parent)
    {
        $columns = [$this->getForeignKey() => $this->getKey()];

        $condition = new RelJoinCondition($parent, $this->getName(), $columns, $this->getForeignSchema());

        $query->joinAliased($this->getForeignTable(), $this->getName(), $condition);
    }

}
