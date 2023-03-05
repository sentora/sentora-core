SSLProtocol All -SSLv2 -SSLv3
SSLHonorCipherOrder on
SSLCipherSuite ECDH+AESGCM:DH+AESGCM:ECDH+AES256:DH+AES256:ECDH+AES128:DH+AES:ECDH+3DES:DH+3DES:RSA+AESGCM:RSA+AES:RSA+3DES:!aNULL:!MD5:!DSS
SSLEngine On
SSLCertificateFile {$vh.hosted_dir}{$vh.vhost_user}/ssl/{$vh.server_name}/cert.pem
SSLCertificateKeyFile {$vh.hosted_dir}{$vh.vhost_user}/ssl/{$vh.server_name}/privkey.pem
SSLCertificateChainFile {$vh.hosted_dir}{$vh.vhost_user}/ssl/{$vh.server_name}/chain.pem
SSLCompression off