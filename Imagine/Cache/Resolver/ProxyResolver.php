<?php


namespace Liip\ImagineBundle\Imagine\Cache\Resolver;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


/**
 * ProxyResolver
 *
 * @author Robert Schönthal <robert.schoenthal@gmail.com>
 */
class ProxyResolver implements ResolverInterface
{
    /**
     * @var ResolverInterface
     */
    private $resolver;

    /**
     * a list of proxy hosts (picks a random one for each generation to seed browser requests among multiple hosts)
     *
     * @var array
     */
    private $hosts = array();

    /**
     * @param ResolverInterface $resolver
     */
    public function __construct(ResolverInterface $resolver, array $hosts)
    {
        $this->resolver = $resolver;
        $this->hosts = $hosts;
    }

    /**
     * {@inheritDoc}
     */
    public function resolve(Request $request, $path, $filter)
    {
        $response = $this->resolver->resolve($request, $path, $filter);
        $this->rewriteResponse($response);

        return $response;
    }

    /**
     * {@inheritDoc}
     */
    public function store(Response $response, $targetPath, $filter)
    {
        $response = $this->resolver->store($response, $targetPath, $filter);
        $this->rewriteResponse($response);

        return $response;
    }

    /**
     * {@inheritDoc}
     */
    public function getBrowserPath($path, $filter, $absolute = false)
    {
        $response = $this->resolver->getBrowserPath($path, $filter, $absolute);
        $this->rewriteResponse($response);

        return $response;
    }

    /**
     * {@inheritDoc}
     */
    public function remove($targetPath, $filter)
    {
        return $this->resolver->remove($targetPath, $filter);
    }

    /**
     * {@inheritDoc}
     */
    public function clear($cachePrefix)
    {
        $this->resolver->clear($cachePrefix);
    }

    private function rewriteResponse($response)
    {
        if ($response instanceof RedirectResponse && $this->hosts) {
            $path = parse_url($response->getTargetUrl(), PHP_URL_PATH);
            if ($path == $response->getTargetUrl()) {
                //relative path, so strip of SCRIPT_FILE_NAME if existient
                $path = substr($path, (strpos($path, '.php') !== false ? strpos($path, '.php') + 4 : 0));
            }
            $response->setTargetUrl($this->createProxyUrl($path));
        }
    }

    private function createProxyUrl($path)
    {
        $domain = $this->hosts[rand(0, count($this->hosts) - 1)];

        return $domain . $path;
    }
}