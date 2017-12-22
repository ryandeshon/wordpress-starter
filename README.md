## Crown Starter Theme
Oh hey, didn't see you there. I'm Starter, an awesome theme for scaling up Crown sites in no time at all. I don't have much in the way of style, but there's plenty going on...

Ok seriously, think of this starter theme more as a library of functions and API's then a full-featured themes. It's a good starting point when building a simple WordPress theme that needs to integrate with various Random House systems. None of the libraries included are entirely feature complete, but have some great helpers for building out themes quickly.

There are a couple of things set up in this theme to make development easier.

### Gulp
Gulp is used to concatenate scripts, compile SASS, and adds livereload. Whenever starting development, you should run the `gulp` command to make things easier.

To get started with gulp, make sure Node is installed, and run:

```
npm install
```

When you are ready to get started, run:

```
gulp
````

This will compile everything and start watching files for changes. The one exception to that is the SVG icon system (covered later). If you add any SVGs, you will need to run the icon task:

```
gulp icons
```

Gulp pulls in several JS vendor files automatically. This includes Bootstrap, Modernizr and an SVG fallback. It's worth going through the `config` variable in gulpfile.js and commenting out any scripts from the array that you are not using, to keep things light. These will be updated from time to time to keep things at their latest versions, but should serve well.

### SASS
Starter uses SASS. To get the theme started, it has several base files, mostly pulled from Bootstrap but modified to include better integration with variables. These include classes for forms, tables, buttons, and other common UI elements. All of this is controlled by `assets/sass/style.scss`. When adding SASS files, make sure to import them here.

The `_vars.scss` file includes some helpful variables to get started. This includes colors, fonts, and some basic typography rules. Modifying these will ensure that changes happen to base elements as well. There is also several variables for media queries. This makes it easier to just use media queries without a complicated mixin. A basic example is:

```
@media #{$small} {
    .header { 
         // Styles for small
    }
```

### JS
Any JS vendor files used by the theme should be added to `assets/js/vendor/`. This already includes individual bootstrap files, modernizr and an SVG fallback script. If JS files are added here, they should also be added to the `config` variable in gulpfile.js. This is to control loading order when the scripts are concatenated.

Any custom scripts can be added to `assets/js/app`. Any file added here will automatically be concatenated by Gulp, in the order that the scripts are in the folder (but this shouldn't be much of a problem).

When the scripts are concatenated, there are two files exported: `scripts.min.js` and `scripts.js`. When debugging, it might be useful to switch over to scripts.js to spot check a problem.

### SVG Icons
I'm thinking SVG icons might be the smartest move as an icon system going forward. I find icon fonts cumbersome to deal with, and they end up loading the CSS files with classes, and importing WAY more weight that is necessary. This theme has an SVG icon system already built into it, that should work a lot better.

In `assets/svg/all` there is a full stock of [Ionicons](http://ionicons.com/), which should cover just about any need. We can talk about if there is a better match out there for us.

To actually use an SVG icon in your project, simply drop it into the `assets/svg/individual` folder, and run `gulp icons` to make sure that it is converted to PNG and an SVG sprite. This does a couple of things:
- The SVG sprite is added to the top of the header.php file, to be used throughout the page.
- PNGs are automatically created. For any browser (ahem IE) that doesn't support SVG, the SVG fallback script will swap these in automatically.

SVGs should be included with the `<use>` element. An example can be found in header.php.

     <svg viewBox="0 0 100 100" class="icon">
       <use xlink:href="#social-twitter"></use>
     </svg>

### PHP
Standard WordPress functions are included in the functions.php file of this theme. There are also a few files that provide a few more PHP classes for special cases.

#### helpers.php
The `helpers.php` file includes common functions abstracted so they can be used fairly easily. It's pretty well documented, but they are:

    base_pagination( $current, $total, $format )
This is really just an abstraction of [paginate_links](https://codex.wordpress.org/Function_Reference/paginate_links), but can be useful for generating pagination on dynamically created pages, or pages stitched up from external API results.

    generate_buy_button( $isbn, $format )
The generate_buy_button function will automatically create a "Buy Now" button which populates a dropdown with retailer links. Pass through the isbn of the title, and format (Paperback, Hardcover, Audio CD, Audio Download, or Ebook) and you will be returned the dropdown. The retailer links that are populated are controlled in get_all_retailer_links.

    filter_excerpt( $content )
Enable this if you want to filter out certain tags (like images, or tables) from a piece of content.

#### lib/template/
The template directory in "includes/lib/template" contains a template class which allows you to render a template, and pass variables to it, which you can't do with get_template_part.

You can use it on a page like:

    render_template( $filename, array( 'foo' => 'bar' ) );

You only have to specify the filename as the first variable, since it searches for the file in "includes/templates" automatically. The second variable can be an array of any data, which can be output in the template. For instance, to get the value of foo within your rendered template you can just call

    echo $this['foo']

Which would return "bar".

#### lib/api/
The API class is pretty complex, but it can be used to call the PRH API. Make sure to go into `class.randomhouse.php` and populate the proper API keys, division and imprint filters, and custom functionality there. Definitely a work in progress.

A basic call looks like this
    
    $args = array(
    	  'categories' => array( 33333, 33334),
          'params' => array(
                 'rows' => '12',
                 'start' => '12'
          	)
    	)
    $get_books = new RandomHouse
    $all_books = $get_books->books

Which will return a list of 12 books with a certain category ID, starting from book 12.

All data is called from the PRH API and then passed through to the `class.randomhouse.utils.php` file which prepares the data for output. This basically means iterating through an array of data and outputting it in a consistent format that can be used in templates. For instance, with the above call, we can then use this in a template:

    <?php foreach( $all_books as $book ) { ?>
        <h1><a href="<?php echo $book->seoFriendlyUrl; ?>"><?php echo $book->title; ?></a></h1>
        <h2>By <?php echo $book->author; ?>
        <p><?php echo $book->description; ?>
    <?php } ?>

To make things a bit easier, there is also a function in the main API class to grab a request from the Google Books API. Not much has been done with this yet, but it should be used to normalize responses across APIs.

### Email
The Email class has a few different components throughout this theme. These all have to do with integrating with Experian to create simple email subscription forms and preference centers. This is probably the library that is in its most unfinished state, and both the back-end and front-end code will need to be tweaked to suit your needs.

#### lib/email
The back-end consists of a basic `RHAjax` class which can be used to set-up AJAX calls, which process a form's data and submits it to the Experian API on the server instead of the client.

I'd recommend looking through this file for full details, but there are two types of calls in there, "newsletter" and "preferences", which make different calls to the API. The way to actually set this up is to pass an instance of the class to a variable, and then pass that variable to a WordPress admin ajax action, like so:

    $newsletter_request = new RHAjax('newsletter', 'rh-newsletter-signup');
    add_action( 'wp_ajax_newsletter_signup', array($newsletter_request, 'run') );
    add_action( 'wp_ajax_nopriv_newsletter_signup', array($newsletter_request, 'run') );

Be sure to go through the class and set the program ID, site ID and other data that will be specific from site to site. These are stored in top-level protected variables.

#### assets/js/newsletter.js + assets/js/preferences.js
To match the back-end requests, there are a couple of Javascript files which hook into basic forms located in the includes/templates section of this theme.

These javascript files will grab the necessary information from these forms and then pass it to the back-end class through the ajax action to be processed and sent to Experian.

To validate forms, [validate.js](http://rickharrison.github.io/validate.js/) is used and added to the init action of both Javascript files.

The newsletter module in `newsletter.js` sets up the basic functionality and the Preferences module in `preferences.js` simply extends this module and changes functionality for form processing.

Note that these files require the use of jQuery.

MORE TO COME...