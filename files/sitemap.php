<?php
echo "\n<div id=\"sitemap\">\n";
if (version_compare(phpversion(), "5.2.3", "<")) { echo "php 5.2.3 or later required"; exit; }

$surl = "http://".$_SERVER['SERVER_NAME'].str_replace("/".$file, "", $_SERVER['PHP_SELF']);

$sxt = explode(",",$cds_conf['sxt']);

function scanDirectories($rootDir, $allowext, &$allData = array()) {
    $dirContent = scandir($rootDir);
    foreach($dirContent as $key => $content) {
        if ($content == '.' || $content == '..') continue;
        $path = $rootDir . DIRECTORY_SEPARATOR . $content;
        $ext = substr($content, strrpos($content, '.') + 1);
        if(in_array($ext, $allowext) && is_file($path) && is_readable($path)) {
            $allData[] = $path;
        }
        else if(is_dir($path) && is_readable($path)) {
            scanDirectories($path, $allowext, $allData);
        }
    }
    return $allData;
}

$page_array = scanDirectories($path.$cds_conf['pgf'], $sxt);

if (!isset($_GET["seo"])) {
	rsort($ext);
	$page_array = preg_replace("#^".$path.$cds_conf['pgf']."/(.*)\.(".implode("|", $ext).")$#", "\\1", $page_array);
	$public_sitemap = "<br />\n<ul>\n";
	foreach ($page_array as $id => $page) {
		$public_sitemap .= "\t<li><a href=\"$surl/$page\">$page</a></li>\n";
	}
	$public_sitemap .= "</ul>\n";
	echo $public_sitemap;
} elseif (is_file($path.$cds_conf['pgf']."/seo.go")) {
	if (($_GET["keypage"] == "" || !is_file($path.$cds_conf['pgf']."/".$_GET["keypage"])) && $_GET["seo"] != "ping") {
		$page_array = preg_replace("#^".$path.$cds_conf['pgf']."/(.*)$#", "\\1", $page_array);
		$edit_sitemap = "<br />\n<ul>\n";
		foreach ($page_array as $id => $page) {
			$edit_sitemap .= "\t<li><a href=\"$surl/$page_in/?seo=select&keypage=$page\">$page</a></li>\n";
		}
		$edit_sitemap .= "<br />\n</ul>\n";
		echo $edit_sitemap;

		$xml_sitemap = "<"."?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<urlset\n\txmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\"\n\txmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\"\n\txsi:schemaLocation=\"http://www.sitemaps.org/schemas/sitemap/0.9\n\thttp://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd\">";
		$xml_sitemap .= "\n\t<url>\n\t\t<loc>$surl/</loc>\n\t\t<lastmod>".date("Y-m-d")."</lastmod>\n\t</url>";
		foreach ($page_array as $id => $page) {
			$page = preg_replace("#^(.*)\.(".implode("|", $ext).")$#", "\\1", $page);
			$xml_sitemap .= "\n\t<url>\n\t\t<loc>$surl/$page</loc>\n\t</url>";
		}
		$xml_sitemap .= "\n</urlset>";
		$finish = "There Was A Problem! Sitemap NOT Saved! Make sure the file exists and has write access permissions!<br />\n";
		if ($fh = @fopen($path."sitemap.xml", 'w+')) {
			if (@fwrite($fh, $xml_sitemap)) {
				if (@fclose($fh)) {
					$finish = "Sitemap Saved!<br />\n<a href=\"$surl/sitemap/?seo=ping\">Ping Search Engines</a> IMPORTANT: Do NOT ping more than once an hour!<br />\n";
				}
			}
		}
		echo "<br /><br />\n".$finish;
	} elseif ($_GET["seo"] == "ping" && is_file($path."sitemap.xml")) {
		$ping_urls = array(
			"Google" => "http://www.google.com/webmasters/tools/ping?sitemap=",
			"Yahoo!" => "http://search.yahooapis.com/SiteExplorerService/V1/ping?sitemap=",
			"Ask.com" => "http://submissions.ask.com/ping?sitemap=",
			"Live (MSN) Search" => "http://webmaster.live.com/ping.aspx?siteMap=",
		);

		$pinged = "";
		foreach ($ping_urls as $id => $purl) {
			if ($fh = @fopen($purl.urlencode($surl."/sitemap.xml"), 'r')) {
				$pinged .= $id." has been pinged.<br />\n";
			} else {
				$pinged .= $id." has NOT been pinged.<br />\n";
			}
		}
		echo "<a href=\"$surl/sitemap/?seo\">Back</a><br /><br />\n".$pinged;
	} else {
		echo $_GET["keypage"]."<br />\n<a href=\"$surl/sitemap/?seo\">Back</a><br /><br />\n";
	}
} else {
	echo "To modify the SEO preferences for your pages, please create a file called seo.go in the ".$path.$cds_conf['pgf']."/ directory.<br />\n";
}

if (is_file($path.$cds_conf['pgf']."/seo.go") && $_GET["seo"] == "select" && $_GET["keypage"] != "" && is_file($path.$cds_conf['pgf']."/".$_GET["keypage"])) {
	ob_start();
	include($path.$cds_conf['pgf']."/".$_GET["keypage"]);
	$discard = ob_get_contents();
	ob_end_clean();
	$ext = substr($path.$cds_conf['pgf']."/".$_GET["keypage"], strrpos($path.$cds_conf['pgf']."/".$_GET["keypage"], '.') + 1);
	$extended_title = preg_replace("#^".$path.$cds_conf['pgf']."/(.*)[\.]".$ext."$#i", " / \\1", $path.$cds_conf['pgf']."/".$_GET["keypage"]);
	$extended_title = str_replace("/", " / ", $extended_title);
	$extended_title = str_replace("  ", " ", $extended_title);
	$extended_title = ucwords($extended_title);
	$mtg["title"] = str_replace($extended_title, "", $mtg["title"]);

	$mtg["keywords_used"] = explode(",", $mtg["keywords"]);

	$page = file_get_contents($path.$cds_conf['pgf']."/".$_GET["keypage"]);
	$xtags = array("title", "script");
	foreach ($xtags as $id => $tag) {
		$page = preg_replace("/<".$tag."(.*)>(.*)<\/".$tag.">/isU", "\n", $page);
	}
	$rtags = array("html", "body");
	foreach ($rtags as $id => $tag) {
		$page = preg_replace("/<".$tag."(.*)>(.*)<\/".$tag.">/isU", "\\2", $page);
	}

	$page = preg_replace("/<([^>]*)\/>/isU", " ", $page);

	function extractBodyText($p_str, $p_allowedtag=NULL){
		$fstr=(preg_match('/<body[^>]*>(.*?)<\/body>/si', $p_str, $regs)?$fstr=$regs[1]:$p_str);
		$rtrn=(isset($p_allowedtag)?strip_tags($fstr, $p_allowedtag):strip_tags($fstr));
		return $rtrn;
	}

	$page = htmlentities($page, ENT_QUOTES, "UTF-8", FALSE);
	$page = html_entity_decode($page, ENT_QUOTES);
	$page = strtolower($page);
	$page = extractBodyText($page);
	$page = preg_replace("/[\W]/", "\n", $page);
	$page_array = explode("\n", $page);
	sort($page_array);
	$page_array = array_unique($page_array);

	if (!($exf = @file_get_contents($path.$cds_conf['pgf']."/keyex.txt"))) {
		echo $path.$cds_conf['pgf']."/keyex.txt file missing. Uploading the correct file will remove commonly used and unneeded keywords from the list below.<br /><br />";
	} else {
		$exf = strtolower($exf);
		$exf = extractBodyText($exf);
		$exf = preg_replace("/[\W]/", "\n", $exf);
		$exf_array = explode("\n", $exf);
		sort($exf_array);
		$exf_array = array_unique($exf_array);

		$page_array = array_diff($page_array, $exf_array);
	}

	echo "<form action=\"$surl/$page_in/?seo=write&keypage=".$_GET["keypage"]."\" method=\"post\">\n<div id=\"keywords\">\n";

	foreach ($page_array as $id => $line) {
		if (!is_numeric($line) && strlen($line) > 1) {
			$checked = "";
			if (in_array($line, $mtg["keywords_used"])) { $checked = "checked=\"checked\""; }
			echo "<input type=\"checkbox\" name=\"keywords[]\" value=\"$line\" $checked />$line<br />\n";
		}
	}

echo <<< HTML
		</div>
		<br />
		title <input type="text" name="title" value="$mtg[title]" /><br />
		author <input type="text" name="author" value="$mtg[author]" /><br />
		description <input type="text" name="description" value="$mtg[description]" /><br />
		language <select name="language"><option value="English">English</option></select><br />
		<input type="hidden" name="robots" value="index,follow" /><br />
		city <input type="text" name="city" value="$mtg[city]" /><br />
		state <input type="text" name="state" value="$mtg[state]" /><br />
		<input type="submit" value="write" />
		</form>
		<div>IMPORTANT NOTE 1: Any values that were previously saved in the file will be replaced with the ones selected above.</div>
		<div>IMPORTANT NOTE 2: The values above may only contain letters, numbers, spaces, and underscores. All other characters will be removed for safety.</div>
		<div>IMPORTANT NOTE 3: Only enter the title of your site in the title field. The script will store it as [site title] / [file name].</div>
HTML;
}

if (is_file($path.$cds_conf['pgf']."/seo.go") && $_GET["seo"] == "write" && $_GET["keypage"] != "" && is_file($path.$cds_conf['pgf']."/".$_GET["keypage"])) {
	function ve_prep($var) {
		$var = preg_replace("/[\W]/", " ", $var);
		$var = preg_replace("/\s\s+/", " ", $var);
		$var = trim($var);
		return $var;
	}
	$ext = substr($path.$cds_conf['pgf']."/".$_GET["keypage"], strrpos($path.$cds_conf['pgf']."/".$_GET["keypage"], '.') + 1);
	$extended_title = preg_replace("#^".$path.$cds_conf['pgf']."/(.*)[\.]".$ext."$#i", " / \\1", $path.$cds_conf['pgf']."/".$_GET["keypage"]);
	$extended_title = str_replace("/", " / ", $extended_title);
	$extended_title = str_replace("  ", " ", $extended_title);
	$extended_title = ucwords($extended_title);
	if (is_array($_POST["keywords"])) {
		$safe_keys = implode(",", $_POST["keywords"]);
	} else {
		$safe_keys = "";
	}
	$mtg = array (
	  'title' => ve_prep($_POST["title"]).$extended_title,
	  'author' => ve_prep($_POST["author"]),
	  'description' => ve_prep($_POST["description"]),
	  'keywords' => $safe_keys,
	  'language' => $_POST["language"],
	  'robots' => $_POST["robots"],
	  'city' => ve_prep($_POST["city"]),
	  'state' => ve_prep($_POST["state"]),
	);
	$page = file_get_contents($path.$cds_conf['pgf']."/".$_GET["keypage"]);
	$page = str_replace("\r", "", $page);
	$page_p = "/<\?php\n\/\/\n(.*)\n\/\/\n\?>/isU";
	$page_r = "<"."?php\n//\n$"."mtg = ".var_export($mtg, true).";\n//\n?".">";
	$page = preg_replace($page_p, "", $page).$page_r;
	$finish =  "There Was A Problem! Meta Tags NOT Saved! Make sure the file exists and has write access permissions!";
	if ($fh = @fopen($path.$cds_conf['pgf']."/".$_GET["keypage"], 'w+')) {
		if (@fwrite($fh, $page)) {
			if (@fclose($fh)) {
				$finish =  "Meta Tags Saved!";
			}
		}
	}
	echo $finish;
}
echo "\n</div>\n";
?>