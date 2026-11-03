<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * A resource as its creator manages it: the place it sells, whether it is on sale, and how the connector last found it.
 *
 * The `ResourceRef` is what the ports exchange; this is what a creator
 * surface lists, with the standing the doctor recorded, so a creator sees
 * from the conversation which place needs attention.
 */
final readonly class ResourceDetails
{
    /**
     * @param  string        $id            The resource's UUID.
     * @param  string        $projectId     The project's UUID.
     * @param  string        $title         The creator-facing title.
     * @param  string|null   $description   What members get, or null.
     * @param  ResourceKind  $kind          The stored kind, `connector:kind`.
     * @param  bool          $active        Whether plans may grant it now.
     * @param  string|null   $healthStatus  How the connector last found the place, in the core's words, or null before any check.
     * @param  string|null   $healthReason  Why, when the place was not found healthy.
     * @param  string|null   $spaceId       The place it is bound to, or null while unbound.
     */
    public function __construct(
        public string $id,
        public string $projectId,
        public string $title,
        public ?string $description,
        public ResourceKind $kind,
        public bool $active,
        public ?string $healthStatus,
        public ?string $healthReason,
        public ?string $spaceId,
    ) {}

    /**
     * @return  ResourceRef  The ref the ports exchange.
     */
    public function ref(): ResourceRef
    {
        return new ResourceRef($this->id, $this->projectId, $this->kind, $this->title, $this->spaceId, $this->active);
    }
}
