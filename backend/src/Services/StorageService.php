<?php

namespace App\Services;

use Aws\S3\S3Client;
use Aws\Exception\AwsException;

class StorageService
{
    protected $s3;
    protected $bucket;

    public function __construct()
    {
        $config = [
            'version' => 'latest',
            'region'  => $_ENV['AWS_REGION'],
            'credentials' => [
                'key'    => $_ENV['AWS_ACCESS_KEY_ID'],
                'secret' => $_ENV['AWS_SECRET_ACCESS_KEY'],
            ],
        ];

        if (!empty($_ENV['AWS_S3_ENDPOINT'])) {
            $config['endpoint'] = $_ENV['AWS_S3_ENDPOINT'];
            $config['use_path_style_endpoint'] = $_ENV['AWS_S3_PATH_STYLE'] === 'true';
        }

        $this->s3 = new S3Client($config);
        $this->bucket = $_ENV['AWS_S3_BUCKET'];
    }

    public function upload($key, $filePath, $mimeType = 'application/octet-stream')
    {
        try {
            return $this->s3->putObject([
                'Bucket'      => $this->bucket,
                'Key'         => $key,
                'SourceFile'  => $filePath,
                'ContentType' => $mimeType,
                'ACL'         => 'private',
            ]);
        } catch (AwsException $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function getPresignedUrl($key, $expires = '+1 hour')
    {
        $cmd = $this->s3->getCommand('GetObject', [
            'Bucket' => $this->bucket,
            'Key'    => $key
        ]);

        $request = $this->s3->createPresignedRequest($cmd, $expires);
        return (string)$request->getUri();
    }
}
