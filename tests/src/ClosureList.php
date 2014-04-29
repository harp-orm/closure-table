<?php

namespace CL\LunaClosuretable\Test;

use CL\Luna\Model\Model;
use CL\Luna\Model\Schema;
use CL\Luna\Model\SchemaTrait;
use CL\Luna\Field;
use CL\Luna\Rel;
use CL\LunaClosuretable as CT;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class ClosureList extends Model {

    use SchemaTrait;
    use CT\ModelTrait;

    public static function findAll()
    {
        return new ClosureListSelect(self::getSchema());
    }

    public $id;
    public $name;

    public function getParent()
    {
        return $this->loadRelLink('parent')->get();
    }

    public function setParent(ClosureList $parent)
    {
        return $this->loadRelLink('parent')->set($parent);
    }

    public function getChildren()
    {
        return $this->loadRelLink('children');
    }

    public static function initialize(Schema $schema)
    {
        $schema
            ->setFields([
                new Field\Integer('id'),
                new Field\String('name'),
            ]);
            // ->setRels([
            //     new CT\ParentRel('parent', $schema, ClosureList::getSchema()),
            //     new CT\ChildrenRel('children', $schema, ClosureList::getSchema()),
            // ]);
    }

}
