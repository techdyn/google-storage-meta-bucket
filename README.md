# Google Cloud Storage Meta Bucket for PHP
A simple GCP storage bucket wrapper to automatically set metadata on uploaded files.

Metadata reference: https://cloud.google.com/storage/docs/metadata

Created for use with other libraries that may not allow you to configure the metadata (eg Cache-Control) for storage bucket objects.

Works with:
- https://github.com/1up-lab/OneupFlysystemBundle
- https://github.com/dustin10/VichUploaderBundle (using Flysystem)
- https://github.com/liip/LiipImagineBundle (using Flysystem)

### Installation

Requires: https://github.com/googleapis/google-cloud-php-storage

To begin, install the preferred dependency manager for PHP, [Composer](https://getcomposer.org/).

Install the wrapper:

```sh
$ composer require techdyn/google-storage-meta-bucket
```

### Sample

```php
require 'vendor/autoload.php';

use TechDyn\GoogleStorageMetaBucket\Storage\ProxyStorageClient;

$storage = new ProxyStorageClient(); // ProxyStorageClient extends Google\Cloud\Storage\StorageClient;

$bucket = $storage->bucket('my_bucket'); // MetaBucket extends Google\Cloud\Storage\Bucket

// https://cloud.google.com/storage/docs/metadata
$bucket->setOption('cacheControl', 'no-cache, max-age=60'); // $name uses metadata field written as camelCase

// Upload a file to the bucket.
$bucket->upload(
    fopen('/data/file.txt', 'r')
);
```

### Symfony & Flysystem

```yaml
# services.yaml

parameters:

    gcp_client_options:
        projectId: 'gcp-project-id'
        keyFilePath: '%kernel.project_dir%/config/gcp/service.json' # Optional - if not configured externally

    gcp_storage_bucket: 'name-of-bucket'

services:
  
  google_cloud_storage.client:
    class: TechDyn\GoogleStorageMetaBucket\Storage\ProxyStorageClient
    arguments: ['%gcp_client_options%']

  google_cloud_storage.bucket:
    class: TechDyn\GoogleStorageMetaBucket\Storage\MetaBucket
    factory: ['@google_cloud_storage.client', bucket]
    arguments: ['%gcp_storage_bucket%']
    calls:
      - method: setOption
        arguments:
          - 'cacheControl'
          - 'no-cache, max-age=60'
```

##### Flysystem

Using: https://github.com/1up-lab/OneupFlysystemBundle

```yaml
# packages/oneup_flysystem.yaml

# Read the documentation: https://github.com/1up-lab/OneupFlysystemBundle/tree/master/Resources/doc/index.md
oneup_flysystem:
    adapters:
        gcp_storage_adapter:
            googlecloudstorage:
                client: google_cloud_storage.client
                bucket: google_cloud_storage.bucket
                prefix: ~

    filesystems:
        gcp_storage_fs:
            adapter: gcp_storage_adapter
            mount:   gcp_storage_fs
```

##### Vich Uploader

Using: https://github.com/dustin10/VichUploaderBundle

```yaml
# packages/vich_uploader.yaml

vich_uploader:
    db_driver: orm
    storage: flysystem
    mappings:
        uploaded_images:
            uri_prefix:         'https://%gcp_storage_bucket%.storage.googleapis.com' # https://name-of-bucket.storage.googleapis.com or your custom domain
            upload_destination: gcp_storage_fs
            namer:              vich_uploader.namer_uniqid
            inject_on_load:     false
            delete_on_update:   true
            delete_on_remove:   true
```

##### Liip Imagine

Using: https://github.com/liip/LiipImagineBundle

```yaml
# packages/liip_imagine.yaml

# See dos how to configure the bundle: https://symfony.com/doc/current/bundles/LiipImagineBundle/basic-usage.html
liip_imagine:
  driver: "gd" # valid drivers options include "gd" or "gmagick" or "imagick"

  loaders:
    gcp_loader:
      flysystem:
        filesystem_service: oneup_flysystem.gcp_storage_fs_filesystem

  data_loader: gcp_loader
  resolvers:
    gcp_cache:
      flysystem:
        root_url: 'https://%gcp_storage_bucket%.storage.googleapis.com'
        filesystem_service: oneup_flysystem.gcp_storage_fs_filesystem

  filter_sets:
    thumb_default:
      data_loader: gcp_loader
      cache: gcp_cache
      quality: 75
      filters:
        downscale: { max: [64, 64], mode: outbound }
```