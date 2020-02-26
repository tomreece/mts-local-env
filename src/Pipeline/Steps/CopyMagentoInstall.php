<?php
namespace Pipeline\Steps;

use Aws\S3\S3Client;

class CopyMagentoInstall extends Step {
    private $arn;
    private $destination;
    /**
     * MagentoInstallRunner constructor.
     *
     * Notice we don't pass credentials to the CodeBuildClient class. These are picked up from environment variables.
     * Set these environment variables in your IDE or your shell before running this script:
     *   AWS_ACCESS_KEY_ID
     *   AWS_SESSION_TOKEN
     *   AWS_SECRET_ACCESS_KEY
     */
    function __construct($artifactARN, $destination)
    {
        $this->arn = $artifactARN;
        $this->destination = rtrim($destination, DIRECTORY_SEPARATOR);
    }

    /**
     * The main function, returns array of info required for other jobs
     */
    public function main()
    {
        if (is_file($this->destination . ".zip")) {
            print("$this->destination is already copied, skipping copy.\n");
            return;
        }
        // Find bucket + key to copy the object form S3
        $parts = explode("/", $this->arn);
        $bucket = str_replace("arn:aws:s3:::", "", $parts[0]);
        array_shift($parts);
        $key = implode("/", $parts);
        $client = new S3Client([
            'version' => 'latest',
            'region' => 'us-east-1'
        ]);

        print("Copying $key from $bucket to $this->destination.zip ...\n");
        $object = $client->getObject([
            "Bucket" => $bucket,
            "Key" => $key
        ]);
        file_put_contents($this->destination . ".zip", $object['Body']->getContents());
        print("Copy complete.\n");

        print("Extracting contents to $this->destination ...\n");
        $zip = new \ZipArchive();
        $zip->open($this->destination . ".zip");
        $zip->extractTo($this->destination);
        $zip->close();
        print("Extract complete.\n");
        if (!is_file($this->destination . "/install.tar")) {
            print ("NO MAGENTO INSTALL FOUND IN $this->destination");
            return;
        }
        print("Untarring contents of $this->destination/install.tar ...\n");
        $phar = new \PharData($this->destination . "/install.tar");
        $phar->extractTo($this->destination);
        print("Untar complete.\n");
    }
}
