<?php
 /*
    WEBlife CMS
    Developed by http://weblife.ua/
*/
define('WEBlife', 1);  //Set flag that this is a parent file
define('WLCMS_ZONE', 'FRONTEND'); //Set flag that this is a site area

require('../kernel.php');

$category_id = intval($_GET['catid']);

$rss_items = getItemsToNewsTicker($newsinticker, $category_id);

header("Content-Type: text/xml");
header("Pragma: no-cache");

$output_rss = '<?xml version="1.0" encoding="windows-1251"?>' . "\n";
$output_rss .= '<rss version="2.0">' . "\n";
$output_rss .= '    <channel>' . "\n";
$output_rss .= '        <title>' . $websiteName . '</title>' . "\n";
$output_rss .= '        <link>' . $websiteUrl . '</link>' . "\n";
$output_rss .= '        <description>' . htmlspecialchars($copyright) . '</description>' . "\n";

if(is_array($rss_items) && !empty($rss_items)){
    foreach($rss_items as $item){
        $output_rss .= '        <item>' . "\n";
        $output_rss .= '            <title>' . htmlspecialchars($item['title']) . '</title>' . "\n";
        $output_rss .= '            <link>' . $websiteUrl . '/news_' . $item['id'] . '.html</link>' . "\n";
        //$output_rss .= '            <description>Here your can put short description of this news</description>' . "\n";
        $output_rss .= '            <pubDate>' . $item['date_add'] . '</pubDate>' . "\n";
        $output_rss .= '        </item>' . "\n";
    }
} else {
        $output_rss .= '        <item>' . "\n";
        $output_rss .= '            <title>Up to Date</title>' . "\n";
        $output_rss .= '            <link>' . $websiteUrl . '</link>' . "\n";
        $output_rss .= '            <description>Rss is Empty. No Items Available</description>' . "\n";
        $output_rss .= '            <pubDate>' . date("Y-m-d H:i:s") . '</pubDate>' . "\n";
        $output_rss .= '        </item>' . "\n";
}

$output_rss .= '    </channel>' . "\n";
$output_rss .= '</rss>' . "\n";

echo $output_rss;
