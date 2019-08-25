<?php
namespace App\Form\DataTransformer;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class NestedCollectionToStringTransformer implements DataTransformerInterface
{
    /** @var string */
    private $delimiter;

    public function __construct(string $delimiter)
    {
        $this->delimiter = $delimiter;
    }

    /**
     * {@inheritdoc}
     */
    public function transform($values): string
    {
        if (!($values instanceof Collection)) {
            throw new TransformationFailedException(
                sprintf(
                    'Expected "%s", but got "%s"',
                    Collection::class,
                    is_object($values) ? get_class($values) : gettype($values)
                )
            );
        }

        if ($values->isEmpty()) {
            return '';
        }

        return implode($this->delimiter, $values->toArray());
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($value): Collection
    {
        if (!is_string($value)) {
            throw new TransformationFailedException(
                sprintf(
                    'Expected string, but got "%s"',
                    is_object($value) ? get_class($value) : gettype($value)
                )
            );
        }

        if ('' === $value) {
            return new ArrayCollection();
        }

        return new ArrayCollection(explode($this->delimiter, $value) ?: []);
    }
}
