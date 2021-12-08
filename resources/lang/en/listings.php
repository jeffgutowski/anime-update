<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Listings Language Lines
    |--------------------------------------------------------------------------
    */

    'general' => [
      'new' => 'New!',
      'newest_listings' => 'Newest Listings',
      'all_listings' => 'All Listings',
      'listings' => 'Listings',
      'sell' => 'Sell',
      'trade' => 'Trade',
      'pickup' => 'Pickup',
      'delivery' => 'Delivery',
      'condition' => 'Condition',
      'digital_download' => 'Digital Download',
      'no_listings' => 'There are no listings available.',
      'no_listings_add' => 'Add Listing',
      'sold' => 'Not available',
      /* Start new strings v1.4.1 */
      'show_all' => 'Show all listings',
      /* End new strings v1.4.1 */
      'deleted' => 'Listing deleted.',
      'deleted_game' => 'Game deleted from system.',
      'no_description' => 'No description',
      'open_google_maps' => 'Open in Google Maps',
      'components' => 'Components',
      'extra_components' => 'Extra Components',
      'conditions' => [
          '8' => 'Graded',
          '7' => 'New and Sealed',
          '6' => 'Mint',
          '5' => 'Near Mint',
          '4' => 'Very Good',
          '3' => 'Good',
          '2' => 'Fair',
          '1' => 'Poor',
          '0' => 'Very Poor',
      ],
      'conditions_description' => [
          '8' => 'Has an official grading (grading details must be stated in description section)',
          '7' => 'Is in new condition and is factory sealed',
          '6' => 'Is in flawless condition without any defects or imperfections',
          '5' => 'Is in near perfect condition with no obvious signs of use',
          '4' => 'Shows signs of slight wear from natural use (very light scratch, very mild wear on corners, etc…)',
          '3' => 'Shows obvious wear from natural use (light scratches, light scuffs, mild bends or warn corners, etc…)',
          '2' => 'Shows heavy wear from natural use (scratches, scuffs, small tears, etc…)',
          '1' => 'Shows obvious wear and may have some unnatural signs of wear (water damage, broken parts, smoke odor, all of which must be stated in the description section)',
          '0' => 'Is not fully functional and/or damaged (details must be stated in description section)',
      ],
    ],

    'overview' => [
      'created' => 'Created',
      'trade_info' => 'Please select the game you want trade for :Game_name.',
      'subheader' => [
          'buy_now' => 'Buy Now',
          'go_gameoverview' => 'Go to Gameoverview',
          'details' => 'Details',
          'media' => 'Images & Videos',
      ],
    ],

    'form' => [
      'edit' => 'Edit Listing',
      'add' => 'Add Listing',
      'game' => [
          'select' => 'Select Product',
          'selected' => 'Selected Product',
          'add' => 'Suggest Product',
          'not_found' => 'Product not found?',
          'reselect' => 'Reselect Product',
          'reselect_info' => 'Warning: All Inputs will be cleared!',
      ],
      'details_title' => 'Details',
      'details' => [
          'digital' => 'Digital Download',
          'limited' => 'Limited Edition',
          'description' => 'Description',
          'delivery_info' => 'No input for free delivery.',
      ],
      /* Start new strings v1.4.0 */
      'image_upload' => [
          'images' => 'Images',
          'empty_message' => 'Drop image files here or click to upload.',
          'max_files_exceeded' => 'You can not upload any more files.',
          'already_exists' => 'A file with this name already exists in the queue.',
          'invalid_type' => 'You cannot upload files of this type.'
      ],
      /* Start new strings v1.4.0 */
      'sell_title' => 'Sell details',
      'sell' => [
          'avgprice' => 'Average selling price for :game_name: <strong>:avgprice</strong>',
          'price' => 'Price',
          'price_suggestions' => 'Price suggestions',
      ],
      'trade_title' => 'Trade details',
      'trade' => [
          'add_to_tradelist' => 'Add game to tradelist',
          'remove' => 'Remove',
          'additional_charge_partner' => 'Additional charge from trade partner',
          'additional_charge_self' => 'Additional charge from you',
          'trade_suggestions' => 'Trade suggestions',
      ],
      'placeholder' => [
          'sell_price_suggestion' => 'In :Currency_name...',
          'limited' => 'Name of limited edition',
          'description' => 'Describe your item (Optional)',
          'delivery' => 'Delivery costs',
          'sell_price' => 'Price in :Currency_name...',
          'additional_charge' => 'In :Currency_name...',
          'game_name' => 'Type your product name...',
      ],
      'validation' => [
          'trade_list' => 'You need to add at least one game to your trade list.',
          'delivery_pickup' => 'You need to select at least one option.',
          'price' => 'You need to enter a valid price.',
          'no_game_found' => 'Sorry, no game found.',
          'no_game_found_add' => 'Add new game to database.',
      ],
      'add_button' => 'Add Listing',
      'save_button' => 'Save Listing',
    ],

    /* General modal translations */
    'modal' => [
      'close' => 'Close',
    ],

    /* Buy modal on listings overview */
    'modal_buy' => [
      'buy' => 'Buy',
      'buy_game' => '<strong>Buy</strong> :Game_name',
      'total' => 'TOTAL',
      'delivery_free' => 'Free Shipping',
      'delivery_price' => '+ :price Shipping',
      'suggest_price' => 'Suggest a price',
    ],

    /* Buy modal on listings overview */
    'modal_trade' => [
      'trade_game' => '<strong>Trade</strong> :Game_name',
      'suggest' => 'Suggest a Game',
    ],

    /* Add new game to database modal */
    'modal_game' => [
      'title' => 'Add Game to Database',
      'more' => 'More',
      'search' => 'Search',
      'select_system' => 'Select System',
      'searching' => 'Searching for games...',
      'adding' => 'Adding game to :Pagename!',
      'wait' => 'Please wait',
      'placeholder' => [
          'value' => 'Enter title...',
      ],
    ],

    /* Alerts */
    'alert' => [
      'saved' => ':Game_name listing saved!',
      'deleted' => ':Game_name listing deleted!',
      'created' => ':Game_name listing created!',
    ],

];
