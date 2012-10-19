<?php
namespace Mazenovi\WsseAuthBundle\Security\Authentication\Provider;

use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\NonceExpiredException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

use Symfony\Component\Filesystem\Filesystem;

use Mazenovi\WsseAuthBundle\Security\Authentication\Token\WsseUserToken;

class WsseProvider implements AuthenticationProviderInterface
{
    private $userProvider;
    private $cacheDir;

    public function __construct(UserProviderInterface $userProvider, $noncesCacheDir, $lifetime, $cacheDir)
    {
        $this->userProvider        = $userProvider;
        $this->noncesCacheDir      = $noncesCacheDir;
        $this->lifetime            = $lifetime;
        $this->cacheDir            = $cacheDir;
        $this->noncesFullCachePath = $this->cacheDir.'/'. $this->noncesCacheDir;
    }

    public function authenticate(TokenInterface $token)
    {
        
        $user = $this->userProvider->loadUserByUsername($token->getUsername());  

        if ($user && $this->validateDigest($token->digest, $token->nonce, $token->created, $user->getPassword())) {
            $authenticatedToken = new WsseUserToken($user->getRoles());
            $authenticatedToken->setUser($user);

            return $authenticatedToken;
        }

        throw new AuthenticationException('The WSSE authentication failed.');
    }

    protected function validateDigest($digest, $nonce, $created, $secret)
    {
        // Expire timestamp after lifetime seconds
        if (time() - strtotime($created) > $this->lifetime) {
            return false;
        }
      
        if (file_exists($this->noncesFullCachePath .'/'. $nonce) 
            && file_get_contents($this->noncesFullCachePath .'/'. $nonce) + $this->lifetime < time()
        ) {
            throw new NonceExpiredException('Previously used nonce detected');
        }

        $fs = new Filesystem();

        if(!$fs->exists($this->noncesFullCachePath))
        {
            $paths = explode('/', $this->noncesCacheDir);
            $pathToCreate = $this->cacheDir;
            foreach($paths as $path)
            {
                $pathToCreate.='/'.$path;
                if(!$fs->exists($pathToCreate))
                {
                   $fs->mkdir($pathToCreate);
                }
            }
        }
        file_put_contents($this->noncesFullCachePath.'/'.$nonce, time());

        // Validate Secret
        $expected = base64_encode(sha1(base64_decode($nonce).$created.$secret, true));

        return $digest === $expected;
    }

    public function supports(TokenInterface $token)
    {
        return $token instanceof WsseUserToken;
    }
}