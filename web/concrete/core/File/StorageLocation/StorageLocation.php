<?php
namespace Concrete\Core\File\StorageLocation;
use Concrete\Core\File\StorageLocation\Configuration\Configuration;
use Database;
use Core;
/**
 * @Entity
 * @Table(name="FileStorageLocations")
 */
class StorageLocation
{

    /**
     * @Column(type="text")
     */
    protected $fslName;


    /**
     * @Column(type="object")
     */
    protected $fslConfiguration;

    /**
     * @Id @Column(type="integer")
     * @GeneratedValue
     */
    protected $fslID;

    /**
     * @Column(type="boolean")
     */
    protected $fslIsDefault = false;

    public function getID()
    {
        return $this->fslID;
    }

    public function getName()
    {
        return $this->fslName;
    }

    public function getConfigurationObject()
    {
        return $this->fslConfiguration;
    }

    public function isDefault()
    {
        return $this->fslIsDefault;
    }

    public static function add(Configuration $configuration, $fslName, $fslIsDefault = false)
    {
        $db = Database::get();
        $em = $db->getEntityManager();
        $o = new static();
        $o->fslName = $fslName;
        $o->fslIsDefault = $fslIsDefault;
        $o->fslConfiguration = $configuration;
        $em->persist($o);
        $em->flush();
        return $o;
    }

    public static function getByID($id)
    {
        $db = Database::get();
        $em = $db->getEntityManager();
        $r = $em->find('\Concrete\Core\File\StorageLocation\StorageLocation', $id);
        return $r;
    }

    public static function getDefault()
    {
        $db = Database::get();
        $em = $db->getEntityManager();
        $location = $em->getRepository('\Concrete\Core\File\StorageLocation\StorageLocation')->findOneBy(
            array('fslIsDefault' => true
            ));
        return $location;
    }

    /**
     * Returns the proper file system object for the current storage location, by mapping
     * it through Gaufrette
     * @return \Gaufrette\Filesystem;
     */
    public function getFileSystemObject()
    {
        $adapter = $this->fslConfiguration->getAdapter();
        $filesystem = new \Gaufrette\Filesystem($adapter);
        return $filesystem;
    }

    public function save()
    {
        $db = Database::get();
        $em = $db->getEntityManager();
        $em->persist($this);
        $em->flush();
    }

}