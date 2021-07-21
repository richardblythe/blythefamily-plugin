/**
 * External dependencies
 */
import classnames from 'classnames';

import './editor.scss';
/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import {
	AlignmentToolbar,
	BlockControls,
	RichText,
	useBlockProps,
} from '@wordpress/block-editor';

import { Fragment } from '@wordpress/element';

function EpisodeEdit( {
						  attributes,
						  setAttributes,
						  mergeBlocks,
						  onReplace,
						  mergedStyle,
						  clientId,
					  } ) {
	const { description, scriptures } = attributes;

	return (
		<div { ...useBlockProps() }>
			{
				<BlockControls>
				</BlockControls>
			}

			<h3>Episode Description:</h3>
			<RichText
				identifier="description"
				tagName="p"
				multiline={ false }
				value={ description }
				onChange={ ( value ) => setAttributes( { description: value } ) }
				aria-label={ __( 'Episode description' ) }
				placeholder={  __( 'Episode description...' ) }
				className="description"
				keepPlaceholderOnFocus={true}
			/>

			<h3>Scripture Reading:</h3>
			<RichText
				tagName="ul"
				multiline="li"
				className="scriptures"
				placeholder={ __(
					'Example: Genesis 1:2-4',
					'blythe'
				) }
				value={ scriptures }
				onChange={ ( value ) => setAttributes( { scriptures: value } ) }
				keepPlaceholderOnFocus={true}
			/>
		</div>
	);
}

export default EpisodeEdit;