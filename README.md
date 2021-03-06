GluTorrent
==========

The BEncoded BitTorrent class developed by GluTorrent.

Unoffical mirror of https://code.google.com/p/glutorrent/

> Source: http://web.archive.org/web/20080127172806/http://www.glutorrent.com/developer/

> This is a BEncoded BitTorrent class that was originally developed by a company called GluTorrent, and was hosted at http://glutorrent.com/developer.

> The http://glutorrent.com domain does not seem to be up any longer, but since this seems to be a useful class, I wanted to make sure others had access to it for posterity.

> I'm not the owner or developer of this code, and I have no idea how to get in touch with the appropriate people, so if you know who to contact about this, please let me know. I will not be continuing development of the class. If you want to contribute, let me know and I'll add you as a contributor on the project. 

> - [BitTorrent specification](http://wiki.theory.org/BitTorrentSpecification)
> - [Original developer site](http://web.archive.org/web/20080127172806/http://www.glutorrent.com/developer/) (via Wayback Machine)
> - [Original blog](http://web.archive.org/web/20080127172806/http://www.glutorrent.com/blog/) (via Wayback Machine)
> - [PEAR BitTorrent2 class](http://www.pear.php.net/package/File_Bittorrent2/)
> - [Bencode](http://en.wikipedia.org/wiki/Bencode) (via Wikipedia)
> - [Original BitTorrent class](http://pypi.python.org/pypi/BitTorrent-bencode/5.0.8) (Python) 

Example
==========

```php
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
```
