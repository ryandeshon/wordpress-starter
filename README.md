## Starter Theme
Oh hey, didn't see you there. I'm Starter, an awesome theme for scaling up sites in no time at all. I don't have much in the way of style, but there's plenty going on...

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
```

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

    filter_excerpt( $content )
Enable this if you want to filter out certain tags (like images, or tables) from a piece of content.

#### lib/template/
The template directory in "includes/lib/template" contains a template class which allows you to render a template, and pass variables to it, which you can't do with get_template_part.

You can use it on a page like:

    render_template( $filename, array( 'foo' => 'bar' ) );

You only have to specify the filename as the first variable, since it searches for the file in "includes/templates" automatically. The second variable can be an array of any data, which can be output in the template. For instance, to get the value of foo within your rendered template you can just call

    echo $this['foo']

Which would return "bar".
