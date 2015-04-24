Image Reprocessor
=================
Reprocesses images in the silverorange site framework.

Usage:
```
Reprocesses all images in an image set and queues them for CDN upload.

Usage:
  ./bin/image-reprocessor.php [options] image-set dsn

Options:
  -v, --verbose  Set verbosity level. Use multiples for more detail (e.g.
                 "-vv".)
  --no-cdn       Don't queue images for upload to CDN after processing.
  -h, --help     show this help message and exit
  --version      show the program version and exit

Arguments:
  image-set  The image set shortname to re-process.
  dsn        Database DSN in MDB2 format (e.g.
             pgsql://username@host/database).

```
