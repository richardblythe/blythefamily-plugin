
/**
 * External dependencies
 */
import classnames from 'classnames';

/**
 * WordPress dependencies
 */
import { RichText, useBlockProps } from '@wordpress/block-editor';
import {__} from "@wordpress/i18n";

export default function save( { attributes } ) {
	const { description, scriptures } = attributes;

	return (
		<div className="wp-block episode-info">
			<RichText.Content
				tagName="p"
				className="description"
				value={ description }
			/>
			<h3>Scripture Reading:</h3>
			<RichText.Content
				tagName="ul"
				multiline="li"
				className="scriptures"
				value={ scriptures }
			/>
		</div>
	);
}