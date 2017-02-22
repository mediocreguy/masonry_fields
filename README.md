# masonry_fields Sandbox

This is hacked together version of the masonry_fields module for Drupal 8.

A couple of things:

1. The code has a lot of duplication in the field formatter classes.  I could not figure out a way to combine the text and image formatter classes in such a way as to remove the duplications.
Perhaps someone with more knowledge of Drupal 8 or the masonry module maintainer can improve it.

2. Your theme must use the classy base theme - if you use the stable base theme, the necessary field classes will not be generated in the markup.  You could also generate the classes in a theme_preprocessor_field hook - the class names are different from Drupal 7.

3. If you use the Bootstrap theme, you are probably going to have to implement a node--field.html.twig template for your field/content type in your theme to add the necessary row and col-xx-xx class wrappers to make the grid look better.

4. I also had to implement theme_preprocess_image() to remove the width and height attributes from the image tags - your mileage may vary.
```
function theme_preprocess_image(&$variables) {
  unset($variables['width'], $variables['height'], $variables['attributes']['width'], $variables['attributes']['height']);
}
```

Hope this helps.
