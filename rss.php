<?php
require_once ("./rss.config.php");
require_once ("./rss.class.php");
if ($config['debug'] == 1) {
    error_reporting(E_ALL);
}elseif($config['debug'] == 0){
    error_reporting(0);
}
if($config['public_key'] == NULL){
    die("Please set the public key in the config file.");
}
if($config['api_sec'] == NULL){
    die("Please set the API Security key in the config file.");
}
(isset($_GET['sid']) ? $pid = $_GET['sid'] : $pid = null);
(isset($_GET['location']) ? $location = $_GET['location'] : $location = null);
(isset($_GET['name']) ? $shelter_name = $_GET['name'] : $shelter_name = null);
(isset($_GET['len']) ? $len = $_GET['len'] : $len = null);
(isset($_GET['rand']) ? $rand = $_GET['rand'] : $rand = null);
(isset($_GET['nobreak']) ? $break_remove = $_GET['nobreak'] : $break_remove = null);
(isset($_GET['species']) ? $species = $_GET['species'] : $species = null);
(isset($_GET['drop_1']) ? $animal_rest = $_GET['drop_1'] : $animal_rest = null);
if ($len > '1000') {
    $alrt = "Max number of records you can show at a time is 1000.";
    $len = "1000";
}
if ($location == '' && $pid == '' || $location != '' && $pid == '' && $species ==
    '') {
    echo '<html>';
    echo ' <head>';
    echo '<title>'.$config['title'].'</title>';

?>
<link rel="stylesheet" href="rss.css" />
</head>
<body>
<div id="header"><img src="./animal_shelter_rss.png" alt="Animal Shelter RSS Feed Generator" /></div>
<div id="subhead" style="font-size: small; text-align: center;">
<div>Data Provided by: <br /><a href="http://www.petfinder.com"><img src="http://www.petfinder.com/banner-images/widgets/40.jpg" border="0" alt="Pet Adoption" /></a></div>
<div>Application by:<br />Anthony Aldridge<br />
<div>Hosting provided by:<br /><?= $config['webmaster_name'] ?></div>
<br />
<b>Need help?</b> - email the webmaster: <?= $config['webmaster_email'] ?>
</div>
<fieldset>
<legend>Shelter Search</legend>
<div style='font-family: Georgia,"Times New Roman",serif;font-size: 12px;font-weight: bold;color: #600;line-height: 22px;margin: 0;text-transform: uppercase;letter-spacing: 1px;'><?php echo
    $alrt; ?></div>
<form name="shelterfinder" method="get" action="">
<ol><li><label>Shelter Name: <input type="text" name="name" value="<?= $shelter_name; ?>"><em>Optional</em></label></li>

<li><label>City State or Zip: <input type="text" name="location" value="<?= $location; ?>"><em>Required</em></label></li>
<li><label>Select max number of animals<br />to display on the agencies listing page:<br />
<select name="len">
<?php
    $pi = '10';

    while ($pi != '210') {
        if ($len == $pi) {
            echo "<option selected=selected value='$pi'>$pi</option>";
        } else {
            echo "<option value='$pi'>$pi</option>";
        }
        $pi = $pi + 10;
    }
    if ($rand == 1) {
        $sela = 'selected="selected"';
    } else {
        $selb = 'selected="selected"';
    }

?>
</select></li>
<li><?= getTierOne($animal_rest) ?></li>
<li><label>Random<select name="rand"><option value="1" <?= $sela ?> >Yes</option><option value="0" <?= $selb ?> >No</option></select></label></li></label>
<b>About Random:</b> I am limited by what I can call to Petfinder, this feature is based on a random offset to the beginning of the record set. So agencies with lower pet counts may have this show "blank" to figure out a workaround contact me anthony at serverstrategies.com.<br /><br />
<input type="submit" value="Find!" /></form></ol>
</fieldset>
</small>
</body>
</html>
    <?php
}
if ($location != '' && $pid == '' && $species == '') {
    echo '<fieldset class="results"><legend>Shelter Results</legend>';
    if ($shelter_name != '') {
        $sfinder = simplexml_load_file("http://api.petfinder.com//shelter.find?key=" . $config['public_key'] .
            "&location=$location&name=$shelter_name");
    } else {
        $sfinder = simplexml_load_file("http://api.petfinder.com//shelter.find?key=" . $config['public_key'] .
            "&location=$location");
    }
    foreach ($sfinder->shelters->shelter as $sxml) {
        $sname = $sxml->name;
        $sid = $sxml->id;
        $scity = $sxml->city;
        $sstate = $sxml->state;
        $sphne = $sxml->phone;
        ((isset($animal_rest)) ? $aset = "&drop_1=$animal_rest" : $aset = '');
        echo '<br />Shelter ID: ' . $sid . '<br /><a href="./rss.php?sid=' . $sid .
            '&len=' . $len . '&rand=' . $rand . $aset . '">' . $sname . '</a><br />' . $scity .
            ', ' . $sstate . ' ' . $sphne . '<br /><hr>';
    }
    echo '</fieldset>';
}elseif ($pid != '' || $species != '' && $location != '') {

    $a = '0'; // This is to figure out how many options are available
    $sinfo = simplexml_load_file("http://api.petfinder.com/shelter.get?key=" . $config['public_key'] .
        "&id=$pid");

    foreach ($sinfo->shelter as $shelter_xml) {
        $shid = $shelter_xml->id;
        $sname = $shelter_xml->name;
        $sname = htmlspecialchars($sname);
        $sname = strip_tags($sname, "<a><b><i>");
        $add1 = $shelter_xml->address1;
        $add2 = $shelter_xml->address2;
        $city = $shelter_xml->city;
        $state = $shelter_xml->state;
        $zip = $shelter_xml->zip;
        $phone = $shelter_xml->phone;
        $fax = $shelter_xml->fax;
        $email = $shelter_xml->email;
    }
    $desco = $sname . "\n" . $add1 . "\n" . $add2 . "\n" . $city . ', ' . $state .
        ' ' . $zip . "\nP: " . $phone . "\n F: " . $fax . "\nEmail: " . $email;
    $desco = htmlentities($desco);
    $rss_channel = new rssGenerator_channel();
    $rss_channel->atomLinkHref = '';

    $rss_channel->title = $sname;

    $rss_channel->link = 'http://www.petfinder.com/pet-search?shelterid=' . $shid .
        '&amp;animal='.$animal_rest;
    $rss_channel->description = $desco;
    $rss_channel->language = 'en-us';
    $rss_channel->generator = $config['title'];
    $rss_channel->webMaster = $config['webmaster_email'].'('.$config['webmaster_name'].')';
    if ($len == '') {
        $len == '50';
    }
    if (isset($_GET['off'])) {
        $offset = "&offset=" . $_GET['off'];
    } else {
        $offset = '';
    }
    if ($_GET['rand'] == '1') {
        if ($_GET['maxrand'] != '') {
            $maxrand = $_GET['maxrand'];
            if (is_numeric($maxrand)) {
                $offsat = rand(0, $maxrand);
                $offset = "&offset=$offsat";
            } else {
                die("Numeric values are only allowed in the random field");
            }
        } else {
            $offsat = rand(0, 30);
            $offset = "&offset=$offsat";
        }
    }
    (isset($_GET['species']) ? $GET_bre = "&animal=" . $_GET['species'] : 'NULL');
    if (isset($_GET['species']) != '') {
        $pfinder = simplexml_load_file("http://api.petfinder.com/pet-search?key=".$config['public_key']."&location=$location&count=$len&status=A$GET_bre$offset");
    } else {
        $pfinder = simplexml_load_file("http://api.petfinder.com/shelter.getPets?key=".$config['public_key']."&id=$pid&count=$len&status=A$offset");
    }
    $a = '0';
    foreach ($pfinder->pets->pet as $xml) {
        if (isset($animal_rest)) {
            $animal_rest = strtolower($animal_rest);
            if ($animal_rest == 'barnyard') {
                $restrict = 'Barnyard';
            } elseif ($animal_rest == 'bird') {
                $restrict = 'Bird';
            } elseif ($animal_rest == 'cat') {
                $restrict = 'Cat';
            } elseif ($animal_rest == 'dog') {
                $restrict = 'Dog';
            } elseif ($animal_rest == 'horse') {
                $restrict = 'Horse';
            } elseif ($animal_rest == 'pig') {
                $restrict = 'Pig';
            } elseif ($animal_rest == 'reptile') {
                $restrict = 'Reptile';
            } elseif ($animal_rest == 'smallfurry') {
                $restrict = 'Smallfurry';
            }
        }
        $animal = $xml->animal;
        if (isset($restrict) && $animal == $restrict || isset($animal_rest) && !isset($restrict) || isset($animal_rest)) {
            $item = new rssGenerator_item();
            global $id, $animal, $breeds;
            $breeds = $xml->breeds->breed;
            $id = $xml->id;
            $animal = $xml->animal;
            $breeds = $xml->breeds->breed;
            $mix = $xml->mix;
            $age = $xml->age;
            $name = $xml->name;
            $name = stripslashes($name);
            $name = strip_tags($name, "<a><b><i>");
            $name = htmlspecialchars($name);
            $shelterId = $xml->shelterId;
            $size = $xml->size;
            $sex = $xml->sex;
            $desc = stripslashes($xml->description);
            $desc = strip_tags($desc, "<a><b><i>");
            $desc = str_replace('&nbsp;', '', $desc);
            $lastupd = $xml->lastUpdate;
            $status = $xml->status;
            if ($size == "S") {
                $size = "Small";
            }
            if ($size == "M") {
                $size = "Medium";
            }
            if ($size == "L") {
                $size = "Large";
            }
            if ($status == 'A') {
                $status = 'Active';
            }
            if ($sex == "M") {
                $sex = "Male";
            }
            if ($sex == "F") {
                $sex = "Female";
            }
            $options = '<ol>';
            foreach ($xml->options->option as $option) {
                $option = str_replace("has", "Has ", $option);
                $option = str_replace("house", "Is house ", $option);
                $option = str_replace("no", "No ", $option);
                $option = str_replace("special", "Special ", $option);
                $options .= '<li>' . $option . '</li>'; // This is to build options into one variable
            }
            $options .= '</ol>';
            $medial = "";

            if ($break_remove == 1) {
                foreach ($xml->media->photos as $photo) {
                    $photo = $photo->photo;
                    $medial = '<img src="' . $photo . '" alt="' . $name . '">';
                    if ($photo != '' || $photo != null) {
                        $desc = '' . $medial . ' ' . $desc . '';
                    } else {
                        $desc = $desc;
                    }
                    $desc = htmlspecialchars($desc);
                }
            } else {
                foreach ($xml->media->photos->photo as $photo) {
                    if (isset($_GET['i'])) {
                        if ($_GET['i'] == 't' && $photo['size'] == 't' && $photo['id'] == '1') {
                            $medial = '<img src="' . $photo . '" alt="' . $name . '"><br />';
                        } elseif ($_GET['i'] == 'm' && $photo['size'] == 'm' && $photo['id'] == '1') {
                            $medial = '<img src="' . $photo . '" alt="' . $name . '"><br />';

                        } elseif ($_GET['i'] == 'l' && $photo['size'] == 'l' && $photo['id'] == '1') {
                            $medial = '<img src="' . $photo . '" alt="' . $name . '"><br />';

                        } elseif ($_GET['i'] == 'x' && $photo['size'] == 'x' && $photo['id'] == '1') {
                            $medial = '<img src="' . $photo . '" alt="' . $name . '"><br />';

                        }

                    } elseif ($photo['size'] == 'x' && $photo['id'] == '1' && !isset($_GET['i'])) {
                        $medial = '<img src="' . $photo . '" alt="' . $name . '"><br />';
                        if ($photo != '' || $photo != null) {
                            $desc = '' . $medial . ' ' . $desc . '';
                        } else {
                            $desc = $desc;
                        }

                    }

                }
            }

            $desc = htmlspecialchars($desc);
            $item->title = '(' . $breeds . ') ' . $name . ' - ' . $size . ' - ' . $sex;
            $item->description = $desc;
            $item->link = "http://www.petfinder.com/petdetail/$id/";
            $rss_channel->items[] = $item;
        }
    }
    $rss_feed = new rssGenerator_rss();
    $rss_feed->encoding = 'UTF-8';
    $rss_feed->version = '2.0';
    header('Content-Type: text/xml');
    echo $rss_feed->createFeed($rss_channel, $config['hosted_by']);
}
?>

