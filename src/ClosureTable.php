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
class ClosureTable
{
    public $depthKey = 'depth';
    public $ansestorKey = 'ansestorId';
    public $descendantKey = 'descendantId';
    public $table;
    public $schema;

    public function getDepthKey()
    {
        return $this->depthKey;
    }

    public function getKey()
    {
        return $this->schema->getPrimaryKey();
    }

    public function getDescendantKey()
    {
        return $this->descendantKey;
    }

    public function getAnsestorKey()
    {
        return $this->ansestorKey;
    }

    public function getTable()
    {
        return $this->table;
    }

    public function getSchema()
    {
        return $this->schema;
    }

    public function getDb()
    {
        return DB::get($this->schema->getDb());
    }

    public function __construct(Schema $schema)
    {
        $this->schema = $schema;
        $this->table = $schema->getTable().'Branches';

        $schema
            ->setEventAfterSave([$this, 'afterClosureTableSave'])
            ->setEventAfterDelete([$this, 'afterClosureTableDelete']);
    }

    public function select()
    {
        return $this->getDb()
            ->select()
            ->from($this->table);
    }

    public function delete()
    {
        return $this->getDb()
            ->delete()
            ->table($this->branchesTable);
    }

    public function insert()
    {
        return $this->getDb()
            ->insert()
            ->into($this->branchesTable);
    }

    public function afterClosureTableSave(Model $model)
    {
        $this
            ->insert()
            ->set([
                $this->ansestorKey => $model->getId(),
                $this->descendantKey => $model->getId(),
                $this->depthKey => 0,
            ])
            ->execute();
    }

    public function afterClosureTableDelete(Model $model)
    {
        $this
            ->delete()
            ->where([
                $this->ansestorKey => $model->getId(),
                $this->descendantKey => $model->getId(),
            ])
            ->execute();
    }

}
