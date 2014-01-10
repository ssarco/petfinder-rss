<?php
require_once ("rss.config.php");
function shelter_info($sid,$public_key) {
    $sinfo = simplexml_load_file("http://api.petfinder.com/shelter.get?key=$public_key&id=$sid");
    foreach($sinfo->shelter as $shelter_xml) {
        $shid = $shelter_xml->id;
        $sname = $shelter_xml->name;
        $sname = htmlspecialchars($sname);
        $sname = strip_tags($sname,"<a><b><i>");
        $add1 = $shelter_xml->address1;
        $add2 = $shelter_xml->address2;
        $city = $shelter_xml->city;
        $state = $shelter_xml->state;
        $zip = $shelter_xml->zip;
        $phone = $shelter_xml->phone;
        $fax = $shelter_xml->fax;
        $email = $shelter_xml->email;
    }
    $desco['shid'] = $sid;
    $desco['name'] = $sname;
    $desco['add1'] = $add1;
    $desco['add2'] = $add2;
    $desco['city'] = $city;
    $desco['stat'] = $state;
    $desco['zipc'] = $zip;
    $desco['phon'] = $phone;
    $desco['faxn'] = $fax;
    $desco['mail'] = $email;
    return $desco;

}
function getTierOne($def = '') {
    if($def != '') {
        if($def == 'barnyard') {
            $A = "SELECTED=SELECTED";
        } else {
            $A = null;
        }
        if($def == 'bird') {
            $B = "SELECTED=SELECTED";
        } else {
            $B = null;
        }
        if($def == 'cat') {
            $C = "SELECTED=SELECTED";
        } else {
            $C = null;
        }
        if($def == 'dog') {
            $D = "SELECTED=SELECTED";
        } else {
            $D = null;
        }
        if($def == 'horse') {
            $E = "SELECTED=SELECTED";
        } else {
            $E = null;
        }
        if($def == 'pig') {
            $F = "SELECTED=SELECTED";
        } else {
            $F = null;
        }
        if($def == 'reptile') {
            $G = "SELECTED=SELECTED";
        } else {
            $G = null;
        }
        if($def == 'smallfurry') {
            $H = "SELECTED=SELECTED";
        } else {
            $H = null;
        }
    }
    echo "<form method='GET'>
    <b>Select Species:</b><select name='drop_1' id='drop_1'>
    <option value='' SELECTED=SELECTED>None</option>
    <option value='barnyard' $A>Barnyard</option>
    <option value='bird' $B>Bird</option>
    <option value='cat' $C>Cat</option>
    <option value='dog' $D>Dog</option>
    <option value='horse' $E>Horse</option>
    <option value='pig' $F>Pig</option>
    <option value='reptile' $G>Reptile</option>
    <option value='smallfurry' $H>Small Furry</option>
    </select>*
    </form>";

}

/*
RSS Feed Generator for PHP 4 or higher version
Version 1.0.3  
Written by Vagharshak Tozalakyan <vagh@armdex.com>
License: GNU Public License

Classes in package:
class rssGenerator_rss
class rssGenerator_channel
class rssGenerator_image
class rssGenerator_textInput
class rssGenerator_item

For additional information please refer the documentation
*/

class rssGenerator_rss {
    var $rss_version = '2.0';
    var $encoding = '';
    var $stylesheet = '';
    function cData($str) {
        return '<![CDATA[ '.$str.' ]]>';
    }
    function createFeed($channel, $hosted_by) {
        
        $selfUrl = (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] != 'on'?'http://':
            'https://');
        $selfUrl .= $_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
        $rss = '<?xml version="1.0"';
        if(!empty($this->encoding)) {
            $rss .= ' encoding="'.$this->encoding.'"';
        }
        $rss .= '?>'."\n";
        if(!empty($this->stylesheet)) {
            $rss .= $this->stylesheet."\n";
        }
        $rss .= '<!-- Generated on '.date('r').' -->'."\n";
        $rss .= '<rss version="'.$this->rss_version.
            '" xmlns:atom="http://www.w3.org/2005/Atom">'."\n";
        $rss .= '  <channel>'."\n";
        $rss .= '    <atom:link href="'.($channel->atomLinkHref?$channel->atomLinkHref:
            $selfUrl).'" rel="self" type="application/rss+xml" />'."\n";
        $rss .= '    <title>'.$channel->title.'</title>'."\n";
        $rss .= '    <link>'.$channel->link.'</link>'."\n";
        $rss .= '    <description>Data by: Petfinder.com and hosted by '.$hosted_by."Agency Listing Information: ".$channel->description.'</description>'."\n";
        if(!empty($channel->language)) {
            $rss .= '    <language>'.$channel->language.'</language>'."\n";
        }
        if(!empty($channel->copyright)) {
            $rss .= '    <copyright>'.$channel->copyright.'</copyright>'."\n";
        }
        if(!empty($channel->managingEditor)) {
            $rss .= '    <managingEditor>'.$channel->managingEditor.'</managingEditor>'."\n";
        }
        if(!empty($channel->webMaster)) {
            $rss .= '    <webMaster>'.$channel->webMaster.'</webMaster>'."\n";
        }
        if(!empty($channel->notice)) {
            $rss .= '    <notice>'.$channel->notice.'</notice>'."\n";
        }
        if(!empty($channel->pubDate)) {
            $rss .= '    <pubDate>'.$channel->pubDate.'</pubDate>'."\n";
        }
        if(!empty($channel->lastBuildDate)) {
            $rss .= '    <lastBuildDate>'.$channel->lastBuildDate.'</lastBuildDate>'."\n";
        }
        foreach($channel->categories as $category) {
            $rss .= '    <category';
            if(!empty($category['domain'])) {
                $rss .= ' domain="'.$category['domain'].'"';
            }
            $rss .= '>'.$category['name'].'</category>'."\n";
        }
        if(!empty($channel->generator)) {
            $rss .= '    <generator>'.$channel->generator.'</generator>'."\n";
        }
        if(!empty($channel->docs)) {
            $rss .= '    <docs>'.$channel->docs.'</docs>'."\n";
        }
        if(!empty($channel->ttl)) {
            $rss .= '    <ttl>'.$channel->ttl.'</ttl>'."\n";
        }
        if(sizeof($channel->skipHours)) {
            $rss .= '    <skipHours>'."\n";
            foreach($channel->skipHours as $hour) {
                $rss .= '      <hour>'.$hour.'</hour>'."\n";
            }
            $rss .= '    </skipHours>'."\n";
        }
        if(sizeof($channel->skipDays)) {
            $rss .= '    <skipDays>'."\n";
            foreach($channel->skipDays as $day) {
                $rss .= '      <day>'.$day.'</day>'."\n";
            }
            $rss .= '    </skipDays>'."\n";
        }
        if(!empty($channel->image)) {
            $image = $channel->image;
            $rss .= '    <image>'."\n";
            $rss .= '      <url>'.$image->url.'</url>'."\n";
            $rss .= '      <title>'.$image->title.'</title>'."\n";
            $rss .= '      <link>'.$image->link.'</link>'."\n";
            if($image->width) {
                $rss .= '      <width>'.$image->width.'</width>'."\n";
            }
            if($image->height) {
                $rss .= '      <height>'.$image->height.'</height>'."\n";
            }
            if(!empty($image->description)) {
                $rss .= '      <description>'.$image->description.'</description>'."\n";
            }
            $rss .= '    </image>'."\n";
        }
        if(!empty($channel->textInput)) {
            $textInput = $channel->textInput;
            $rss .= '    <textInput>'."\n";
            $rss .= '      <title>'.$textInput->title.'</title>'."\n";
            $rss .= '      <description>'.$textInput->description.'</description>'."\n";
            $rss .= '      <name>'.$textInput->name.'</name>'."\n";
            $rss .= '      <link>'.$textInput->link.'</link>'."\n";
            $rss .= '    </textInput>'."\n";
        }
        if(!empty($channel->cloud_domain) || !empty($channel->cloud_path) || !empty($channel->cloud_registerProcedure) ||
            !empty($channel->cloud_protocol)) {
            $rss .= '    <cloud domain="'.$channel->cloud_domain.'" ';
            $rss .= 'port="'.$channel->cloud_port.'" path="'.$channel->cloud_path.'" ';
            $rss .= 'registerProcedure="'.$channel->cloud_registerProcedure.'" ';
            $rss .= 'protocol="'.$channel->cloud_protocol.'" />'."\n";
        }
        if(!empty($channel->extraXML)) {
            $rss .= $channel->extraXML."\n";
        }
        foreach($channel->items as $item) {
            $rss .= '    <item>'."\n";
            if(!empty($item->image_large->url)) {
                $rss .= '    <image_large>'.$item->image_large->url.'</image_large>'."\n";
            }
            if(!empty($item->image->url)) {
                $rss .= '    <image>'.$item->image->url.'</image>'."\n";
            }
            if(!empty($item->image_large->title)) {
                $rss .= '    <title_large>'.$item->image_large->title.'</title_large>'."\n";
            }
            if(!empty($item->image_medium->url)) {
                $rss .= '    <image_medium>'.$item->image_medium->url.'</image_medium>'."\n";
            }
            if(!empty($item->image_medium->title)) {
                $rss .= '    <title_medium>'.$item->image_medium->title.'</title_medium>'."\n";
            }
            if(!empty($item->image_small->url)) {
                $rss .= '    <image_small>'.$item->image_small->url.'</image_small>'."\n";
            }
            if(!empty($item->image_small->title)) {
                $rss .= '    <title_small>'.$item->image_small->title.'</title_small>'."\n";
            }
            if(!empty($item->image_thumb->url)) {
                $rss .= '    <image_thumb>'.$item->image_thumb->url.'</image_thumb>'."\n";
            }
            if(!empty($item->image_thumb->title)) {
                $rss .= '    <title_thumb>'.$item->image_thumb->title.'</title_thumb>'."\n";
            }
            if(!empty($item->title)) {
                $rss .= '      <title>'.$item->title.'</title>'."\n";
            }
            if(!empty($item->description)) {
                $rss .= '      <description>'.$item->description.'</description>'."\n";
            }
            if(!empty($item->link)) {
                $rss .= '      <link>'.$item->link.'</link>'."\n";
            }
            if(!empty($item->pubDate)) {
                $rss .= '      <pubDate>'.$item->pubDate.'</pubDate>'."\n";
            }
            if(!empty($item->author)) {
                $rss .= '      <author>'.$item->author.'</author>'."\n";
            }
            if(!empty($item->comments)) {
                $rss .= '      <comments>'.$item->comments.'</comments>'."\n";
            }
            if(!empty($item->guid)) {
                $rss .= '      <guid isPermaLink="';
                $rss .= ($item->guid_isPermaLink?'true':'false').'">';
                $rss .= $item->guid.'</guid>'."\n";
            }
            if(!empty($item->source)) {
                $rss .= '      <source url="'.$item->source_url.'">';
                $rss .= $item->source.'</source>'."\n";
            }
            if(!empty($item->enclosure_url) || !empty($item->enclosure_type)) {
                $rss .= '      <enclosure url="'.$item->enclosure_url.'" ';
                $rss .= 'length="'.$item->enclosure_length.'" ';
                $rss .= 'type="'.$item->enclosure_type.'" />'."\n";
            }
            foreach($item->categories as $category) {
                $rss .= '      <category';
                if(!empty($category['domain'])) {
                    $rss .= ' domain="'.$category['domain'].'"';
                }
                $rss .= '>'.$category['name'].'</category>'."\n";
            }
            $rss .= '    </item>'."\n";
        }
        $rss .= '  </channel>'."\r";
        return $rss .= '</rss>';
    }
}
class rssGenerator_channel {
    var $atomLinkHref = '';
    var $title = '';
    var $link = '';
    var $description = '';
    var $language = '';
    var $copyright = '';
    var $managingEditor = '';
    var $webMaster = '';
    var $pubDate = '';
    var $lastBuildDate = '';
    var $categories = array();
    var $generator = '';
    var $docs = '';
    var $ttl = '';
    var $image = '';
    var $textInput = '';
    var $skipHours = array();
    var $skipDays = array();
    var $cloud_domain = '';
    var $cloud_port = '80';
    var $cloud_path = '';
    var $cloud_registerProcedure = '';
    var $cloud_protocol = '';
    var $items = array();
    var $extraXML = '';
}

class rssGenerator_image {
    var $url = '';
    var $title = '';
    var $link = '';
    var $width = '88';
    var $height = '31';
    var $description = '';
}

class rssGenerator_textInput {
    var $title = '';
    var $description = '';
    var $name = '';
    var $link = '';
}

class rssGenerator_item {
    var $title = '';
    var $description = '';
    var $link = '';
    var $author = '';
    var $pubDate = '';
    var $comments = '';
    var $guid = '';
    var $guid_isPermaLink = true;
    var $source = '';
    var $source_url = '';
    var $enclosure_url = '';
    var $enclosure_length = '0';
    var $enclosure_type = '';
    var $categories = array();
}

// END  RSS Feed Generator Class

?>