
/**
 * External dependencies
 */
import classnames from 'classnames';

/**
 * WordPress dependencies
 */
import { RichText, useBlockProps } from '@wordpress/block-editor';

export default function save( { attributes } ) {
	const { title, phrases } = attributes;

	return (
		<div className="wp-block lyrics-section">
			<RichText.Content
				tagName="span"
				className="title"
				value={ title }
			/>
			<RichText.Content
				tagName="ul"
				multiline="li"
				className="phrases"
				value={ phrases }
			/>
		</div>
	);
}