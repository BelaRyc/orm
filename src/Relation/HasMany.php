<?php

declare(strict_types=1);

namespace Cycle\ORM\Relation;

use Cycle\ORM\Heap\Node;
use Cycle\ORM\Iterator;
use Cycle\ORM\Reference\EmptyReference;
use Cycle\ORM\Reference\Reference;
use Cycle\ORM\Reference\ReferenceInterface;
use Cycle\ORM\Relation;
use Cycle\ORM\Relation\Traits\HasSomeTrait;
use Cycle\ORM\Select;
use Cycle\ORM\Transaction\Pool;
use Cycle\ORM\Transaction\Tuple;

/**
 * Provides the ability to own the collection of entities.
 */
class HasMany extends AbstractRelation
{
    use HasSomeTrait;

    public function prepare(Pool $pool, Tuple $tuple, mixed $related, bool $load = true): void
    {
        $node = $tuple->node;
        $original = $node->getRelation($this->getName());
        $tuple->state->setRelation($this->getName(), $related);

        if ($original instanceof ReferenceInterface) {
            if (!$load && $this->compareReferences($original, $related) && !$original->hasValue()) {
                $node->setRelationStatus($this->getName(), RelationInterface::STATUS_RESOLVED);
                return;
            }
            $original = $this->resolve($original, true);
            $node->setRelation($this->getName(), $original);
        }

        if ($related instanceof ReferenceInterface) {
            $related = $this->resolve($related, true);
            $tuple->state->setRelation($this->getName(), $related);
        }
        foreach ($this->calcDeleted($related, $original ?? []) as $item) {
            $this->deleteChild($pool, $item);
        }

        if (\count($related) === 0) {
            $node->setRelationStatus($this->getName(), RelationInterface::STATUS_RESOLVED);
            return;
        }
        $node->setRelationStatus($this->getName(), RelationInterface::STATUS_PROCESS);

        // $relationName = $this->getTargetRelationName()
        // Store new and existing items
        foreach ($related as $item) {
            $rTuple = $pool->attachStore($item, true);
            $this->assertValid($rTuple->node);
            if ($this->isNullable()) {
                // todo?
                // $rNode->setRelationStatus($relationName, RelationInterface::STATUS_DEFERRED);
            }
        }
    }

    public function queue(Pool $pool, Tuple $tuple): void
    {
        if ($tuple->task === Tuple::TASK_STORE) {
            $this->queueStoreAll($pool, $tuple);
        }
        // todo
            // $this->queueDelete($pool, $tuple, $related);
    }

    private function queueStoreAll(Pool $pool, Tuple $tuple): void
    {
        $node = $tuple->node;
        $related = $tuple->state->getRelation($this->getName());
        $related = $this->extract($related);

        $node->setRelationStatus($this->getName(), RelationInterface::STATUS_RESOLVED);

        if ($related instanceof ReferenceInterface && !$related->hasValue()) {
            return;
        }

        $relationName = $this->getTargetRelationName();
        foreach ($related as $item) {
            $rTuple = $pool->offsetGet($item);
            $this->applyChanges($tuple, $rTuple);
            $rTuple->node->setRelationStatus($relationName, RelationInterface::STATUS_RESOLVED);
        }
    }

    /**
     * Init relation state and entity collection.
     */
    public function init(Node $node, array $data, bool $typecast = false): iterable
    {
        $elements = [];
        foreach ($data as $item) {
            $elements[] = $this->orm->make($this->target, $item, Node::MANAGED);
        }

        $node->setRelation($this->getName(), $elements);
        return $this->collect($elements);
    }

    public function cast(?array $data): array
    {
        if (!$data) {
            return [];
        }
        $mapper = $this->orm->getEntityRegistry()->getMapper($this->target);
        foreach ($data as &$item) {
            $item = $mapper->cast($item);
        }
        return $data;
    }

    public function initReference(Node $node): ReferenceInterface
    {
        $scope = $this->getReferenceScope($node);
        return $scope === null
            ? new EmptyReference($node->getRole(), [])
            : new Reference($this->target, $scope);
    }

    protected function getReferenceScope(Node $node): ?array
    {
        $scope = [];
        $nodeData = $node->getData();
        foreach ($this->innerKeys as $i => $key) {
            if (!isset($nodeData[$key])) {
                return null;
            }
            $scope[$this->outerKeys[$i]] = $nodeData[$key];
        }
        return $scope;
    }

    public function resolve(ReferenceInterface $reference, bool $load): ?iterable
    {
        if ($reference->hasValue()) {
            return $reference->getValue();
        }
        if ($reference->getScope() === []) {
            // nothing to proxy to
            $reference->setValue([]);
            return [];
        }
        if ($load === false) {
            return null;
        }

        $scope = array_merge($reference->getScope(), $this->schema[Relation::WHERE] ?? []);
        $select = (new Select($this->orm, $this->target))
            ->scope($this->orm->getSource($this->target)->getScope())
            ->where($scope)
            ->orderBy($this->schema[Relation::ORDER_BY] ?? []);

        $iterator = new Iterator($this->orm, $this->target, $select->fetchData(), true);
        $result = \iterator_to_array($iterator, false);

        $reference->setValue($result);

        return $result;
    }

    public function collect(mixed $data): iterable
    {
        if (!\is_iterable($data)) {
            throw new \InvalidArgumentException('Collected data in the HasMany relation should be iterable.');
        }
        return $this->orm->getFactory()->collection(
            $this->orm,
            $this->schema[Relation::COLLECTION_TYPE] ?? null
        )->collect($data);
    }

    /**
     * Convert entity data into array.
     */
    public function extract(mixed $data): array
    {
        if ($data instanceof \Doctrine\Common\Collections\Collection) {
            return $data->toArray();
        }
        if ($data instanceof \Traversable) {
            return \iterator_to_array($data);
        }
        return \is_array($data) ? $data : [];
    }

    /**
     * Return objects which are subject of removal.
     */
    protected function calcDeleted(iterable $related, iterable $original): array
    {
        $related = $this->extract($related);
        $original = $this->extract($original);
        return array_udiff(
            $original ?? [],
            $related,
            // static fn(object $a, object $b): int => strcmp(spl_object_hash($a), spl_object_hash($b))
            static fn (object $a, object $b): int => (int)($a === $b) - 1
        );
    }
}
