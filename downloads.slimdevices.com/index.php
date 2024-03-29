<?php

## Array of possible file names to search for
# LMS
$fileList["/.*\/LogitechMediaServer-.*-[0-9]+\-win64.exe/"] = "Logitech Media Server: Windows 64-bit Executable Installer - <b>EXPERIMENTAL!</b>";
$fileList["/.*\/LogitechMediaServer-.*-[0-9]+\.exe/"] = "Logitech Media Server: Windows Executable Installer";
#$fileList["/.*\/LogitechMediaServer-.*-[0-9]+\-whs.msi/"] = "Logitech Media Server: Windows Home Server Installer";
$fileList["/.*\/LogitechMediaServer-.*-[0-9]+\-win64.zip/"] = "Logitech Media Server: Windows 64-bit - <b>EXPERIMENTAL: requires <a href=\"https://strawberryperl.com\">Strawberry Perl 5.32</a>!</b> (<a href=\"https://strawberryperl.com/download/5.32.1.1/strawberry-perl-5.32.1.1-64bit.msi\">download</a>)";
$fileList["/.*\/LogitechMediaServer-[0-9.]*-[0-9]+\.pkg/"] = "Logitech Media Server: Mac OSX Installer";
// $fileList["/.*\/LogitechMediaServer-.*-[0-9]+\.dmg/"] = "Logitech Media Server: Mac OSX Installer";
$fileList["/.*\/logitechmediaserver-.*-[0-9]+\.tgz/"] = "Logitech Media Server: Unix Tarball (i386, x86_64, i386 FreeBSD, ARM EABI, PowerPC)";
$fileList["/.*\/logitechmediaserver-.*-[0-9]+-arm-linux\.tgz/"] = "Logitech Media Server: ARM Linux Tarball (ARM EABI)";
$fileList["/.*\/logitechmediaserver-.*-[0-9]+\-noCPAN.tgz/"] = "Logitech Media Server: Unix Tarball - No CPAN Libraries";
$fileList["/.*\/logitechmediaserver.*~[0-9]+_all\.deb/"] = "Logitech Media Server: Debian Installer Package (i386, x86_64, ARM EABI, PowerPC)";
$fileList["/.*\/logitechmediaserver.*~[0-9]+_amd64\.deb/"] = "Logitech Media Server: Debian Installer Package (x86_64)";
$fileList["/.*\/logitechmediaserver.*~[0-9]+_arm\.deb/"] = "Logitech Media Server: Debian Installer Package (ARM)";
$fileList["/.*\/logitechmediaserver.*~[0-9]+_i386\.deb/"] = "Logitech Media Server: Debian Installer Package (i386)";
$fileList["/.*\/logitechmediaserver-.*-[0-9.]+.[0-9]+\.noarch\.rpm/"] = "Logitech Media Server: RedHat (RPM) Installer Package";

$changelog["7.7"] = "http://htmlpreview.github.io/?https://github.com/Logitech/slimserver/blob/public/7.7/Changelog7.html";
$changelog["7.9"] = "http://htmlpreview.github.io/?https://github.com/Logitech/slimserver/blob/public/7.9/Changelog7.html";
$changelog["8.0"] = "http://htmlpreview.github.io/?https://github.com/Logitech/slimserver/blob/public/8.0/Changelog8.html";
$changelog["8.1"] = "http://htmlpreview.github.io/?https://github.com/Logitech/slimserver/blob/public/8.1/Changelog8.html";
$changelog["8.2"] = "http://htmlpreview.github.io/?https://github.com/Logitech/slimserver/blob/public/8.2/Changelog8.html";
$changelog["8.3"] = "http://htmlpreview.github.io/?https://github.com/Logitech/slimserver/blob/public/8.3/Changelog8.html";
$changelog["8.4"] = "http://htmlpreview.github.io/?https://github.com/Logitech/slimserver/blob/public/8.4/Changelog8.html";
$changelog["10.0"] = "http://htmlpreview.github.io/?https://github.com/Logitech/slimserver/blob/public/10.0/Changelog10.html";

$gitlog["7.7"] = "https://github.com/Logitech/slimserver/commits/public/7.7";
$gitlog["7.9"] = "https://github.com/Logitech/slimserver/commits/public/7.9";
$gitlog["8.0"] = "https://github.com/Logitech/slimserver/commits/public/8.0";
$gitlog["8.1"] = "https://github.com/Logitech/slimserver/commits/public/8.1";
$gitlog["8.2"] = "https://github.com/Logitech/slimserver/commits/public/8.2";
$gitlog["8.3"] = "https://github.com/Logitech/slimserver/commits/public/8.3";
$gitlog["8.4"] = "https://github.com/Logitech/slimserver/commits/public/8.4";
$gitlog["10.0"] = "https://github.com/Logitech/slimserver/commits/public/10.0";

$v = $_GET["ver"];

if (!$changelog[$v]) {
	$v = '';
}

## Check if a version has been supplied to us or not...
## Print a nice header
print_header($v, $changelog[$v], $gitlog[$v]);

## If a variable was given to us, lets see how valid it is.
if (!empty($v)) {
	if (!$_GET["xml"]) {
		print("<TABLE cellpadding=2 cellspacing=2>\n<TH>Version</TH><TH>File</TH><TH>Size (mb)</TH><TH>Date</TH>");
	}

	showLatest($v, $fileList);

	if (!$_GET["xml"]) {
		print("</TABLE>\n");
	}
} else {
	showTree();
}

print_footer();

## showLatest walks through the tree and finds the latest version of each file in the @fileList
function showLatest($version, $fileList) {
	##  Clear out some variables for this pass
	$highestmTime = 0;
	$latestFile = "";
	$searchList = "";


	## Do the find, and find the latest modified file
	$all_downloads = array();
	exec("find ./$version -type f ", $all_downloads);

	## Walk one by one through each file type we're looking for...
	foreach ($fileList as $filere => $filedesc) {
		## Start the table entry
		$pretty_desc = $filedesc;

		## Clear out the mtime
		$fileFound = 0;
		$best_mtime = 0;
		$best_file = "";

		## Now, walk through the array of actual known files
		foreach ($all_downloads as $file) {
			#echo "<br>checking [$filere] against [$file]";

			## If the file name matches the kind we're looking for, then ...
			if (preg_match($filere, $file)) {
				## Set foundFile to 1, just so we know that at least one file of this type was found
				$fileFound = 1;
				## Stat it first to get all the info we need
				$stats = stat($file);
				## Now, check its mTIME vs the best mtime
				if ($stats['mtime'] > $best_mtime) {
					## Rest best_mtime now with the new highest #
					$best_mtime = $stats['mtime'];
					$pretty_size = ByteSize($stats['size']);

					## Set the best matchin file name so far
					$best_file = $file;
				}
			}
		}

		## If no file was found, print error
		if ($fileFound !== 1) {
			#print "<TD>&nbsp;</TD><TD>&nbsp;</TD>";
		} elseif ($_GET["xml"]) {
			$os = '';

			preg_match("/[-_](\d+\.\d+\.\d+).*?(\d{10})/", $best_file, $matches);
			$version = $matches[1];
			$revision = $matches[2];

			if (preg_match("/win64\.exe/", $best_file))         { $os = 'win64'; }
			elseif (preg_match("/\.exe$/", $best_file))          { $os = 'win'; }
			elseif (preg_match("/\.msi/", $best_file))          { $os = 'whs'; }
			// elseif (preg_match("/win64\.zip/", $best_file))     { $os = ''; }

			elseif (preg_match("/\.pkg/", $best_file))          { $os = 'osx'; }

			elseif (preg_match("/amd64\.deb/", $best_file))     { $os = 'debamd64'; }
			elseif (preg_match("/arm\.deb/", $best_file))       { $os = 'debarm'; }
			elseif (preg_match("/i386\.deb/", $best_file))      { $os = 'debi386'; }
			elseif (preg_match("/all\.deb/", $best_file))       { $os = 'deb'; }
			elseif (preg_match("/\.rpm/", $best_file))          { $os = 'rpm'; }

			elseif (preg_match("/arm-linux\.tgz/", $best_file)) { $os = 'tararm'; }
			elseif (preg_match("/noCPAN/", $best_file))         { $os = 'nocpan'; }
			elseif (preg_match("/\d+\.tgz/", $best_file))       { $os = 'src'; }

			if ($os) {
				print("<$os revision=\"$revision\" url=\"http://downloads.slimdevices.com/nightly/$best_file\" version=\"$version\" size=\"$pretty_size\"/>");
			}
		} else {
			print("<TR>\n");
			print("<TD align=left>$pretty_desc</TD>\n");
			## Generate a pretty file name
			foreach ((explode("/", $best_file)) as $x) {
				$pretty_file_name = $x;
			}

			## Get a pretty date
			$pretty_date = date ("F d Y H:i", $best_mtime);

			## Ok, at this point we have the best match... now print out some pretty HTML for our users
			print("<TD align=left><a href=\"$best_file\">$pretty_file_name</a></TD>\n");
			print("<TD align=center>$pretty_size</TD>\n");

			## Check how old the date is, and determine the color for that tab
			## If time difference is less than 24 hours, background is green
			## If localtime() is more than 25 hours greater than $best_mtime, then set the color to orange
			$localtime = time();
			$middleaged = (60 * 60 * 24 * 7);
			$oldish = (60 * 60 * 24 * 30);

			if (($localtime - $best_mtime) > $oldish) {
				$pretty_date_color = "red";
			} elseif (($localtime - $best_mtime) > $middleaged) {
				$pretty_date_color = "orange";
			} else {
				$pretty_date_color = "black";
			}

			print("<TD><span style=\"color:$pretty_date_color\">$pretty_date</TD>\n");
			print("</TR>\n");
		}
	}
}

## This function looks at the available versions and directories and prints a basic set of links for the user
function showTree() {
	## Find each available version #
	exec("/usr/bin/find . -maxdepth 1 -type d \( ! -name '.*' \)", $versions);
	foreach ($versions as $x) {
		$x = trim($x, '.');
		$x = trim($x, '/');
		print("<a href=?ver=$x>Version $x</a><br>\n");
	}
}

## Header
function print_header($version, $changelog, $gitlog) {
	if ($_GET["xml"]) {
		header("Content-Type: application/xml; charset=utf-8");
		header("Cache-Control: max-age=3600");
		print "<servers>";
	}
	else {
		print "<HTML><HEAD><TITLE>Some Software Beta Downloads";
		if ($version) {
			print " - Version $version";
		}
		print "</TITLE></HEAD>\n";
		print "<BODY>\n";
		print "<center>\n";
		print "<h2>Some Software Beta Downloads";
		if ($version) {
			print " - Version $version";
		}
		print "</h2>\n";
		if ($changelog) {
			print "<p><a href=\"$changelog\">Changelog</a>\n";
			print "<br><a href=\"$gitlog\">Git commit log</a></p>\n";
		}
		print "<hr>\n";
	}
}

function print_footer() {
	if ($_GET["xml"]) {
		print "</servers>";
	}
	else {
		print "<p><hr><p><a href=\"/\">Release Version</a> | <a href=\"index.php\">Other Versions</a></BODY></HTML>";
	}
}

## ByteSize function stolen online... didn't feel like writing my own
function ByteSize($bytes) {
    $size = $bytes / 1024;
    if($size < 1024)
        {
        $size = number_format($size, 2);
        $size .= ' KB';
        }
    else
        {
        if($size / 1024 < 1024)
            {
            $size = number_format($size / 1024, 2);
            $size .= ' MB';
            }
        else if ($size / 1024 / 1024 < 1024)
            {
            $size = number_format($size / 1024 / 1024, 2);
            $size .= ' GB';
            }
        }
    return $size;
}

