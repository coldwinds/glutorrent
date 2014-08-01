<?php
// http://www.glutorrent.com/developer/
// http://web.archive.org/web/20080127172806/http://www.glutorrent.com/developer/

require 'IBEncodedValue.php';
require 'BEncodingException.class.php';
require 'BEncodingParserException.class.php';
require 'BEncodingInvalidIndexException.class.php';
require 'BEncodingInvalidValueException.class.php';
require 'BEncodingOutOfRangeException.class.php';
require 'BEncodedDictionaryCollection.class.php';
require 'BEncodedDictionary.class.php';
require 'BEncodedDictionaryCollectionIterator.class.php';
require 'BEncodedListCollection.class.php';
require 'BEncodedList.class.php';
require 'BEncodedString.class.php';
require 'BEncodedInteger.class.php';


$MyNewTracker = 'http://tracker.example.com:6969/announce';

// Example of modifying the announce and announce list fields of a torrent

foreach(glob('C:\\torrents\\*.torrent') as $TorrentFile){
    $Torrent = new BEncodedDictionary();
    $Torrent->FromString(file_get_contents($TorrentFile));
    $Torrent['announce-list'] = new BEncodedList();
    $Torrent['announce'] = $MyNewTracker;
    $Torrent['announce-list'][] = new BEncodedList(array($MyNewTracker));
    // Let's see the torrent
    print_r($Torrent->ToArray());
    echo "\n";
    file_put_contents($TorrentFile, $Torrent->Encode());
}
?>
