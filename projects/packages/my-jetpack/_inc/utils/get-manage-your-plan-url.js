// eslint-disable-next-line no-unused-vars
/* global myJetpackInitialState */

/**
 * External dependencies
 */
import { getRedirectUrl } from '@automattic/jetpack-components';

/**
 * Internal dependencies
 */
import {
	MY_JETPACK_MY_PLANS_MANAGE_SOURCE,
	MY_JETPACK_MY_PLANS_PURCHASE_SOURCE,
} from '../constants';

/**
 * Return the redurect URL, according to the Jetpack redurects source.
 *
 * @param  {boolean} hasPlan  - Whether the site has a plan already.
 * @returns {string}            the redirect URL
 */
export default function ( hasPlan ) {
	const site = window?.myJetpackInitialState?.siteSuffix;
	return hasPlan
		? getRedirectUrl( MY_JETPACK_MY_PLANS_MANAGE_SOURCE, { site } )
		: getRedirectUrl( MY_JETPACK_MY_PLANS_PURCHASE_SOURCE, { site } );
}
