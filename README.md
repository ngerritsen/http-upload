# php-http-deploy

Using a standard Apache webhost? And you have no nice SSH access, or it's too much of a hassle. FTP is slow or doesn't work at all? Deploy to your server over HTTP(S).
 
## Installation

1. Install dependencies with: `composer install`.
2. Change the `key` in `etc/config.php` to a secret key.
3. You can also change to `rootDir` by default it is `/../` which should be the `public_html` if you place this application in a folder there. 
4. Put this on your server in a public folder in the root called upload, for most Apache configurations this will be something like: `/public_html/deploy`.
5. With most common webhost configurations you should now be able to `POST` to `https://my-website.com/upload/`.

## Usage

To deploy, zip your files and then simply POST the zip to the endpoint with the key and the directory as headers. Like `curl -F 'data=@./my-zip.zip' -H 'X-Key: secret-key' -H 'X-ExtractTo: my-app' https://my-website.com/upload/`.

> I am not in any way responsible for the security of your server. Check out the sourcecode for yourself. Make sure to use a long, random, secret key and only use the endpoint over HTTPS.
