<?php
/*
Plugin Name: Mercator Domain Replacements
Version: 1.0.1
Plugin URI: https://beapi.fr
Description: Force the replacement of all the original domains of the network by the corresponding mapped domains
Author: Be API
Author URI: https://beapi.fr

----

Copyright 2020 Be API Technical team (humans@beapi.fr)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

namespace BEAPI\Domain_Mapping;

use function Mercator\mangle_url;

class Mapping {
	/** @var array $domains */
	private $domains = [];

	/**
	 * Mapping constructor.
	 *
	 * @return void
	 */
	public function __construct() {
		if ( ! function_exists( '\Mercator\mangle_url' ) ) {
			return;
		}

		add_action( 'init', [ $this, 'domain_mapping_replacements' ], 999 );
	}

	/**
	 * Force to replace URL to final mapped domain
	 *
	 * @return void
	 *
	 * @author Alexandre Sadowski
	 */
	public function domain_mapping_replacements() {
		if ( ! isset( $GLOBALS['mercator_current_mapping'] ) ) {
			return;
		}

		$this->translate_sites_url();
		$this->translate_network_url();
		if ( empty( $this->domains ) ) {
			return;
		}

		ob_start( [ $this, 'replace_urls' ] );
	}

	/**
	 * @return void
	 */
	public function translate_network_url() {
		$current_network = get_network();
		$current_site    = get_site();
		if ( null === $current_site || null === $current_network ) {
			return;
		}

		$network_domain_internal = untrailingslashit( 'https://' . $current_network->domain . $current_network->path );
		$domain_internal         = untrailingslashit( 'https://' . $current_site->domain . $current_site->path );
		$domain_mapped           = mangle_url( untrailingslashit( $current_site->siteurl ), '', '', $current_site->id );

		// Site domain URL
		if ( $domain_internal !== $domain_mapped ) {
			$this->domains[ $domain_internal ] = $domain_mapped;
		}

		// Network domain URL
		if ( $network_domain_internal !== $domain_mapped ) {
			$upload_mapped_url                                        = wp_upload_dir()['baseurl'];
			$upload_path                                              = str_replace( $domain_mapped, '', $upload_mapped_url );
			$this->domains[ $network_domain_internal . $upload_path ] = $domain_mapped . $upload_path;
		}
	}

	/**
	 * @return void
	 */
	public function translate_sites_url() {
		$site_query = new \WP_Site_Query(
			[
				'fields'  => 'ids',
				'number'  => 500,
				'public'  => '1',
				'order'   => 'ASC',
				'orderby' => 'id',
			]
		);

		$sites = $site_query->get_sites();
		if ( empty( $sites ) ) {
			return;
		}

		// Backup GLOBAL if existing
		$_tmp = isset( $GLOBALS['mercator_current_mapping'] ) ? $GLOBALS['mercator_current_mapping'] : null;

		foreach ( $sites as $site_id ) {
			$mappings = \Mercator\Mapping::get_by_site( $site_id );
			if ( ! $mappings || is_wp_error( $mappings ) ) {
				continue;
			}

			$current_site = get_site( $site_id );
			if ( null === $current_site ) {
				continue;
			}

			$domain_internal = untrailingslashit( $current_site->siteurl );

			foreach ( $mappings as $mapping ) {
				/** @var \Mercator\Mapping $mapping */
				if ( ! $mapping->is_active() ) {
					continue;
				}

				$GLOBALS['mercator_current_mapping'] = $mapping;

				$mapped_url = mangle_url( $domain_internal, '', '', $site_id );
				if ( $mapped_url === $domain_internal ) {
					continue;
				}

				$this->domains[ $domain_internal ] = $mapped_url;
			}
		}

		$GLOBALS['mercator_current_mapping'] = $_tmp;
	}

	/**
	 * Replace URLS
	 * Example :
	 *      https://monsite.com > https://mondomain.com
	 *      https:\/\/monsite.com > https:\/\/mondomain.com localized in JS
	 *
	 * @param string $buffer
	 *
	 * @return string
	 *
	 * @author Alexandre Sadowski
	 */
	public function replace_urls( $buffer ) {
		foreach ( $this->domains as $source => $target ) {
			$buffer = str_replace(
				[
					$source,
					$this->replace_slashes( $source ),
					$this->replace_scheme( $source ),
				],
				[
					$target,
					$this->replace_slashes( $target ),
					$this->replace_scheme( $target ),
				],
				$buffer
			);
		}

		return $buffer;
	}

	/**
	 * Add backslashes for JS
	 *
	 * @param string $content
	 *
	 * @return string
	 *
	 * @author Alexandre Sadowski
	 */
	private function replace_slashes( $content ) {
		return str_replace( '/', '\/', $content );
	}

	/**
	 * Transform also URL with ://, without defined scheme
	 *
	 * @param string $content
	 *
	 * @return string
	 *
	 * @author Alexandre Sadowski
	 */
	private function replace_scheme( $content ) {
		return str_replace( [ 'http://', 'https://' ], '//', $content );
	}

}

new Mapping();
