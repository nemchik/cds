<?php
/*-//-//-//-//-//-//-//-//-//-//-//-//-//-//-//-//-//-/*-*/
/*---------------------9i/CDS (Content Display System)/*-*/
/*---------------------Brought to you by: Eric Nemchik/*-*/
/*-//-//-//-//-//-//-//-//-//-//-//-//-//-//-//-//-//-/*-*/
$cds_conf['tpl'] = "template.cds.php";
$cds_conf['pvr'] = "page";
$cds_conf['tvr'] = "temp";
$cds_conf['pgf'] = "files";
$cds_conf['ext'] = "htm,html,php";
$cds_conf['sxt'] = "html";
$cds_conf['def'] = "home";

/*-//-//-//-//-//-//-//-//-//-//-//-//-//-//-//-//-//-/*-*//*
This file MUST be placed in the same folder as the CDS file.

$cds_conf['tpl']
This variable should contain the name of your template file.
The template file should be in the same folder as the config.

$cds_conf['pvr']
This variable should contain the name of the url extension
that will be used to request your pages. In the example
main.php?page=home the word 'page' would be this variable.
*
If this variable is changed the included .htaccess
file will need to be updated to reflect the changes.

$cds_conf['tvr']
This variable should contain the name of the url extension
that will be used to request template pieces. In the example
main.php?temp=header the word 'temp' would be this variable.

$cds_conf['pgf']
This variable should contain the name of the folder that
contains your site files. This folder must be in the same
directory as the CDS file. You may use subdirectories.
Ex: $cds_conf['pgf'] = "files/mysite/pages";
Leading and trailing slashes are not needed and will be
automatically removed by the script if they are included.

$cds_conf['ext']
This variable should contain the the extensions for files
that are allowed to be displayed by the script. Extensions
should be separated by commas (,). The asterick (*)
character is a wildcard extension and will allow all
extensions to be processed. The percent (%) character
tells the script to process extensions in the url.

$cds_conf['sxt']
This variable should contain the the extensions for files
that should be included in the sitemap. This variable does
not support special characters like * and %.

$cds_conf['def']
This variable should contain the name of your website's
default page. This variable respects the rules listed above
for extensions.
*//*-//-//-//-//-//-//-//-//-//-//-//-//-//-//-//-//-//-/*-*/
