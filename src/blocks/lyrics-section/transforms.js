
/**
 * WordPress dependencies
 */
import { createBlock } from '@wordpress/blocks';

const transforms = {
	from: [
		{
			type: 'block',
			blocks: [ 'core/paragraph' ],
			transform: function ( attributes ) {

				var lines = ( typeof attributes.content ) === 'string' ? attributes.content.trim().split('<br>') : '';
				var sectionTitle = '';
				var titles = {
					'v1': 'Verse 1',
					'v2': 'Verse 2',
					'v3': 'Verse 3',
					'v4': 'Verse 4',
					'ch': 'Chorus',
					'bridge' : 'Bridge'
				};

				var lowered = lines[0].toLowerCase();
				var substring = null;
				for (const [shortTitle, fullTitle] of Object.entries( titles )) {

					if ( lowered.startsWith( fullTitle.toLowerCase() ) ) {
						substring = fullTitle.length;
					} else if ( lowered.startsWith( shortTitle ) ) {
						substring = shortTitle.length;
					}

					if ( substring ) {
						sectionTitle = fullTitle;
						lines[0] = lines[0].substr( substring )
						break;
					}
				}

				var phrases = [];
				for ( var i = 0; i < lines.length; i++ ) {
					if ( lines[i].match( /[a-z\d ]+/g ) ) {
						phrases.push( { type: 'li', props: { children: [lines[i]] } } );
					}
				}

				return createBlock( 'blythe/lyrics-section', { title: sectionTitle, phrases:  phrases  });
			},
		},
	],
};

export default transforms;