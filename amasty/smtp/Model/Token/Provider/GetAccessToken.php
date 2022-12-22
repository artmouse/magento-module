<?php

declare(strict_types=1);

namespace Amasty\Smtp\Model\Token\Provider;

use Amasty\Smtp\Model\Token\Generator\AuthTokenGeneratorInterface;
use Magento\Framework\App\CacheInterface;
use Magento\Framework\Encryption\EncryptorInterface;

class GetAccessToken implements AuthTokenProviderInterface
{
    /**
     * We are using lifetime gap to prevent token invalidation because of computing time gap
     * between receiving access token and saving it to the cache storage.
     */
    public const LIFETIME_GAP = 100;

    /**
     * @var CacheInterface
     */
    private $cache;

    /**
     * @var EncryptorInterface
     */
    private $encryptor;

    /**
     * @var AuthTokenGeneratorInterface
     */
    private $accessTokenGenerator;

    public function __construct(
        CacheInterface $cache,
        EncryptorInterface $encryptor,
        AuthTokenGeneratorInterface $accessTokenGenerator
    ) {
        $this->cache = $cache;
        $this->encryptor = $encryptor;
        $this->accessTokenGenerator = $accessTokenGenerator;
    }

    public function execute(): string
    {
        if ($encryptedToken = $this->cache->load('google_smtp_access_token')) {
            return $this->encryptor->decrypt($encryptedToken);
        }

        $token = $this->accessTokenGenerator->generateToken();
        $this->cache->save(
            $this->encryptor->encrypt($token),
            'google_smtp_access_token',
            [],
            AuthTokenGeneratorInterface::TOKEN_LIFETIME - self::LIFETIME_GAP
        );

        return $token;
    }
}
