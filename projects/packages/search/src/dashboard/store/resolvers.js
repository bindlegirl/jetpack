/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import { fetchJetpackSettings, fetchSearchPlanInfo, fetchSearchStats } from './controls';
import { setJetpackSettings } from './actions/jetpack-settings';
import { setSearchPlanInfo } from './actions/site-plan';
import { setSearchStats } from './actions/site-stats';
import { errorNotice } from '../components/global-notices/store/actions';

/**
 * Yield actions to get Search Module Status
 *
 * @yields {object} - an action object.
 * @returns {object} - an action object.
 */
export function* getSearchModuleStatus() {
	try {
		const settings = yield fetchJetpackSettings();
		if ( settings ) {
			return setJetpackSettings( settings );
		}
	} catch ( e ) {
		return errorNotice( __( 'Error fetching settings…', 'jetpack-search-pkg' ) );
	}
}

/**
 * Yield actions to get search plan info
 *
 * @yields {object} - an action object.
 * @returns {object} - an action object.
 */
export function* getSearchPlanInfo() {
	try {
		const planInfo = yield fetchSearchPlanInfo();
		if ( planInfo ) {
			return setSearchPlanInfo( planInfo );
		}
	} catch ( e ) {
		return errorNotice( __( 'Error fetching search plan…', 'jetpack-search-pkg' ) );
	}
}

/**
 * Yield actions to get search stats
 *
 * @yields {object} - an action object.
 * @returns {object} - an action object.
 */
export function* getSearchStats() {
	try {
		const stats = yield fetchSearchStats();
		if ( stats ) {
			return setSearchStats( stats );
		}
	} catch ( e ) {
		return errorNotice( __( 'Error fetching search stats', 'jetpack-search-pkg' ) );
	}
}

export default { getSearchModuleStatus, getSearchPlanInfo, getSearchStats };
