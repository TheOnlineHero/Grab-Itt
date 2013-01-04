<?php
require_once(dirname(__FILE__).'../../../../wp-admin/admin.php'); 
?>
<h2>Grab Itt</h2>

<p>This is a really simple plugin to use, provided that you know about short codes and css. 
  If you for example wanted to import the content from http://www.realestate.com.au/new-homes/new-house+land-in-perth%2c+wa+6000/list-1, you 
  can use Grab Itt to do this. 
<p>First create a short code in your post/page that looks like this:</p>
<p>[grab-itt url="URL" css_selector="CSS_SELECTOR"][/grab-itt]</p>
<p>url: Tells Grab Itt which website to grab the content from. Obviously replace URL with the url you wish to grab content from. For example, you could replace URL with http://www.realestate.com.au/new-homes/new-house+land-in-perth%2c+wa+6000/list-1</p>
<p>css_selector: Tells Grab Itt which section of the page to grab content from. This parameter takes a css selector to make it easier for you. Obviously replace CSS_SELECTOR with your css selector such as body, #header, #footer, etc.</p>
<p>After using Grab Itt, it may display the message: "undefined". This means you have used an incorrect css selector, you will have to choose another.</p>

<?php tom_add_social_share_links("http://wordpress.org/extend/plugins/grab-itt/"); ?>