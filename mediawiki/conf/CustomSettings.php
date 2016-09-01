<?php

# $wgLogo = "";
$wgEnableUploads = true; # Enable uploads
$wgMaxUploadSize=1024 * 1024 * 200;
$wgAllowCopyUploads = true;
$wgGroupPermissions['user']['upload_by_url'] = true;
$wgUseImageMagick = true;
$wgImageMagickConvertCommand = "/usr/bin/convert";
$wgFileExtensions[] = 'zip';
$wgTrustedMediaFormats[] = 'application/zip';
$wgFileExtensions = array_merge( $wgFileExtensions,
    array( 'doc', 'xls', 'mpp', 'pdf', 'ppt', 'xlsx', 'jpg',
        'tiff', 'odt', 'odg', 'ods', 'odp', 'ai', 'psd'
    )
);

#$wgShowExceptionDetails = true;
#$wgCachePages = false;

/*
$wgSMTP = array(
 "host" => '',
 "IDHost" => '',
 "localhost" => '',
 "port" => 587,
 "auth" => true,
 "username" => '',
 "password" => ''
);
*/
#$wgPasswordSender = "";


wfLoadExtension( 'Cite' );

wfLoadExtension( 'CiteThisPage' );

wfLoadExtension( 'ConfirmEdit' );
wfLoadExtension( 'ConfirmEdit/ReCaptchaNoCaptcha' );

$wgCaptchaClass = 'ReCaptchaNoCaptcha';
$wgReCaptchaSiteKey = '';
$wgReCaptchaSecretKey = '';

wfLoadExtension( 'Gadgets' );

wfLoadExtension( 'ImageMap' );

wfLoadExtension( 'InputBox' );

wfLoadExtension( 'Interwiki' );
$wgGroupPermissions['sysop']['interwiki'] = true;

wfLoadExtension( 'Nuke' );

wfLoadExtension( 'ParserFunctions' );

wfLoadExtension( 'PdfHandler' );

wfLoadExtension( 'Renameuser' );

wfLoadExtension( 'SpamBlacklist' );
$wgSpamBlacklistFiles = array(
   "[[m:Spam blacklist]]",
   "https://en.wikipedia.org/wiki/MediaWiki:Spam-blacklist",
   "https://fr.wikipedia.org/wiki/MediaWiki:Spam-blacklist",
   "https://de.wikipedia.org/wiki/MediaWiki:Spam-blacklist"
);

wfLoadExtension( 'SyntaxHighlight_GeSHi' );
$wgPygmentizePath = "/usr/bin/pygmentize";

wfLoadExtension( 'TitleBlacklist' );
$wgTitleBlacklistSources = array(
    array(
         'type' => 'localpage',
         'src'  => 'MediaWiki:Titleblacklist',
    ),
    array(
         'type' => 'url',
         'src'  => 'https://meta.wikimedia.org/w/index.php?title=Title_blacklist&action=raw',
    ),
);

wfLoadExtension( 'WikiEditor' );

$GLOBALS['egSPLAutorefresh'] = true;
$GLOBALS['wgNamespacesWithSubpages'][NS_MAIN] = 1;

wfLoadExtension( 'VisualEditor' );
$wgDefaultUserOptions['visualeditor-enable'] = 1;
$wgHiddenPrefs[] = 'visualeditor-enable';
#$wgDefaultUserOptions['visualeditor-enable-experimental'] = 1;
$wgVirtualRestConfig['modules']['parsoid'] = array(
	// URL to the Parsoid instance
	// Use port 8142 if you use the Debian package
	'url' => 'http://mediawiki-node-services:8142',
	// Parsoid "domain", see below (optional)
	'domain' => 'localhost',
	// Parsoid "prefix", see below (optional)
	'prefix' => 'localhost'
);

## MediaWiki_Language_Extension_Bundle
$EXT = "$IP/extensions";
$wgExtensionAssetsPath = "{$wgScriptPath}/extensions";
wfLoadExtension( 'Babel' );
wfLoadExtension( 'cldr' );

wfLoadExtension( 'CleanChanges' );
$wgCCTrailerFilter = true;
$wgCCUserFilter = false;
$wgDefaultUserOptions['usenewrc'] = 1;
wfLoadExtension( 'LocalisationUpdate' );
$wgLocalisationUpdateDirectory = "$IP/cache";

require_once "$EXT/Translate/Translate.php";
$wgGroupPermissions['translator']['translate'] = true;
$wgGroupPermissions['translator']['skipcaptcha'] = true; // Bug 34182: needed with ConfirmEdit
$wgGroupPermissions['user']['translate'] = true;
$wgGroupPermissions['user']['translate-messagereview'] = true;
$wgGroupPermissions['user']['translate-groupreview'] = true;
$wgGroupPermissions['user']['translate-import'] = true;
$wgGroupPermissions['sysop']['pagetranslation'] = true;
$wgGroupPermissions['sysop']['translate-manage'] = true;
$wgTranslateDocumentationLanguageCode = 'qqq';
$wgExtraLanguageNames['qqq'] = 'Message documentation'; # No linguistic content. Used for documenting messages


wfLoadExtension( 'UniversalLanguageSelector' );
?>