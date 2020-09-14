<?php

namespace IXP\Models\Aggregators;

/*
 * Copyright (C) 2009 - 2020 Internet Neutral Exchange Association Company Limited By Guarantee.
 * All Rights Reserved.
 *
 * This file is part of IXP Manager.
 *
 * IXP Manager is free software: you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the Free
 * Software Foundation, version v2.0 of the License.
 *
 * IXP Manager is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License v2.0
 * along with IXP Manager.  If not, see:
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 */

use Illuminate\Database\Eloquent\{
    Builder,
};

use IXP\Models\{
    Customer,
    Infrastructure,
    PhysicalInterface,
    VirtualInterface
};
use Illuminate\Support\Collection;

/**
 * IXP\Models\Aggregators\VirtualInterfaceAggregator
 *
 * @property int $id
 * @property int|null $custid
 * @property string|null $name
 * @property string|null $description
 * @property int|null $mtu
 * @property int|null $trunk
 * @property int|null $channelgroup
 * @property int $lag_framing
 * @property int $fastlacp
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \IXP\Models\Customer|null $customer
 * @property-read \Illuminate\Database\Eloquent\Collection|\IXP\Models\PhysicalInterface[] $physicalInterfaces
 * @property-read int|null $physical_interfaces_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\IXP\Models\PhysicalInterface[] $physicalInterfacesConnected
 * @property-read int|null $physical_interfaces_connected_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\IXP\Models\VlanInterface[] $vlanInterfaces
 * @property-read int|null $vlan_interfaces_count
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Aggregators\VirtualInterfaceAggregator newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Aggregators\VirtualInterfaceAggregator newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Aggregators\VirtualInterfaceAggregator query()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Aggregators\VirtualInterfaceAggregator whereChannelgroup($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Aggregators\VirtualInterfaceAggregator whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Aggregators\VirtualInterfaceAggregator whereCustid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Aggregators\VirtualInterfaceAggregator whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Aggregators\VirtualInterfaceAggregator whereFastlacp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Aggregators\VirtualInterfaceAggregator whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Aggregators\VirtualInterfaceAggregator whereLagFraming($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Aggregators\VirtualInterfaceAggregator whereMtu($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Aggregators\VirtualInterfaceAggregator whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Aggregators\VirtualInterfaceAggregator whereTrunk($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Aggregators\VirtualInterfaceAggregator whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class VirtualInterfaceAggregator extends VirtualInterface
{
    /**
     * Utility function to provide an array of all virtual interface objects on a given
     * infrastructure
     *
     * @param Infrastructure    $infra The infrastructure to gather VirtualInterfaces for
     * @param int|bool          $proto Either 4 or 6 to limit the results to interface with IPv4 / IPv6
     * @param bool              $externalOnly If true (default) then only external (non-internal) interfaces will be returned
     *
     * @return Collection
     *
     * @throws
     */
    public static function getForInfrastructure( Infrastructure $infra, $proto = false, $externalOnly = true ): Collection
    {
        return self::select( [ 'vi.*' ] )
            ->from( 'virtualinterface AS vi' )
            ->Join( 'cust AS cust', 'cust.id', 'vi.custid' )
            ->Join( 'vlaninterface AS vli', 'vli.virtualinterfaceid', 'vi.id' )
            ->Join( 'physicalinterface AS pi', 'pi.virtualinterfaceid', 'vi.id' )
            ->Join( 'switchport AS sp', 'sp.id', 'pi.switchportid' )
            ->Join( 'switch AS s', 's.id', 'sp.switchid' )
            ->Join( 'infrastructure AS i', 'i.id', 's.infrastructure' )
            ->where( 'i.id', $infra->id )
            ->whereRaw( Customer::SQL_CUST_ACTIVE )
            ->whereRaw( Customer::SQL_CUST_CURRENT )
            ->whereRaw( Customer::SQL_CUST_TRAFFICING )
            ->where( 'pi.status', PhysicalInterface::STATUS_CONNECTED )
            ->when( $proto , function( Builder $q, $proto ) {
                $p = in_array( $proto, [ 4, 6 ], true ) ? $proto : 4;
                return $q->whereRaw( "vli.ipv{$p}enabled = 1" );
            })
            ->when( $externalOnly , static function( Builder $q ) {
                return $q->whereRaw( Customer::SQL_CUST_EXTERNAL );
            })->orderBy( 'cust.name' )->get()->keyBy( 'id' );
    }


    /**
     * Utility function to provide an array of all virtual interface objects on a given
     * infrastructure
     *
     * @return array
     *
     * @throws
     */
    public static function getByLocation(): array
    {
        return self::select( [
            'cust.id AS customerid',
            'vi.id AS id',
            'pi.speed AS speed',
            'i.name AS infrastructure',
            'l.name AS locationname'
        ] )
            ->from( 'virtualinterface AS vi' )
            ->Join( 'cust AS cust', 'cust.id', 'vi.custid' )
            ->Join( 'physicalinterface AS pi', 'pi.virtualinterfaceid', 'vi.id' )
            ->Join( 'switchport AS sp', 'sp.id', 'pi.switchportid' )
            ->Join( 'switch AS s', 's.id', 'sp.switchid' )
            ->Join( 'infrastructure AS i', 'i.id', 's.infrastructure' )
            ->Join( 'cabinet AS ca', 'ca.id', 's.cabinetid' )
            ->Join( 'location AS l', 'l.id', 'ca.locationid' )
            ->whereRaw( Customer::SQL_CUST_EXTERNAL )
            ->orderBy( 'cust.name' )->get()->toArray();
    }


    /**
     * Get statistics/percentage of connected customer by VLAN
     *
     * - customer type full or probono
     * - at least one pi connected
     *
     * Returns an array of objects such as:
     *
     * [
     *      {#4344
     *          "vlanname": "INEX LAN1",
     *          "count": 105,
     *          "percent": "96.3303",
     *      },
     * ]
     *
     * @return array
     */
    public static function getPercentageCustomersByVlan(): array
    {
        return self::selectRaw(
            "v.name AS vlanname, 
            COUNT( DISTINCT vi.custid ) AS count, 
            COUNT( DISTINCT vi.custid ) / ( 
            SELECT COUNT( DISTINCT vi.custid ) FROM virtualinterface AS vi
                        JOIN vlaninterface as vli ON vli.virtualinterfaceid = vi.id
                        JOIN vlan AS v ON v.id = vli.vlanid
                        JOIN cust AS c ON c.id = vi.custid
                        LEFT JOIN physicalinterface AS pi ON pi.virtualinterfaceid = vi.id
                        WHERE v.private = 0 AND pi.status = 1 AND c.`type`IN (1,4) ) * 100 AS percent"
        )
            ->from( 'virtualinterface AS vi' )
            ->join( 'vlaninterface AS vli', 'vli.virtualinterfaceid', 'vi.id' )
            ->join( 'vlan AS v', 'v.id', 'vli.vlanid' )
            ->leftJoin( 'physicalinterface AS pi', 'pi.virtualinterfaceid', 'vi.id' )
            ->join( 'cust AS c', 'c.id', 'vi.custid' )
            ->where( 'v.private', false )
            ->where( 'pi.status', PhysicalInterface::STATUS_CONNECTED )
            ->whereIn( 'c.type', [1,4] )
            ->groupBy( 'v.name' )
            ->orderByDesc( 'count' )->get()->toArray();
    }
}