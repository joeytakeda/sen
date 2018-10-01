<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Nines\UtilBundle\Entity\AbstractEntity;

/**
 * Location
 *
 * @ORM\Table(name="location")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\LocationRepository")
 */
class Location extends AbstractEntity
{

    /**
     * @var LocationCategory
     * @ORM\ManyToOne(targetEntity="LocationCategory", inversedBy="locations")
     */
    private $category;

    /**
     * @var Collection|Event[]
     * @ORM\OneToMany(targetEntity="Event", mappedBy="location")
     */
    private $events;

    /**
     * Returns a string representation of this entity.
     *
     * @return string
     */
    public function __toString() {
        return get_class($this) . "#" . $this->getId();
    }

    /**
     * Set category.
     *
     * @param LocationCategory|null $category
     *
     * @return Location
     */
    public function setCategory(LocationCategory $category = null)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category.
     *
     * @return LocationCategory|null
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Add event.
     *
     * @param Event $event
     *
     * @return Location
     */
    public function addEvent(Event $event)
    {
        $this->events[] = $event;

        return $this;
    }

    /**
     * Remove event.
     *
     * @param Event $event
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeEvent(Event $event)
    {
        return $this->events->removeElement($event);
    }

    /**
     * Get events.
     *
     * @return Collection
     */
    public function getEvents()
    {
        return $this->events;
    }
}
