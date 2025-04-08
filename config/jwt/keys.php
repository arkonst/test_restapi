<?php
$config = [
         'private_key_bits' => 4096,
         'private_key_type' => OPENSSL_KEYTYPE_RSA,
     ];
     $privateKey = openssl_pkey_new($config);
     openssl_pkey_export($privateKey, $privateKeyPem);
     file_put_contents('private.pem', $privateKeyPem);

     $publicKey = openssl_pkey_get_details($privateKey)['key'];
     file_put_contents('public.pem', $publicKey);

     echo 'Ключи успешно сгенерированы!';