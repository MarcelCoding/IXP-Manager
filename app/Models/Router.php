<?php

namespace IXP\Models;

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
    Model,
    Relations\BelongsTo
};


/**
 * IXP\Models\Router
 *
 * @property int $id
 * @property int $vlan_id
 * @property string $handle
 * @property int $protocol
 * @property int $type
 * @property string $name
 * @property string $shortname
 * @property string $router_id
 * @property string $peering_ip
 * @property int $asn
 * @property string $software
 * @property string $mgmt_host
 * @property string|null $api
 * @property int $api_type
 * @property bool|null $lg_access
 * @property bool $quarantine
 * @property bool $bgp_lc
 * @property string $template
 * @property bool $skip_md5
 * @property string|null $last_updated
 * @property bool $rpki
 * @property string|null $software_version
 * @property string|null $operating_system
 * @property string|null $operating_system_version
 * @property int $rfc1997_passthru
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \IXP\Models\Vlan $vlan
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Router hasApi()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Router iPv4()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Router iPv6()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Router largeCommunities()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Router newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Router newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Router notQuarantine()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Router query()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Router routeServer()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Router rpki()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Router whereApi($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Router whereApiType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Router whereAsn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Router whereBgpLc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Router whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Router whereHandle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Router whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Router whereLastUpdated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Router whereLgAccess($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Router whereMgmtHost($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Router whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Router whereOperatingSystem($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Router whereOperatingSystemVersion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Router wherePeeringIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Router whereProtocol($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Router whereQuarantine($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Router whereRfc1997Passthru($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Router whereRouterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Router whereRpki($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Router whereShortname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Router whereSkipMd5($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Router whereSoftware($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Router whereSoftwareVersion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Router whereTemplate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Router whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Router whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Router whereVlanId($value)
 * @mixin \Eloquent
 */
class Router extends Model
{
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'asn'        => 'integer',
        'lg_access'  => 'boolean',
        'quarantine' => 'boolean',
        'bgp_lc'     => 'boolean',
        'skip_md5'   => 'boolean',
        'rpki'       => 'boolean',
    ];

    /**
     * CONST PROTOCOL
     */
    public const PROTOCOL_IPV4                 = '4';
    public const PROTOCOL_IPV6                 = '6';

    /**
     * @var array Email ids to classes
     */
    public static $PROTOCOLS = [
        self::PROTOCOL_IPV4     =>      'IPv4',
        self::PROTOCOL_IPV6     =>      'IPv6'
    ];

    /**
     * CONST TYPES
     */
    public const TYPE_ROUTE_SERVER                 = 1;
    public const TYPE_ROUTE_COLLECTOR              = 2;
    public const TYPE_AS112                        = 3;
    public const TYPE_OTHER                        = 99;

    /**
     * @var array Email ids to classes
     */
    public static $TYPES = [
        self::TYPE_ROUTE_SERVER             => 'Route Server',
        self::TYPE_ROUTE_COLLECTOR          => 'Route Collector',
        self::TYPE_AS112                    => 'AS112',
        self::TYPE_OTHER                    => 'Other'
    ];

    /**
     * @var array Email ids to classes
     */
    public static $TYPES_SHORT = [
        self::TYPE_ROUTE_SERVER             => 'RS',
        self::TYPE_ROUTE_COLLECTOR          => 'RC',
        self::TYPE_AS112                    => 'AS112',
        self::TYPE_OTHER                    => 'Other'
    ];

    /**
     * CONST SOFTWARES
     */
    public const SOFTWARE_BIRD                     = 1;
    public const SOFTWARE_BIRD2                    = 6;
    public const SOFTWARE_QUAGGA                   = 2;
    public const SOFTWARE_FRROUTING                = 3;
    public const SOFTWARE_OPENBGPD                 = 4;
    public const SOFTWARE_CISCO                    = 5;
    public const SOFTWARE_OTHER                    = 99;

    /**
     * @var array Email ids to classes
     */
    public static $SOFTWARES = [
        self::SOFTWARE_BIRD                 => 'Bird v1',
        self::SOFTWARE_BIRD2                => 'Bird v2',
        self::SOFTWARE_QUAGGA               => 'Quagga',
        self::SOFTWARE_FRROUTING            => 'FRRouting',
        self::SOFTWARE_OPENBGPD             => 'OpenBGPd',
        self::SOFTWARE_CISCO                => 'Cisco',
        self::SOFTWARE_OTHER                => 'Other'
    ];

    /**
     * CONST SOFTWARES
     */
    public const API_TYPE_NONE                     = 0;
    public const API_TYPE_BIRDSEYE                 = 1;
    public const API_TYPE_OTHER                    = 99;

    /**
     * @var array Email ids to classes
     */
    public static $API_TYPES = [
        self::API_TYPE_NONE                 => 'None',
        self::API_TYPE_BIRDSEYE             => 'Birdseye',
        self::API_TYPE_OTHER                => 'Other'
    ];

    /**
     * Get the vlan that own the router
     */
    public function vlan(): BelongsTo
    {
        return $this->belongsTo(Vlan::class, 'vlan_id' );
    }

    /**
     * Get the API
     *
     * Alias to allow Entities\Router and Models\Router to work interchangably
     */
    public function api()
    {
        return $this->api;
    }

    /**
     * Get the API type
     *
     * Alias to allow Entities\Router and Models\Router to work interchangably
     */
    public function apiType(): int
    {
        return $this->api_type;
    }

    /**
     * Scope a query to only include servers with an API
     *
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeHasApi( Builder $query ): Builder
    {
        return $query->where('api_type', '>', 0);
    }

    /**
     * Scope a query to only include route servers
     *
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeRouteServer( Builder $query ): Builder
    {
        return $query->where('type', self::TYPE_ROUTE_SERVER);
    }

    /**
     * Scope a query to match IPv4 routers only
     *
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeIPv4( Builder $query ): Builder
    {
        return $query->where('protocol', self::PROTOCOL_IPV4);
    }

    /**
     * Scope a query to match IPv6 routers only
     *
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeIPv6( Builder $query ): Builder
    {
        return $query->where('protocol', self::PROTOCOL_IPV6);
    }

    /**
     * Scope a query to match BGP Large Communities enabled
     *
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeLargeCommunities( Builder $query ): Builder
    {
        return $query->where('bgp_lc', true);
    }

    /**
     * Scope a query to match against quarantine routers
     *
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeNotQuarantine( Builder $query ): Builder
    {
        return $query->where('quarantine', false);
    }

    /**
     * Scope a query to match RPKI enabled
     *
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeRpki( Builder $query ): Builder
    {
        return $query->where('rpki', true);
    }


    /**
     * Turn the database integer representation of the software into text as
     * defined in the self::$SOFTWARES array (or 'Unknown')
     *
     * @return string
     */
    public function resolveSoftware(): string
    {
        return self::$SOFTWARES[ $this->software ] ?? 'Unknown';
    }
}