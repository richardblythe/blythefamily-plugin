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

function LyricsSectionEdit( {
						  attributes,
						  setAttributes,
						  mergeBlocks,
						  onReplace,
						  mergedStyle,
						  clientId,
					  } ) {
	const { title, phrases } = attributes;

	return (
		<div { ...useBlockProps() }>
			{
				<BlockControls>
				</BlockControls>
			}

			<RichText
				identifier="content"
				tagName="span"
				multiline={ false }
				value={ title }
				onChange={ ( value ) => setAttributes( { title: value } ) }
				aria-label={ __( 'Section title text' ) }
				placeholder={  __( 'Section Title (Verse, Chorus, etc)' ) }
				className="title"
				keepPlaceholderOnFocus={true}
			/>

			<RichText
				tagName="ul"
				multiline="li"
				className="phrases"
				placeholder={ __(
					'Lyric phrases...',
					'blythe'
				) }
				value={ phrases }
				onChange={ ( value ) => setAttributes( { phrases: value } ) }
				keepPlaceholderOnFocus={true}
			/>
		</div>
	);
}

export default LyricsSectionEdit;