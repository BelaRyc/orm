<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\ORM\Relation;

use Spiral\ORM\Command\CarrierInterface;
use Spiral\ORM\Command\CommandInterface;
use Spiral\ORM\Command\Control\Nil;
use Spiral\ORM\DependencyInterface;
use Spiral\ORM\Exception\Relation\NullException;
use Spiral\ORM\Point;
use Spiral\ORM\Util\Promise;

// todo: what is the difference with refers to?
class BelongsToRelation extends AbstractRelation implements DependencyInterface
{
    // todo: class
    public function initPromise(Point $state, $data): array
    {
        if (empty($innerKey = $this->fetchKey($state, $this->innerKey))) {
            return [null, null];
        }

        if ($this->orm->getHeap()->hasPath("{$this->class}:$innerKey")) {
            // todo: has it!
            $i = $this->orm->getHeap()->getPath("{$this->class}:$innerKey");
            return [$i, $i];
        }

        $pr = new Promise(
            [$this->outerKey => $innerKey]
            , function ($context) use ($innerKey) {
            // todo: check in map

            // todo: CHECK IN HEAP?
            // todo: CHECK IN HEAP VIA REPOSITORY?

            // todo: THIS CAN BE UNIFIED!!!

            if ($this->orm->getHeap()->hasPath("{$this->class}:$innerKey")) {
                // todo: improve it?
                return $this->orm->getHeap()->getPath("{$this->class}:$innerKey");
            }

            return $this->orm->getMapper($this->class)->getRepository()->findOne($context);
        });

        return [$pr, $pr];
    }

    /**
     * @inheritdoc
     */
    public function queueRelation(
        CarrierInterface $parentCommand,
        $entity,
        Point $state,
        $related,
        $original
    ): CommandInterface {
        if (is_null($related)) {
            if ($this->isRequired()) {
                throw new NullException("Relation {$this} can not be null");
            }

            if (!is_null($original)) {
                $parentCommand->setContext($this->innerKey, null);
            }

            return new Nil();
        }

        $relStore = $this->orm->queueStore($related);
        $relState = $this->getPoint($related);
        $relState->addReference();

        $this->forwardContext($relState, $this->outerKey, $parentCommand, $state, $this->innerKey);

        return $relStore;
    }
}