<?php

namespace CL\LunaClosuretable\Test;

use CL\Luna\Util\Log;
use CL\Luna\Mapper\Repo;

class IntegrationTest extends AbstractTestCase
{
    public function dataAnsestors()
    {
        return array(
            array(3, array(2, 1)),
            array(6, array(4, 1)),
            array(1, array()),
        );
    }

    /**
     * @dataProvider dataAnsestors
     */
    public function testAnsestors($itemId, $expectedAnsestorsIds)
    {
        $item = ClosureList::find($itemId);

        $this->assertEquals($expectedAnsestorsIds, $item->ansestors()->loadIds());
    }

    public function dataDescendants()
    {
        return array(
            array(2, array(3)),
            array(4, array(5, 6)),
            array(1, array(2, 3, 4, 5, 6)),
        );
    }

    /**
     * @dataProvider dataDescendants
     */
    public function testDescendants($itemId, $expectedDescendantIds)
    {
        $item = ClosureList::find($itemId);

        $this->assertEquals($expectedDescendantIds, $item->descendants()->loadIds());
    }

    public function dataIsAnsestorOf()
    {
        return array(
            array(2, 1, false),
            array(2, 2, false),
            array(2, 3, true),
            array(1, 6, true),
            array(4, 3, false),
        );
    }

    /**
     * @dataProvider dataIsAnsestorOf
     */
    public function testIsAnsestorOf($ansestorId, $descendantId, $expected)
    {
        $ansestor = ClosureList::find($ansestorId);
        $descendant = ClosureList::find($descendantId);

        $this->assertEquals($expected, $ansestor->isAnsestorOf($descendant));
    }

    public function dataIsDescendantOf()
    {
        return array(
            array(2, 1, true),
            array(3, 1, true),
            array(2, 2, false),
            array(4, 3, false),
            array(5, 2, false),
        );
    }

    /**
     * @dataProvider dataIsDescendantOf
     */
    public function testIsDescendantOf($descendantId, $ansestorId, $expected)
    {
        $descendant = ClosureList::find($descendantId);
        $ansestor = ClosureList::find($ansestorId);

        $this->assertEquals($expected, $descendant->isDescendantOf($ansestor));
    }

    public function dataDepth()
    {
        return array(
            array(1, 0),
            array(2, 1),
            array(6, 2),
        );
    }

    /**
     * @dataProvider dataDepth
     */
    public function testDepth($itemId, $expected)
    {
        $item = ClosureList::find($itemId);

        $this->assertEquals($expected, $item->depth());
    }

    public function testDeep()
    {
        // $eight = new ClosureList(array('name' => 'eight'));

        // $two = ClosureList::find(2);
        // $two->getChildren()->add($eight);

        // Repo::get()->persist($two);

        // $two->children->add($eight);
        // $two->save();

        // $one = Jam::find('test_closurelist', 1);
        // $two = Jam::find('test_closurelist', 2);
        // $four = Jam::find('test_closurelist', 4);

        // $this->assertTrue($eight->is_descendant_of($two));
        // $this->assertTrue($eight->is_descendant_of($one));
        // $this->assertFalse($eight->is_descendant_of($four));

        // $seven = Jam::find('test_closurelist', 7);

        // $seven->parent = $eight;
        // $seven->save();

        // $this->assertTrue($seven->is_descendant_of($eight));
        // $this->assertTrue($seven->is_descendant_of($two));
        // $this->assertTrue($seven->is_descendant_of($one));
        // $this->assertFalse($seven->is_descendant_of($four));

        // $this->assertTrue($eight->children->has($seven));

        // $two->delete();

        // $this->assertEquals(array(4,5,6), $one->descendants()->ids());

    }
}
