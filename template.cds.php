<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<?php
// The #head# tag below is able to be called by the cds by visiting main.php?temp=head
// The cds will then display only the code from the template found inside the #head# tag
// I used this when I needed to include a template (or pieces of a template) in another program like a shopping cart
// You can name #head# whatever you want, and make as many of these as you want, like #body# or #topOfTemplate#
// When using the #tags# I always made sure to use proper tag nesting, but I could imagine improper nesting could result in breaking the code
// The temp piece of the url is customizable using $cds_conf['tvr'], so you can make it main.php?template=head or whatever
?>
<#head#>
<head>
<?php
if (!isset($mtg)) {
	$mtg = array (
	  'title' => '',
	  'author' => '',
	  'description' => '',
	  'keywords' => '',
	  'language' => 'English',
	  'robots' => '',
	  'city' => '',
	  'state' => '',
	);
}
// This is for the sitemap.php, which is why the variables are in php rather than just right in the html
// Also in the html below you will notice php variables are used out in the open and not enclosed in php tags
// This is because the cds interprets the template and all of the content/pages as php inside an echo <<<TAG
?>
<title>$mtg[title]</title>
<meta http-equiv="Content-Type" content="application/xhtml+xml; charset=utf-8" />
<meta name="author" content="$mtg[author]" />
<meta name="description" content="$mtg[description]" />
<meta name="keywords" content="$mtg[keywords]" />
<meta name="language" content="$mtg[language]" />
<meta name="robots" content="$mtg[robots]" />
<meta name="city" content="$mtg[city]" />
<meta name="state" content="$mtg[state]" />
</head>
</#head#>
<body>
{content}
</body>
</html>