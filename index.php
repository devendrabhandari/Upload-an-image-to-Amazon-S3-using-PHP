<?php

// Include the AWS SDK using the Composer autoloader
require_once(dirname(__FILE__) . '/vendor/autoload.php');

use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;

// Set AWS S3 credentials
define('ACCESS_KEY', 'XXXX');
define('SECRET_KEY', 'XXXX');
define('BUCKET_NAME', 'XXXX');
define('REGION', 'XXXX');
define('END_POINT', 'XXXX');
define('FOLDER_NAME', 'XXXX');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['post_image']) && $_FILES['post_image']['error'] == UPLOAD_ERR_OK) {
	try {
		// Instantiate the S3 client
		$client = S3Client::factory(array(
			'credentials' => array(
				'key' => ACCESS_KEY,
				'secret' => SECRET_KEY,
			),
			'region' => REGION,
			'version' => 'latest',
			'base_url' => END_POINT,
		));

		// Get image extension
		$path = $_FILES['post_image']['name'];
		$ext = pathinfo($path, PATHINFO_EXTENSION);

		// Generate unique file name
		$fileName = bin2hex(openssl_random_pseudo_bytes(10)) . '.' . $ext;
	
		// Upload an image to Amazon S3
		$result = $client->putObject(array(
			'Bucket'		=> BUCKET_NAME,
			'Key'			=> FOLDER_NAME . '/' .$fileName,
			'SourceFile'	=> $_FILES['post_image']['tmp_name'],
			'ContentType'	=> 'image/' . $ext,
			'ACL'			=> 'public-read',
		));

		// Get the URL of the image
		$imageUrl = $result['ObjectURL'];
		$msg = 'Image has been uploaded successfully, Please check the path of the image:' . $imageUrl;
	} catch(S3Exception $e) {
		$msg = $e->getMessage();
	}
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Upload Image To Amazon S3 Using PHP</title>
</head>
<body>
<h1>Upload Image To Amazon S3 Using PHP</h1>
<?php if (isset($msg)) { echo '<p  style="background: #FCF7A3;padding: 10px 10px;">' . $msg . '</p>'; } ?>
<form method="post" enctype="multipart/form-data">
<p><input type="file" name="post_image"></p>
<p><button type="submit">Submit</button></p>
</form>
</body>
</html>