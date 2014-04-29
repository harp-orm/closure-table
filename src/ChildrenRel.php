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
class ChildrenRel extends Mapper\AbstractRelMany implements RelJoinInterface
{
    use LoadFromDataTrait;

    protected $foreignKey;

    public function __construct($name, Schema $schema, Schema $foreignSchema, array $options = array())
    {
        $this->foreignKey = lcfirst($schema->getName()).'Id';

        parent::__construct($name, $schema, $foreignSchema, $options);
    }

    public function getForeignKey()
    {
        return $this->foreignKey;
    }

    public function getKey()
    {
        return $this->getSchema()->getPrimaryKey();
    }

    public function hasForeign(array $models)
    {
        return ! empty(Arr::extractUnique($models, $this->getKey()));
    }

    public function getBranchesKey()
    {
        return $this->getName().'Key';
    }

    public function loadForeign(array $models)
    {
        $modelClass = $this->getSchema()->getModelClass();
        $ct = $modelClass::getClosureTable();

        return $this
            ->getSelectQuery()
            ->column($ct->getTable().'.'.$ct->getAnsestorKey(), $this->getBranchesKey())
            ->join($modelClass::getBranchesTable(), [
                $modelClass::getDescendantKey() => $this->getPrimaryKey(),
                $modelClass::getDepthKey() => new SQL('?', [1])
            ])
            ->where([
                $modelClass::getAnsestorKey() => Arr::extractUnique($models, $this->getPrimaryKey())
            ])
            ->loadRaw();
    }

    public function linkToForeign(array $models, array $foreign)
    {
        $return = Objects::groupCombineArrays($models, $foreign, function ($model, $foreign) {
            return $model->{$this->getKey()} == $foreign->{$this->getForeignKey()};
        });

        return $return;
    }

    public function joinRel(AbstractQuery $query, $parent)
    {
        $columns = [$this->getForeignKey() => $this->foreignSchema->getPrimaryKey()];

        $condition = new RelJoinCondition($parent, $this->getName(), $columns, $this->foreignSchema);

        $query->joinAliased($this->foreignSchema->getTable(), $this->getName(), $condition);
    }

    public function update(Mapper\AbstractNode $model, Mapper\AbstractLink $link)
    {
        $db = DB::get($this->getSchema()->getDb());

        $db
            ->delete()
            ->table($modelClass::getBranchesTable())
            ->whereRaw("{$modelClass::getDescendantKey()} = ? AND {$modelClass::getDepthKey()} > 0", [$link->getAddedIds()]);

        $ansestorIds = $db
            ->select()
            ->type('DISTINCT')
            ->table($modelClass::getBranchesTable())
            ->column($modelClass::getAnsestorKey())
            ->where([
                $modelClass::getDescendantKey() => $this->getAddedIds()
            ])
            ->execute()
            ->fetchColumn();

        $db
            ->insert()
            ->into($modelClass::getBranchesTable())
            ->select(
                $db->select()
                    ->
            )


        foreach ($link->getRemoved() as $added) {
            $added->{$this->getForeignKey()} = null;
        }
    }

}
