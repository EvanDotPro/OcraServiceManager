<?php
/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license.
 */

namespace OcraServiceManager\ServiceFactory;

use OcraServiceManager\Proxy\ServiceProxyAbstractFactory;
use OcraServiceManager\Proxy\ServiceProxyGenerator;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

use Doctrine\Common\Proxy\Autoloader as ProxyAutoloader;

use Zend\Cache\Storage\Adapter\Memory;

/**
 * Service factory responsible of building a ServiceProxyAbstractFactory
 *
 * @author  Marco Pivetta <ocramius@gmail.com>
 * @license MIT
 */
class ServiceProxyAbstractFactoryFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     *
     * @return ServiceProxyAbstractFactory
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config         = $serviceLocator->get('Config');
        $proxyDir       = isset($config['service_proxies_dir']) ? $config['service_proxies_dir'] : sys_get_temp_dir();
        $proxyNamespace = isset($config['service_proxies_ns'])
            ? $config['service_proxies_ns'] : ServiceProxyGenerator::DEFAULT_SERVICE_PROXY_NS;
        $autoloader     = new ProxyAutoloader();
        $cache          = isset($config['service_proxies_cache'])
            ? $serviceLocator->get($config['service_proxies_cache']) : new Memory();
        $factory        = new ServiceProxyAbstractFactory($cache);

        $factory->setProxyGenerator(new ServiceProxyGenerator($proxyDir, $proxyNamespace));
        $autoloader->register($proxyDir, $proxyNamespace);

        return $factory;
    }
}
