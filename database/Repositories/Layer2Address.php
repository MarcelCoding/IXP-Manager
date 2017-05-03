<?php

namespace Repositories;

use Doctrine\ORM\EntityRepository;

/**
 * Layer2Address
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Layer2Address extends EntityRepository
{
    /**
     * Check if a mac address already exists within a given VLAN
     *
     * @param  string $mac The MAC address to search for
     * @param  int $vlanid The ID of the VLAN to search
     * @return bool true if it exists
     */
    public function existsInVlan( string $mac, int $vlanid ): bool {
        $dql = "SELECT COUNT(l2a.id)
                    FROM Entities\Layer2Address l2a
                    LEFT JOIN l2a.vlanInterface vli
                    LEFT JOIN vli.Vlan v
                    WHERE l2a.mac = ?1 
                        AND v.id = ?2";

        $query = $this->getEntityManager()->createQuery( $dql );
        $query->setParameter( 1, $mac );
        $query->setParameter( 2, $vlanid );
        return ( $query->getSingleScalarResult() > 0 ) ? true : false;
    }

    /**
     * Load all Layer2Addresses (optionally limited to vlan)
     *
     * @param  int $vlid The ID of the VLAN to search
     * @return array Layer2Interface entities
     */
    public function getAll( int $vlid = null ): array {
        $dql = "SELECT l2a
                    FROM Entities\Layer2Address l2a
                    LEFT JOIN l2a.vlanInterface vli
                    LEFT JOIN vli.Vlan vl
                    LEFT JOIN vli.VirtualInterface vi
                    LEFT JOIN vi.Customer c ";

        if( $vlid ){
            $dql .= " WHERE vl.id = $vlid ";
        }

        $dql .= " ORDER BY c.name ASC";

        $query = $this->getEntityManager()->createQuery( $dql );

        return $query->getResult();
    }
}