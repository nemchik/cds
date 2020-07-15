<?php
if (version_compare(phpversion(), "4.1.0", "<")) {
    echo "PHP 4.1.0 or later required.";
    exit;
}

$path = str_replace("\\", "/", dirname($_SERVER['SCRIPT_FILENAME'])."\\");
$file = str_replace($path, "", str_replace("\\", "/", $_SERVER['SCRIPT_FILENAME']));

$cds_conf = array();
if (is_file($path."config.cds.php")) {
    include($path."config.cds.php");
} else {
    echo "could not find config.cds.php";
    exit;
}
$cds_conf['pgf'] = preg_replace("/^\/(.*)\/$/", "\\1", $cds_conf['pgf']);

$page_in = (isset($_GET[$cds_conf['pvr']]) && $_GET[$cds_conf['pvr']] != "") ? $_GET[$cds_conf['pvr']] : $cds_conf['def'];
$ext = explode(",", $cds_conf['ext']);
if (in_array("%", $ext) && in_array("*", $ext) && is_file($path.$cds_conf['pgf']."/".$page_in)) {
    $page_out = $path.$cds_conf['pgf']."/".$page_in;
} elseif (!in_array("%", $ext) && in_array("*", $ext)) {
    $wildfile = glob($path.$cds_conf['pgf']."/".$page_in.".*");
    $page_out = $wildfile[0];
} else {
    foreach ($ext as $id => $end) {
        if (in_array("%", $ext) && preg_match("/\.".$end."$/i", $page_in) && is_file($path.$cds_conf['pgf']."/".$page_in)) {
            $page_out = $path.$cds_conf['pgf']."/".$page_in;
            break;
        }
        if (!in_array("%", $ext) && is_file($path.$cds_conf['pgf']."/".$page_in.".".$end)) {
            $page_out = $path.$cds_conf['pgf']."/".$page_in.".".$end;
            break;
        }
    }
}

if (!isset($page_out)) {
    $error = "The page, <span style=\"font-weight:bold;\">'".$page_in."'</span>, that you are looking for could not be found.";
}

ob_start();
include($path.$cds_conf['tpl']);
$display = ob_get_contents();
ob_end_clean();

ob_start();
if (isset($error)) {
    echo $error;
} else {
    include($page_out);
}
$content = ob_get_contents();
ob_end_clean();

$display = preg_replace("/{content}/i", $content, $display);
$display = preg_replace("/<\?php(.*)\?>/isU", "\nHTML;\n\\1\necho <<< HTML\n", $display);

preg_match_all("/<#([\w]+)#>(.*)<\/#\\1#>/sU", $display, $matches, PREG_SET_ORDER);
foreach ($matches as $id => $match) {
    $tpl[$match['1']] = $match['2'];
    $display = str_replace("<#".$match['1']."#>", "", $display);
    $display = str_replace("</#".$match['1']."#>", "", $display);
}

if (isset($_GET[$cds_conf['tvr']]) and isset($tpl[$_GET[$cds_conf['tvr']]])) {
    $display = $tpl[$_GET[$cds_conf['tvr']]];
}
echo $display_xml;
eval("\necho <<< HTML\n".$display."\n\n<!-- Powered By: 9i/CDS v2.5.0.0 -->\nHTML;\n");
