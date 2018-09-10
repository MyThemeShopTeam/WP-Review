<?php
/**
 * Schemas list
 *
 * @package WP_Review
 * @since 3.0.0
 */

return array(
	'none' => array(
		'label' => __( 'None', 'wp-review' ),
	),
	'Article' => array(
		'label' => __( 'Article', 'wp-review' ),
		'fields' => array(
			array(
				'name'    => 'headline',
				'label'   => __( 'Article Title', 'wp-review' ),
				'type'    => 'text',
				'default' => '',
			),
			array(
				'name'    => 'description',
				'label'   => __( 'Short Description', 'wp-review' ),
				'type'    => 'textarea',
				'default' => '',
			),
			array(
				'name'    => 'image',
				'label'   => __( 'Article Image', 'wp-review' ),
				'type'    => 'image',
				'default' => '',
			),
			array(
				'name'    => 'author',
				'label'   => __( 'Author', 'wp-review' ),
				'type'    => 'text',
				'default' => '',
				'part_of' => 'author',
				'@type'   => 'Person',
			),
			array(
				'name'    => 'publisher',
				'label'   => __( 'Publisher - Orgnization', 'wp-review' ),
				'type'    => 'text',
				'default' => '',
				'part_of' => 'publisher',
				'@type'   => 'Organization',
			),
			array(
				'name'    => 'publisher_logo',
				'label'   => __( 'Publisher Logo', 'wp-review' ),
				'type'    => 'image',
				'default' => '',
				'part_of' => 'publisher',
				'@type'   => 'Organization',
			),
		),
	),
	'Book' => array(
		'label' => __( 'Book', 'wp-review' ),
		'fields' => array(
			array(
				'name' => 'name',
				'label' => __( 'Book Title', 'wp-review' ),
				'type' => 'text',
				'default' => '',
			),
			array(
				'name' => 'description',
				'label' => __( 'Book Description', 'wp-review' ),
				'type' => 'textarea',
				'default' => ''
			),
			array(
				'name' => 'image',
				'label' => __( 'Book Image', 'wp-review' ),
				'type' => 'image',
				'default' => ''
			),
			array(
				'name' => 'more_text',
				'label' => __( 'More link text', 'wp-review' ),
				'type' => 'text',
				'default' => __( '[ More ]', 'wp-review' ),
				'omit'    => true,
			),
			array(
				'name' => 'url',
				'label' => __( 'More link URL', 'wp-review' ),
				'type' => 'text',
				'default' => ''
			),
			array(
				'name' => 'use_button_style',
				'label' => __( 'Use button style', 'wp-review' ),
				'type' => 'switch',
				'default' => false,
				'omit'    => true,
				'on_label' => __( 'Button', 'wp-review' ),
				'off_label' => __( 'Link', 'wp-review' ),
			),
			array(
				'name' => 'author',
				'label' => __( 'Book Author', 'wp-review' ),
				'type' => 'text',
				'default' => ''
			),
			array(
				'name' => 'bookEdition',
				'label' => __( 'Book Edition', 'wp-review' ),
				'type' => 'text',
				'default' => ''
			),
			array(
				'name' => 'bookFormat',
				'label' => __( 'Book Format', 'wp-review' ),
				'type' => 'select',
				'default' => '',
				'options' => array(
					'' => '---',
					'AudiobookFormat' => 'AudiobookFormat',
					'EBook' => 'EBook',
					'Hardcover' => 'Hardcover',
					'Paperback' => 'Paperback'
				)
			),
			array(
				'name' => 'datePublished',
				'label' => __( 'Date published', 'wp-review' ),
				'type' => 'date',
				'default' => '',
			),
			array(
				'name' => 'illustrator',
				'label' => __( 'Illustrator', 'wp-review' ),
				'type' => 'text',
				'default' => ''
			),
			array(
				'name' => 'isbn',
				'label' => __( 'ISBN', 'wp-review' ),
				'type' => 'text',
				'default' => ''
			),
			array(
				'name' => 'numberOfPages',
				'label' => __( 'Number Of Pages', 'wp-review' ),
				'type' => 'number',
				'default' => ''
			)
		)
	),
	'Game' => array(
		'label' => __( 'Game', 'wp-review' ),
		'fields' => array(
			array(
				'name' => 'name',
				'label' => __( 'Game title', 'wp-review' ),
				'type' => 'text',
				'default' => '',
			),
			array(
				'name' => 'description',
				'label' => __( 'Game description', 'wp-review' ),
				'type' => 'textarea',
				'default' => ''
			),
			array(
				'name' => 'image',
				'label' => __( 'Game Image', 'wp-review' ),
				'type' => 'image',
				'default' => ''
			),
			array(
				'name' => 'more_text',
				'label' => __( 'More link text', 'wp-review' ),
				'type' => 'text',
				'default' => __( '[ More ]', 'wp-review' ),
				'omit'    => true,
			),
			array(
				'name' => 'url',
				'label' => __( 'More link URL', 'wp-review' ),
				'type' => 'text',
				'default' => ''
			),
			array(
				'name' => 'use_button_style',
				'label' => __( 'Use button style', 'wp-review' ),
				'type' => 'switch',
				'default' => false,
				'omit'    => true,
				'on_label' => __( 'Button', 'wp-review' ),
				'off_label' => __( 'Link', 'wp-review' ),
			),
		)
	),
	'Movie' => array(
		'label' => __( 'Movie', 'wp-review' ),
		'fields' => array(
			array(
				'name' => 'name',
				'label' => __( 'Movie title', 'wp-review' ),
				'type' => 'text',
				'default' => '',
			),
			array(
				'name' => 'description',
				'label' => __( 'Movie description', 'wp-review' ),
				'type' => 'textarea',
				'default' => ''
			),
			array(
				'name' => 'image',
				'label' => __( 'Movie Image', 'wp-review' ),
				'type' => 'image',
				'default' => ''
			),
			array(
				'name' => 'more_text',
				'label' => __( 'More link text', 'wp-review' ),
				'type' => 'text',
				'default' => __( '[ More ]', 'wp-review' ),
				'omit'    => true,
			),
			array(
				'name' => 'url',
				'label' => __( 'More link URL', 'wp-review' ),
				'type' => 'text',
				'default' => ''
			),
			array(
				'name' => 'use_button_style',
				'label' => __( 'Use button style', 'wp-review' ),
				'type' => 'switch',
				'default' => false,
				'omit'    => true,
				'on_label' => __( 'Button', 'wp-review' ),
				'off_label' => __( 'Link', 'wp-review' ),
			),
			array(
				'name' => 'dateCreated',
				'label' => __( 'Date published', 'wp-review' ),
				'type' => 'date',
				'default' => '',
			),
			array(
				'name' => 'director',
				'label' => __( 'Director(s)', 'wp-review' ),
				'type' => 'textarea',
				'multiline' => true,
				'default' => '',
				'info' => __('Add one director per line', 'wp-review'),
			),
			array(
				'name' => 'actor',
				'label' => __( 'Actor(s)', 'wp-review' ),
				'type' => 'textarea',
				'multiline' => true,
				'default' => '',
				'info' => __('Add one actor per line', 'wp-review'),
			),
			array(
				'name' => 'genre',
				'label' => __( 'Genre', 'wp-review' ),
				'type' => 'textarea',
				'multiline' => true,
				'default' => '',
				'info' => __('Add one item per line', 'wp-review'),
			),
		)
	),
	'MusicRecording' => array(
		'label' => __( 'MusicRecording', 'wp-review' ),
		'fields' => array(
			array(
				'name' => 'name',
				'label' => __( 'Track name', 'wp-review' ),
				'type' => 'text',
				'default' => '',
			),
			array(
				'name' => 'more_text',
				'label' => __( 'More link text', 'wp-review' ),
				'type' => 'text',
				'default' => __( '[ More ]', 'wp-review' ),
				'omit'    => true,
			),
			array(
				'name' => 'url',
				'label' => __( 'More link URL', 'wp-review' ),
				'type' => 'text',
				'default' => ''
			),
			array(
				'name' => 'use_button_style',
				'label' => __( 'Use button style', 'wp-review' ),
				'type' => 'switch',
				'default' => false,
				'omit'    => true,
				'on_label' => __( 'Button', 'wp-review' ),
				'off_label' => __( 'Link', 'wp-review' ),
			),
			array(
				'name' => 'byArtist',
				'label' => __( 'Author', 'wp-review' ),
				'type' => 'text',
				'default' => '',
			),
			array(
				'name' => 'duration',
				'label' => __( 'Track Duration', 'wp-review' ),
				'type' => 'text',
				'default' => ''
			),
			array(
				'name' => 'inAlbum',
				'label' => __( 'Album name', 'wp-review' ),
				'type' => 'text',
				'default' => ''
			),
			array(
				'name' => 'genre',
				'label' => __( 'Genre', 'wp-review' ),
				'type' => 'textarea',
				'multiline' => true,
				'default' => '',
				'info' => __('Add one item per line', 'wp-review'),
			),
		)
	),
	'Painting' => array(
		'label' => __( 'Painting', 'wp-review' ),
		'fields' => array(
			array(
				'name' => 'name',
				'label' => __( 'Name', 'wp-review' ),
				'type' => 'text',
				'default' => '',
			),
			array(
				'name' => 'author',
				'label' => __( 'Author', 'wp-review' ),
				'type' => 'text',
				'default' => '',
			),
			array(
				'name' => 'image',
				'label' => __( 'Image', 'wp-review' ),
				'type' => 'image',
				'default' => ''
			),
			array(
				'name' => 'more_text',
				'label' => __( 'More link text', 'wp-review' ),
				'type' => 'text',
				'default' => __( '[ More ]', 'wp-review' ),
				'omit'    => true,
			),
			array(
				'name' => 'url',
				'label' => __( 'More link URL', 'wp-review' ),
				'type' => 'text',
				'default' => ''
			),
			array(
				'name' => 'use_button_style',
				'label' => __( 'Use button style', 'wp-review' ),
				'type' => 'switch',
				'default' => false,
				'omit'    => true,
				'on_label' => __( 'Button', 'wp-review' ),
				'off_label' => __( 'Link', 'wp-review' ),
			),
			array(
				'name' => 'genre',
				'label' => __( 'Genre', 'wp-review' ),
				'type' => 'textarea',
				'multiline' => true,
				'default' => '',
				'info' => __('Add one item per line', 'wp-review'),
			),
			array(
				'name' => 'datePublished',
				'label' => __( 'Date published', 'wp-review' ),
				'type' => 'date',
				'default' => '',
			),
		)
	),
	'Place' => array(
		'label' => __( 'Place', 'wp-review' ),
		'fields' => array(
			array(
				'name' => 'name',
				'label' => __( 'Place Name', 'wp-review' ),
				'type' => 'text',
				'default' => '',
			),
			array(
				'name' => 'description',
				'label' => __( 'Place Description', 'wp-review' ),
				'type' => 'textarea',
				'default' => ''
			),
			array(
				'name' => 'image',
				'label' => __( 'Place Image', 'wp-review' ),
				'type' => 'image',
				'default' => ''
			),
			array(
				'name' => 'more_text',
				'label' => __( 'More link text', 'wp-review' ),
				'type' => 'text',
				'default' => __( '[ More ]', 'wp-review' ),
				'omit'    => true,
			),
			array(
				'name' => 'url',
				'label' => __( 'More link URL', 'wp-review' ),
				'type' => 'text',
				'default' => ''
			),
			array(
				'name' => 'use_button_style',
				'label' => __( 'Use button style', 'wp-review' ),
				'type' => 'switch',
				'default' => false,
				'omit'    => true,
				'on_label' => __( 'Button', 'wp-review' ),
				'off_label' => __( 'Link', 'wp-review' ),
			),
		)
	),
	'Product' => array(
		'label' => __( 'Product', 'wp-review' ),
		'fields' => array(
			array(
				'name' => 'name',
				'label' => __( 'Product Name', 'wp-review' ),
				'type' => 'text',
				'default' => '',
			),
			array(
				'name' => 'description',
				'label' => __( 'Product Description', 'wp-review' ),
				'type' => 'textarea',
				'default' => ''
			),
			array(
				'name' => 'image',
				'label' => __( 'Product Image', 'wp-review' ),
				'type' => 'image',
				'default' => ''
			),
			array(
				'name' => 'more_text',
				'label' => __( 'More link text', 'wp-review' ),
				'type' => 'text',
				'default' => __( '[ More ]', 'wp-review' ),
				'omit'    => true,
			),
			array(
				'name' => 'url',
				'label' => __( 'More link URL', 'wp-review' ),
				'type' => 'text',
				'default' => ''
			),
			array(
				'name' => 'use_button_style',
				'label' => __( 'Use button style', 'wp-review' ),
				'type' => 'switch',
				'default' => false,
				'omit'    => true,
				'on_label' => __( 'Button', 'wp-review' ),
				'off_label' => __( 'Link', 'wp-review' ),
			),
			array(
				'name' => 'price',
				'label' => __( 'Price', 'wp-review' ),
				'type' => 'text',
				'default' => '',
				'part_of' => 'offers',
				'@type' => 'Offer'
			),
			array(
				'name' => 'priceCurrency',
				'label' => __( 'Currency', 'wp-review' ),
				'type' => 'text',
				'default' => '',
				'part_of' => 'offers',
				'@type' => 'Offer'
			),
			array(
				'name' => 'availability',
				'label' => __( 'Availability', 'wp-review' ),
				'type' => 'select',
				'default' => '',
				'options' => array(
					'' => '---',
					'Discontinued' => 'Discontinued',
					'InStock' => 'In Stock',
					'InStoreOnly' => 'In Store Only',
					'LimitedAvailability' => 'Limited',
					'OnlineOnly' => 'Online Only',
					'OutOfStock' => 'Out Of Stock',
					'PreOrder' => 'Pre Order',
					'PreSale' => 'Pre Sale',
					'SoldOut' => 'Sold Out'
				),
				'part_of' => 'offers',
				'@type' => 'Offer'
			),
		)
	),
	'Recipe' => array(
		'label' => __( 'Recipe', 'wp-review' ),
		'fields' => array(
			array(
				'name' => 'name',
				'label' => __( 'Recipe Name', 'wp-review' ),
				'type' => 'text',
				'default' => '',
			),
			array(
				'name' => 'author',
				'label' => __( 'Author', 'wp-review' ),
				'type' => 'text',
				'default' => '',
			),
			array(
				'name' => 'description',
				'label' => __( 'Description', 'wp-review' ),
				'type' => 'textarea',
				'default' => ''
			),
			array(
				'name' => 'image',
				'label' => __( 'Image', 'wp-review' ),
				'type' => 'image',
				'default' => ''
			),
			array(
				'name' => 'prepTime',
				'label' => __( 'Preperation time', 'wp-review' ),
				'type' => 'text',
				'default' => '',
				'info' => __('Format: 1H30M. H - Hours, M - Minutes', 'wp-review')
			),
			array(
				'name' => 'cookTime',
				'label' => __( 'Cook Time', 'wp-review' ),
				'type' => 'text',
				'default' => '',
				'info' => __('Format: 1H30M. H - Hours, M - Minutes', 'wp-review')
			),
			array(
				'name' => 'totalTime',
				'label' => __( 'Total Time', 'wp-review' ),
				'type' => 'text',
				'default' => '',
				'info' => __('Format: 1H30M. H - Hours, M - Minutes', 'wp-review')
			),
			array(
				'name' => 'recipeCategory',
				'label' => __( 'Type', 'wp-review' ),
				'type' => 'text',
				'default' => '',
				'info' => __('Type of dish, for example "appetizer", "entree", or "dessert"', 'wp-review')
			),
			array(
				'name' => 'recipeYield',
				'label' => __( 'Recipe Yield', 'wp-review' ),
				'type' => 'text',
				'default' => '',
				'info' => __('Quantity produced by the recipe, for example "4 servings"', 'wp-review')
			),
			array(
				'name' => 'recipeIngredient',
				'label' => __( 'Recipe Ingredients', 'wp-review' ),
				'type' => 'textarea',
				'multiline' => true,
				'default' => '',
				'info' => __('Recipe ingredients, add one item per line', 'wp-review'),
			),
			array(
				'name' => 'recipeInstructions',
				'label' => __( 'Recipe Instructions', 'wp-review' ),
				'type' => 'textarea',
				'default' => '',
				'info' => __('Steps to take', 'wp-review')
			),
			array(
				'name' => 'calories',
				'label' => __( 'Calories', 'wp-review' ),
				'type' => 'text',
				'default' => '',
				'info' => __('The number of calories', 'wp-review'),
				'part_of' => 'nutrition',
				'@type' => 'NutritionInformation'
			),

		)
	),
	'Restaurant' => array(
		'label' => __( 'Restaurant', 'wp-review' ),
		'fields' => array(
			array(
				'name' => 'name',
				'label' => __( 'Restaurant Name', 'wp-review' ),
				'type' => 'text',
				'default' => '',
			),
			array(
				'name' => 'description',
				'label' => __( 'Restaurant Description', 'wp-review' ),
				'type' => 'textarea',
				'default' => '',
			),
			array(
				'name' => 'image',
				'label' => __( 'Restaurant Image', 'wp-review' ),
				'type' => 'image',
				'default' => '',
			),
			array(
				'name' => 'more_text',
				'label' => __( 'More link text', 'wp-review' ),
				'type' => 'text',
				'default' => __( '[ More ]', 'wp-review' ),
				'omit'    => true,
			),
			array(
				'name' => 'url',
				'label' => __( 'More link URL', 'wp-review' ),
				'type' => 'text',
				'default' => ''
			),
			array(
				'name' => 'use_button_style',
				'label' => __( 'Use button style', 'wp-review' ),
				'type' => 'switch',
				'default' => false,
				'omit'    => true,
				'on_label' => __( 'Button', 'wp-review' ),
				'off_label' => __( 'Link', 'wp-review' ),
			),
			array(
				'name'    => 'priceRange',
				'label'   => __( 'Price range', 'wp-review' ),
				'type'    => 'text',
				'default' => '$10 - $30',
			),
		),
	),
	'SoftwareApplication' => array(
		'label' => __( 'SoftwareApplication', 'wp-review' ),
		'fields' => array(
			array(
				'name' => 'name',
				'label' => __( 'Name', 'wp-review' ),
				'type' => 'text',
				'default' => '',
			),
			array(
				'name' => 'description',
				'label' => __( 'Description', 'wp-review' ),
				'type' => 'textarea',
				'default' => ''
			),
			array(
				'name' => 'image',
				'label' => __( 'Image', 'wp-review' ),
				'type' => 'image',
				'default' => ''
			),
			array(
				'name' => 'more_text',
				'label' => __( 'More link text', 'wp-review' ),
				'type' => 'text',
				'default' => __( '[ More ]', 'wp-review' ),
				'omit'    => true,
			),
			array(
				'name' => 'url',
				'label' => __( 'More link URL', 'wp-review' ),
				'type' => 'text',
				'default' => ''
			),
			array(
				'name' => 'use_button_style',
				'label' => __( 'Use button style', 'wp-review' ),
				'type' => 'switch',
				'default' => false,
				'omit'    => true,
				'on_label' => __( 'Button', 'wp-review' ),
				'off_label' => __( 'Link', 'wp-review' ),
			),
			array(
				'name' => 'price',
				'label' => __( 'Price', 'wp-review' ),
				'type' => 'text',
				'default' => '',
				'part_of' => 'offers',
				'@type' => 'Offer'
			),
			array(
				'name' => 'priceCurrency',
				'label' => __( 'Currency', 'wp-review' ),
				'type' => 'text',
				'default' => '',
				'part_of' => 'offers',
				'@type' => 'Offer'
			),
			array(
				'name' => 'operatingSystem',
				'label' => __( 'Operating System', 'wp-review' ),
				'type' => 'text',
				'default' => '',
				'info' => __('For example, "Windows 7", "OSX 10.6", "Android 1.6"', 'wp-review')
			),
			array(
				'name' => 'applicationCategory',
				'label' => __( 'Application Category', 'wp-review' ),
				'type' => 'text',
				'default' => '',
				'info' => __('For example, "Game", "Multimedia"', 'wp-review')
			)
		)
	),
	'Store' => array(
		'label' => __( 'Store', 'wp-review' ),
		'fields' => array(
			array(
				'name' => 'name',
				'label' => __( 'Store Name', 'wp-review' ),
				'type' => 'text',
				'default' => '',
			),
			array(
				'name' => 'description',
				'label' => __( 'Store Description', 'wp-review' ),
				'type' => 'textarea',
				'default' => '',
			),
			array(
				'name' => 'image',
				'label' => __( 'Store Image', 'wp-review' ),
				'type' => 'image',
				'default' => '',
			),
			array(
				'name' => 'more_text',
				'label' => __( 'More link text', 'wp-review' ),
				'type' => 'text',
				'default' => __( '[ More ]', 'wp-review' ),
				'omit'    => true,
			),
			array(
				'name' => 'url',
				'label' => __( 'More link URL', 'wp-review' ),
				'type' => 'text',
				'default' => ''
			),
			array(
				'name' => 'use_button_style',
				'label' => __( 'Use button style', 'wp-review' ),
				'type' => 'switch',
				'default' => false,
				'omit'    => true,
				'on_label' => __( 'Button', 'wp-review' ),
				'off_label' => __( 'Link', 'wp-review' ),
			),
			array(
				'name'    => 'priceRange',
				'label'   => __( 'Price range', 'wp-review' ),
				'type'    => 'text',
				'default' => '$10 - $30',
			),
			array(
				'name'    => 'address',
				'label'   => __( 'Address', 'wp-review' ),
				'type'    => 'text',
			),
			array(
				'name'    => 'telephone',
				'label'   => __( 'Telephone', 'wp-review' ),
				'type'    => 'text',
			),
		),
	),
	'Thing' => array(
		'label' => __( 'Thing (Default)', 'wp-review' )
	),
	'TVSeries' => array(
		'label' => __( 'TVSeries', 'wp-review' ),
		'fields' => array(
			array(
				'name' => 'name',
				'label' => __( 'Name', 'wp-review' ),
				'type' => 'text',
				'default' => '',
			),
			array(
				'name' => 'description',
				'label' => __( 'Description', 'wp-review' ),
				'type' => 'textarea',
				'default' => ''
			),
			array(
				'name' => 'image',
				'label' => __( 'Image', 'wp-review' ),
				'type' => 'image',
				'default' => ''
			),
			array(
				'name' => 'more_text',
				'label' => __( 'More link text', 'wp-review' ),
				'type' => 'text',
				'default' => __( '[ More ]', 'wp-review' ),
				'omit'    => true,
			),
			array(
				'name' => 'url',
				'label' => __( 'More link URL', 'wp-review' ),
				'type' => 'text',
				'default' => ''
			),
			array(
				'name' => 'use_button_style',
				'label' => __( 'Use button style', 'wp-review' ),
				'type' => 'switch',
				'default' => false,
				'omit'    => true,
				'on_label' => __( 'Button', 'wp-review' ),
				'off_label' => __( 'Link', 'wp-review' ),
			),
		)
	),
	'WebSite' => array(
		'label' => __( 'WebSite', 'wp-review' ),
		'fields' => array(
			array(
				'name' => 'name',
				'label' => __( 'Name', 'wp-review' ),
				'type' => 'text',
				'default' => '',
			),
			array(
				'name' => 'description',
				'label' => __( 'Description', 'wp-review' ),
				'type' => 'textarea',
				'default' => ''
			),
			array(
				'name' => 'image',
				'label' => __( 'Image', 'wp-review' ),
				'type' => 'image',
				'default' => ''
			),
			array(
				'name' => 'more_text',
				'label' => __( 'More link text', 'wp-review' ),
				'type' => 'text',
				'default' => __( '[ More ]', 'wp-review' ),
				'omit'    => true,
			),
			array(
				'name' => 'url',
				'label' => __( 'More link URL', 'wp-review' ),
				'type' => 'text',
				'default' => ''
			),
			array(
				'name' => 'use_button_style',
				'label' => __( 'Use button style', 'wp-review' ),
				'type' => 'switch',
				'default' => false,
				'omit'    => true,
				'on_label' => __( 'Button', 'wp-review' ),
				'off_label' => __( 'Link', 'wp-review' ),
			),
		)
	)
);
