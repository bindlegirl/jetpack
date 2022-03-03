/* * External dependencies
 */
import React, { useState } from 'react';
import { __ } from '@wordpress/i18n';

/**
 * WordPress dependencies
 */
import { useSelect } from '@wordpress/data';

/**
 * Internal dependencies
 */
import { STORE_ID } from 'store';
import { BarChart } from './barchart';
import getRecordInfo from './lib/recordInfo';
import createData from './lib/createData';

import './style.scss';

/**
 * Generate Record Meter showing how many records the user has indexed
 *
 * @param {object} props - Props
 * @param {number} props.postCount - Post count
 * @param {object} props.postTypeBreakdown - Post type breakdown (post type => number of posts)
 * @param {number} props.tierMaximumRecords - Max number of records allowed in user's current tier
 * @returns {React.Component} RecordMeter React component
 */
export default function RecordMeter() {
	const tierMaximumRecords = useSelect( select => select( STORE_ID ).getTierMaximumRecords() );

	const [ recordInfo, setRecordInfo ] = useState(
		getRecordInfo( createData().data, createData().planInfo )
	);

	// quick console log for the moment, just to check the correct data & correct form is coming through the state
	console.log( recordInfo.data );
	console.log( recordInfo.isValid );

	return (
		<div className="jp-search-record-meter jp-search-dashboard-wrap">
			<div className="jp-search-dashboard-row">
				<div className="jp-search-record-meter__title lg-col-span-8 md-col-span-6 sm-col-span-4">
					<h2>{ __( 'Your search records', 'jetpack-search-pkg' ) }</h2>
					{ tierMaximumRecords && (
						<p>
							Tier maximum records: <strong>{ tierMaximumRecords }</strong>
							<BarChart data={ recordInfo.data } isValid={ recordInfo.isValid } />
						</p>
					) }
					{ postCount && (
						<p>
							Post count: <strong>{ postCount }</strong>
						</p>
					) }
					{ postTypeBreakdown && (
						<p>
							Post type breakdown:
							<table>
								{ Object.entries( postTypeBreakdown ).map( postType => (
									<tr>
										<td>{ postType[ 0 ] }</td>
										<td>
											<strong>{ postType[ 1 ] }</strong>
										</td>
									</tr>
								) ) }
							</table>
						</p>
					) }
				</div>
				<div className="lg-col-span-2 md-col-span-1 sm-col-span-0"></div>
			</div>
		</div>
	);
}
